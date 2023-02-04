<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BooksFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr FR');
        $listAuthor = [];
        for ($i=0; $i<10 ; $i++)
        {
            $author = new Author();
            $author->setFirstName($faker->firstName);
            $author->setLastName($faker->lastName);
            $manager->persist($author);
            $listAuthor[] = $author;
            $manager->flush();
        }
         for ($i = 0; $i<20 ; $i++)
         {
             $livre = new Book();
             $livre->setTitle('Livre'. $i);
             $livre->setCovertext('Quatrime de couverture numéro : '. $i);
             $livre->setAuthor($listAuthor[array_rand($listAuthor)]);
             $manager->persist($livre);
             $manager->flush();

         }


    }
}
