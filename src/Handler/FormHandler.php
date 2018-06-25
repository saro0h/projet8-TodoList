<?php

namespace App\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class FormHandler
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var FlashBagInterface
     */
    protected $flashBag;

    /**
     * @var mixed|null
     */
    protected $data;

    /**
     * @var mixed|null
     */
    protected $parent;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $extraData;

    /**
     * FormHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FlashBagInterface $flashBag
     * @param \Twig_Environment $twig
     */
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, RequestStack $requestStack, FlashBagInterface $flashBag, \Twig_Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    /**
     * @param null $parent
     * @param $data
     * @param $view
     * @param array $extraData
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handle($parent = null, $data, $view, $extraData = [])
    {
        $this->parent = $parent;
        $this->data = $data;
        $this->view = $view;
        $this->extraData = $extraData;
        $this->createForm()->handleRequest($this->requestStack->getCurrentRequest());
        if ($this->form->isSubmitted() and $this->form->isValid()) {
            return $this->onSuccess();
        }
        return $this->onRender();
    }

    /**
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onRender()
    {
        return new Response($this->twig->render($this->view, ['form' => $this->createView()] + $this->extraData));
    }

    /**
     * @return FormInterface
     */
    public function createForm()
    {
        $this->form = $this->formFactory->create($this->getFormType(), $this->data);
        return $this->form;
    }

    /**
     * @return \Symfony\Component\Form\FormView
     */
    public function createView()
    {
        return $this->form->createView();
    }

    /**
     * @return string
     */
    abstract public function getFormType();

    /**
     * @return Response
     */
    abstract public function onSuccess();
}
