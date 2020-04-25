<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'entidades/Sede.php';
require 'empresasmodel.php';
require 'usuariosmodel.php';
/**
 * Description of sedemodel
 *
 * @author jcpm0
 */
class sedesmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Obtiene la lita de sedes de la base de datos
     */
    function getSedes($pagina=0)
    {
        $registrosPorPagina=constant('REG_POR_PAGINA');
        $registroInicial=($pagina>1)? (($pagina * $registrosPorPagina)- $registrosPorPagina) :0;

        try {
            $query = $this->db->connect()->prepare('SELECT * FROM sede LIMIT :registroInicial,:registrosPorPagina ');

            $query->execute(['registroInicial'=>$registroInicial,'registrosPorPagina'=>$registrosPorPagina]);
            $sedes = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               // var_dump($row);
                $sede= new Sede();

                $sede->setIdSede($row['idSEDE']);
                $sede->setNombre($row['nombre']);
                $sede->setLongitud($row['longitud']);
                $sede->setLatitud($row['latitud']);
                $sede->setDireccion($row['direccion']);
              
                
                $empresas = new empresasmodel;
                $empresa= $empresas->getEmpresaById($row['idEMPRESA']);
 
               
                $sede->setIdEMPRESA($empresa);
               
                array_push($sedes, $sede);
            }
            $totalRegistros=$this->db->connect()->query("SELECT COUNT(*) as total from sede")->fetch()['total'];
            $totalPaginas = ceil($totalRegistros/$registrosPorPagina);
            
            $salida=  array('paginacion'=>array('registros'=>$totalRegistros,'paginas'=>$totalPaginas),'sedes'=>$sedes);
            return $salida;
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }
/**
     * Obtiene la lita de sedes de una empresa dada
     */
    function getSedesByEmpresaId($idEmpresa)
    {
       

        try {
            $query = $this->db->connect()->prepare('SELECT * FROM sede WHERE idEMPRESA = :idEmpresa ');

            $query->execute(['idEmpresa'=>$idEmpresa]);
            $sedes = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               // var_dump($row);
                $sede= new Sede();

                $sede->setIdSede($row['idSEDE']);
                $sede->setNombre($row['nombre']);
                $sede->setLongitud($row['longitud']);
                $sede->setLatitud($row['latitud']);
                $sede->setDireccion($row['direccion']);
              
                
                $empresas = new empresasmodel;
                $empresa= $empresas->getEmpresaById($row['idEMPRESA']);
 
               
                $sede->setIdEMPRESA($empresa);
               
                array_push($sedes, $sede);
            }
            
            
           
            return $sedes;
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }
    /**
     * Obtiene un sede por su id
     */
    function getSedeById($id)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT * FROM sede WHERE idSEDE = :idSede');
            $query->execute(['idSede' => $id]);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            
                $sede= new Sede();

                $sede->setIdSede($row['idSEDE']);
                $sede->setNombre($row['nombre']);
                $sede->setLongitud($row['longitud']);
                $sede->setLatitud($row['latitud']);
                $sede->setDireccion($row['direccion']);
              
                
                $empresas = new empresasmodel;
                $empresa= $empresas->getEmpresaById($row['idEMPRESA']);
 
               
                $sede->setIdEMPRESA($empresa);
            }
            return $sede;
        } catch (PDOException $e) {
            throw $e;
        }
    }
   
    /**
     * Inserta u sede en la bbdd
     */
    function createSede($sede)
    {
        // var_dump( $sede);
        try {
            $query = $this->db->connect()->prepare("INSERT INTO sede( nombre,longitud,latitud,direccion,idEMPRESA) VALUES (:nombre, :longitud, :latitud, :direccion, :idEMPRESA) ");

            if ($query->execute(['nombre' => $sede->getNombre(),
                                 'latitud' => $sede->getLatitud(),
                                 'longitud' => $sede->getLongitud(),
                                 'direccion' => $sede->getDireccion(),
                                 'idEMPRESA' => $sede->getIdEmpresa()]))
                                  {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas sede_rol, sede_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idSEDE) AS id FROM sede");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
               
                return $this->getSedeById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Elimina un sede de la BBDD
     */
    function deleteSede($id)
    {
        try {
            $query = $this->db->connect()->prepare("DELETE FROM sede WHERE idSEDE = :idSede ");
            $query->execute(["idSede" => $id]);
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
     * Actualiza un sede en la bbdd
     */
    function updateSede($sede)
    {
       
        try{
            //actualiza el sede
            $query= $this->db->connect()->prepare("UPDATE sede SET nombre = :nombre, longitud = :longitud, latitud = :latitud, direccion = :direccion, idEMPRESA = :idEmpresa  WHERE idSEDE = :idSede");
            $query->execute(['idSede'=> $sede->getIdSede(),
                             'nombre'=>$sede->getNombre(),                             
                             'longitud' =>$sede->getLongitud(),
                             'latitud' =>$sede->getLatitud(),
                             'direccion' =>$sede->getDireccion(),
                             'idEmpresa' =>$sede->getIdEmpresa()]);

            
            return $this->getSedeById($sede->getIdSede());
        }catch(PDOException $e){
            return $e;
        }
    }
}
