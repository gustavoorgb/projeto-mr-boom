<?php

namespace App\EventSubscriber;

use App\Admin\Service\StoreActiveService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class StoreActiveSubscriber implements EventSubscriberInterface {

    public function __construct(private RouterInterface $router, private StoreActiveService $storeActiveService) {
    }

    public function onLogin(InteractiveLoginEvent $event) {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if (!method_exists($user, 'getUserStores'))
            return;

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) return;

        $stores = $user->getUserStores()->map(fn($us) => $us->getStore())->toArray();

        if (count($stores) == 1)
            $this->storeActiveService->setActive($stores[0]);
    }

    public static function getSubscribedEvents(): array {

        return [
            InteractiveLoginEvent::class => ['onLogin', 10]
        ];
    }
}
