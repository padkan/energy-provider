<?php
namespace tests\Services\Tariff;

use PHPUnit\Framework\TestCase;
use App\Services\Tariff\ServiceProvider1;
use tests\Services\Helpers\TariffHelper;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class ServiceProvider1Test extends TestCase {

    public function setUp(): void {
    }

    public function testGetTarffsSuccess() {

        //mocking
        $mock = new MockHandler([
            new Response(200, [], TariffHelper::dataProviderForGetTariffs()),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        $serviceProvider1 = new ServiceProvider1($client);
        $response = $serviceProvider1->getTariffs();
        //asssert
        $this->assertIsArray($response);
        $this->assertEquals('Product A', $response[0]['name']);
        $this->assertEquals('Product B', $response[1]['name']);

    }
     
}