<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessDeniedListener implements EventSubscriberInterface
{
    private $urlGenerator;
    private $security;
    private $session;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, RequestStack $request)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->session = $request->getSession();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        if ($this->security->isGranted('ROLE_USER')) {
            $this->session->getFlashBag()->add('error', "Vous n'avez pas les permissions.");

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('homepage')));
        } else {
            $this->session->getFlashBag()->add('error', "Veuillez vous connecter.");

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('login')));
        }
    }
}
