<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Referer
{
    private Request $request;
    private $requestSession;
    private $router;


    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->requestSession = $this->request->getSession();
        $this->router = $router;
    }

    public function set(): ?string
    {
        $referer = $this->request->headers->get('referer');
        $baseURL = $this->request->getSchemeAndHttpHost();
        $refererPath = str_replace($baseURL, '', $referer);
        $loginPath = $this->router->generate('app_login');

        if($referer != null && $refererPath != $loginPath) {
            return $this->requestSession->set('referer', $referer);
        }
        return null;
    }

    public function get(): ?string
    {
        return $this->requestSession->get('referer') ?? null;
    }

    public function goTo(): Response
    {
        if ($this->get()) {
            return new RedirectResponse($this->get());
        } else {
            return new RedirectResponse($this->router->generate('homepage'));
        }
    }

    public function setAndGo(): Response
    {
        $this->set();
        return $this->goTo();
    }
}