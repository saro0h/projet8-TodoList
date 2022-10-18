<?php


namespace App\Tests\Controller;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;//reload les fixtures
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    private $client;

    public function SetUp(): void
    {
        $this->client = static::createClient();
    }

    public function LoginUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['_username' => 'utilisateur', '_password' => 'password']);
    }

    public function LoginAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['_username' => 'Durand', '_password' => 'password']);
    }


    public function testAccessToTasksListAsAsAnonymeShouldRedirectToLoginPage()
    {
        $this->client->request('GET', '/tasks');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessToTasksListAsConnectedOk()
    {
        $this->LoginUser();
        $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    // public function testListEndingAction()
    // {
    //     $this->loginUser();
    //     $this->client->request('GET', '/tasks/ending');
    //     $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    // }

    public function testCreateTaskAction()
    {
        $this->LoginUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'The title';
        $form['task[content]'] = 'The content';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testModifyTaskAction()
    {
        $this->LoginUser();

        $crawler = $this->client->request('GET', '/tasks/2/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'The titlee';
        $form['task[content]'] = 'The contentt';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testToggleTaskAction(): void
    {
        $this->LoginUser();

        $this->client->request('GET', '/tasks/2/toggle');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function testDeleteTaskByOwnerAction()
    {
        $this->LoginUser();

        $this->client->request('GET', '/tasks/2/delete');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testDeleteTaskByNotOwnerAction()
    {
        $this->LoginUser();


        $this->client->request('GET', '/tasks/1/delete');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

    }

    public function testDeleteAnonymousTaskByAdminAction()
    {
        $this->LoginAdmin();

        $this->client->request('GET', '/tasks/12/delete');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }


    public function testDeleteAnonymousTaskByNotAdminAction()
    {
        $this->LoginUser();


        $this->client->request('GET', '/tasks/13/delete');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

    }

}
