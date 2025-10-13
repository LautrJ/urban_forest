<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@urban-forest.fr');
        $admin->setName('Administrateur');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $agent = new User();
        $agent->setEmail('agent@urban-forest.fr');
        $agent->setName('Agent Municipal');
        $agent->setRoles(['ROLE_AGENT']);
        $agent->setPassword($this->passwordHasher->hashPassword($agent, 'agent123'));
        $manager->persist($agent);

        $citizen = new User();
        $citizen->setEmail('citoyen@urban-forest.fr');
        $citizen->setName('Citoyen Test');
        $citizen->setRoles(['ROLE_CITIZEN']);
        $citizen->setPassword($this->passwordHasher->hashPassword($citizen, 'citoyen123'));
        $manager->persist($citizen);

        $manager->flush();
    }
}
