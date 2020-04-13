<?php
use \PHPUnit\Framework\TestCase;

class Usuariosmodeltest extends TestCase
{
    private $http;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/registrJornadaBackend/']);
    }






    
    public function tearDown() {
        $this->http = null;
    }
}