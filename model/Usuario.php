<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuario
 *
 * @author jcpm0
 */
class Usuario {
    //put your code here
    protected $login;
    protected $password;
    protected $idUsuario;
    
    
    function __construct($login, $password, $idUsuario) {
        $this->login = $login;
        $this->password = $password;
        $this->idUsuario = $idUsuario;
    }

    
    function getLogin() {
        return $this->login;
    }

    function getPassword() {
        return $this->password;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function setLogin($login): void {
        $this->login = $login;
    }

    function setPassword($password): void {
        $this->password = $password;
    }

    function setIdUsuario($idUsuario): void {
        $this->idUsuario = $idUsuario;
    }


    
    
    
}
