<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        $date->setDate('2021', '01', '20');
        $date->setTime('10', '00', '00');

        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle("Ma Tâche".$i);
            $task->setContent("Je dois faire ".$i);
            $task->setCreatedAt($date);
            $manager->persist($task);
            $manager->flush();
        }
    }
}
