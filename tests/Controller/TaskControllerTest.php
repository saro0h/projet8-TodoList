<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;

class TaskControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function task_should_be_displayed()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('jean@sf.com');
        $client->loginUser($testUser);

        /*
            on récupère le router pour générer directement une url
            car plus tard l'url peut évolué donc on ne veut pas l'écrire en dure
        */
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_list")
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(20, $crawler->filter('.thumbnail'));
    }

    /**
     * @test
     */
    public function added_task_should_be_displayed()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->findOneBy([]);

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_create", ["id" => $task->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'new task',
            'task[content]' => 'new task',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertSelectorTextContains('html', 'new task');
    }

    /**
     * @test
     */
    public function edited_task_should_be_displayed()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('jean@sf.com');
        $client->loginUser($testUser);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $task->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
