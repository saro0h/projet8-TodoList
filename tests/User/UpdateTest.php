<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\User;

class UpdateTest extends WebTestCase
{
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
