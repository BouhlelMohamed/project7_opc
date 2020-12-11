<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\DataTraits\TruncateDB;
use App\Entity\Customer;
use App\Entity\Phone;
use App\Entity\User;

class AuthTest extends WebTestCase
{
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

    }

    public function testRegister()
    {
        $this->client->request('POST', "/auth/register?email=admin@admin.com&password=admin");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLogin()
    {
        $this->client->request('POST', "/auth/login?email=admin@admin.com&password=admin");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}