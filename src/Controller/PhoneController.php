<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PhoneController extends AbstractController
{

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("/api/phones", name="all_phones",methods={"GET"})
     */
    public function getAll(PhoneRepository $repo)
    {
        $value = $this->cache->get('cache_all_phone', function (ItemInterface $item) use ($repo) {
            $item->expiresAfter(60);
            return $repo->findAll();
        });

        return $this->json($value,200,[],['groups' => 'phone:read']);
    }

    /**
    * @Route("/api/phones/{id}", name="onePhone",methods={"GET"})
    */
    public function getOnePhone(PhoneRepository $repo,int $id)
    {
        $value = $this->cache->get('cache_one_phone', function (ItemInterface $item) use ($repo,$id) {
            $item->expiresAfter(60);
            return $repo->findOneById($id);
        });
        if(isset($value)) {
            return $this->json($value,200,[],['groups' => 'phone:read']);
        }
        return new Response("the phone with id $id does not exist",422);
    }

    /**
    * @Route("/api/phones", name="insert_phone",methods={"POST"})
    */
    public function insertOnePhone(Request $request,EntityManagerInterface $em)
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
