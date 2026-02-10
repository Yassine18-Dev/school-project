<?php

namespace App\Controller;

use App\Entity\SupportRequest;
use App\Entity\SupportCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SupportController extends AbstractController
{
    #[Route('/support', name: 'app_support')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $supportRequest = new SupportRequest();

        // 🎯 1️⃣ Get category from URL (?category=ID)
        $categoryId = $request->query->get('category');

        if ($categoryId) {
            $category = $em->getRepository(SupportCategory::class)->find($categoryId);
            if ($category) {
                $supportRequest->setCategory($category);
            }
        }

        // 🧠 2️⃣ Build form
        $form = $this->createFormBuilder($supportRequest)
            ->add('category', EntityType::class, [
                'class' => SupportCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a category',
            ])
            ->add('subject', TextType::class, [
                'attr' => ['placeholder' => 'Briefly describe your issue']
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Provide full details about your issue...'
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        // 🚀 3️⃣ Handle submission
        if ($form->isSubmitted() && $form->isValid()) {

            $supportRequest->setStatus('open');
            $supportRequest->setCreatedAt(new \DateTimeImmutable());

            $em->persist($supportRequest);
            $em->flush();

            $this->addFlash('success', 'Your request has been sent successfully.');

            return $this->redirectToRoute('app_support');
        }

        // 🎨 4️⃣ Render page
        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
