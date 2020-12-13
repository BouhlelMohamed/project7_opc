<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use OpenApi\Annotations as OA;
use App\Services\Hateoas as Hateoas;

/**
 * Class PhoneController
 * @package App\Controller
 * @Route("/api")
 */
class PhoneController extends AbstractController
{

    const EXPIRES_AFTER = 3600;

    public function __construct(CacheInterface $cache,Hateoas $hateoas)
    {
        $this->cache = $cache;
        $this->hateoas = $hateoas;
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
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
     *
     */
    public function getAll(Request $request,PhoneRepository $repo,SerializerInterface $serializer)
    {
        $page = $request->query->get('page');

        $value = $this->cache->get('cache_all_phone', function (ItemInterface $item) use ($repo,$page) {
            $limit = 10;

            $item->expiresAfter(self::EXPIRES_AFTER);
            return $repo->findAllPhones($page,$limit);
        });
        $value = $serializer->serialize($value,"json",
            ["groups" => "list_phone"]);

        return new JsonResponse($this->hateoas->getHateoasAllPhones($value)
            , JsonResponse::HTTP_OK,
            [],
            true
        );
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
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
    */
    public function getOnePhone(PhoneRepository $repo,int $id,SerializerInterface $serializer)
    {
        $value = $this->cache->get('cache_one_phone_'.$id, function (ItemInterface $item) use ($repo,$id) {
            $item->expiresAfter(self::EXPIRES_AFTER);
            return $repo->findOneById($id);
        });
        if(isset($value)) {
            $value = $serializer->serialize($value,"json",
                ["groups" => "show_phone"]);

            return new JsonResponse($this->hateoas->getHateoasOnePhone($value)
                , JsonResponse::HTTP_OK,
                [],
                true
            );
        }
        return new JsonResponse("the phone with id $id does not exist",JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
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

        return $this->json($phone,JsonResponse::HTTP_OK,[],['groups' => 'show_phone']);
    }
}
