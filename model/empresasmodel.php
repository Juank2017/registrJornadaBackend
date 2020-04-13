<?php

class empresasmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene la lita de empresas de la base de datos
     */
    function getEmpresas()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM empresa ');

            $query->execute();
            $empresas=[];
            while ($row= $query->fetch(PDO::FETCH_ASSOC)){
                
                array_push($empresas,array("id"=>$row['idEMPRESA'],"nombre"=>$row['nombre'],"cif"=>$row['cif']));
            }
            return $empresas;

        }catch (PDOException $e) {
            echo($e);
            return $e;
        }

    }
    /**
     * Obitne una empresa por el id
     */
    function getEmpresaById($id){
        try {
            $query= $this->db->connect()->prepare("SELECT * FROM empresa WHERE idEMPRESA = :idEmpresa");
            $query->execute(["idEmpresa"=>$id]);

            while($row= $query->fetch(PDO::FETCH_ASSOC)){
                $empresa=array("id"=>$row['idEMPRESA'],"nombre"=>$row['nombre'],"cif"=>$row['cif']);
            }
            return $empresa;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Crea un rol en la bbdd
     */
    function createEmpresa($nombre,$cif){
        try {
            $query = $this->db->connect()->prepare("INSERT INTO empresa( nombre,cif) VALUES (:nombre, :cif) ");

            if ($query->execute(['nombre' => $nombre,"cif"=>$cif])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas usuario_rol, usuario_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idEMPRESA) AS id FROM empresa");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
                
                return $this->getEmpresaById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }

    /**
     * Borra una empresa de la bbdd
     */
    function deleteEmpresa($id){
        try {
            $query = $this->db->connect()->prepare("DELETE FROM empresa WHERE idEMPRESA = :idEmpresa ");
            $query->execute(["idEmpresa" => $id]);
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
    function updateEmpresa($id,$nombre,$cif)
    {
       
        try{
            //actualiza el usuario
            $query= $this->db->connect()->prepare("UPDATE empresa SET nombre = :nombre, cif = :cif  WHERE idEMPRESA = :idEmpresa");
            $query->execute(['idEmpresa'=>$id,'nombre'=> $nombre,'cif'=>$cif]);
           
            return $this->getEmpresaById($id);
        }catch(PDOException $e){
            return null;
        }
    }
}