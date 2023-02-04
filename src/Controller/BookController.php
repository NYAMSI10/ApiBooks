<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    #[Route('api/books',name: 'books', methods: ['GET'])]
    public function getAllBook( BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $book = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($book,'json',['groups' => 'getBooks']);

        return new JsonResponse($jsonBookList, Response::HTTP_OK,[],true);
    }

    #[Route('api/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getDetailBook( Book $book, SerializerInterface $serializer): JsonResponse
    {

            $jsonBookList = $serializer->serialize($book,'json',['groups' => 'getBooks']);

            return new JsonResponse($jsonBookList, Response::HTTP_OK,[],true);


    }
    #[Route('api/book/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook( Book $book, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($book);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

     #[Route('api/books', name: 'createBook', methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                               UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository,
     ValidatorInterface $validator): JsonResponse
    {
        // deserialiser le json .
       $book = $serializer->deserialize($request->getContent(),Book::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($book);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $em->persist($book);
        $em->flush();

        // Récupération de l'ensemble des données envoyées sous forme de tableau

        $content= $request->toArray();

        // Récupération de l'idauthor . s'il n pas definit pas on met -1

        $idauthor = $content['idauthor'] ?? -1;

        $book->setAuthor($authorRepository->find($idauthor));

        // permet d'afficher le livre qui a été crée à l'instant

        /* Notez également l’apparition d’un $urlGenerator ,
        qui permet de générer la route qui pourrait être utilisée pour récupérer des informations sur le livre ainsi créé. */

       $jsonBook= $serializer->serialize($book,'json',['groups'=>'getBooks']);
       $location =$urlGenerator->generate('detailBook',['id'=>$book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

       return  new JsonResponse($jsonBook , Response::HTTP_CREATED,["location"=>$location],true);
    }


    
    #[Route('api/books/{id}', name: 'updateBook', methods: ['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                        Book $currentBook      ,     AuthorRepository $authorRepository): JsonResponse
    {
        // deserialiser le json .
        $updatedBook = $serializer->deserialize($request->getContent(),Book::class, 'json',[AbstractNormalizer::OBJECT_TO_POPULATE=>$currentBook]);


        // Récupération de l'ensemble des données envoyées sous forme de tableau

        $content= $request->toArray();

        // Récupération de l'idauthor . s'il n pas definit pas on met -1

        $idauthor = $content['idauthor'] ?? -1;

        $updatedBook->setAuthor($authorRepository->find($idauthor));

        $em->persist($updatedBook);
        $em->flush();

        // permet d'afficher le livre qui a été crée à l'instant

        /* Notez également l’apparition d’un $urlGenerator ,
        qui permet de générer la route qui pourrait être utilisée pour récupérer des informations sur le livre ainsi créé. */

     //   $jsonBook= $serializer->serialize($book,'json',['groups'=>'getBooks']);
       // $location =$urlGenerator->generate('detailBook',['id'=>$book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return  new JsonResponse(null    , Response::HTTP_NO_CONTENT);
    }
}
