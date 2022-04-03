<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    public function set()
    {
        $referer = $this->request->headers->get('referer');
        $baseURL = $this->request->getSchemeAndHttpHost();
        $refererPath = str_replace($baseURL, '', $referer);
        $loginPath = $this->router->generate('app_login');

        if($referer != null && $refererPath != $loginPath) return $this->requestSession->set('referer', $referer);
    }

    public function get()
    {
        return $this->requestSession->get('referer');
    }

    public function goTo()
    {
        if ($this->get()) {
            return new RedirectResponse($this->get());
        } else {
            return new RedirectResponse($this->router->generate('home'));
        }
    }

    public function setAndGo()
    {
        $this->set();
        return $this->goTo();
    }
}