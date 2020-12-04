<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CustomerController extends AbstractController
{
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("/api/customers/{id}/users", name="customers_users",methods={"GET"})
     */
    public function getAllUsersWhoHaveAConnectionWithACustomer(CustomerRepository $customerRepo,int $id)
    {
        $value = $this->cache->get('cache_all_users_with_a_customer', function (ItemInterface $item) use ($customerRepo,$id) {
            $item->expiresAfter(60);
            return $customerRepo->findOneById($id)->getUsers()->toArray();
        });
        return $this->json($value,200,[],['groups' => ['customer:read']]);
    }

    /**
     * @Route("/api/customers/{id}/users/{userId}", name="customer_one_user",methods={"GET"})
     */
    public function getOneUserWhoHaveAConnectionWithACustomer(
    UserRepository $userRepo,int $id,int $userId)
    {

        $value = $this->cache->get('cache_user_with_a_customer', function (ItemInterface $item) use ($userRepo,$userId) {
            $item->expiresAfter(60);
            return $userRepo->findOneById($userId);
        });

        if($value->getCustomer()->getId() === $id){
            return $this->json($value,200,[],['groups' => ['customer:read']]);
        }
        return $this->json("Il n'existe pas de lien direct entre le client et l'utilisateur",403,[],['groups' => ['customer:read']]);
    }
}
