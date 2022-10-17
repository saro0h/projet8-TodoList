<?php

namespace App\Tests\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Task;
use App\Entity\User;

class ToggleTest extends WebTestCase
{
    /**
     * @test
     */
    public function task_should_toggled()
    {
        // création du client
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $client->loginUser($user);

        /** @var Task $task */
        $task = $entityManager->getRepository(Task::class)->find(2);
        /* on stock la valeur de $task->isDone() plutôt que de $task car task
        est modifié par la requête et on se retrouve à comparer 2 true après*/
        $originalTask = $task->isDone(); // isDone return bool(false)

        // requête du toggle
        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate("task_toggle", ["id" => $task->getId(2)])
        ); // isDone return bool(true)

        // récupère la nouvelle tâche toggled stocké dans $toggleTask
        $toggleTask = $entityManager->getRepository(Task::class)->find(2);
        // comparaison des 2 états
        $this->assertNotSame($originalTask, $toggleTask->isDone());
    }
}
