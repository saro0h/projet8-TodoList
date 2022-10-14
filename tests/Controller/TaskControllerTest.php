<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Task;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    /**
     *  @dataProvider provideAuthorizedTaskPages 
     */
    public function testAuthorizedTaskPages(string $url, string $message): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $username = 'simple';
        $user = $userRepository->findOneBy(['username' => $username]);
        $client->loginUser($user);

        $client->request('GET', $url);
        //dd($client->getResponse()->getContent());

        $this->assertSelectorTextContains('h1', $message);
    }

    public function provideAuthorizedTaskPages(): array
    {
        return [
            ['/tasks/todo', 'Liste des tâches à faire'],
            ['/tasks/done', 'Liste des tâches terminées'],
            ['/task/4/edit', 'Modifier une tâche'],
            ['/task/create', 'Créer une tâche']
        ];
    }

    /**
     *  @dataProvider provideDataToTestTaskToggleButtonForChangeIsDoneStatus 
     */
    public function testTaskToggleButtonToChangeIsDoneStatus(int $doneStatus, int $idTask, string $url, string $sentence): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $username = 'simple';
        $user = $userRepository->findOneBy(['username' => $username]);
        $client->loginUser($user);

        $client->followRedirects();

        $crawler = $client->request('GET', $url);
        $form = $crawler->filter("#button_" . $idTask)->selectButton($sentence)->form();
        $crawler = $client->submit($form);

        //$this->assertSelectorExists('.alert.alert-success');
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToControl = $taskRepository->findOneBy(['id' => $idTask]);
        $this->assertSame(!$doneStatus, $taskToControl->getIsDone());
    }

    public function provideDataToTestTaskToggleButtonForChangeIsDoneStatus(): array
    {
        return [
            [1, 4, '/tasks/done', 'Marquer non terminée'],
            [0, 3, '/tasks/todo', 'Marquer comme à faire']
        ];
    }

    public function testEditTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $username = 'simple';
        $user = $userRepository->findOneBy(['username' => $username]);
        $client->loginUser($user);
        $client->followRedirects();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToEdit = $taskRepository->findOneBy([], ['id' => 'DESC']); // Last Task

        $crawler = $client->request('GET', '/task/' . $taskToEdit->getId() . '/edit');
        $crawler = $client->submitForm('Modifier', [
            'task[title]' => $taskToEdit->getTitle() . ' Updated',
            'task[content]' => $taskToEdit->getContent() . ' Updated',
        ]);

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

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $username = 'simple';
        $user = $userRepository->findOneBy(['username' => $username]);
        $client->loginUser($user);

        $client->followRedirects();

        $client->request('GET', '/task/create');
        $randomTitle = $faker->sentence(2);
        $randomContent = $faker->text();

        $client->submitForm('Ajouter', [
            'task[title]' => $randomTitle,
            'task[content]' => $randomContent,
        ]);


        $this->assertSelectorExists('.alert.alert-success');

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskToControl = $taskRepository->findOneBy([], ['id' => 'DESC']);

        $this->assertSame($taskToControl->getTitle(), $randomTitle);
        $this->assertSame($taskToControl->getContent(), $randomContent);
    }

    /**
     *  @dataProvider provideDataToTestDeleteTaskByUserProfile 
     */
    public function testDeleteTask(bool $typeOfClient, int $idTask, string $alert, string $message): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $username = 'simple';
        if ($typeOfClient) $username = 'admin';
        $user = $userRepository->findOneBy(['username' => $username]);
        $client->loginUser($user);

        $client->followRedirects();

        $client->request('GET', '/task/' . $idTask . '/delete');

        $this->assertSelectorTextContains($alert, $message);
    }

    /**
     * @return [typeOfClient (false => simple, true => admin), $idTask, titleTask, alert]
     */
    public function provideDataToTestDeleteTaskByUserProfile(): array
    {
        self::ensureKernelShutdown();

        return [
            [false, 4, '.alert.alert-success', 'La tâche a bien été supprimée.'],
            [true, 1, '.alert.alert-success', 'La tâche a bien été supprimée.'],
            [false, 2, '.alert.alert-danger', 'Vous n\'avez pas le droit de supprimer cette tâche !'],
            [false, 1, '.alert.alert-danger', 'Vous n\'avez pas le droit de supprimer cette tâche !'],
            [true, 3, '.alert.alert-danger', 'Vous n\'avez pas le droit de supprimer cette tâche !'],
        ];
    }
}
