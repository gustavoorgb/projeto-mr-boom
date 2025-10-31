<?php

namespace App\Admin\Service;

use App\Entity\Store;
use App\Entity\User;
use App\Repository\StoreRepository;
use App\Repository\UserStoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StoreService {

    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private StoreRepository $storeRepository,
        private UserStoreRepository $userStoreRepository,
        private StoreActiveService $storeActiveService
    ) {
    }

    public function listStores(User $loggedUser): array {
        if (in_array('ROLE_SUPER_ADMIN', $loggedUser->getRoles())) {
            return $this->storeRepository->findAll();
        }

        return array_map(fn($userStore) => $userStore->getStore(), $loggedUser->getUserStores()->toArray());
    }

    public function selectStore(Store $store, User $user): void {

        $userStore = $this->userStoreRepository->findOneBy(['user' => $user, 'store' => $store]);

        if (!$userStore && !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException('Você não tem permissão para acessar esta loja.');
        }

        $this->storeActiveService->setActive($store);

        $this->logger->info('Loja selecionada para a sessão', [
            'store_id' => $store->getId(),
            'store_nome' => $store->getCorporateName(),
            'user' => $user->getUserIdentifier(),
        ]);
    }

    public function saveStore(Store $store, User $user, string $actionType): void {
        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $this->logger->info('Loja ' . $actionType, [
            'id' => $store->getId(),
            'nome' => $store->getCorporateName(),
            'user' => $user->getUserIdentifier(),
        ]);
    }

    public function deleteStore(Store $store, User $user): void {
        $storeName = $store->getCorporateName();

        $this->entityManager->remove($store);
        $this->entityManager->flush();

        $this->logger->critical('Loja excluída', [
            'id' => $store->getId(),
            'nome' => $storeName,
            'user' => $user->getUserIdentifier(),
        ]);
    }
}
