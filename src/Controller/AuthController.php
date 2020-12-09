<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use \Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{

    /**
     * @Route("/auth/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $password = $request->get('password');
        $email = $request->get('email');
        $customer = new Customer();
        $customer->setPassword($encoder->encodePassword($customer, $password));
        $customer->setEmail($email);
        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();
        return $this->json([
            'user' => $customer->getEmail()
        ]);
    }

    /**
     * @Route("/auth/login", name="login", methods={"POST"})
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
            "exp"  => (new \DateTime())->modify("+50 day")->getTimestamp(),
        ];


        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
        return $this->json([
                'message' => 'success!',
                'token' => sprintf('Bearer %s', $jwt),
            ]);
    }

}
