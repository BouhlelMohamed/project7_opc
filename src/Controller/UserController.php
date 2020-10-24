<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/users", name="all_phones",methods={"GET"})
     */
    public function getAllUsersWhoHaveAConnectionWithACustomer(UserRepository $repo)
    {
        return $this->json($repo->findAll(),200,[],['groups' => 'phone:read']);
    }
}
