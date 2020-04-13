<?php

class turnosmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene la lita de turnos de la base de datos
     */
    function getTurnos()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM turno ');

            $query->execute();
            $turnos=[];
            while ($row= $query->fetch(PDO::FETCH_ASSOC)){
                
                array_push($turnos,array("id"=>$row['idTURNO'],"turno"=>$row['turno']));
            }
            return $turnos;

        }catch (PDOException $e) {
            echo($e);
            return $e;
        }

    }
    /**
     * Obitne una turno por el id
     */
    function getTurnoById($id){
        try {
            $query= $this->db->connect()->prepare("SELECT * FROM turno WHERE idTURNO = :idTurno");
            $query->execute(["idTurno"=>$id]);

            while($row= $query->fetch(PDO::FETCH_ASSOC)){
                $turno=array("id"=>$row['idTURNO'],"turno"=>$row['turno']);
            }
            return $turno;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Crea un rol en la bbdd
     */
    function createTurno($turno){
        try {
            $query = $this->db->connect()->prepare("INSERT INTO turno( turno) VALUES (:turno) ");

            if ($query->execute(['turno' => $turno])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas usuario_rol, usuario_turno
                $queryID = $this->db->connect()->prepare("SELECT MAX(idTURNO) AS id FROM turno");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
                
                return $this->getTurnoById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }

    /**
     * Borra una turno de la bbdd
     */
    function deleteTurno($id){
        try {
            $query = $this->db->connect()->prepare("DELETE FROM turno WHERE idTURNO = :idTurno ");
            $query->execute(["idTurno" => $id]);
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
    function updateTurno($id,$turno)
    {
       
        try{
            //actualiza el usuario
            $query= $this->db->connect()->prepare("UPDATE turno SET turno = :turno  WHERE idTURNO = :idTurno");
            $query->execute(['idTurno'=>$id,'turno'=> $turno]);
           
            return $this->getTurnoById($id);
        }catch(PDOException $e){
            return null;
        }
    }
}