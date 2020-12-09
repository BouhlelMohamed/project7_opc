<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use \Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AuthController
 * @package App\Controller
 * @Route("/auth")
 */
class AuthController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder,ValidatorInterface $validator)
    {
        $password = $request->get('password');
        $email = $request->get('email');
        $customer = new Customer();
        $customer->setPassword($encoder->encodePassword($customer, $password));
        $customer->setEmail($email);
        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse($messages);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();
        return $this->json([
            'user' => $customer->getEmail()
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, CustomerRepository $customerRepo, UserPasswordEncoderInterface $encoder)
    {
        $customer = $customerRepo->findOneBy([
                'email'=>$request->get('email'),
        ]);
        if (!$customer || !$encoder->isPasswordValid($customer, $request->get('password'))) {
                return $this->json([
                    'message' => 'email or password is wrong.',
                ]);
        }
        $payload = [
            "customer" => $customer->getUsername(),
            "exp"  => (new \DateTime())->modify("+1 day")->getTimestamp(),
        ];


        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
        return $this->json([
                'message' => 'success!',
                'token' => sprintf('Bearer %s', $jwt),
            ]);
    }

}
