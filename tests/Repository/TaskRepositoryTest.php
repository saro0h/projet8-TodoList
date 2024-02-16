<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testConstruct()
    {
        $repo = new TaskRepository($this->createMock(ManagerRegistry::class));
        $this->assertInstanceOf(TaskRepository::class, $repo);
    }

    public function testFindAll()
    {
        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAll();

        $this->assertContainsOnly(Task::class, $tasks);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
