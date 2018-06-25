<?php
/**
 * Created by PhpStorm.
 * User: kalaki
 * Date: 25/06/18
 * Time: 18:13
 */

namespace App\Tests\Controller;

use App\Tests\Authentication;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest
 * @package App\Tests\Controller
 */
class UserControllerTest extends WebTestCase
{
    use Authentication;

    public function testAddUserIsUp()
    {
        $client = $this->login();

        $client->request('GET', '/users/create');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testAddUser()
    {
        $client = $this->login();

        $crawler = $client->followRedirect();

        $link = $crawler->selectLink('Créer un utilisateur')->link();

        $crawler = $client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();

        $form['user[username]'] = 'newUser';

        $form['user[password][first]'] = 'test';

        $form['user[password][second]'] = 'test';

        $form['user[email]'] = 'newUser@gmail.com';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
    }

    public function testEditUserIsUp()
    {
        $client = $this->login();

        $client->request('GET', '/users/1/edit');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $client = $this->login();

        $crawler = $client->followRedirect();

        $link = $crawler->selectLink('Gestion des utilisateurs')->link();

        $crawler = $client->click($link);

        $link = $crawler->selectLink('Edit')->link();

        $crawler = $client->click($link);

        $form = $crawler->selectButton('Modifier')->form();

        $form['user[username]'] = 'newUser2';

        $form['user[password][first]'] = 'test2';

        $form['user[password][second]'] = 'test2';

        $form['user[email]'] = 'newUser2@gmail.com';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
    }
}