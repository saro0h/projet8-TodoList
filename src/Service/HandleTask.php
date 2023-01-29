<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;

/**
 * HandleTask class
 */
final class HandleTask implements HandleTaskInterface
{
    /**
     * HandleTask constructor
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        private EntityManagerInterface $manager
    ) {
    }

    /**
     * @param Task $task
     * @return void
     */
    public function createTask(Task $task): void
    {
        $this->manager->persist($task);
        $this->manager->flush();
    }

    /**
     * @return void
     */
    public function editTask(): void
    {
        $this->manager->flush();
    }

    /**
     * @param Task $task
     * @return void
     */
    public function toggleTask(Task $task): void
    {
        $task->toggle(!$task->isDone());
        $this->manager->flush();
    }

    /**
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $this->manager->remove($task);
        $this->manager->flush();
    }
}
