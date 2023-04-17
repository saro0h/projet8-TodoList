<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'app.title',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'app.content',
            ])
            ->add('category', EntityType::class, [
                'choice_label' => 'title',
                'class' => Category::class,
                'expanded' => true,
                'label' => 'app.category',
                'multiple' => false,
            ])
        ;
    }
}
