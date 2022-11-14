<?php

namespace App\Tests\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;

class CreateTest extends WebTestCase
{
    /**
     * @test
     */
    public function added_task_should_be_displayed_and_redirect_to_tasks_list(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_create")
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
    public function task_should_not_be_registered_due_to_blank_title_and_raise_form_error(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->findOneBy([]);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_create", ["id" => $task->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => '',
            'task[content]' => 'blank title task',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir un titre.');
    }

    /**
     * @test
     */
    public function task_should_not_be_registered_due_to_blank_description_and_raise_form_error(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->findOneBy([]);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_create", ["id" => $task->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'blank description task',
            'task[content]' => '',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir du contenu.');
    }

    /**
     * @test
     */
    public function task_should_not_be_registered_due_to_existed_title_and_raise_form_error(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->findOneBy([]);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_create", ["id" => $task->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'Tâche N° 1',
            'task[content]' => 'Lorem ipsum',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Cette tâche existe déjà.');
    }
}
