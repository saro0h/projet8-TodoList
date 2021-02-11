<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testListTask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche',
            $crawler->filter('.container .btn-info')->text());
    }

    public function testCreateTask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Ma nouvelle tache Test Fonctionelle',
            'task[content]' => 'Description de ma nouvelle tache Test Fonctionelle',
        ]);
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            "La tâche a été bien été ajoutée.",
            $crawler->filter('.alert-success')->text()
        );
    }

    public function testEditTask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/1/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Mon edtition de la tache',
            'task[content]' => 'Edition description de ma tache',
        ]);
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            "Superbe ! La tâche a bien été modifiée",
            $crawler->filter('.alert-success')->text()
        );
    }

    public function testToggleTask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/1/toggle');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            "Superbe ! La tâche Mon edtition de la tache a bien été marquée comme faite.",
            $crawler->filter('.alert-success')->text()
        );
    }

    public function testDeleteTask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin',
            '_password' => 'pass',
        ]);;
        $client->submit($form);

        $crawler = $client->request('GET', '/tasks/2/delete');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            "Superbe ! La tâche a bien été supprimée.",
            $crawler->filter('.alert-success')->text()
        );
    }
}