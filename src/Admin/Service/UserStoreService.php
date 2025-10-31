<?php

namespace App\Admin\Service;

use App\Entity\Store;
use App\Entity\User;
use App\Entity\UserStore;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserStoreService {

    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function attach(User $user, Store $store, ?UserStore $data = null): UserStore {
        // Evita duplicado
        $repo = $this->entityManager->getRepository(UserStore::class);
        $existing = $repo->findOneBy(['user' => $user, 'store' => $store]);

        if ($existing) {
            return $existing; // já existe, não cria novo
        }

        $userStore = $data ?? new UserStore();
        $userStore->setUser($user);
        $userStore->setStore($store);

        $this->entityManager->persist($userStore);
        $this->entityManager->flush();

        $this->logger->info('Usuário vinculado à loja', [
            'store_id'   => $store->getId(),
            'store_nome' => $store->getCorporateName(),
            'user'       => $user->getUserIdentifier(),
        ]);

        return $userStore;
    }
}
