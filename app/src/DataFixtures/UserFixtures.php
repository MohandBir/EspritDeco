<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHacher)
    {
    }
    public function load(ObjectManager $manager ): void
    {
        $admin = new User();
        $hashedPassword = $this->passwordHacher->hashPassword($admin, 'admin');
        $admin->setEmail('amin@esprit-deco.fr')
            ->setPassword($hashedPassword)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($admin);

        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $hashedPassword = $this->passwordHacher->hashPassword($user, "pass$i");
            $user->setEmail("user$i@gmail.com")
                ->setPassword($hashedPassword)
                ->setRoles(['ROLE_USER'])
            ;
            $manager->persist($user);
        }

        $manager->flush();

    }
}
