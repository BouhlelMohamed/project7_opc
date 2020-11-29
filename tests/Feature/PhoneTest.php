<?php

namespace App\Tests\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhoneTest extends WebTestCase
{
    public function setup(): void
    {
        $this->client = static::createClient();

        $this->client->request('POST', "/auth/register?email=admin@admin.com&password=admin", ['email' => 'admin@admin.com', 'password' => 'admin']);

        $customerRepository = static::$container->get(CustomerRepository::class);

        $this->loginCustomer = $customerRepository->findOneByEmail('admin@admin.com');

        $this->client->loginUser($this->loginCustomer);
    }

    public function testShowAllPhones()
    {
        $this->client->request('GET', '/api/phone');

        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());
    }

    public function testShowOnePhone()
    {
        $this->client->request('POST', '/api/phone',['name'=> 'Iphone XR','price' => 1250,
        'color' => 'Red','description' => 'Best Phone']);

        $phone = (array)json_decode( $this->client->getResponse()->getContent());

        $this->client->request('GET', '/api/phone/'.$phone['id']);

        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());

        $response = (array)json_decode( $this->client->getResponse()->getContent());

        foreach(array_keys($response) as $value){
            $this->assertArrayHasKey($value,$phone);
        }
    }

    public function testInsertOnePhone()
    {
        $this->client->request('POST', '/api/phone',['name'=> 'Iphone XR','price' => 1250,
        'color' => 'Red','description' => 'Best Phone']);

        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());
    }
}