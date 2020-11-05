<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/api/users/customers/{id}", name="add_user_for_customers",methods={"POST"})
     */
    public function addANewUserLinkedToACustomer(Request $request,EntityManagerInterface $em,int $id,CustomerRepository $customerRepo)
    {
        $customer = $customerRepo->findOneById($id);
        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setAge($request->get('age'));
        $user->setCustomer($customer);

        $em->persist($user);

        $em->flush();

        return $this->json($user,200,[],['groups' => ['customer:read']]);
    }

    /**
     * @Route("/api/users/{userId}/customers/{customerId}", name="delete_user",methods={"DELETE"})
     */
    public function deleteUser(int $userId,int $customerId,UserRepository $userRepo,CustomerRepository $customerRepo,EntityManagerInterface $em)
    {
        $customerId = $customerRepo->findOneById($customerId);

        $user = $userRepo->findOneById($userId);

        if($user->getCustomer()->getId() === $customerId->getId()){
            $em->remove($user);
            $em->flush();    
            return $this->json('User '.$user->getUsername().' is deleted',200);
        }
        return $this->json('Unauthorized',403);
    }
}
