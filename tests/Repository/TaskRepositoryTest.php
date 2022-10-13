<?php

namespace App\Tests\Repository;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskRespositoryTest extends WebTestCase
{
    public function testSaveTask()
    {
        $faker = Factory::create('fr-FR');
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy([], ['id' => 'DESC']);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $counterBefore = $taskRepository->count([]);
        $task =  (new Task())->setTitle($faker->sentence(2))
            ->setContent($faker->text())
            ->setCreatedAt($faker->dateTime())
            ->setIsDone(0)
            ->setAuthor($user);
        $taskRepository->save($task, true);
        $counterAfter = $taskRepository->count([]);
        $this->assertEquals($counterBefore + 1, $counterAfter);
    }

    public function testRemoveTask()
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $counterBefore = $taskRepository->count([]);
        $task = $taskRepository->findOneBy([], ['id' => 'DESC']);
        $taskRepository->remove($task, true);
        $counterAfter = $taskRepository->count([]);
        $this->assertEquals($counterBefore - 1, $counterAfter);
    }
}
