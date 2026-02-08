<?php

namespace App\Tests\Controller;

use App\Entity\Prediction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PredictionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $predictionRepository;
    private string $path = '/prediction/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->predictionRepository = $this->manager->getRepository(Prediction::class);

        foreach ($this->predictionRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Prediction index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'prediction[scorePredit]' => 'Testing',
            'prediction[datePrediction]' => 'Testing',
            'prediction[tournoi]' => 'Testing',
            'prediction[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->predictionRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Prediction();
        $fixture->setScorePredit('My Title');
        $fixture->setDatePrediction('My Title');
        $fixture->setTournoi('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Prediction');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Prediction();
        $fixture->setScorePredit('Value');
        $fixture->setDatePrediction('Value');
        $fixture->setTournoi('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'prediction[scorePredit]' => 'Something New',
            'prediction[datePrediction]' => 'Something New',
            'prediction[tournoi]' => 'Something New',
            'prediction[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/prediction/');

        $fixture = $this->predictionRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getScorePredit());
        self::assertSame('Something New', $fixture[0]->getDatePrediction());
        self::assertSame('Something New', $fixture[0]->getTournoi());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Prediction();
        $fixture->setScorePredit('Value');
        $fixture->setDatePrediction('Value');
        $fixture->setTournoi('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/prediction/');
        self::assertSame(0, $this->predictionRepository->count([]));
    }
}
