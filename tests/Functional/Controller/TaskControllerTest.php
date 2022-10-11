<?php

namespace App\Tests\Functional\Controller;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Task;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testTaskTodoListPage(): void
    {
        $client = static::createAuthenticatedAdmin();

        $crawler = $client->request('GET', '/tasks/todo');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Liste des tâches à faire")')->count()
        );
    }

    public function testTaskIsDoneListPage(): void
    {
        $client = static::createAuthenticatedAdmin();

        $crawler = $client->request('GET', '/tasks/done');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Liste des tâches terminées")')->count()
        );
    }


    public function testTaskEditPage(): void
    {
        $client = static::createAuthenticatedAdmin();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToEdit = $taskRepository->findOneBy([]);

        $crawler = $client->request('GET', '/task/' . $taskToEdit->getId() . '/edit');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Modifier une tâche")')->count()
        );
    }

    public function testCreateTaskPage(): void
    {
        $client = static::createAuthenticatedAdmin();

        $crawler = $client->request('GET', '/task/create');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Créer une tâche")')->count()
        );
    }


    public function testToDoTaskToggleToDone(): void
    {
        $client = static::createAuthenticatedAdmin();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToDo = $taskRepository->findOneBy(['isDone' => 0], ['id' => 'DESC']);

        $crawler = $client->request('GET', '/tasks/todo');

        $form = $crawler->filter("#toDone_{$taskToDo->getId()}")->selectButton("Marquer comme faite")->form();
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $taskToDone = $taskRepository->findOneBy(['id' => $taskToDo->getId()]);
        $this->assertEquals(1, $taskToDone->isDone());
    }

    public function testToDoneTaskToggleToDo(): void
    {
        $client = static::createAuthenticatedAdmin();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskDone = $taskRepository->findOneBy(['isDone' => 1], ['id' => 'DESC']);

        $crawler = $client->request('GET', '/tasks/done');

        $form = $crawler->filter("#toDo_{$taskDone->getId()}")->selectButton("Marquer non terminée")->form();
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $taskToDo = $taskRepository->findOneBy(['id' => $taskDone->getId()]);
        $this->assertEquals(0, $taskToDo->isDone());
    }

    public function testEditTask(): void
    {
        $client = static::createAuthenticatedAdmin();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToEdit = $taskRepository->findOneBy([], ['id' => 'DESC']);

        $client->request('GET', '/task/' . $taskToEdit->getId() . '/edit');
        $client->submitForm('Modifier', [
            'task[title]' => $taskToEdit->getTitle() . ' Updated',
            'task[content]' => $taskToEdit->getContent() . ' Updated',
        ]);
        $crawler = $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');

        $card = $crawler->filter("#task_{$taskToEdit->getId()}");
        $controlTitle = $card->filter("a")->text();
        $controlContent = $card->filter(".card-text")->text();

        $this->assertSame($controlTitle, $taskToEdit->getTitle() . ' Updated');
        $this->assertSame($controlContent, $taskToEdit->getContent() . ' Updated');
    }

    public function testCreateTask(): void
    {
        $faker = Factory::create('fr-FR');

        $client = static::createAuthenticatedAdmin();

        $client->request('GET', '/task/create');
        $randomTitle = $faker->sentence(2);
        $randomContent = $faker->text();

        $client->submitForm('Ajouter', [
            'task[title]' => $randomTitle,
            'task[content]' => $randomContent,
        ]);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToControl = $taskRepository->findOneBy([], ['id' => 'DESC']);

        $this->assertSame($taskToControl->getTitle(), $randomTitle);
        $this->assertSame($taskToControl->getContent(), $randomContent);
    }

    public function testDeleteAnonymousTaskByAdmin(): void
    {
        $client = static::createAuthenticatedAdmin();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        // Anonymous Task
        $taskFromAnonymous = $taskRepository->findOneBy([], ['id' => 'ASC']);
        $client->followRedirects();

        $client->request('GET', '/task/' . $taskFromAnonymous->getId() . '/delete');


        $this->assertSelectorExists('.alert.alert-success');
        $taskToControl = $taskRepository->findOneBy([], ['id' => 'ASC']);
        $this->assertNotSame($taskToControl->getId(), $taskFromAnonymous->getId());
    }

    public function testDeleteAnonymousTaskByUser(): void
    {
        $client = static::createAuthenticatedUser();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        // Anonymous Task
        $taskFromAnonymous = $taskRepository->findOneBy([], ['id' => 'ASC']);
        $client->followRedirects();

        $client->request('GET', '/task/' . $taskFromAnonymous->getId() . '/delete');


        //$this->assertSelectorExists('.alert.alert-danger');
        //$taskToControl = $taskRepository->findOneBy([], ['id' => 'ASC']);
        //$this->assertSame($taskToControl->getId(), $taskFromAnonymous->getId());
    }
}
