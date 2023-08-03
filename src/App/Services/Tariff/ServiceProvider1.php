<?php
namespace App\Services\Tariff;


class ServiceProvider1 {

    protected $url = "http:localhost/getAll";
    protected $client;
    
    public function __construct(\GuzzleHttp\Client $client) {
        $this->client = $client;
    }

    public function getTariffs() {
        try {
            $response = $this->client->get($this->url);
            return json_decode($response->getBody(), true);
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
           echo 'error! ' . $e->getMessage();
        }
    }

}