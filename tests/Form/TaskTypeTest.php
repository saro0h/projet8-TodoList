<?php

namespace App\Tests\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidDataTaskType(): void
    {
        $formData = array(
            'title' => 'Tâche de TaskType',
            'content' => 'Le contenu de la tâche de TaskType',
        );

        $objectToCompare = new Task();

        $form = $this->factory->create(TaskType::class, $objectToCompare);

        $task = new Task();
        $task->setTitle($formData['title']);
        $task->setContent($formData['content']);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($task->getTitle(), $form->get('title')->getData());
        $this->assertEquals($task->getContent(), $form->get('content')->getData());
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) 
        {
            $this->assertArrayHasKey($key, $children);
        }
    }

}
