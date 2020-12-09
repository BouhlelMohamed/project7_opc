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

class CustomerTest extends WebTestCase
{
    use CustomersData;
    use UsersData;
    use TruncateDb;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->truncateEntities([
            Customer::class,
            User::class,
            Phone::class,
        ]);

        $this->client->request('POST', "/auth/register?email=admin@admin.com&password=admin");

        $userRepository = static::$container->get(CustomerRepository::class);

        $this->loginCustomer = $userRepository->findOneByEmail('admin@admin.com');

        $this->client->loginUser($this->loginCustomer);

        $this->userTest1 = $this->addUsers($this->entityManager,$this->loginCustomer);

        $this->user = $this->addUsers($this->entityManager,$this->loginCustomer);

    }

    public function testGetAllUsersWhoHaveAConnectionWithACustomer()
    {
        $customerId = $this->loginCustomer->getId();

        $this->client->request('GET', "/api/customers/$customerId/users");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(2,$response);
    }

    public function testGetOneUserWhoHaveAConnectionWithACustomer()
    {
        $customerId = $this->loginCustomer->getId();

        $userId = $this->userTest1->getId();

        $this->client->request('GET', "/api/customers/$customerId/users/$userId");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response[] = (array)json_decode($this->client->getResponse()->getContent());

        $this->assertCount(1,$response);

        $this->assertSame($userId,$response[0]['id']);
    }

}