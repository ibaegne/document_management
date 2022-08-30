<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/app/user", name="app_users_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/search-receiver/{query}/{fileName}", name="search", methods={"GET"})
     */
    public function searchReceiver(string $query, string $fileName, UserRepository $repository): JsonResponse
    {
        return $this->json($repository->findReceiver(
            $query,
            $fileName,
            $this->getUser()->getId()
        ));
    }
}
