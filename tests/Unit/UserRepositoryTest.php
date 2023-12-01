<?php

namespace Tests\Unit;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    /** @var EntityManager */
    private $entityManager;
    private $passwordEncoder;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->passwordEncoder = $kernel->getContainer()->get('security.password_encoder');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testCreateTask()
    {
        $username = substr(md5(time()), 0, 25);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($username.'@gmail.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'test'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $foundUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        $this->assertSame($username, $foundUser->getUsername());
    }

}