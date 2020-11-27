<?php

namespace App\Tests\Controller;

use App\Repository\CustomerRepository;
use App\Tests\DataTraits\CustomersData;
use App\Tests\DataTraits\UsersData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    use CustomersData;
    use UsersData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->customer = $this->addCustomers($this->entityManager);

        $this->user = $this->addUsers($this->entityManager,$this->customer);        
    }

    public function testAddANewUserLinkedToACustomer()
    {
        $this->client->request('POST', "/auth/register?email=admin@admin.com&password=admin", ['email' => 'admin@admin.com', 'password' => 'admin']);

        $userRepository = static::$container->get(CustomerRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@admin.com');

        $this->client->loginUser($testUser);

        $customerId = $this->customer->getId();

        $this->client->request('POST', "/api/users/customers/$customerId", ['username' => 'TestUsername', 'age' => 10, 'customerId' => $customerId]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response[] = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(1,$response);
    }

    public function testDeleteOneUser()
    {
        $customerId = $this->customer->getId();

        $userId = $this->user->getId();

        $this->client->request("DELETE", "/api/users/$userId/customers/$customerId");

        if($this->user->getCustomer()->getId() === $customerId){
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        }else {
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }
    }

    public function testCannotDeleteOneUser()
    {
        $customer2 = $this->addCustomers($this->entityManager);
        
        $customerId = $customer2->getId();

        $userId = $this->user->getId();

        $this->client->request("DELETE", "/api/users/$userId/customers/$customerId");

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}