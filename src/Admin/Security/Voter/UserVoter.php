<?php

namespace App\Admin\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter {

    public const EDIT = 'user_edit';
    public const VIEW = 'user_view';
    public const CREATE = 'user_create';
    public const DELETE = 'user_delete';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }


    protected function supports(string $attribute, $subject): bool {
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            and $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, ?Vote $vote = null): bool {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            $vote?->addReason('O usuário não esta logado.');
            return false;
        }

        $userRoles = $currentUser->getRoles();

        switch ($attribute) {
            case self::VIEW:
            case self::CREATE:
            case self::EDIT:
                return count(array_intersect($userRoles, ['ROLE_STORE_OWNER', 'ROLE_USER_MANAGER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) > 0;

            case self::DELETE:
                return count(array_intersect($userRoles, ['ROLE_STORE_OWNER', 'ROLE_USER_MANAGER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) > 0
                    and $currentUser->getId() !== $subject->getId();
        }
        return false;
    }
}
