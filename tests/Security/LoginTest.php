<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class LoginTest extends WebTestCase
{
    /**
     * @test
     */
    public function user_should_be_authenticated_and_redirect_to_homepage(): void
    {
        // simule l'envoie d'une requête HTTP
        $client = static::createClient();
        // on récupère le crawler & on souhaite accéder à la page de connexion
        $crawler = $client->request(Request::METHOD_GET, '/login');
        // on test d'abord si on arrive bien sur notre page
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // crawler pertmet de récupérer le contenu d'une page
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "Jean",
            "_password" => "password"
        ]);

        $client->submit($form);

        // on test so on est bien en FOUND (code 302 redirection)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        // on test si on est bien redirigé vers notre page d'accueil
        $this->assertRouteSame('homepage');
    }

    /**
     * @test
     */
    public function user_should_not_be_authenticated_due_to_invalid_credentials_and_raise_form_error(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "Jean",
            "_password" => "fail"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Invalid credentials.');
    }

    /**
     * @test
     */
    public function user_should_not_be_authenticated_due_to_blank_username_raise_form_error_and_redirect_to_login(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "",
            "_password" => "passworD1!"
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('http://localhost/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Invalid credentials.');
    }

    /**
     * @test
     */
    public function user_should_not_be_authenticated_due_to_blank_password_raise_form_error_and_redirect_to_login(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "Jean",
            "_password" => ""
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('http://localhost/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('html', 'Invalid credentials.');
    }
}
