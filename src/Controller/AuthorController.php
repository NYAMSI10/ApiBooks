<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('api/author', name: 'getauthors', methods: ['GET'])]
    public function index( AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $author = $authorRepository->findAll();

        $jsonlistauthor = $serializer->serialize($author, 'json',['groups'=>'getauthors']);

        return new JsonResponse($jsonlistauthor, Response::HTTP_OK,[],true);
    }

    #[Route('api/author/{id}', name: 'getauthors', methods: ['GET'])]
    public function getDetailAuthor( Author $author, SerializerInterface $serializer): JsonResponse
    {
        $jsonlistauthor = $serializer->serialize($author, 'json',['groups'=>'getauthors']);

        return new JsonResponse($jsonlistauthor, Response::HTTP_OK,[],true);
    }
}
