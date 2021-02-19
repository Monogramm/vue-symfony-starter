<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

// https://codereviewvideos.com/course/beginners-guide-back-end-json-api-front-end-2018/video/healthcheck-raw-symfony-4
class HealthcheckController extends AbstractController
{
    /**
     * @Route ("/ping", name="ping", methods={"GET"},)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse('pong');
    }

    /**
     * @Route ("/health", name="health", methods={"GET"},)
     *
     * @return JsonResponse
     */
    public function health(
        Request $request,
        EntityManagerInterface $emi
    ): JsonResponse {
        $health = !empty($request) && !empty($emi);
        // TODO Execute all existing healtcheck
        // TODO Return KO if any healthcheck is false

        if ($health) {
            $response = new JsonResponse('UP');
        } else {
            $response = new JsonResponse('KO');
        }

        return $response;
    }
}
