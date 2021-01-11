<?php


namespace App\Security;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * AccessDeniedHandler constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param FlashBagInterface $flashBag
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, FlashBagInterface $flashBag)
    {
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }


    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        foreach ($accessDeniedException->getAttributes() as $attribute) {
            if ($attribute == 'ROLE_ADMIN') {
                $this->flashBag->add('error', 'Vous n\'êtes pas autorisé à acceder à la gestion des utilisateurs.');
                return new RedirectResponse($this->urlGenerator->generate('homepage'));
            }

            if ($attribute == 'TASK_DELETE') {
                $this->flashBag->add('error', 'Tâche non supprimée, une tâche ne peut etre supprimée que par son auteur.');
                return new RedirectResponse($this->urlGenerator->generate('task_list'));
            }
        }
    }
}