<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
    * @Route("/api/phone/{id}", name="onePhone",methods={"GET"})
    */
    public function getOne(PhoneRepository $repo,int $id)
    {
        $findOnePhone = $repo->findOneById($id);
        if(isset($findOnePhone)) {
            return $this->json($findOnePhone,200,[],['groups' => 'phone:read']);
        }
        return new Response("the phone with id $id does not exist",422);
    }

    /**
    * @Route("/api/phone", name="insert_phone",methods={"POST"})
    */
    public function insert(Request $request,EntityManagerInterface $em)
    {
        $phone = new Phone;
        $phone->setName($request->get('name'));
        $phone->setPrice($request->get('price'));
        $phone->setColor($request->get('color'));
        $phone->setDescription($request->get('description'));
        $phone->setCreatedAt(new \DateTime());

        $em->persist($phone);

        $em->flush();

        return $this->json($phone,200,[],['groups' => 'phone:read']);
    }
}
