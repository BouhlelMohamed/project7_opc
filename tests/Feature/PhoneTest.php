<?php

namespace App\Tests\Controller;

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

        $client->request('GET', '/api/phone/111');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}