<?php

namespace App\Security;

use App\Security\Voter\DeleteTaskVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/*
*   catch events about security and make flash message on
*   LogOut - Login Success - Login Failure
*/

class SecurityEventSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => ['logoutMessage', 5],
            LoginSuccessEvent::class => ['loginSuccesMessage', 6],
            LoginFailureEvent::class => ['loginFailureMessage', 3],
            KernelEvents::EXCEPTION => ['onKernelException', 10]
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }
        $session = $event->getRequest()->getSession();
        $message = "Oupsssss !!!";

        if ($exception->getAttributes()[0] == DeleteTaskVoter::DELETE_TASK) {
            $message = "Vous n'avez pas le droit de supprimer cette tâche !";
        }

        $session->getFlashBag()->add('danger', $message);

        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('homepage')));
    }

    public function logoutMessage(LogoutEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'Vous êtes déconnecté !');
    }

    public function loginSuccesMessage(LoginSuccessEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'Vous vous êtes connecté !');
    }

    public function loginFailureMessage(LoginFailureEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('danger', 'Nom utilisateur ou mot de passe incorrect !');
    }
}
