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
    public function task_should_be_edited_and_redirect_to_tasks_list()
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
}
