<?php

class rolesmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene la lita de roles de la base de datos
     */
    function getRoles()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM rol ');

            $query->execute();
            $roles=[];
            while ($row= $query->fetch(PDO::FETCH_ASSOC)){
                
                array_push($roles,array("id"=>$row['idROL'],"rol"=>$row['ROL']));
            }
            return $roles;

        }catch (PDOException $e) {
            echo($e);
            return $e;
        }

    }
    /**
     * Obitne un rol por el id
     */
    function getRolById($id){
        try {
            $query= $this->db->connect()->prepare("SELECT * FROM rol WHERE idROL = :idRol");
            $query->execute(["idRol"=>$id]);

            while($row= $query->fetch(PDO::FETCH_ASSOC)){
                $rol=array("id"=>$row['idROL'],"rol"=>$row['ROL']);
            }
            return $rol;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Crea un rol en la bbdd
     */
    function createRol($rol){
        try {
            $query = $this->db->connect()->prepare("INSERT INTO rol( rol) VALUES (:rol) ");

            if ($query->execute(['rol' => $rol])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas usuario_rol, usuario_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idROL) AS id FROM rol");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
                
                return $this->getRolById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }

    /**
     * Borra un rol de la bbdd
     */
    function deleteRol($id){
        try {
            $query = $this->db->connect()->prepare("DELETE FROM rol WHERE idROL = :idRol ");
            $query->execute(["idRol" => $id]);
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
    function updateRol($rol,$id)
    {
       
        try{
            //actualiza el usuario
            $query= $this->db->connect()->prepare("UPDATE rol SET rol = :rol  WHERE idROL = :idRol");
            $query->execute(['idRol'=>$id,'rol'=> $rol]);
           
            return $this->getRolById($id);
        }catch(PDOException $e){
            return null;
        }
    }
}