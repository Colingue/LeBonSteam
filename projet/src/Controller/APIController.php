<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use App\Repository\ArticlesRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api", name="api_")
 */
class APIController extends AbstractController
{

    /**
     * @Route("/posts/liste", name="liste", methods={"GET"})
     */
    public function liste(PostRepository $postRepo)
    {
        $posts = $postRepo->apiFindAll();

        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize('posts', 'json', [
            'circular_reference_handler' => function($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }
}
