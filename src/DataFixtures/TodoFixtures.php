<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Todo;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TodoFixtures extends Fixture
{
    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $status = [
            'pending',
            'in progress',
            'done'
        ];

        $priority = [
            'low',
            'medium',
            'high'
        ];

        for ($i = 0; $i < 20; $i++) {
            // set the user
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setRoles(['ROLE_USER']);

            // store hased password only (uses the "auto" hash)
            $plaintextPassword = 'password';
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setPassword($hashedPassword);

            // set the todo
            $todo = new Todo();
            $todo
                ->setName($faker->sentence(8))
                ->setPriority($priority[$faker->numberBetween(0, 2)])
                ->setStatus($status[$faker->numberBetween(0, 2)])
                ->setDateDue($faker->dateTimeBetween('0 day', '+1 week'))
                ->setDateCreated($faker->dateTimeInInterval('-2 week', '+0 days'))
                ->setDescription($faker->realText());

            $user->addTodo($todo);

            $manager->persist($todo);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
