<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Marcado
 *
 * @author jcpm0
 */
class Marcado {
    
    protected $idMarcado;
    protected $fecha;
    protected $hora_inicio;
    protected $hora_final;
    protected $longitud;
    protected $latitud;
    protected $idTipo_Marcaje;
    protected $idEMPLEADO;
    
    
    function __construct() {

    }
    function getIdMarcado() {
        return $this->idMarcado;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getHora_inicio() {
        return $this->hora_inicio;
    }

    function getHora_final() {
        return $this->hora_final;
    }

    function getLongitud() {
        return $this->longitud;
    }

    function getLatitud() {
        return $this->latitud;
    }

    function getIdTipo_Marcaje() {
        return $this->idTipo_Marcaje;
    }

    function getIdEMPLEADO() {
        return $this->idEMPLEADO;
    }

    function setIdMarcado($idMarcado): void {
        $this->idMarcado = $idMarcado;
    }

    function setFecha($fecha): void {
        $this->fecha = $fecha;
    }

    function setHora_inicio($hora_inicio): void {
        $this->hora_inicio = $hora_inicio;
    }

    function setHora_final($hora_final): void {
        $this->hora_final = $hora_final;
    }

    function setLongitud($longitud): void {
        $this->longitud = $longitud;
    }

    function setLatitud($latitud): void {
        $this->latitud = $latitud;
    }

    function setIdTipo_Marcaje($idTipo_Marcaje): void {
        $this->idTipo_Marcaje = $idTipo_Marcaje;
    }

    function setIdEMPLEADO($idEMPLEADO): void {
        $this->idEMPLEADO = $idEMPLEADO;
    }


}
