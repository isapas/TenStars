<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
    // public function load(ObjectManager $manager)
// {
//     $user = new User();
//     $user->setUsername('admin');

//     $password = $this->encoder->encodePassword($user, 'pass_1234');
//     $user->setPassword($password);

//     $manager->persist($user);
//     $manager->flush();
// }
}
