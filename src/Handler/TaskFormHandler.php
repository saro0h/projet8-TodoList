<?php

namespace App\Handler;

use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class TaskFormHandler extends FormHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var null|\Symfony\Component\Security\Core\User\UserInterface
     */
    private $user;

    /**
     * TrickFormHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FlashBagInterface $flashBag
     * @param EntityManagerInterface $manager
     * @param \Twig_Environment $twig
     */
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, RequestStack $requestStack, FlashBagInterface $flashBag, EntityManagerInterface $manager, \Twig_Environment $twig, Security $security)
    {
        parent::__construct($formFactory, $router, $requestStack, $flashBag, $twig);
        $this->manager = $manager;
        $this->user = $security->getUser();
    }

    /**
     * @return RedirectResponse
     */
    public function onSuccess()
    {
        $task = $this->form->getData();
        if (null == $task->getCreatedAt()) {
            $this->user->addTask($task);
            $this->manager->persist($task);
            $this->manager->flush();
            $this->flashBag->add("success", "La tâche a été bien été ajoutée.");
            return new RedirectResponse($this->router->generate('task_list'));
        }
        $this->manager->flush();
        $this->flashBag->add("success", "La tâche a été bien été modifiée.");
        return new RedirectResponse($this->router->generate('task_list'));
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return TaskType::class;
    }
}
