<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $password = 'IchTeste';
        $hash = $this->passwordHasher->hashPassword($user, $password);

        $user->setEmail('test@example.com')
            ->setRoles(["ROLE_USER"])
            ->setPassword($hash);
        $manager->persist($user);

        $user = new User();
        $password = '12345678';
        $hash = $this->passwordHasher->hashPassword($user, $password);

        $user->setEmail('student@example.com')
            ->setRoles(["ROLE_USER"])
            ->setPassword($hash);
        $manager->persist($user);

        $manager->flush();
    }
}
