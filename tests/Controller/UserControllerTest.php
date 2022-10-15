<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\User;

class UserControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function user_should_be_displayed()
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_list")
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(14, $crawler->filter('.user-infos'));
    }

    /**
     * @test
     */
    public function user_should_be_registered_and_redirect_to_users_list()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'new user',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'newuser@sf.com',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'new user');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_blank_username_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => '',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'newuser@sf.com',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir un nom d\'utilisateur.');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_used_username_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'Jean',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'usedUsername@sf.com',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Ce pseudo est déjà utilisé.');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_invalid_password_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'invalid password',
            'user[plainPassword][first]' => 'password',
            'user[plainPassword][second]' => 'password',
            'user[email]' => 'invalidPassword@sf.com',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Le format de l\'adresse n\'est pas correcte.');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_blank_password_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'blank password',
            'user[plainPassword][first]' => '',
            'user[plainPassword][second]' => '',
            'user[email]' => 'blankPassword@sf.com',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir un mot de passe.');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_invalid_email_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'invalid email',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'eguhegi',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Le format de l\'adresse n\'est pas correcte.');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_blank_email_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'blank email',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => '',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir une adresse email.');
    }

    /**
     * @test
     */
    public function user_should_not_be_registered_due_to_used_email_and_raise_form_error()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_create", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'used email',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'jean@sf.com',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'L\'email est déjà utilisé.');
    }

    /**
     * @test
     */
    public function existant_user_should_be_edited()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_edit", ["id" => $user->getId(1)])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'new edited user',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'newuser@sf.com',
        ]);

        $client->submit($form);

        $editedUser = $entityManager->getRepository(User::class)->find(1);

        //comparer le changement d'état
        $this->assertNotSame($user, $editedUser);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('td', 'new edited user');
    }
}
