<?php

namespace App\EventSubscriber;

use App\Admin\Service\StoreActiveService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CheckStoreSubscriber implements EventSubscriberInterface {

    private const ADMIN_PREFIX = '/admin';

    public function __construct(
        private StoreActiveService $storeActiveService,
        private UrlGeneratorInterface $urlGenerator,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void {
        if (!$event->isMainRequest())
            return;

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        //pula se a rota ja for a de seleção ou se nao tem prefixo admin
        if (in_array($route, ['app_store_select', 'app_store_set']) or str_starts_with($request->getPathInfo(), self::ADMIN_PREFIX) === false)
            return;

        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser() || !is_object($token->getUser())) {
            return;
        }

        if (in_array('ROLE_SUPER_ADMIN', $token->getUser()->getRoles())) return;

        if (!$this->storeActiveService->getActive()) {
            $request->getSession()->getFlashBag()->add('info', 'Por favor, selecione uma loja para continuar.');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_store_select')));
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }
}
