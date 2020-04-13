<?php

class horariosmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene la lita de horarios de la base de datos
     */
    function getHorarios()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM horario ');

            $query->execute();
            $horarios=[];
            while ($row= $query->fetch(PDO::FETCH_ASSOC)){
                
                array_push($horarios,array("id"=>$row['idHORARIO'],"hora_entrada"=>$row['hora_entrada'],"hora_salida"=>$row['hora_salida'],"idEmpleado"=>$row['idEMPLEADO']));
            }
            return $horarios;

        }catch (PDOException $e) {
            echo($e);
            return $e;
        }

    }
    /**
     * Obitne una horario por el id
     */
    function getHorarioById($id){
        try {
            $query= $this->db->connect()->prepare("SELECT * FROM horario WHERE idHORARIO = :idHorario");
            $query->execute(["idHorario"=>$id]);

            while($row= $query->fetch(PDO::FETCH_ASSOC)){
                $horario=array("id"=>$row['idHORARIO'],"hora_entrada"=>$row['hora_entrada'],"hora_salida"=>$row['hora_salida'],"idEmpleado"=>$row['idEMPLEADO']);
            }
            return $horario;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Crea un rol en la bbdd
     */
    function createHorario($hora_entrada,$hora_salida,$idEMPLEADO){
        try {
            $query = $this->db->connect()->prepare("INSERT INTO horario( hora_entrada,hora_salida,idEMPLEADO) VALUES (:horaentrada,:horasalida,:idEmpleado) ");

            if ($query->execute(['horaentrada' => $hora_entrada,'horasalida'=>$hora_salida,'idEmpleado'=>$idEMPLEADO])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas usuario_rol, usuario_horario
                $queryID = $this->db->connect()->prepare("SELECT MAX(idHORARIO) AS id FROM horario");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
                
                return $this->getHorarioById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }

    /**
     * Borra una horario de la bbdd
     */
    function deleteHorario($id){
        try {
            $query = $this->db->connect()->prepare("DELETE FROM horario WHERE idHORARIO = :idHorario ");
            $query->execute(["idHorario" => $id]);
            if ($query->rowCount() > 0) {

                return 200;
            } else {

                return 400;
            }
        } catch (Exception $e) {
            return $e;
        }
    }

     /**
     * Actualiza un usuario en la bbdd
     */
    function updateHorario($id,$hora_entrada,$hora_salida,$idEmpleado)
    {
       
        try{
            //actualiza el usuario
            $query= $this->db->connect()->prepare("UPDATE horario SET hora_entrada = :horaentrada,hora_salida = :horasalida , idEMPLEADO = :idEmpleado  WHERE idHORARIO = :idHorario");
            $query->execute(['idHorario'=>$id,'horasalida'=> $hora_salida,'horaentrada'=>$hora_entrada,'idEmpleado'=>$idEmpleado]);
           
            return $this->getHorarioById($id);
        }catch(PDOException $e){
            return null;
        }
    }
}