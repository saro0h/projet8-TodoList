<?php

namespace App\Tests\Controller;

use App\Tests\Authentication;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TaskControllerTest
 * @package App\Tests\Controller
 */
class TaskControllerTest extends  WebTestCase
{
    use Authentication;

    public function testAddTaskIsUp()
    {
        $client = $this->login();

        $client->request('GET', '/tasks/create');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testAddTask()
    {
        $client = $this->login();

        $crawler = $client->followRedirect();

        $link = $crawler->selectLink('Créer une nouvelle tâche')->link();

        $crawler = $client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'test1';

        $form['task[content]'] = 'test1';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
    }
}