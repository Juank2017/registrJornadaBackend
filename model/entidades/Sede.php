<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sede
 *
 * @author jcpm0
 */
class Sede {
    protected $idSede;
    protected $nombre;
    protected $direccion;
    protected $longitud;
    protected $latitud;
    protected $idEMPRESA;
    
    function __construct($idSede, $nombre, $direccion, $longitud, $latitud, $idEMPRESA) {
        $this->idSede = $idSede;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->longitud = $longitud;
        $this->latitud = $latitud;
        $this->idEMPRESA = $idEMPRESA;
    }
    function getIdSede() {
        return $this->idSede;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getLongitud() {
        return $this->longitud;
    }

    function getLatitud() {
        return $this->latitud;
    }

    function getIdEMPRESA() {
        return $this->idEMPRESA;
    }

    function setIdSede($idSede): void {
        $this->idSede = $idSede;
    }

    function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    function setDireccion($direccion): void {
        $this->direccion = $direccion;
    }

    function setLongitud($longitud): void {
        $this->longitud = $longitud;
    }

    function setLatitud($latitud): void {
        $this->latitud = $latitud;
    }

    function setIdEMPRESA($idEMPRESA): void {
        $this->idEMPRESA = $idEMPRESA;
    }


}
