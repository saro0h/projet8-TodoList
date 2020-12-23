<?php


namespace App\Security;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * AccessDeniedHandler constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        foreach ($accessDeniedException->getAttributes() as $attribute) {
            if ($attribute == 'ROLE_ADMIN') {
                return new RedirectResponse($this->urlGenerator->generate('home'));
            }

            if ($attribute == 'ROLE_USER_VERIFIED') {
                return new RedirectResponse($this->urlGenerator->generate('app_denied_unverified'));
            }

            if ($attribute == 'TRICK_DELETE') {
                return new RedirectResponse($this->urlGenerator->generate('trick_delete_denied', ['slug' => $request->get('slug')]));
            }
        }
    }
}