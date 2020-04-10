<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Empleado
 *
 * @author jcpm0
 */
class Empleado {
    //put your code here
    
    protected $idEmpleado;
    protected $nombre;
    protected $apellidos;
    protected $dni;
    protected $idUsuario;
    protected $idTurno;
    protected $idSede;
    
    function __construct($idEmpleado, $nombre, $apellidos, $dni, $idUsuario, $idTurno, $idSede) {
        $this->idEmpleado = $idEmpleado;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->dni = $dni;
        $this->idUsuario = $idUsuario;
        $this->idTurno = $idTurno;
        $this->idSede = $idSede;
    }

    function getIdEmpleado() {
        return $this->idEmpleado;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getApellidos() {
        return $this->apellidos;
    }

    function getDni() {
        return $this->dni;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getIdTurno() {
        return $this->idTurno;
    }

    function getIdSede() {
        return $this->idSede;
    }

    function setIdEmpleado($idEmpleado): void {
        $this->idEmpleado = $idEmpleado;
    }

    function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    function setApellidos($apellidos): void {
        $this->apellidos = $apellidos;
    }

    function setDni($dni): void {
        $this->dni = $dni;
    }

    function setIdUsuario($idUsuario): void {
        $this->idUsuario = $idUsuario;
    }

    function setIdTurno($idTurno): void {
        $this->idTurno = $idTurno;
    }

    function setIdSede($idSede): void {
        $this->idSede = $idSede;
    }


}
