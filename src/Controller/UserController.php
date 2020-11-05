<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/customers/{id}", name="add_user_for_customers",methods={"POST"})
     */
    public function getAllUsersWhoHaveAConnectionWithACustomer(EntityManagerInterface $em,int $id)
    {

        // $product = new Product();
        // $product->setName('Keyboard');
        // $product->setPrice(1999);
        // $product->setDescription('Ergonomic and stylish!');

        // // tell Doctrine you want to (eventually) save the Product (no queries yet)
        // $entityManager->persist($product);

        // // actually executes the queries (i.e. the INSERT query)
        // $entityManager->flush();
/* 
        return new Response('Saved new product with id '.$product->getId());
        return $this->json($customer,200,[],['groups' => ['customer:read']]); */
    }

}
