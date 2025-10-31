<?php

namespace App\Admin\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {

    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function listUsers(User $loggedUser): array {
        $userRoles = $loggedUser->getRoles();
        if (in_array('ROLE_SUPER_ADMIN', $userRoles))
            return $this->userRepository->findAll();

        if (in_array('ROLE_STORE_OWNER', $userRoles)) {
            $store = $loggedUser->getUserStores()->first()->getStore();

            if ($store) {
                $users = $store->getUserStores()->map(fn($item) => $item->getUser());
                return $users->toArray();
            }
        }

        return [];
    }

    public function saveUser(User $user, ?string $plainPassword = null, string $actionType): User {
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('Usuário ' . $actionType, [
            'id' => $user->getId(),
            'nome' => $user->getName(),
            'email' => $user->getUserIdentifier(),
        ]);

        return $user;
    }

    public function deleteUser(User $user): void {
        $userName = $user->getName();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->logger->critical('Usuário excluído', [
            'id' => $user->getId(),
            'nome' => $userName,
            'email' => $user->getUserIdentifier(),
        ]);
    }
}
