<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SecurityControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function login_is_successful()
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
    public function login_failed()
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
}
