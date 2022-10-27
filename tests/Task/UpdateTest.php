<?php

namespace App\Tests\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Task;
use App\Entity\User;

class UpdateTest extends WebTestCase
{
    /**
     * @test
     */
    public function task_should_be_edited_by_author_and_redirect_to_tasks_list()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $originalTask = $entityManager->getRepository(Task::class)->findOneBy([]);

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $originalTask->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'new edited task',
            'task[content]' => 'new edited task',
        ]);

        $client->submit($form);

        $editedTask = $entityManager->getRepository(Task::class)->findOneBy([]);
        // var_dump($editedTask);

        //comparer le changement d'état
        $this->assertNotSame($originalTask, $editedTask);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été modifiée.');
    }

    /**
     * @test
     */
    public function edit_task_should_not_be_available_to_other_user_and_raise_message_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // user id 2 Morgane has ROLE_USER
        $user = $entityManager->getRepository(User::class)->find(2);
        $client->loginUser($user);

        // task id 23 has been created by user id 3 Clement (ROLE_USER)
        $client->request(Request::METHOD_GET, '/tasks/23/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorTextContains('html', 'Access Denied.');
    }

    /**
     * @test
     */
    public function user_task_can_be_update_by_admin()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // task id 23 has been created by user id 3 Clement (ROLE_USER)
        /** @var Task $task */
        $originalTask = $entityManager->getRepository(Task::class)->find(23);

        // user id 1 Audrey has ROLE_ADMIN
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $originalTask->getId(23)])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'new edited task',
            'task[content]' => 'new edited task',
        ]);

        $client->submit($form);

        $editedTask = $entityManager->getRepository(Task::class)->find(23);

        $this->assertNotSame($originalTask, $editedTask);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été modifiée.');
    }

    /**
     * @test
     */
    public function anonyme_task_can_be_update_by_admin_only()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // task id 1 is 'anonyme' -> no related user
        /** @var Task $task */
        $originalTask = $entityManager->getRepository(Task::class)->find(1);

        // user id 1 Audrey has ROLE_ADMIN
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $originalTask->getId(1)])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'new edited task',
            'task[content]' => 'new edited task',
        ]);

        $client->submit($form);

        $editedTask = $entityManager->getRepository(Task::class)->find(1);

        $this->assertNotSame($originalTask, $editedTask);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été modifiée.');
    }

    /**
     * @test
     */
    public function anonyme_task_can_not_be_available_by_non_admin_and_raise_message_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // user id 2 Morgane has ROLE_USER
        $user = $entityManager->getRepository(User::class)->find(2);
        $client->loginUser($user);

        // task id 1 is 'anonyme' -> no related user
        $client->request(Request::METHOD_GET, '/tasks/1/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorTextContains('html', 'Access Denied.');
    }

    /**
     * @test
     */
    public function edited_task_should_be_displayed()
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

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $task->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function task_should_not_be_edited_due_to_blank_title_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $originalTask = $entityManager->getRepository(Task::class)->findOneBy([]);

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $originalTask->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => '',
            'task[content]' => 'new edited task',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir un titre.');
    }

    /**
     * @test
     */
    public function task_should_not_be_edited_due_to_blank_content_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Task $task */
        $originalTask = $entityManager->getRepository(Task::class)->findOneBy([]);

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_edit", ["id" => $originalTask->getId()])
        );

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'new edited task',
            'task[content]' => '',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir du contenu.');
    }
}
