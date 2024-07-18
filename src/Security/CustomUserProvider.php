<?php

namespace App\Security;

use App\Security\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class CustomUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifierAndToken(string $identifier, string $token): User
    {
        return new User($identifier, $token);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // This method must be implemented, but you can leave it empty if not used.
        throw new \Exception('Not implemented');
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}