<?php

namespace App\Tests\Controller;

use App\Tests\DataTraits\CustomersData;
use App\Tests\DataTraits\UsersData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    use CustomersData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->customer = $this->addCustomers($this->entityManager);
    }

    public function testAddANewUserLinkedToACustomer()
    {
        $customerId = $this->customer->getId();

        $this->client->request('POST', "/api/users/customers/$customerId", ['username' => 'TestUsername', 'age' => 10, 'customerId' => $customerId]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response[] = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(1,$response);
    }
}