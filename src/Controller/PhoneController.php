<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use OpenApi\Annotations as OA;

/**
 * Class PhoneController
 * @package App\Controller
 * @Route("/api")
 */
class PhoneController extends AbstractController
{

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("/phones", name="all_phones",methods={"GET"})
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
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
     *
     */
    public function getAll(PhoneRepository $repo)
    {
        $value = $this->cache->get('cache_all_phone', function (ItemInterface $item) use ($repo) {
            $item->expiresAfter(10);
            return $repo->findAll();
        });

        return $this->json($value,200,[],['groups' => 'phone:read']);
    }

    /**
    * @Route("/phones/{id}", name="onePhone",methods={"GET"})
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
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
    */
    public function getOnePhone(PhoneRepository $repo,int $id)
    {
        $value = $this->cache->get('cache_one_phone', function (ItemInterface $item) use ($repo,$id) {
            $item->expiresAfter(10);
            return $repo->findOneById($id);
        });
        if(isset($value)) {
            return $this->json($value,200,[],['groups' => 'phone:read']);
        }
        return new JsonResponse("the phone with id $id does not exist",422);
    }

    /**
     * @Route("/phones", name="insert_phone",methods={"POST"})
     * @OA\Parameter(
     *   name="Phone",
     *   description="Fields to provide to create a phone",
     *   in="query",
     *   required=true,
     *   @OA\Schema(
     *     type="object",
     *     title="Phone field",
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="price", type="integer"),
     *     @OA\Property(property="color", type="string"),
     *     @OA\Property(property="description", type="string")
     *     )
     * )
     * @OA\Response(
     *      response=201,
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
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
    */
    public function insertOnePhone(Request $request,EntityManagerInterface $em,ValidatorInterface $validator)
    {
        $phone = new Phone;
        $phone->setName($request->get('name'));
        $phone->setPrice($request->get('price'));
        $phone->setColor($request->get('color'));
        $phone->setDescription($request->get('description'));
        $phone->setCreatedAt(new \DateTime());
        $errors = $validator->validate($phone);

        if (count($errors) > 0) {
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse($messages);
        }
        $em->persist($phone);

        $em->flush();

        $this->cache->delete('cache_one_phone');
        $this->cache->delete('cache_all_phone');

        return $this->json($phone,200,[],['groups' => 'phone:read']);
    }
}
