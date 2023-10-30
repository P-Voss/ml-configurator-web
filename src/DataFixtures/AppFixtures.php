<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;
    private string $sampleUserFile;

    public function __construct(UserPasswordHasherInterface $passwordHasher, $sampleUserFile)
    {
        $this->passwordHasher = $passwordHasher;
        $this->sampleUserFile = $sampleUserFile;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $password = 'IchTeste';
        $hash = $this->passwordHasher->hashPassword($user, $password);

        $user->setEmail('test@example.com')
            ->setRoles(["ROLE_USER"])
            ->setIsDemoUser(false)
            ->setPassword($hash);
        $manager->persist($user);

        $user = new User();
        $password = '12345678';
        $hash = $this->passwordHasher->hashPassword($user, $password);

        $user->setEmail('student@example.com')
            ->setRoles(["ROLE_USER"])
            ->setIsDemoUser(false)
            ->setPassword($hash);
        $manager->persist($user);

        $usernames = [];
        $users = [];
        while (count($usernames) < 25) {

            $username = $this->getAdjective() . $this->getAnimal();
            while (in_array($username, $usernames)) {
                $username = $this->getAdjective() . $this->getAnimal();
            }
            $password = uniqid();
            $usernames[] = $username;
            $users[] = [
                'name' => $username . '@example.com',
                'password' => $password,
            ];

            $user = new User();
            $hash = $this->passwordHasher->hashPassword($user, $password);

            $user->setEmail($username . '@example.com')
                ->setRoles(["ROLE_USER"])
                ->setIsDemoUser(true)
                ->setPassword($hash);
            $manager->persist($user);
        }

        $manager->flush();

        $jsonData = json_encode(array_values($users));
        file_put_contents($this->sampleUserFile, $jsonData);
    }

    private function getAdjective(): string {
        $adverbs = ['apt', 'amazing', 'angry', 'radiant', 'pretty', 'smart', 'cool', 'friendly', 'best', 'bold', 'busy', 'brave', 'calm', 'captivating', 'clever', 'cheerful', 'cute', 'eager', 'enchanted', 'educated', 'fair', 'fine', 'free'];
        $key = array_rand($adverbs);
        return ucfirst($adverbs[$key]);
    }

    private function getAnimal(): string {
        $animals = ['alpaca', 'bear', 'crow', 'dolphin', 'duck', 'eagle', 'ferret', 'fox', 'frog', 'gecko', 'giraffe', 'goose', 'guppy', 'hare', 'hawk', 'hornet', 'horse', 'jaguar', 'panda', 'raccoon', 'squirrel'];
        $key = array_rand($animals);
        return ucfirst($animals[$key]);
    }
}
