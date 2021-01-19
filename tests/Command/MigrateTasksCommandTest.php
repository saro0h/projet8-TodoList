<?php

namespace Tests\Command;

use App\Command\MigrateTasksCommand;
use App\Service\ManageUsers;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MigrateTasksCommandTest extends TestCase
{

    /**
     * @var MigrateTasksCommand
     */
    private $migrateTasksCommand;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $manageUser = new ManageUsers($em, $encoder);
        $this->migrateTasksCommand = new MigrateTasksCommand($em, $encoder, $manageUser);
        parent::__construct($name, $data, $dataName);
    }

    public function testConfigure(){
        $this->migrateTasksCommand->setDescription();
        $this->assertEquals('Attach old tasks to anonymous user.',$this->migrateTasksCommand->getDescription());
    }
}
