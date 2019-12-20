<?php
namespace App\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
    * @var
    */
    private $client;
    /**
     * @var
     */
    private $crawler;
    /**
     * test de l'affichage du formulaire de connexion
     */
    public function testShowLogin()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("utilisateur")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Mot de passe")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Se connecter")')->count());
    }
    /**
     * Test de connexion avec bon identifiant utilisateur
     */
    public function testGoodAuthentification()
    {
        $this->client = static::createClient();
        $this->login('admin', 'admin');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    /**
     * Test de connexion avec mauvais  identifiant utilisateur
     */
    public function testBadAuthentification()
    {
        $this->client = static::createClient();
        $this->login('badUser@gmail.com', 'badPassword');
        $this->assertSame(1, $this->crawler->filter('html .alert:contains("Invalid credentials")')
            ->count());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    /**
     * Connexion d'un utilisateur
     * @param $user
     * @param $password
     */
    private function login($user, $password)
    {
        $this->crawler = $this->client->request('GET', '/login');
        $this->hydrateForm('Se connecter', ['_username' => $user, '_password' => $password]);
    }
    /**
     * Remplissage du formulaire
     * @param $button
     * @param $inputs
     */
    private function hydrateForm($button, $inputs)
    {
        $form = $this->crawler->selectButton($button)->form();
        $this->crawler = $this->client->submit($form, $inputs);
        $this->crawler = $this->client->followRedirect();
    }
}