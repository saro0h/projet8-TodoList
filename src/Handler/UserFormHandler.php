<?php

namespace App\Handler;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFormHandler
 * @package App\Handler
 */
class UserFormHandler extends FormHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFormHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FlashBagInterface $flashBag
     * @param EntityManagerInterface $manager
     * @param \Twig_Environment $twig
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, RequestStack $requestStack, FlashBagInterface $flashBag, EntityManagerInterface $manager, \Twig_Environment $twig, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($formFactory, $router, $requestStack, $flashBag, $twig);
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return RedirectResponse
     */
    public function onSuccess()
    {
        $user = $this->form->getData();
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        if (null == $user->getCreatedAt()) {
            $this->manager->persist($user);
            $this->manager->flush();
            $this->flashBag->add("success", "L'utilisateur a bien été ajouté.");
            return new RedirectResponse($this->router->generate('user_list'));
        }
        $this->manager->flush();
        $this->flashBag->add("success", "L'utilisateur a bien été modifié.");
        return new RedirectResponse($this->router->generate('user_list'));
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return UserType::class;
    }
}
