<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'entidades/usuario.php';

/**
 * Description of usuariomodel
 *
 * @author jcpm0
 */
class usuariosmodel extends model
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Obtiene la lita de usuarios de la base de datos
     */
    function getUsuarios()
    {


        try {
            $query = $this->db->connect()->prepare('SELECT * FROM usuario ');

            $query->execute();
            $usuarios = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $user = new Usuario();
                $user->setIdUsuario($row['idUSUARIO']);
                $user->setLogin($row['login']);
                //obtengo roles
                $query1 = $this->db->connect()->prepare('SELECT rol.rol, rol.idROL, usuario_rol.idUSUARIO FROM rol LEFT JOIN usuario_rol ON usuario_rol.idROL = rol.idROL WHERE usuario_rol.idUSUARIO = :idUSUARIO');

                $query1->execute(['idUSUARIO' => $user->getIdUsuario()]);
                $roles = [];
                while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {

                    $rol = array("id" => $row['idROL'], "rol" => $row['rol']);
                    array_push($roles, $rol);
                }
                $user->setRoles($roles);
                //obtengo empresas
                $query2 = $this->db->connect()->prepare('SELECT empresa.idEMPRESA, empresa.nombre FROM empresa LEFT JOIN usuario_empresa ON usuario_empresa.idEmpresa = empresa.idEMPRESA WHERE usuario_empresa.idUSUARIO = :idUSUARIO');

                $query2->execute(['idUSUARIO' => $user->getIdUsuario()]);
                $empresas = [];
                while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $empresa = array("id" => $row['idEMPRESA'], "nombre" => $row['nombre']);
                    array_push($empresas, $empresa);
                }
                $user->setEmpresas($empresas);
                array_push($usuarios, $user);
            }
            return $usuarios;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtiene un usuario por su id
     */
    function getUserById($id)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT * FROM usuario WHERE idUSUARIO = :idUsuario');
            $query->execute(['idUsuario' => $id]);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $user = new Usuario();
                $user->setIdUsuario($row['idUSUARIO']);
                $user->setLogin($row['login']);
                //obtengo roles
                $query1 = $this->db->connect()->prepare('SELECT rol.rol, rol.idROL, usuario_rol.idUSUARIO FROM rol LEFT JOIN usuario_rol ON usuario_rol.idROL = rol.idROL WHERE usuario_rol.idUSUARIO = :idUSUARIO');

                $query1->execute(['idUSUARIO' => $user->getIdUsuario()]);
                $roles = [];
                while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
                    $rol = array("id" => $row['idROL'], "rol" => $row['rol']);
                    array_push($roles, $rol);
                }
                //obtengo empresas
                $query2 = $this->db->connect()->prepare('SELECT empresa.idEMPRESA, empresa.nombre FROM empresa LEFT JOIN usuario_empresa ON usuario_empresa.idEmpresa = empresa.idEMPRESA WHERE usuario_empresa.idUSUARIO = :idUSUARIO');

                $query2->execute(['idUSUARIO' => $user->getIdUsuario()]);
                $empresas = [];
                while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $empresa = array("id" => $row['idEMPRESA'], "nombre" => $row['nombre']);
                    array_push($empresas, $empresa);
                }
                $user->setEmpresas($empresas);
                $user->setRoles($roles);
            }
            return $user;
        } catch (PDOException $e) {
            return null;
        }
    }
    /**
     * busca un usuario por el login
     */
    function getUserByLogin($login)
    {
        try {
            $query = $this->db->connect()->prepare('SELECT * FROM usuario WHERE login = :login');
            $query->execute(['login' => $login]);
            $user = new Usuario();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
              
                $user->setIdUsuario($row['idUSUARIO']);
                $user->setLogin($row['login']);
                //obtengo roles
                $query1 = $this->db->connect()->prepare('SELECT rol.rol, rol.idROL, usuario_rol.idUSUARIO FROM rol LEFT JOIN usuario_rol ON usuario_rol.idROL = rol.idROL WHERE usuario_rol.idUSUARIO = :idUSUARIO');

                $query1->execute(['idUSUARIO' => $user->getIdUsuario()]);
                $roles = [];
                while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
                    $rol = array("id" => $row['idROL'], "rol" => $row['rol']);
                    array_push($roles, $rol);
                }
                //obtengo empresas
                $query2 = $this->db->connect()->prepare('SELECT empresa.idEMPRESA, empresa.nombre FROM empresa LEFT JOIN usuario_empresa ON usuario_empresa.idEmpresa = empresa.idEMPRESA WHERE usuario_empresa.idUSUARIO = :idUSUARIO');

                $query2->execute(['idUSUARIO' => $user->getIdUsuario()]);
                $empresas = [];
                while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $empresa = array("id" => $row['idEMPRESA'], "nombre" => $row['nombre']);
                    array_push($empresas, $empresa);
                }
                $user->setEmpresas($empresas);
                $user->setRoles($roles);
            }
            return $user;
        } catch (PDOException $e) {
            return null;
        }
    }
    /**
     * Inserta u usuario en la bbdd
     */
    function insertaUsuario($usuario)
    {
        // var_dump( $usuario);
        try {
            $query = $this->db->connect()->prepare("INSERT INTO usuario( login,password) VALUES (:login, :password) ");

            if ($query->execute(['login' => $usuario->getLogin(), 'password' => $usuario->getPassword()])) {
                //si se inserta correcatmente hay que averiguar el id que se le ha asignado para hacer la inserción en las
                //tablas usuario_rol, usuario_empresa
                $queryID = $this->db->connect()->prepare("SELECT MAX(idUSUARIO) AS id FROM usuario");
                $queryID->execute();
                $row = $queryID->fetch();
                $id = $row[0];
                echo $id;
                $roles = $usuario->getRoles();
                foreach ($roles as $key => $value) {
                    //preparamos consulta para insertar rol
                    $queryRol = $this->db->connect()->prepare("INSERT INTO usuario_rol( idUSUARIO,idROL) VALUES (:idusuario, :idrol)");
                    // var_dump( $value);
                    $idrol = $value['id'];
                    $insertadoRol = $queryRol->execute(["idusuario" => $id, "idrol" => $idrol]);
                }
                $empresas = $usuario->getEmpresas();
                foreach ($empresas as $key => $value) {
                    //preparamos consulta para insertar rol
                    $queryEmpresa = $this->db->connect()->prepare("INSERT INTO usuario_empresa( idUSUARIO,idEmpresa) VALUES (:idusuario, :idempresa)");
                    $idEmpresa = $value['id'];
                    $insertadoEmpresa = $queryEmpresa->execute(["idusuario" => $id, "idempresa" => $idEmpresa]);
                }
                return $this->getUserById($id);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }

/**
 * Elimina un usuario de la BBDD
 */
function eliminaUsuario($id){
    try {
        $query = $this->db->connect()->prepare("DELETE FROM usuario WHERE idUSUARIO = :idUsuario ");
        $query->execute(["idUsuario"=>$id]);
       if($query->rowCount() > 0 ){
 
            return 200;
       }else{
  
            return 400;
       }

    }catch(Exception $e){
return $e;
    }
}

/**
 * Actualiza un usuario en la bbdd
 */
function actualizar($usuario){
    
}
}
