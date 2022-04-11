<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {


        foreach (range(0,50) as $b){
            $product = new Product();
            $product->setName('Product '.$b);
            $product->setPrice(rand(100,1000));
            $product->setQuantity(rand(0,20));
            $manager->persist($product);
        }

        $user1 = new User();
        $user1->setUsername('user1');
        $user1->setPassword($this->userPasswordHasherInterface->hashPassword($user1, '12user34'));
        $manager->persist($user1);
        $user2 = new User();
        $user2->setUsername('user2');
        $user2->setPassword($this->userPasswordHasherInterface->hashPassword($user2, '12user34'));
        $manager->persist($user2);
        $user3 = new User();
        $user3->setUsername('user3');
        $user3->setPassword($this->userPasswordHasherInterface->hashPassword($user3, '12user34'));
        $manager->persist($user3);
        $manager->flush();


    }
}
