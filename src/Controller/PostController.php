<?php
// src/Controller/PostController.php
namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostImage;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/community')]
class PostController extends AbstractController
{
    #[Route('/', name: 'community_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupère tous les posts, du plus récent au plus ancien
        $posts = $em->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('community/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/create', name: 'community_create')]
#[IsGranted('ROLE_USER')]
public function create(Request $request, EntityManagerInterface $em): Response
{
    $post = new Post();

    // Crée le formulaire via AbstractController
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $post->setAuthor($this->getUser());
        $post->setCreatedAt(new \DateTime());

        // ✅ Gestion des images uploadées
        $imageFiles = $form->get('images')->getData(); // tableau de UploadedFile
        if ($imageFiles) {
            foreach ($imageFiles as $imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );

                    // Crée l'entité PostImage et lie au Post
                    $postImage = new PostImage();
                    $postImage->setFilename($newFilename);
                    $post->addImage($postImage);

                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }
        }

        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Votre post a été publié avec succès !');

        return $this->redirectToRoute('community_index');
    }

    return $this->render('community/create.html.twig', [
        'form' => $form->createView(),
    ]);
}



    #[Route('/{id}/delete', name: 'community_delete')]
    public function delete(Post $post, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Seul l'auteur ou un admin peut supprimer
        if ($post->getAuthor() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce post.');
        }

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Le post a été supprimé.');

        return $this->redirectToRoute('community_index');
    }
}
