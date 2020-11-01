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

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customers/{id}/users", name="customers_users",methods={"GET"})
     */
    public function getAllUsersWhoHaveAConnectionWithACustomer(CustomerRepository $customerRepo,int $id)
    {
        $customer = $customerRepo->findOneById($id)->getUsers()->toArray();
        return $this->json($customer,200,[],['groups' => ['customer:read']]);
    }

    /**
     * @Route("/api/customers/{id}/users/{userId}", name="customer_one_user",methods={"GET"})
     */
    public function getOneUserWhoHaveAConnectionWithACustomer(
    UserRepository $userRepo,int $id,int $userId)
    {
        $user = $userRepo->findOneById($userId);
        if($user->getCustomer()->getId() === $id){
            return $this->json($user,200,[],['groups' => ['customer:read']]);
        }
        return $this->json('Erreur',200,[],['groups' => ['customer:read']]);
    }
}
