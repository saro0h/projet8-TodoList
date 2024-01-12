<?php

namespace App\Tests\Fonctional;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testListUser()
    {
        $client = DefaultControllerTest::createAuthenticationClient();
        $client->request('GET', '/users');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testCreateUser()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/create');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="user[email]"]')->count());

        $username = substr(md5(rand()), 0, 25);
        $password = substr(md5(rand()), 0, 25);

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => $username,
            'user[password][first]' => $password,
            'user[password][second]' => $password,
            'user[email]' => $username.'@test.com',
        ]);
        $client->submit($form);

        $client->followRedirect();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => $username]);
        $this->assertNotNull($user);
    }

    public function testEditUser()
    {
        $client = DefaultControllerTest::createAuthenticationClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByNot('username', 'nonoland')[0];
        $this->assertNotNull($user);

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="user[email]"]')->count());

        $randomValue = substr(md5(rand()), 0, 25);
        $email = $randomValue . '@test.com';

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => $user->getUsername(),
            'user[password][first]' => $randomValue,
            'user[password][second]' => $randomValue,
            'user[email]' => $email,
        ]);
        $client->submit($form);

        $client->followRedirect();

        $newUser = $userRepository->findOneBy(['username' => $user->getUsername()]);
        $this->assertNotNull($newUser);
    }
}
