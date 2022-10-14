<?php

namespace App\Tests\Controller;

use Faker\Factory;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use AuthenticationTrait;

    /*
    *   TEST ADMINISTATOR USERS LIST
    */
    public function testShowListUsers(): void
    {
        $client = static::createAuthenticatedAdmin();

        $crawler = $client->request('GET', '/users');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Liste des utilisateurs")')->count()
        );
    }

    /*
    *   TEST ADMINISTATOR EDIT USER PAGE
    */
    public function testShowEditUser(): void
    {
        $client = static::createAuthenticatedAdmin();

        $userRepository = static::getContainer()->get(UserRepository::class);
        // DESC TO AVOID "Anonymous User" of Fixtures DATA
        $userToEdit = $userRepository->findOneBy([], ['id' => 'DESC']);

        $crawler = $client->request('GET', '/user/' . $userToEdit->getId());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Modifier")')->count()
        );
    }

    /*
    *   TEST ADMINISTATOR EDIT USER FUNCTION // UPDATE EMAIL && USERNAME
    */
    public function testUpdateUser(): void
    {
        $faker = Factory::create('fr-FR');
        $client = static::createAuthenticatedAdmin();

        $userRepository = static::getContainer()->get(UserRepository::class);
        // DESC TO AVOID "Anonymous User" of Fixtures DATA
        $userToEdit = $userRepository->findOneBy([], ['id' => 'DESC']);

        $fakeEmail = $faker->email();
        $fakeName = $faker->name();
        $crawler = $client->request('GET', '/user/' . $userToEdit->getId());
        $form = $crawler->selectButton('Modifier')->form([
            'user[email]' => $fakeEmail,
            'user[username]' => $fakeName,
            'user[plainPassword][first]' => 'password',
            'user[plainPassword][second]' => 'password'
        ]);
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $userToEdit = $userRepository->findOneBy([], ['id' => 'DESC']);
        $this->assertEquals($fakeEmail, $userToEdit->getEmail());
        $this->assertEquals($fakeName, $userToEdit->getUsername());
    }
}
