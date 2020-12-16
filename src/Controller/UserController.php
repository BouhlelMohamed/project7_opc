<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Cache\ItemInterface;
use App\Services\Hateoas as Hateoas;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/api")
 */
class UserController extends AbstractController
{

    const EXPIRES_AFTER = 3600;

    public function __construct(CacheInterface $cache,Hateoas $hateoas)
    {
        $this->cache = $cache;
        $this->hateoas = $hateoas;
    }

    /**
     * @Route("/users/customers/{id}", name="customers_users",methods={"GET"},requirements = {"id"="\d+"})
     * @OA\Parameter(
     *   name="Page",
     *   in="query",
     *   required=true,
     *   @OA\Schema(
     *     @OA\Property(property="page", type="number")
     *     )
     * )
     * @OA\Response(
     *      response=200,
     *      description="Success",
     * )
     * @OA\Response(
     *     response=400,
     *     description="BAD REQUEST"
     * )
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT Token not found | Expired JWT Token | Invalid JWT Token"
     * )
     * @OA\Response(
     *     response=403,
     *     description="ACCESS DENIED"
     * )
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function getAllUsersWhoHaveAConnectionWithACustomer(Request $request,UserRepository $repo,int $id,SerializerInterface $serializer)
    {
        $page = $request->query->get('page');

        $value = $this->cache->get('cache_all_users_with_a_customer_'.$page, function (ItemInterface $item) use ($repo,$id,$page) {
            $limit = 5;

            $item->expiresAfter(self::EXPIRES_AFTER);
            return $repo->findUsersByCustomersId($id,$page,$limit);
        });
        $value = $serializer->serialize($value,"json",
            ["groups" => "getUsers"]);

        return new JsonResponse($this->hateoas->getHateoasToAllUsers($value,$id)
        , JsonResponse::HTTP_OK,
        [],
        true
        );
    }

    /**
     * @Route("/users/{userId}/customers/{id}", name="customer_one_user",methods={"GET"})
     * @OA\Response(
     *      response=200,
     *      description="Success",
     * )
     * @OA\Response(
     *     response=400,
     *     description="BAD REQUEST"
     * )
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT Token not found | Expired JWT Token | Invalid JWT Token"
     * )
     * @OA\Response(
     *     response=403,
     *     description="ACCESS DENIED"
     * )
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function getOneUserWhoHaveAConnectionWithACustomer(
        UserRepository $userRepo,int $id,int $userId,SerializerInterface $serializer)
    {
        $value = $this->cache->get('cache_user_with_a_customer_'.$userId, function (ItemInterface $item) use ($userRepo,$userId) {
            $item->expiresAfter(self::EXPIRES_AFTER);
            return $userRepo->findOneById($userId);
        });

        if($value->getCustomer()->getId() === $id){

            $value = $serializer->serialize($value,"json",
                ["groups" => ["show_one_user"]]);

            return new JsonResponse($this->hateoas->getHateoasToOneUser($value)
                , JsonResponse::HTTP_OK,
                [],
                true
            );
        }

    }

    /**
     * @Route("/users/customers/{id}", name="add_user_for_customers",methods={"POST"})
     * @OA\Parameter(
     *   name="User",
     *   description="Add user",
     *   in="query",
     *   required=true,
     *   @OA\Schema(
     *     type="object",
     *     title="User field",
     *     @OA\Property(property="username", type="string"),
     *     @OA\Property(property="age", type="integer"),
     *     )
     * )
     * @OA\Response(
     *      response=200,
     *      description="Success",
     * )
     * @OA\Response(
     *     response=400,
     *     description="BAD REQUEST"
     * )
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT Token not found | Expired JWT Token | Invalid JWT Token"
     * )
     * @OA\Response(
     *     response=403,
     *     description="ACCESS DENIED"
     * )
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function addANewUserLinkedToACustomer(Request $request,EntityManagerInterface $em,int $id,CustomerRepository $customerRepo)
    {
        $customer = $customerRepo->findOneById($id);
        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setAge($request->get('age'));
        if($customer != null)
        {
            $user->setCustomer($customer);
            $em->persist($user);
            $em->flush();
            exec("php bin/console cache:clear");

            return $this->json($user,JsonResponse::HTTP_OK,[],["groups" => ["show_one_user","getCustomer"]]);
        }

        return $this->json(['message'=>'Customer not exist'],500);

    }

    /**
     * @Route("/users/{userId}/customers/{customerId}", name="delete_user",methods={"DELETE"})
     * @OA\Response(
     *      response=200,
     *      description="Success",
     * )
     * @OA\Response(
     *     response=400,
     *     description="BAD REQUEST"
     * )
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT Token not found | Expired JWT Token | Invalid JWT Token"
     * )
     * @OA\Response(
     *     response=403,
     *     description="ACCESS DENIED"
     * )
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function deleteUser(int $userId,int $customerId,UserRepository $userRepo,CustomerRepository $customerRepo,EntityManagerInterface $em)
    {
        $user = $userRepo->findOneById($userId);

        if($user->getCustomer()->getId() === $customerRepo->findOneById($customerId)->getId()){
            $em->remove($user);
            $em->flush();
            exec("php bin/console cache:clear");
            return $this->json('User '.$user->getUsername().' is deleted',200);
        }

        return $this->json(['message'=>'User '.$user->getUsername().' is not your user'],403);
    }
}
