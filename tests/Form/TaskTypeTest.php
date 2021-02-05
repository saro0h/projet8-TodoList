<?php
namespace App\Tests\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testCreateTaskSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2020', '07', '01');
        $date->setTime('19', '00', '00');

        $formData = [
            'title' => 'testtask',
            'content' => 'testtask2',
            'createdAt' => $date
        ];

        $model = new Task();
        $model->setCreatedAt($date);
        $form = $this->factory->create(TaskType::class, $model);

        $expected = new Task();
        $expected->setTitle('testtask');
        $expected->setContent('testtask2');
        $expected->setCreatedAt($date);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

    public function testCreateTaskIsDoneSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2020', '07', '01');
        $date->setTime('19', '00', '00');

        $formData = [
            'title' => 'testtask',
            'content' => 'testtask2',
            'createdAt' => $date
        ];

        $model = new Task();
        $model->setCreatedAt($date);
        $model->setIsDone(1);

        $form = $this->factory->create(TaskType::class, $model);

        $expected = new Task();
        $expected->setTitle('testtask');
        $expected->setContent('testtask2');
        $expected->setCreatedAt($date);
        $expected->setIsDone(1);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

    public function testDeleteTaskSuccess()
    {}

}