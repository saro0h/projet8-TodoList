<?php

namespace Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    protected $em;
    protected $client;

    const USERS_URL = '/users';
    const USERS_BASE_URL = '/users/';
    const USER_CREATE_URL = '/users/create';
    const USER_EDIT_SUFFIX_URL = '/edit';

    protected function setUp()
    {
        parent::setUp();

        $this->client = $client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::$container->get(EntityManagerInterface::class);
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    /**
     * After each test, a rollback reset the state of
     * the database
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
        }
    }


    /** Test User list page */
    public function testList()
    {
        $rep = $this->em->getRepository(User::class);

        /* No user connected */
        $this->client->request('GET', self::USERS_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::USERS_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::USERS_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $rep = $this->em->getRepository(User::class);

        /* No user connected */
        $this->client->request('GET', self::USER_CREATE_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::USER_CREATE_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', self::USER_CREATE_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* User form */
        $this->assertStringContainsString('<input type="text" id="user_username"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<input type="password" id="user_password_first"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<input type="password" id="user_password_second"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<input type="email" id="user_email"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<select id="user_roles" name="user[roles]"', $this->client->getResponse()->getContent());

        /* Submit Form */
        $form = $crawler->selectButton('Ajouter')->form();
        $this->client->submit($form);
        $this->assertStringContainsString('div class="form-group has-error"', $this->client->getResponse()->getContent());


        // set some values
        $form['user[username]'] = 'Roger';
        $form['user[email]'] = 'roger@todolist.fr';
        $form['user[roles]'] = 'ROLE_USER';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';

        // submit the form
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('Liste des utilisateurs', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Roger', $this->client->getResponse()->getContent());
    }

    public function testEdit()
    {
        $rep = $this->em->getRepository(User::class);
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $id = $testUser->getId();

        /* No user connected */
        $this->client->request('GET', self::USERS_BASE_URL.$id.self::USER_EDIT_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::USERS_BASE_URL.$id.self::USER_EDIT_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', self::USERS_BASE_URL.$id.self::USER_EDIT_SUFFIX_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* User form */
        $this->assertStringContainsString('<input type="text" id="user_username"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<input type="password" id="user_password_first"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<input type="password" id="user_password_second"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<input type="email" id="user_email"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<select id="user_roles" name="user[roles]"', $this->client->getResponse()->getContent());

        /* Submit Form */
        $form = $crawler->selectButton('Modifier')->form();

        // set some values
        $form['user[username]'] = 'John';
        $form['user[email]'] = 'john@todolist.fr';
        $form['user[roles]'] = 'ROLE_USER';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';

        // submit the form
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('Liste des utilisateurs', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('John', $this->client->getResponse()->getContent());
    }
}
