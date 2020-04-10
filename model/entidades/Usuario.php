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
    protected $roles;
    protected $empresas;
    function getEmpresas() {
        return $this->empresas;
    }

    function setEmpresas($empresas): void {
        $this->empresas = $empresas;
    }

        function getRoles() {
        return $this->roles;
    }

    function setRoles($roles): void {
        $this->roles = $roles;
    }

        function __construct() {
   
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
