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

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/client/{id}", name="users_customers",methods={"GET"})
     */
    public function getAllUsersWhoHaveAConnectionWithACustomer(CustomerRepository $customerRepo,UserRepository $repo,$id)
    {
        $customer = $customerRepo->findOneById($id);
        dd(json_encode($customer->getUsers()->toArray()));
        return $this->json($customer->getUsers()->toArray(),200,[],['groups' => 'customer:read']);
    }
}
