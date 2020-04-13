<?php

class tipo_marcajesmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene la lita de tipo_marcajes de la base de datos
     */
    function getTipo_marcajes()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM tipo_marcaje ');

            $query->execute();
            $tipo_marcajes=[];
            while ($row= $query->fetch(PDO::FETCH_ASSOC)){
                
                array_push($tipo_marcajes,array("id"=>$row['idTIPO_MARCAJE'],"tipo_marcaje"=>$row['tipo']));
            }
            return $tipo_marcajes;

        }catch (PDOException $e) {
            echo($e);
            return $e;
        }

    }
    /**
     * Obitne una tipo_marcaje por el id
     */
    function getTipo_marcajeById($id){
        try {
            $query= $this->db->connect()->prepare("SELECT * FROM tipo_marcaje WHERE idTIPO_MARCAJE = :idTipo_marcaje");
            $query->execute(["idTipo_marcaje"=>$id]);

            while($row= $query->fetch(PDO::FETCH_ASSOC)){
                $tipo_marcaje=array("id"=>$row['idTIPO_MARCAJE'],"tipo_marcaje"=>$row['tipo']);
            }
            return $tipo_marcaje;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Crea un rol en la bbdd
     */
    function createTipo_marcaje($tipo_marcaje){
        try {
            $query = $this->db->connect()->prepare("INSERT INTO tipo_marcaje( tipo) VALUES (:tipo_marcaje) ");

            if ($query->execute(['tipo_marcaje' => $tipo_marcaje])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas usuario_rol, usuario_tipo_marcaje
                $queryID = $this->db->connect()->prepare("SELECT MAX(idTIPO_MARCAJE) AS id FROM tipo_marcaje");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
                
                return $this->getTipo_marcajeById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }

    /**
     * Borra una tipo_marcaje de la bbdd
     */
    function deleteTipo_marcaje($id){
        try {
            $query = $this->db->connect()->prepare("DELETE FROM tipo_marcaje WHERE idTIPO_MARCAJE = :idTipo_marcaje ");
            $query->execute(["idTipo_marcaje" => $id]);
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
    function updateTipo_marcaje($id,$tipo_marcaje)
    {
       
        try{
            //actualiza el usuario
            $query= $this->db->connect()->prepare("UPDATE tipo_marcaje SET tipo = :tipo_marcaje  WHERE idTIPO_MARCAJE = :idTipo_marcaje");
            $query->execute(['idTipo_marcaje'=>$id,'tipo_marcaje'=> $tipo_marcaje]);
           
            return $this->getTipo_marcajeById($id);
        }catch(PDOException $e){
            return null;
        }
    }
}