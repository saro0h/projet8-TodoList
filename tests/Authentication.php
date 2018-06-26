<?php

namespace App\Tests;

/**
 * Trait Authentication
 * @package App\Tests
 */
trait Authentication
{
    /**
     * @return \Symfony\Component\HttpKernel\Client
     */
    public function login()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'test';
        $form['_password'] = 'test';

        $client->submit($form);


        return $client;
    }
}