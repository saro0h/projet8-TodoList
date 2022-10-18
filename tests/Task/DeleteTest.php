<?php

namespace App\Tests\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Task;
use App\Entity\User;

class DeleteTest extends WebTestCase
{
    /**
     * @test
     */
    public function task_should_be_deleted_and_redirect_to_tasks_list()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $task = $entityManager->getRepository(Task::class)->find(3);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_delete", ["id" => $task->getId(3)])
        );

        // $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Superbe ! La tâche a bien été supprimée.');

        $task = $entityManager->getRepository(Task::class)->find(3);
        $this->assertNull($task);
    }
}
