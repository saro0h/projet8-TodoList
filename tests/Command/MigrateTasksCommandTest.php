<?php

namespace Tests\Command;

use App\Command\MigrateTasksCommand;
use App\Entity\User;
use App\Service\ManageUsers;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MigrateTasksCommandTest extends KernelTestCase
{

    /**
     * @var MigrateTasksCommand
     */
    private $migrateTasksCommand;
    /**
     * @var ManageUsers|MockObject
     */
    private $manageUser;

    public function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->manageUser = $this->createMock(ManageUsers::class);
        $this->migrateTasksCommand = new MigrateTasksCommand($em, $encoder, $this->manageUser);
    }

    public function testConfigure()
    {
        $this->assertEquals('Attach old tasks to anonymous user.', $this->migrateTasksCommand->getDescription());
    }
}
