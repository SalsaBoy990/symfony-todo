<?php

namespace App\Utils;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Base extends AbstractController
{
    public function redirectWithMessage(string $redirectRoute, string $type, string $message): RedirectResponse
    {
        $this->addFlash($type, $message);
        return $this->redirectToRoute($redirectRoute);
    }

    public function notFoundError(mixed $exists, string $entityName, int $id = null): void
    {
        if (!$exists) {
            if ($id) {
                throw $this->createNotFoundException('No ' . $entityName . ' found for id ' . $id);
            } else {
                throw $this->createNotFoundException('No ' . $entityName . 's found');
            }
        }
    }

}
