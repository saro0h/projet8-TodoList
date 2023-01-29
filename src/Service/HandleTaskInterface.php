<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;

/**
 * HandleTaskInterface interface
 */
interface HandleTaskInterface
{
    public function __construct(EntityManagerInterface $manager);
    public function createTask(Task $task): void;
    public function editTask(): void;
    public function toggleTask(Task $task): void;
    public function deleteTask(Task $task): void;
}
