<?php

namespace Tests\Service;

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

    protected function setUp():void
    {
        $this->userRep = $this->createMock(EntityRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($this->userRep);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->manageUser = new ManageUsers($em, $encoder);

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




}
