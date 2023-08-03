<?php
namespace tests\Integrations\ElectricityManagementSystem\Handlers\Impl\ElectricityProvider1;

use PHPUnit\Framework\TestCase;
use App\Integrations\ElectricityManagementSystem\Handlers\Impl\ElectricityProvider1\ElectricityProvider1Handler;
use tests\Integrations\ElectricityManagementSystem\Handlers\Impl\ElectricityProvider1\Helpers\ElectricityProvider1Helper;
use App\Services\Tariff\ServiceProvider1;
use App\Models\Product;


class ElectricityProvider1Handlertest extends TestCase {
    
    public $electricityProvider1Handler;
    

    public function testGetTarffList() {
        $productMock = $this->getMockBuilder(Product::class)->setConstructorArgs([])->getMock();
        $serviceProviderMock = $this->getMockBuilder(ServiceProvider1::class)->setConstructorArgs([new \GuzzleHttp\Client()])->getMock();
        $serviceProviderMock->method('getTariffs')->will($this->returnValue(ElectricityProvider1Helper::dataProviderForElectricityProvider1())); 
        $electricityProvider1Handler = new ElectricityProvider1Handler($serviceProviderMock, $productMock);
        //asssert
        $this->assertIsArray($electricityProvider1Handler->getTariffList());
    }

    public function testGetAnnualPriceBaseType1() {
        $data =["name" => "Product A", "type" => 1, "baseCost" => 5, "additionalKwhCost" => 22];

        $productMock = new Product([]);
        $serviceProviderMock = $this->getMockBuilder(ServiceProvider1::class)->setConstructorArgs([new \GuzzleHttp\Client()])->getMock();
        $electricityProvider1Handler = new ElectricityProvider1Handler($serviceProviderMock, $productMock);

        $this->assertSame(830, $electricityProvider1Handler->getAnnualPriceBaseType($data, 3500));
    }

    public function testGetAnnualPriceBaseType2() {
        $data = [
            "name" => "Product B",
            "type" => 2,
            "includedKwh" => 4000,
            "baseCost" => 800,
            "additionalKwhCost" => 30
        ];

        $productMock = new Product([]);
        $serviceProviderMock = $this->getMockBuilder(ServiceProvider1::class)->setConstructorArgs([new \GuzzleHttp\Client()])->getMock();
        $electricityProvider1Handler = new ElectricityProvider1Handler($serviceProviderMock, $productMock);

        $this->assertSame(800, $electricityProvider1Handler->getAnnualPriceBaseType($data, 3500));
    }
}