<?php

namespace App\Tests\Controller;

use App\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhoneTest extends WebTestCase
{
    public function testShowAllPhones()
    {
        $client = static::createClient();

        $client->request('GET', '/api/phone');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowOnePhone()
    {
        $client = static::createClient();

        $client->request('POST', '/api/phone',['name'=> 'Iphone XR','price' => 1250,
        'color' => 'Red','description' => 'Best Phone']);

        $phone = (array)json_decode($client->getResponse()->getContent());

        $client->request('GET', '/api/phone/'.$phone['id']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $response = (array)json_decode($client->getResponse()->getContent());

        foreach(array_keys($response) as $value){
            $this->assertArrayHasKey($value,$phone);
        }
    }
}