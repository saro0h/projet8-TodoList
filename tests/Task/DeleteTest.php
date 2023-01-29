<?php

namespace App\Tests\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;

class DeleteTest extends WebTestCase
{
    /**
     * @test
     */
    public function task_should_be_deleted_by_author_and_redirect_to_tasks_list(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->find(3);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_delete", ["id" => $task->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été supprimée.');
        $this->assertResponseIsSuccessful();

        $task = $entityManager->getRepository(Task::class)->find(3);
        $this->assertNull($task);
    }

    /**
     * @test
     */
    public function task_should_not_be_deleted_by_other_user_and_raise_message_error(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // User id 2 Morgane has ROLE_USER
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(2);
        $client->loginUser($user);

        // Task id 23 has been created by user id 3 Clement (ROLE_USER)
        $client->request(Request::METHOD_GET, '/tasks/23/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorTextContains('html', 'Access Denied.');
    }

    /**
     * @test
     */
    public function user_task_should_be_deleted_by_admin(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // User id 1 Audrey has ROLE_ADMIN
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        // Task id 23 has been created by user id 3 Clement (ROLE_USER)
        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->find(23);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_delete", ["id" => $task->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été supprimée.');

        $task = $entityManager->getRepository(Task::class)->find(23);
        $this->assertNull($task);
    }

    /**
     * @test
     */
    public function anonyme_task_should_be_deleted_by_admin(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // User id 1 Audrey has ROLE_ADMIN
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        // Task id 1 is 'anonyme' -> no related user
        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->find(1);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_delete", ["id" => $task->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été supprimée.');

        $task = $entityManager->getRepository(Task::class)->find(1);
        $this->assertNull($task);
    }

    /**
     * @test
     */
    public function anonyme_task_should_not_be_deleted_by_non_admin(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        // User id 2 Morgane has ROLE_USER
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(2);
        $client->loginUser($user);

        // Task id 1 is 'anonyme' -> no related user
        $client->request(Request::METHOD_GET, '/tasks/1/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorTextContains('html', 'Access Denied.');
    }
}
