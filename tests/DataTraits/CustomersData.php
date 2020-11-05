<?php
namespace App\Tests\DataTraits;

use App\Entity\Customer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

trait CustomersData {
   
    public function addCustomers(EntityManagerInterface $em,int $quantity=1)
    {
        for($i = 0; $i < $quantity; $i++)
        {
            $customer = new Customer();
            $customer->setName('test');
            $customer->setEmail('email@gmail.com');
            $customer->setPassword('password');
    
            $em->persist($customer);
        }
        $em->flush();
        
        return $customer;
    }
}