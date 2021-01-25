<?php

namespace Tests\Command;

use App\Command\MigrateTasksCommand;
use App\Entity\User;
use App\Service\ManageUsers;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
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
    /**
     * @var EntityManagerInterface|MockObject
     */
    private $em;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->manageUser = $this->createMock(ManageUsers::class);
        $this->migrateTasksCommand = new MigrateTasksCommand($this->em, $encoder, $this->manageUser);
    }

    public function testConfigure()
    {
        $this->assertEquals('Attach old tasks to anonymous user.', $this->migrateTasksCommand->getDescription());
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $this->manageUser
            ->method('getAnonymousUser')
            ->willReturn(null);

        $command = $application->find('app:migrate-tasks');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // the output of the command in the console
        $this->assertStringContainsString(Command::SUCCESS, $commandTester->getStatusCode());

    }
}
