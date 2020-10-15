<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phone", name="all_phones",methods={"GET"})
     */
    public function getAll(PhoneRepository $repo)
    {
        return $this->json($repo->findAll(),200,[],['groups' => 'phone:read']);
    }
}
