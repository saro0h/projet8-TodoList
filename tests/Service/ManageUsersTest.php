<?php

namespace Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\ManageUsers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManageUsersTest extends KernelTestCase
{
    protected $manageUser;
    /**
     * @var EntityRepository|MockObject
     */
    private $userRep;
    /**
     * @var MockObject|UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface|MockObject
     */
    private $em;

    protected function setUp(): void
    {
        $this->userRep = $this->createMock(EntityRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('getRepository')->willReturn($this->userRep);
        $this->encoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->manageUser = new ManageUsers($this->em, $this->encoder);

        parent::setUp();
    }

    public function testFindUser()
    {
        $user = new User();
        $user->setUsername('Username');

        $this->userRep->method('findOneBy')
            ->with(['id' => 10])
            ->willReturn($user);

        $this->assertEquals($user, $this->manageUser->findUser(10));
    }

    public function testGetAnonymousUser()
    {
        $user = new User();
        $user->setUsername('anonymous');

        $this->userRep->method('findOneBy')
            ->with(['username' => 'anonymous'])
            ->willReturn($user);

        $this->assertEquals($user, $this->manageUser->getAnonymousUser());
    }

    public function testCreateAnonymousUser()
    {
        $anonymous = new User();
        $anonymous->setUsername('anonymous');
        $anonymous->setPassword($this->encoder->encodePassword($anonymous, 'anonymous_password'));
        $anonymous->setEmail('no-reply@todolist.fr');

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($anonymous));
        $this->manageUser->createAnonymousUser();
    }

    public function testDeleteUser()
    {
        $user = new User();
        $user->setUsername('test');
        $user->setPassword($this->encoder->encodePassword($user, 'test_password'));
        $user->setEmail('no-reply@todolist.fr');
        $user->addTask($task1 = new Task());
        $user->addTask($task2 = new Task());

        $this->em
            ->expects($this->exactly(2))
            ->method('persist')
            ->with($this->logicalOr($this->equalTo($task2),$this->equalTo($task1)));

        $this->manageUser->deleteUser($user);


    }
}
