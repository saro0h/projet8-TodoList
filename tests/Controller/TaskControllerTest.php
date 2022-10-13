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

    /**
    *  @dataProvider provideAuthorizedTaskPages 
    */
    public function testAuthorizedTaskPages(string $url, string $message): void
    {
        $client = static::createAuthenticatedUser();

        $crawler = $client->request('GET', $url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("'.$message.'")')->count()
        );
    }

    public function provideAuthorizedTaskPages(): array
    {
        return [
            ['/tasks/todo', 'Liste des tâches à faire'],
            ['/tasks/done', 'Liste des tâches terminées'],
            ['/task/3/edit', 'Modifier une tâche'],
            ['/task/create', 'Créer une tâche']
        ];
    }

    /**
    *  @dataProvider provideDataToTestTaskToggleButtonForChangeIsDoneStatus 
    */
    public function testTaskToggleButtonToChangeIsDoneStatus(bool $doneStatus, string $buttonLabel, string $url, string $sentence): void
    {
        $client = static::createAuthenticatedUser();
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToToggle = $taskRepository->findOneBy(['isDone' => 0], ['id' => 'DESC']);

        $crawler = $client->request('GET', $url);

        $form = $crawler->filter("#".$buttonLabel."_{$taskToToggle->getId()}")->selectButton($sentence)->form();
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $taskToControl = $taskRepository->findOneBy(['id' => $taskToToggle->getId()]);
        $this->assertEquals(!$doneStatus, $taskToControl->isDone());
    }

    public function provideDataToTestTaskToggleButtonForChangeIsDoneStatus(): array
    {
        return [
            [0, '/tasks/todo', 'toDone' 'Marquer comme faite'],
            [1, '/tasks/done', 'toDo', 'Marquer non terminée']
        ];
    }

    public function testEditTask(): void
    {
        $client = static::createAuthenticatedUser();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToEdit = $taskRepository->findOneBy([], ['id' => 'DESC']); // Task id 5

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

        $client = static::createAuthenticatedUser();

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

    /**
    *  @dataProvider provideDataToTestDeleteTaskByUserProfile 
    */
    public function testDeleteTask(bool $typeOfClient, string $titleTask, string $alert): void
    {
        if ($typeOfClient) $client = $static::createAuthenticatedAdmin();
        else $client = $static::createAuthenticatedUser();
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy([], ['title' => $titleTask]);
        $client->followRedirects();

        $client->request('GET', '/task/' . $task->getId() . '/delete');

        $this->assertSelectorExists($alert);
    }

    /**
     * @return [typeOfClient (false => simple, true => admin), titleTask, alert]
     */
    public function provideDataToTestDeleteTaskByUserProfile(): array
    {
        return [
            [false, 'admin_task', '.alert.alert-danger'],
            [false, 'anomymous_task', '.alert.alert-danger'],
            [true, 'simple_task_todo', '.alert.alert-danger'],
            [false, 'simple_task_todo', '.alert.alert-success'],
            [true, 'anomymous_task', '.alert.alert-success'],
        ];
    }


}