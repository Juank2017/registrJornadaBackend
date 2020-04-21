<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'entidades/Marcado.php';
require 'tipo_marcajesmodel.php';
require 'empleadosmodel.php';
/**
 * Description of marcadomodel
 *
 * @author jcpm0
 */
class marcadosmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Obtiene la lita de marcados de la base de datos
     */
    function getMarcados($pagina=0)
    {
        $registrosPorPagina=constant('REG_POR_PAGINA');
        $registroInicial=($pagina>1)? (($pagina * $registrosPorPagina)- $registrosPorPagina) :0;

        try {
            $query = $this->db->connect()->prepare('SELECT * FROM marcado LIMIT :registroInicial,:registrosPorPagina');

            $query->execute(['registroInicial'=>$registroInicial,'registrosPorPagina'=>$registrosPorPagina]);
            $marcados = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               // var_dump($row);
                $marcado= new Marcado();

                $marcado->setIdMarcado($row['idMarcado']);
                $marcado->setFecha($row['fecha']);
                $marcado->setLongitud($row['longitud']);
                $marcado->setLatitud($row['latitud']);
                $marcado->setHora_inicio($row['hora_inicio']);
                $marcado->setHora_final($row['hora_final']);
                
               
              
                $tipos_marcaje = new tipo_marcajesmodel;
                $tipo_marcaje= $tipos_marcaje->getTipo_marcajeById($row['idTIPO_MARCAJE']);
                $empleados = new empleadosmodel;
                $empleado= $empleados->getEmpleadoById($row['idEMPLEADO']);
 
                $marcado->setIdTipo_Marcaje($tipo_marcaje);
                $marcado->setIdEMPLEADO($empleado);
               
                array_push($marcados, $marcado);
            }
            $totalRegistros=$this->db->connect()->query("SELECT COUNT(*) as total from marcado")->fetch()['total'];
            $totalPaginas = ceil($totalRegistros/$registrosPorPagina);
            $salida=  array('paginacion'=>array('total registros'=>$totalRegistros,'paginas'=>$totalPaginas),'marcados'=>$marcados);
            return $salida;
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Obtiene un marcado por su id
     */
    function getMarcadoById($id)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT * FROM marcado WHERE idMARCADO = :idMarcado');
            $query->execute(['idMarcado' => $id]);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            
                $marcado= new Marcado();

                $marcado->setIdMarcado($row['idMarcado']);
                $marcado->setFecha($row['fecha']);
                $marcado->setLongitud($row['longitud']);
                $marcado->setLatitud($row['latitud']);
                $marcado->setHora_inicio($row['hora_inicio']);
                $marcado->setHora_final($row['hora_final']);
                
               
              
                $tipos_marcaje = new tipo_marcajesmodel;
                $tipo_marcaje= $tipos_marcaje->getTipo_marcajeById($row['idTIPO_MARCAJE']);
                $empleados = new empleadosmodel;
                $empleado= $empleados->getEmpleadoById($row['idEMPLEADO']);
 
                $marcado->setIdTipo_Marcaje($tipo_marcaje);
                $marcado->setIdEMPLEADO($empleado);
               
            }
            return $marcado;
        } catch (PDOException $e) {
            return null;
        }
    }
   
    /**
     * Inserta u marcado en la bbdd
     */
    function createMarcado($marcado)
    {
        // var_dump( $marcado);
        try {
            $query = $this->db->connect()->prepare("INSERT INTO marcado( nombre, longitud,  latitud, hora_inicio,hora_final,idTIPO_MARCAJE,idEMPLEADO) VALUES 
                                                                       (:nombre, :longitud, :latitud, :hora_inicio, :hora_final, :idTIPO_MARCADO, :idEMPLEADO) ");

            if ($query->execute(['nombre' => $marcado->getNombre(),
                                 'latitud' => $marcado->getLatitud(),
                                 'longitud' => $marcado->getLongitud(),
                                 'hora_inicio' => $marcado->getHora_inicio(),
                                 'hora_final' => $marcado->getHora_final(),
                                 'idTIPO_MARCADO'=>$marcado->getIdTipo_marcaje(),
                                 'idEMPLEADO'=>$marcado->getIdEMPLEADO()]))
                                  {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas marcado_rol, marcado_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idMARCADO) AS id FROM marcado");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
              
               
                return $this->getMarcadoById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo($e);
            return $e;
        }
    }

    /**
     * Elimina un marcado de la BBDD
     */
    function deleteMarcado($id)
    {
        try {
            $query = $this->db->connect()->prepare("DELETE FROM marcado WHERE idMARCADO = :idMarcado ");
            $query->execute(["idMarcado" => $id]);
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
     * Actualiza un marcado en la bbdd
     */
    function updateMarcado($marcado)
    {
       
        try{
            //actualiza el marcado
            $query= $this->db->connect()->prepare("UPDATE marcado SET nombre = :nombre, longitud = :longitud, latitud = :latitud, hora_inicio = :hora_inicio, hora_final = :hora_final,idTIPO_MARCAJE = :idTIPO_MARCADO  WHERE idMARCADO = :idMarcado");
            $query->execute(['idMarcado'=> $marcado->getIdMarcado(),
                             'nombre'=>$marcado->getNombre(),                             
                             'longitud' =>$marcado->getLongitud(),
                             'latitud' =>$marcado->getLatitud(),
                             'hora_inicio' => $marcado->getHora_inicio(),
                                 'hora_final' => $marcado->getHora_final(),
                                 'idTIPO_MARCADO'=>$marcado->getIdTipo_marcaje(),
                                 'idEMPLEADO'=>$marcado->getIdEMPLEADO()]);

            
            return $this->getMarcadoById($marcado->getIdMarcado());
        }catch(PDOException $e){
            return $e;
        }
    }
}