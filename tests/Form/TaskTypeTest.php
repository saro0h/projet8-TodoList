<?php

namespace App\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use App\Form\TaskType;
use App\Entity\Task;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'title' => 'Titre',
            'content' => 'Contenu',
        ];

        $expectedTask = new Task();
        $expectedTask->setTitle($formData['title']);
        $expectedTask->setContent($formData['content']);

        $task = new Task();
        $form = $this->factory->create(TaskType::class, $task);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedTask->getTitle(), $form->get('title')->getData());
        $this->assertEquals($expectedTask->getContent(), $form->get('content')->getData());

        $children = $form->createView()->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
