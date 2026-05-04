<?php

namespace App\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i<30; $i++ ) {
            $category = $this->getReference(CategoryFixtures::CAT_DECO, Category::class);
            $product = new Product;
            $product 
                ->setTitle($faker->word())
                ->setDescription($faker->paragraph(3))
                ->setPrice($faker->randomFloat(2, 5, 500))
                ->setCategory($category)
            ;
            $manager->persist($product);
        }
       // $manager->flush();
    }
}
