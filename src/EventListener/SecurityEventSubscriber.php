<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SecurityEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'logoutMessage',
            LoginSuccessEvent::class => 'loginSuccesMessage',
            LoginFailureEvent::class => 'loginFailureMessage'
        ];
    }

    public function logoutMessage(LogoutEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'Vous êtes déconnecté !');
    }

    public function loginSuccesMessage(LoginSuccessEvent $event)
    {
        //dd($event);
        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'Vous vous êtes connecté !');
    }

    public function loginFailureMessage(LoginFailureEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('danger', 'Nom utilisateur ou mot de passe incorrect !');
    }
}
