<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private string $userId;
    private string $jwtToken;

    public function __construct(string $userId, string $jwtToken)
    {
        $this->userId = $userId;
        $this->jwtToken = $jwtToken;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->userId;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getJwtToken(): string
    {
        return $this->jwtToken;
    }
}