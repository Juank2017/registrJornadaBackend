<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'entidades/usuario.php';
require 'entidades/Empresa.php';

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
    function getUsuarios($pagina=0)
    {
        $registrosPorPagina=constant('REG_POR_PAGINA');
        $registroInicial=($pagina>1)? (($pagina * $registrosPorPagina)- $registrosPorPagina) :0;

        try {
            $query = $this->db->connect()->prepare('SELECT * FROM usuario LIMIT :registroInicial,:registrosPorPagina ');

            $query->execute(['registroInicial'=>$registroInicial,'registrosPorPagina'=>$registrosPorPagina]);
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
             
                 $empresas =[];
                 // $query2->fetchAll(PDO::FETCH_CLASS, 'Empresa');
                while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $empresa = array("id" => $row['idEMPRESA'], "nombre" => $row['nombre']);
                    array_push($empresas, $empresa);
                }

                $user->setEmpresas($empresas);
               // print_r($user->getEmpresas());
                array_push($usuarios, $user);
            }
            $totalRegistros=$this->db->connect()->query("SELECT COUNT(*) as total from usuario")->fetch()['total'];
            $totalPaginas = ceil($totalRegistros/$registrosPorPagina);
            
            $salida=  array('paginacion'=>array('total registros'=>$totalRegistros,'paginas'=>$totalPaginas),'usuarios'=>$usuarios);
            return $salida;
        } catch (PDOException $e) {
            echo($e);
            return $e;
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
            $user=null;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $user = new Usuario();
                $user->setIdUsuario($row['idUSUARIO']);
                $user->setLogin($row['login']);
                $user->setPassword($row['password']);
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
            echo($e);
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
            $user = null;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               $user= new Usuario();

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
    function createUser($usuario)
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
           throw $e;
        }
    }

    /**
     * Elimina un usuario de la BBDD
     */
    function deleteUser($id)
    {
        try {
            $query = $this->db->connect()->prepare("DELETE FROM usuario WHERE idUSUARIO = :idUsuario ");
            $query->execute(["idUsuario" => $id]);
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
     * Actualiza un usuario en la bbdd
     */
    function updateUser($usuario)
    {
       
        try{
            //actualiza el usuario
            $query= $this->db->connect()->prepare("UPDATE usuario SET login = :login  WHERE idUSUARIO = :idUsuario");
            $query->execute(['login'=>$usuario->getLogin(),'idUsuario'=> $usuario->getIdUsuario()]);

            //elimino los roles para insertar los que vienen modificados
            $queryEliminaRoles= $this->db->connect()->prepare("DELETE FROM usuario_rol  WHERE idUSUARIO = :idUsuario");
            $queryEliminaRoles->execute(['idUsuario'=>$usuario->getIdUsuario()]);
            //inserta los roles de nuevo          
            $roles = $usuario->getRoles();
            foreach ($roles as $key => $value) {
                //preparamos consulta para insertar rol
                $queryRol = $this->db->connect()->prepare("INSERT INTO usuario_rol( idUSUARIO,idROL) VALUES (:idusuario, :idrol)");
                // var_dump( $value);
                $idrol = $value['id'];
               $queryRol->execute(["idusuario" => $usuario->getIdUsuario(), "idrol" => $idrol]);
            }
            //elimino empresas
            $queryEliminaEmpresas= $this->db->connect()->prepare("DELETE FROM usuario_empresa  WHERE idUSUARIO = :idUsuario");
            $queryEliminaEmpresas->execute(['idUsuario'=>$usuario->getIdUsuario()]);
            $empresas = $usuario->getEmpresas();
            foreach ($empresas as $key => $value) {
                //preparamos consulta para insertar rol
                $queryEmpresa = $this->db->connect()->prepare("INSERT INTO usuario_empresa( idUSUARIO,idEmpresa) VALUES (:idusuario, :idempresa)");
                $idEmpresa = $value['id'];
                $queryEmpresa->execute(["idusuario" => $usuario->getIdUsuario(), "idempresa" => $idEmpresa]);
            }
            return $this->getUserById($usuario->getIdUsuario());
        }catch(PDOException $e){
            return $e;
        }
    }
}
