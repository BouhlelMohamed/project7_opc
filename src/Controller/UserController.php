<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
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

}
