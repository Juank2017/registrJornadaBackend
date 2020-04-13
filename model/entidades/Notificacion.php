<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Notificacion
 *
 * @author jcpm0
 */
class Notificacion {
    protected $idNotificacion;
    protected $fecha;
    protected $texto_notificacion;
    protected $texto_respuesta;
    protected $leida;
    protected $idEMPLEADO;
    
    function __construct() {
 
    }
    function getIdNotificacion() {
        return $this->idNotificacion;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getTexto_notificacion() {
        return $this->texto_notificacion;
    }

    function getTexto_respuesta() {
        return $this->texto_respuesta;
    }

    function getLeida() {
        return $this->leida;
    }

    function getIdEMPLEADO() {
        return $this->idEMPLEADO;
    }

    function setIdNotificacion($idNotificacion): void {
        $this->idNotificacion = $idNotificacion;
    }

    function setFecha($fecha): void {
        $this->fecha = $fecha;
    }

    function setTexto_notificacion($texto_notificacion): void {
        $this->texto_notificacion = $texto_notificacion;
    }

    function setTexto_respuesta($texto_respuesta): void {
        $this->texto_respuesta = $texto_respuesta;
    }

    function setLeida($leida): void {
        $this->leida = $leida;
    }

    function setIdEMPLEADO($idEMPLEADO): void {
        $this->idEMPLEADO = $idEMPLEADO;
    }


}
