<?php

namespace App\Tests;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Tests\DataTraits\TruncateDB;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhoneTest extends WebTestCase
{
    use TruncateDb;

    public function setup(): void
    {
        $this->client = static::createClient();

        $this->truncateEntities([
            Customer::class,
            User::class,
            Phone::class,
        ]);

        $this->client->request('POST', "/auth/register?email=admin@admin.com&password=admin");

        $this->client->request('POST', "/auth/login?email=admin@admin.com&password=admin");

        $bearer = json_decode($this->client->getResponse()->getContent())->token;

        $this->headers = array(
            'HTTP_AUTHORIZATION' => $bearer,
            'CONTENT_TYPE' => 'application/json',
        );
    }

    public function testShowAllPhones()
    {
        $this->client->request('GET', '/api/phones',array(),array(),$this->headers);

        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());
    }

    public function testShowOnePhone()
    {
        $this->client->request('POST', '/api/phones',['name'=> 'Iphone XR','price' => 1250,
        'color' => 'Red','description' => 'Best Phone'],[],$this->headers);

        $phone = (array)json_decode( $this->client->getResponse()->getContent());

        $phone['_links']['allPhones'] = "/api/phones";

        $this->client->request('GET', '/api/phones/'.$phone['id'],[],[],$this->headers);

        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());

        $response = (array)json_decode( $this->client->getResponse()->getContent());

        foreach(array_keys($response) as $value){
            $this->assertArrayHasKey($value,$phone);
        }
    }

    public function testInsertOnePhone()
    {
        $this->client->request('POST', '/api/phones',['name'=> 'Iphone XR','price' => 1250,
        'color' => 'Red','description' => 'Best Phone'],[],$this->headers);

        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());
    }
}