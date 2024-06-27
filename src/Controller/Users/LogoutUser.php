<?php

namespace App\Controller\Users;

use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController()]
final class LogoutUser
{
    #[Route('/logout', name: 'app_logout')]
    public function __invoke(): void
    {}
}