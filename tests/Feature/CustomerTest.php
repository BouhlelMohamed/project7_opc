<?php

namespace App\Tests\Controller;

use App\Tests\DataTraits\CustomersData;
use App\Tests\DataTraits\UsersData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerTest extends WebTestCase
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

        $this->userTest1 = $this->addUsers($this->entityManager,$this->customer,1);

        $this->user = $this->addUsers($this->entityManager,$this->customer,1);
    }

    public function testGetAllUsersWhoHaveAConnectionWithACustomer()
    {
        $customerId = $this->customer->getId();

        $this->client->request('GET', "/api/customers/$customerId/users");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(2,$response);
    }

    public function testGetOneUserWhoHaveAConnectionWithACustomer()
    {
        $customerId = $this->customer->getId();

        $userId = $this->userTest1->getId();

        $this->client->request('GET', "/api/customers/$customerId/users/$userId");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response[] = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(1,$response);

        $this->assertSame($userId,$response[0]['id']);
    }

}