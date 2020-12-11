<?php

namespace App\Tests;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Tests\DataTraits\CustomersData;
use App\Tests\DataTraits\TruncateDB;
use App\Tests\DataTraits\UsersData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    use CustomersData;
    use UsersData;
    use TruncateDB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->truncateEntities([
            Customer::class,
            User::class,
            Phone::class,
        ]);

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->customer = $this->addCustomers($this->entityManager);

        $this->client->request('POST', "/auth/register?email=admin@admin.com&password=admin");

        $customerRepository = static::$container->get(CustomerRepository::class);

        $this->loginCustomer = $customerRepository->findOneByEmail('admin@admin.com');

        $this->client->loginUser($this->loginCustomer);

        $this->userTest1 = $this->addUsers($this->entityManager,$this->loginCustomer);

        $this->user = $this->addUsers($this->entityManager,$this->loginCustomer);
    }

    public function testGetAllUsersWhoHaveAConnectionWithACustomer()
    {
        $customerId = $this->loginCustomer->getId();

        $this->client->request('GET', "/api/users/customers/$customerId");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(2,$response);
    }

    public function testGetOneUserWhoHaveAConnectionWithACustomer()
    {
        $customerId = $this->loginCustomer->getId();

        $userId = $this->userTest1->getId();

        $this->client->request('GET', "/api/users/$userId/customers/$customerId");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response[] = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(1,$response);

        $this->assertSame($userId,$response[0]['id']);
    }

    public function testAddANewUserLinkedToACustomer()
    {
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