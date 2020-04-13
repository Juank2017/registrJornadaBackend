<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Empresa
 *
 * @author jcpm0
 */
class Empresa {
    //put your code here
    protected $idEMPRESA;
    protected $nombre;
    protected $cif;
    
    function __construct() {
 
    }

    
    function getIdEmpresa() {
        return $this->idEmpresa;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getCif() {
        return $this->cif;
    }

    function setIdEmpresa($idEmpresa): void {
        $this->idEmpresa = $idEmpresa;
    }

    function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    function setCif($cif): void {
        $this->cif = $cif;
    }


}
