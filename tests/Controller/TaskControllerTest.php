<?php

namespace App\Tests\Controller;

use App\DataFixtures\ORM\LoadFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private \DOMDocument $domId;
    private Object $taskRepository;
    private Object $userRepository;

    public static function setUpBeforeClass(): void
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $loader = new Loader();
        $loader->addFixture(new LoadFixtures());

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->domId = new \DOMDocument();
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function getUser2() {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => 'user2']);
    }

    public function userLogged ()
    {
        $testUser = $this->getUser2();

        return $this->client->loginUser($testUser);
    }

    public function adminLogged ()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['username' => 'user1']);

        return $this->client->loginUser($testAdmin);
    }

    public function testListAction(): void
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Marquer');
    }

    public function testAccessCreateActionPageWhenLoggedIn(): void
    {
        $client = $this->userLogged();
        $client->request('GET', '/tasks/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains('div', 'Vous devez vous connecter pour créer une tâche');
    }

    public function testAccessCreateActionPageWhenNotLoggedIn(): void
    {
        $this->client->request('GET', "/tasks/create");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('authenticated'), 'Vous devez vous connecter pour créer une tâche');
    }

    public function testCreateTaskWhenLoggedIn(): void
    {
        $client = $this->userLogged();
        $form = $client->request('GET', "/tasks/create")->selectButton('Ajouter')->form();

        $this->client->submit($form, [
            'task[title]' => 'Titre ajouté',
            'task[content]' => 'Contenu ajouté',
        ]);
        $client->followRedirect();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'La tâche a été bien été ajoutée.');

        $taskCreated = $this->taskRepository->findOneBy(['title' => 'Titre ajouté']);
        $this->assertSame('Titre ajouté',$taskCreated->getTitle());
        $this->assertSame('Contenu ajouté',$taskCreated->getContent());

        $testUser = $this->getUser2();

        $this->assertSame($testUser->getId(), $taskCreated->getAuthor()->getId());
    }

    public function testCreateTaskWhenNotLoggedIn(): void
    {
        $form = $this->client->request('GET', "/tasks/create")->selectButton('Ajouter')->form();

        $this->client->submit($form, [
            'task[title]' => 'Titre ajouté',
            'task[content]' => 'Contenu ajouté',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('authenticated'), 'Vous devez vous connecter pour créer une tâche');
    }

    public function testAccessEditActionPage(): void
    {
        $taskCreated = $this->taskRepository->findOneBy(['title' => 'Titre test']);

        $this->client->request('GET', '/tasks/' .$taskCreated->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Modifier');
    }

    public function testModifyTaskWhenAuthor(): void
    {
        $client = $this->userLogged();
        $taskCreated = $this->taskRepository->findOneBy(['title' => 'Titre test']);

        $form = $client->request('GET', '/tasks/' .$taskCreated->getId() . '/edit')->selectButton('Modifier')->form();

        $this->client->submit($form, [
            'task[title]' => 'Titre modifié',
            'task[content]' => 'Contenu modifié',
        ]);
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'La tâche a bien été modifiée.');

        $taskModified = $this->taskRepository->findOneBy(['title' => 'Titre modifié']);
        $this->assertSame('Titre modifié',$taskModified->getTitle());
        $this->assertSame('Contenu modifié',$taskModified->getContent());
    }

    public function testModifyTaskWhenNotAuthor(): void
    {
        $client = $this->adminLogged();
        $taskModified = $this->taskRepository->findOneBy(['title' => 'Titre modifié']);

        $form = $client->request('GET', '/tasks/' .$taskModified->getId() . '/edit')->selectButton('Modifier')->form();

        $this->client->submit($form, [
            'task[title]' => 'Titre modifié auteur différent',
            'task[content]' => 'Contenu modifié auteur différent',
        ]);

        $client->followRedirect();

        $taskModified2 = $this->taskRepository->findOneBy(['title' => 'Titre modifié auteur différent']);
        $this->assertSame('Titre modifié auteur différent',$taskModified2->getTitle());
        $this->assertSame('Contenu modifié auteur différent',$taskModified2->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'La tâche a bien été modifiée.');
    }

    public function testDoneTaskAction(): void
    {
        $taskToBeDone = $this->taskRepository->findOneBy(['title' => 'Titre modifié auteur différent']);

        $this->client->request('GET', '/tasks/' . $taskToBeDone->getId() . '/toggle');

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById($taskToBeDone->getId()), 'Marquer non terminée');
    }

    public function testNotDoneTaskAction(): void
    {
        $taskToBeDone = $this->taskRepository->findOneBy(['title' => 'Titre modifié auteur différent']);

        $this->client->request('GET', '/tasks/' . $taskToBeDone->getId() . '/toggle');

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById($taskToBeDone->getId()), 'Marquer comme faite');
    }

    public function testDeleteTaskWhenNotLogged(): void
    {
        $taskToDelete = $this->taskRepository->findOneBy(['title' => 'Titre modifié auteur différent']);

        $this->client->request('GET', '/tasks/' .$taskToDelete->getId() . '/delete');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('error'), 'Vous ne pouvez pas supprimer cette tâche');
    }

    public function testDeleteTaskWhenNotAuthor(): void
    {
        $client = $this->adminLogged();
        $taskToDelete = $this->taskRepository->findOneBy(['title' => 'Titre modifié auteur différent']);

        $client->request('GET', '/tasks/' .$taskToDelete->getId() . '/delete');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('error'), 'Vous ne pouvez pas supprimer cette tâche');
    }

    public function testDeleteTaskWhenAuthor(): void
    {
        $client = $this->userLogged();
        $taskToDelete = $this->taskRepository->findOneBy(['title' => 'Titre modifié auteur différent']);

        $client->request('GET', '/tasks/' .$taskToDelete->getId() . '/delete');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'La tâche a bien été supprimée.');
    }

    public function testDeleteTaskAnonymousWhenNotAdmin(): void
    {
        $client = $this->userLogged();
        $taskToDelete = $this->taskRepository->findOneBy(['author' => $this->userRepository->findOneBy(['username' => 'anonymous'])]);

        $client->request('GET', '/tasks/' .$taskToDelete->getId() . '/delete');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('error'), 'Vous ne pouvez pas supprimer cette tâche');
    }

    public function testDeleteTaskAnonymousWhenAdmin(): void
    {
        $client = $this->adminLogged();
        $taskToDelete = $this->taskRepository->findOneBy(['author' => $this->userRepository->findOneBy(['username' => 'anonymous'])]);

        $client->request('GET', '/tasks/' .$taskToDelete->getId() . '/delete');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'La tâche a bien été supprimée.');
    }
}
