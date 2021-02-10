<?php
namespace App\Tests\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testCreateTaskSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2021', '01', '20');
        $date->setTime('10', '00', '00');

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

    public function testCreateTaskIsNotDoneSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2021', '01', '20');
        $date->setTime('10', '00', '00');

        $formData = [
            'title' => 'testtask',
            'content' => 'testtask2',
            'createdAt' => $date
        ];

        $model = new Task();
        $model->setCreatedAt($date);
        $model->setIsDone(0);

        $form = $this->factory->create(TaskType::class, $model);

        $expected = new Task();
        $expected->setTitle('testtask');
        $expected->setContent('testtask2');
        $expected->setCreatedAt($date);
        $expected->setIsDone(0);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

    public function testCreateTaskIsDoneSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2021', '01', '20');
        $date->setTime('10', '00', '00');

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

    public function testCreateTaskByUserAnonymeSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2021', '01', '20');
        $date->setTime('10', '00', '00');

        $formData = [
            'title' => 'testtask',
            'content' => 'testtask2',
            'createdAt' => $date
        ];

        $model = new Task();
        $model->setCreatedAt($date);
        $model->setUser(NULL);

        $form = $this->factory->create(TaskType::class, $model);

        $expected = new Task();
        $expected->setTitle('testtask');
        $expected->setContent('testtask2');
        $expected->setCreatedAt($date);
        $expected->setUser(NULL);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

    public function testCreateTaskByUserNotAnonymeSuccess()
    {
        $date = new \DateTime;
        $date->setDate('2021', '01', '20');
        $date->setTime('10', '00', '00');

        $formData = [
            'title' => 'testtask',
            'content' => 'testtask2',
            'createdAt' => $date
        ];

        $model = new Task();
        $model->setCreatedAt($date);
        $model->setUser(new User());

        $form = $this->factory->create(TaskType::class, $model);

        $expected = new Task();
        $expected->setTitle('testtask');
        $expected->setContent('testtask2');
        $expected->setCreatedAt($date);
        $expected->setUser(new User());

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}