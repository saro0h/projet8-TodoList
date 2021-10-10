<?php

namespace App\Tests\Controller;

use App\DataFixtures\ORM\LoadFixtures;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private \DOMDocument $domId;
    private object $userRepository;

    public static function setUpBeforeClass(): void
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

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
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function getUser2()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => 'user2']);
    }

    public function userLogged()
    {
        $testUser = $this->getUser2();

        return $this->client->loginUser($testUser);
    }

    public function adminLogged()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['username' => 'user1']);

        return $this->client->loginUser($testAdmin);
    }

    public function testListActionPageWhenNotLoggedIn(): void
    {
        $this->client->request('GET', "/users");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('accessdenied'), 'Vous n\'avez pas accès à cette page.');
    }

    public function testListActionPageWhenNotAdmin(): void
    {
        $this->userLogged();
        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('accessdenied'), 'Vous n\'avez pas accès à cette page.');
    }

    public function testListActionPageWhenAdmin(): void
    {
        $this->adminLogged();
        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testAccessCreateActionPageWhenNotLoggedIn(): void
    {
        $this->client->request('GET', "/users/create");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('accessdenied'), 'Vous n\'avez pas accès à cette page.');
    }

    public function testAccessCreateActionPageWhenNotAdmin(): void
    {
        $client = $this->userLogged();
        $client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('accessdenied'), 'Vous n\'avez pas accès à cette page.');
    }

    public function testAccessCreateActionPageWhenAdmin(): void
    {
        $this->adminLogged();
        $this->client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');
    }

    public function testCreateUserWhenAdmin(): void
    {
        $client = $this->adminLogged();
        $form = $client->request('GET', "/users/create")->selectButton('Ajouter')->form();

        $this->client->submit($form, [
            'user[username]' => 'user4',
            'user[password][first]' => 'coucou',
            'user[password][second]' => 'coucou',
            'user[email]' => 'user4@gmail.com',
            'user[roles]' => 'ROLE_USER',
        ]);
        $client->followRedirect();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'L\'utilisateur a bien été ajouté.');

        $userCreated = $this->userRepository->findOneBy(['username' => 'user4']);
        $this->assertSame('user4', $userCreated->getUserIdentifier());
        $this->assertSame('user4@gmail.com', $userCreated->getEmail());
        $this->assertSame(['ROLE_USER'], $userCreated->getRoles());
    }

    public function testEditActionPageWhenNotLoggedIn(): void
    {
        $userToEdit = $this->userRepository->findOneBy(['username' => 'anonymous']);
        $this->client->request('GET', '/users/' . $userToEdit->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('accessdenied'), 'Vous n\'avez pas accès à cette page.');
    }

    public function testEditActionPageWhenNotAdmin(): void
    {
        $this->userLogged();
        $userToEdit = $this->userRepository->findOneBy(['username' => 'anonymous']);
        $this->client->request('GET', '/users/' . $userToEdit->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains((string)$this->domId->getElementById('accessdenied'), 'Vous n\'avez pas accès à cette page.');
    }

    public function testAdminActionPageWhenAdmin(): void
    {
        $this->adminLogged();
        $userToEdit = $this->userRepository->findOneBy(['username' => 'anonymous']);
        $this->client->request('GET', '/users/' . $userToEdit->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modifier');
    }

    public function testModifyUserWhenAdmin(): void
    {
        $client = $this->adminLogged();
        $userToModify = $this->userRepository->findOneBy(['username' => 'anonymous']);
        $form = $this->client->request('GET', '/users/' . $userToModify->getId() . '/edit')->selectButton('Modifier')->form();

        $this->client->submit($form, [
            'user[username]' => 'userModifié',
            'user[password][first]' => 'coucou',
            'user[password][second]' => 'coucou',
            'user[email]' => 'anonymous@gmail.com',
            'user[roles]' => 'ROLE_USER',
        ]);
        $client->followRedirect();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('success'), 'L\'utilisateur a bien été modifié');

        $userModified = $this->userRepository->findOneBy(['username' => 'userModifié']);
        $this->assertSame('userModifié', $userModified->getUserIdentifier());
        $this->assertSame('anonymous@gmail.com', $userModified->getEmail());
        $this->assertSame(['ROLE_USER'], $userModified->getRoles());

    }
}