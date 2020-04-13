<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'entidades/Notificacion.php';

require 'empleadosmodel.php';
/**
 * Description of notificacionmodel
 *
 * @author jcpm0
 */
class notificacionesmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Obtiene la lita de notificaciones de la base de datos
     */
    function getNotificaciones()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM notificacion ');

            $query->execute();
            $notificaciones = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               // var_dump($row);
                $notificacion= new Notificacion();

                $notificacion->setIdNotificacion($row['idNOTIFICACION']);
                $notificacion->setFecha($row['fecha']);
                $notificacion->setTexto_notificacion($row['texto_notificacion']);
                $notificacion->setTexto_respuesta($row['texto_respuesta']);
                $notificacion->setLeida($row['leida']);
                
                $empleados = new empleadosmodel;
                $empleado= $empleados->getEmpleadoById($row['idEMPLEADO']);
 
                
                $notificacion->setIdEMPLEADO($empleado);
               
                array_push($notificaciones, $notificacion);
            }
            return $notificaciones;
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Obtiene un notificacion por su id
     */
    function getNotificacionById($id)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT * FROM notificacion WHERE idNOTIFICACION = :idNotificacion');
            $query->execute(['idNotificacion' => $id]);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            
                $notificacion= new Notificacion();

                $notificacion->setIdNotificacion($row['idNOTIFICACION']);
                $notificacion->setFecha($row['fecha']);
                $notificacion->setTexto_notificacion($row['texto_notificacion']);
                $notificacion->setTexto_respuesta($row['texto_respuesta']);
                $notificacion->setLeida($row['leida']);
                
                $empleados = new empleadosmodel;
                $empleado= $empleados->getEmpleadoById($row['idEMPLEADO']);
 
                
                $notificacion->setIdEMPLEADO($empleado);
               
            }
            return $notificacion;
        } catch (PDOException $e) {
            return null;
        }
    }
   
    /**
     * Inserta u notificacion en la bbdd
     */
    function createNotificacion($notificacion)
    {
        // var_dump( $notificacion);
        try {
            $query = $this->db->connect()->prepare("INSERT INTO notificacion( fecha,  texto_notificacion,texto_respuesta,leida,idEMPLEADO) VALUES 
                                                                       ( :fecha, :texto_notificacion,:texto_respuesta,:leida, :idEMPLEADO) ");

            if ($query->execute(['fecha' => $notificacion->getFecha(),
                                 'texto_notificacion' => $notificacion->getTexto_notificacion(),
                                 'texto_respuesta' => $notificacion->getTexto_respuesta(),
                                 'leida' => $notificacion->getLeida(),
                                 'idEMPLEADO' => $notificacion->getidEMPLEADO()]))
                                  {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas notificacion_rol, notificacion_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idNOTIFICACION) AS id FROM notificacion");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
               
                return $this->getNotificacionById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Elimina un notificacion de la BBDD
     */
    function deleteNotificacion($id)
    {
        try {
            $query = $this->db->connect()->prepare("DELETE FROM notificacion WHERE idNOTIFICACION = :idNotificacion ");
            $query->execute(["idNotificacion" => $id]);
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
     * Actualiza un notificacion en la bbdd
     */
    function updateNotificacion($notificacion)
    {
       
        try{
            //actualiza el notificacion
            $query= $this->db->connect()->prepare("UPDATE notificacion SET fecha = :fecha, texto_notificacion = :texto_notificacion, texto_respuesta = :texto_respuesta, leida = :leida, idEMPLEADO = :idEMPLEADO  WHERE idNOTIFICACION = :idNotificacion");
            $query->execute(['fecha' => $notificacion->getFecha(),
                                 'texto_notificacion' => $notificacion->getTexto_notificacion(),
                                 'texto_respuesta' => $notificacion->getTexto_respuesta(),
                                 'leida' => $notificacion->getLeida(),
                                 'idEMPLEADO' => $notificacion->getidEMPLEADO(),
                                 'idNotificacion'=>$notificacion->getIdNOTIFICACION()]);

            
            return $this->getNotificacionById($notificacion->getIdNotificacion());
        }catch(PDOException $e){
            return $e;
        }
    }
}