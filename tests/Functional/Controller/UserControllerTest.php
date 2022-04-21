<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class UserControllerTest extends AbstractWebTestCase
{
    const PAGES = [
        '/users',
        '/users/create'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = static::getContainer()->get(FormFactoryInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);

    }

    public function testUserList(): void
    {
        $this->testEntityList('admin@admin.com', 'user_list');
    }


    public function testCreateAdminUser()
    {
        $this->loginAs('admin@admin.com');
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/create');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $username = 'Joe';

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]']->setValue($username);
        //dd($form['user[roles]']);
        $form['user[password][first]']->setValue('test');
        $form['user[password][second]']->setValue('test');
        $form['user[email]']->setValue('test@test.com');
        $this->client->submit($form);

        $user = $this->userRepository->findOneBy(['username' => $username]);

        $this->assertEquals(true, (bool)$user);
    }

    public function testAsAdmin()
    {
        $this->loginAs('admin@admin.com');

        foreach (self::PAGES as $page) {
            $this->testPage($page);
        }
    }

    public function testAsUser()
    {
        $this->loginAs('user@user.com');

        foreach (self::PAGES as $page) {
            $this->testPage($page, false);
        }
    }

    protected function testPage(string $page, bool $expected = true)
    {
        $this->client->request('GET', $page);
        if ($expected) {
            $this->assertResponseIsSuccessful();
        } else {
            $this->assertNotEquals(200, $this->client->getResponse()->getStatusCode());
        }
    }


}
