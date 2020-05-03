<?php
use \PHPUnit\Framework\TestCase;

class Usuariosmodeltest extends TestCase
{

    public function setUp()
    {
        $this->stmMock = $this->getMockBuilder('PDOStatement')
            ->setMethods(['execute', 'fetch'])
            ->getMock();
        $this->stmMock->expects($this->any())->method('execute')
            ->will($this->returnValue(true));

        $this->pdoMock = $this->getMockBuilder('PDOMock')
            ->setMethods(['prepare'])
            ->getMock();
        $this->pdoMock->espects($this->any())
            ->method('prepare')
            ->will($this->returnValue($this->stmMock));

    }

public function testCreateUser(){
    $new_user = [ 'login' => 'eleuterio@test.com',
                       'password' =>'$2y$10$PYGAhBGjJPge0BLqcEdhZeTz8HWRjGmM7X0QB7qmyUSw9kjmqqaMe',
                       'idUSUARIO' => '',
                       'roles' =>[
                           'id' => '1',
                           'rol' => 'ADMIN_ROL'
                       ],
                       'empresas' =>[
                           'id' => '1',
                           'nombre' => '',
                           'cif' => ''
                       ]];
    $expected_user = ['id' => '74',
                       ];
}




    
    public function tearDown() {
        
    }
}