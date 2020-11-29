<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->json('Erreur',403,[],['groups' => ['customer:read']]);
    }
}
