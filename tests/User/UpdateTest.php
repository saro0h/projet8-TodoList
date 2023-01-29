<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UpdateTest extends WebTestCase
{
    /**
     * @test
     */
    public function existant_user_should_be_edited_by_admin(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_edit", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'new edited user',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => 'newuser@sf.com',
        ]);

        $client->submit($form);

        /** @var User $editedUser */
        $editedUser = $entityManager->getRepository(User::class)->find(1);

        // Comparer le changement d'état
        $this->assertNotSame($user, $editedUser);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('td', 'new edited user');
    }

    /**
     * @test
     */
    public function existant_user_should_not_be_edited_by_admin_due_to_blank_username_and_raise_form_error(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_edit", ["id" => $user->getId()])
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
    public function existant_user_should_not_be_edited_by_admin_due_to_blank_password(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_edit", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'new edited user',
            'user[plainPassword][first]' => '',
            'user[plainPassword][second]' => '',
            'user[email]' => 'newuser@sf.com',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir un mot de passe.');
    }

    /**
     * @test
     */
    public function existant_user_should_not_be_edited_by_admin_due_to_blank_email(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("user_edit", ["id" => $user->getId()])
        );

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => 'new edited user',
            'user[plainPassword][first]' => 'passworD1!',
            'user[plainPassword][second]' => 'passworD1!',
            'user[email]' => '',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('html', 'Vous devez saisir une adresse email.');
    }
}
