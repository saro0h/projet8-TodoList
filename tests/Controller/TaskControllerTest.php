<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Task;
use App\Entity\User;

class TaskControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function task_should_be_displayed()
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

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
    public function added_task_should_be_displayed_and_redirect_to_tasks_list()
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
    public function task_should_not_be_registered_due_to_blank_title_and_raise_form_error()
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
            'task[title]' => '',
            'task[content]' => 'blank title task',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir un titre.');
    }

    /**
     * @test
     */
    public function task_should_not_be_registered_due_to_blank_description_and_raise_form_error()
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
            'task[title]' => 'blank description task',
            'task[content]' => '',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir du contenu.');
    }

    /**
     * @test
     */
    public function task_should_not_be_registered_due_to_existed_title_and_raise_form_error()
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
            'task[title]' => 'Tâche N° 1',
            'task[content]' => 'Lorem ipsum',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Cette tâche existe déjà.');
    }

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

    /**
     * @test
     */
    public function task_should_toggled()
    {
        // création du client
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->find(2);
        /* on stock la valeur de $task->isDone() plutôt que de $task car task
        est modifié par la requête et on se retrouve à comparer 2 true après*/
        $originalTask = $task->isDone(); // isDone return bool(false)

        // requête du toggle
        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_toggle", ["id" => $task->getId(2)])
        ); // isDone return bool(true)

        // récupère la nouvelle tâche toggled stocké dans $toggleTask
        $toggleTask = $entityManager->getRepository(Task::class)->find(2);
        // comparaison des 2 états
        $this->assertNotSame($originalTask, $toggleTask->isDone());
    }

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
