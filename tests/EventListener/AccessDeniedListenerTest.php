<?php

namespace App\Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\EventListener\AccessDeniedListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\ExceptionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AccessDeniedListenerTest extends KernelTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testOnKernelException($event, $isGranted, $expected)
    {
        $kernel = self::bootKernel();

        $urlGenerator = $kernel->getContainer()->get('router');

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn($isGranted);

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $listener = new AccessDeniedListener(
            $urlGenerator,
            $security,
            $requestStack
        );

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('onKernelException', [$listener, 'onKernelException']);
        $dispatcher->dispatch($event, 'onKernelException');

        $this->assertTrue(isset($listener->getSubscribedEvents()['kernel.exception']));

        if ($expected) {
            $this->assertSame($expected, $event->getResponse()->getTargetUrl());
        } else {
            $this->assertNull($event->getResponse());
        }
    }

    public function provideCases()
    {
        return [
            [
                new ExceptionEvent(
                    $this->createMock(HttpKernelInterface::class),
                    $this->createMock(Request::class),
                    1,
                    $this->createMock(AccessDeniedException::class)
                ),
                true,
                '/'
            ],
            [
                new ExceptionEvent(
                    $this->createMock(HttpKernelInterface::class),
                    $this->createMock(Request::class),
                    1,
                    $this->createMock(AccessDeniedException::class)
                ),
                false,
                '/login'
            ],
            [
                new ExceptionEvent(
                    $this->createMock(HttpKernelInterface::class),
                    $this->createMock(Request::class),
                    1,
                    $this->createMock(ExceptionInterface::class)
                ),
                true,
                null
            ]
        ];
    }
}
