<?php
namespace App\Tests\DataTraits;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

trait UsersData {
   
    public function addUsers(EntityManagerInterface $entityManager,Customer $customer,int $quantity=0)
    {
        for($i = 0; $i <= $quantity; $i++)
        {
            $user = new User();
            $user->setUsername('testUser');
            $user->setAge(15);
            $user->setCustomer($customer);

            $entityManager->persist($user);
        }
        $entityManager->flush();

        return $user;
    }
}