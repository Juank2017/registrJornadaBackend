<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'entidades/Empleado.php';
require 'turnosmodel.php';
require 'usuariosmodel.php';
/**
 * Description of empleadomodel
 *
 * @author jcpm0
 */
class empleadosmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Obtiene la lita de empleados de la base de datos
     */
    function getEmpleados()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM empleado ');

            $query->execute();
            $empleados = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               // var_dump($row);
                $empleado= new Empleado();

                $empleado->setIdEmpleado($row['idEMPLEADO']);
                $empleado->setNombre($row['nombre']);
                $empleado->setApellidos($row['apellidos']);
                $empleado->setDni($row['dni']);
                
                $turnos = new turnosmodel;
                $turno= $turnos->getTurnoById($row['idTURNO']);
 
                $usuarios= new usuariosmodel;
                $usuario = $usuarios->getUserById($row['idUSUARIO']);
              //  var_dump($usuario);
                $empleado->setIdTurno($turno);
                $empleado->setIdUsuario($usuario);
                array_push($empleados, $empleado);
            }
            return $empleados;
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Obtiene un empleado por su id
     */
    function getEmpleadoById($id)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT * FROM empleado WHERE idEMPLEADO = :idEmpleado');
            $query->execute(['idEmpleado' => $id]);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            
                 $empleado= new Empleado();

                 $empleado->setIdEmpleado($row['idEMPLEADO']);
                 $empleado->setNombre($row['nombre']);
                 $empleado->setApellidos($row['apellidos']);
                 $empleado->setDni($row['dni']);
                 
                 $turnos = new turnosmodel;
                 $turno= $turnos->getTurnoById($row['idTURNO']);
  
                 $usuarios= new usuariosmodel;
                 $usuario = $usuarios->getUserById($row['idUSUARIO']);

         
                 $empleado->setIdTurno($turno);
                 $empleado->setIdUsuario($usuario);
            }
            return $empleado;
        } catch (PDOException $e) {
            return null;
        }
    }
    /**
     * busca un empleado por el login 
     */
    function getEmpleadoByUserLogin($login)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT usuario.idUSUARIO, usuario.login, empleado.*  FROM usuario LEFT JOIN empleado ON empleado.idUSUARIO = usuario.idUSUARIO WHERE login = :login');
            $query->execute(['login' => $login]);
            $empleado = null;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
              $empleado= array('usuario'=> array('idUsuario'=>$row['idUSUARIO'],
                                'login'=>$row['login']),
                                'empleado'=>array(
                                'idEmpleado'=> $row['idEMPLEADO'],
                                'nombre'=> $row['nombre'],
                                'apellidos'=>$row['apellidos'],
                                'dni'=>$row['dni'],
                                'idTurno'=>$row['idTURNO'],
                                'idSede'=>$row['idSEDE']));
            }
            return $empleado;
        } catch (PDOException $e) {
            return null;
        }
    }
    /**
     * Inserta u empleado en la bbdd
     */
    function createEmpleado($empleado)
    {
        // var_dump( $empleado);
        try {
            $query = $this->db->connect()->prepare("INSERT INTO empleado( nombre,apellidos,dni,idUSUARIO,idTURNO,idSEDE) VALUES (:nombre, :apellidos, :dni, :idUsuario, :idTurno, :idSede) ");

            if ($query->execute(['nombre' => $empleado->getNombre(),
                                 'apellidos' => $empleado->getApellidos(),
                                 'dni' => $empleado->getDni(),
                                 'idUsuario' => $empleado->getIdUsuario(),
                                 'idTurno' => $empleado->getIdTurno(),
                                 'idSede' => $empleado->getIdSede()])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas empleado_rol, empleado_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idEMPLEADO) AS id FROM empleado");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
               
                return $this->getEmpleadoById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Elimina un empleado de la BBDD
     */
    function deleteEmpleado($id)
    {
        try {
            $query = $this->db->connect()->prepare("DELETE FROM empleado WHERE idEMPLEADO = :idEmpleado ");
            $query->execute(["idEmpleado" => $id]);
            if ($query->rowCount() > 0) {

                return 200;
            } else {

                return 400;
            }
        } catch (Exception $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Actualiza un empleado en la bbdd
     */
    function updateEmpleado($empleado)
    {
       
        try{
            //actualiza el empleado
            $query= $this->db->connect()->prepare("UPDATE empleado SET nombre = :nombre, apellidos = :apellidos, dni = :dni, idTURNO = :idTurno, idSEDE = :idSede  WHERE idEMPLEADO = :idEmpleado");
            $query->execute(['idEmpleado'=> $empleado->getIdEmpleado(),
                             'nombre'=>$empleado->getNombre(),                             
                             'apellidos' =>$empleado->getEmpleados(),
                             'dni' =>$empleado->getDni(),
                             'idTurno' =>$empleado->getIdTurno(),
                             'idSede' =>$empleado->getIdSede()]);

            
            return $this->getEmpleadoById($empleado->getIdEmpleado());
        }catch(PDOException $e){
            return $e;
        }
    }
}
