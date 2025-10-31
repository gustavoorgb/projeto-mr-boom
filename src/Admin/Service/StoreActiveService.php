<?php

namespace App\Admin\Service;

use App\Entity\Store;
use App\Repository\StoreRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StoreActiveService {

    public function __construct(private RequestStack $requestStack, private StoreRepository $storeRepository) {
    }

    private function getSession(): ?SessionInterface {
        if ($this->requestStack->getSession()->isStarted())
            return $this->requestStack->getSession();

        return null;
    }

    public function setActive(Store $store): void {
        $session = $this->getSession();
        if ($session)
            $session->set('store_active', $store->getId());
    }

    public function getActive(): ?Store {
        $session = $this->getSession();
        if ($session) {
            $id = $session->get('store_active');
            return $id ? $this->storeRepository->find($id) : null;
        }

        return null;
    }

    public function clearActive(): void {
        $session = $this->getSession();
        if ($session)
            $session->remove('store_active');
    }
}
