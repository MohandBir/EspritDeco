<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public const CAT_DECO = 'decoration';
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $category = (new Category)->setName('decorer')
                    ->setColor('red')
        ;
        $manager->persist($category);
        // reference 
        $this->addReference(self::CAT_DECO, $category);
        for ($i=0; $i<5; $i++ ) {
            $category = new Category;
            $category->setName($faker->word())
                ->setColor($faker->colorName());
            ;

            $manager->persist($category);
        }
        //$manager->flush();
    }
}
