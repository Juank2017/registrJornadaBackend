<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'entidades/usuario.php';
/**
 * Description of loginmodel
 *
 * @author jcpm0
 */
class loginModel extends model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Comprueba si existe el usuario y si la contraseña facilitada es válida. devuelve un objeto de tipo Usuario
     */
    function getUsuario($login, $password)
    {

        $user = new Usuario;
        //si no viene nada me salgo 
        if (empty($login) || empty($password)) {
            return;
        } else {

            //compruebo si existe el login
            try {
                $query = $this->db->connect()->prepare('SELECT * FROM usuario WHERE login = :login');

                $query->execute(['login' => $login]);

                $row = $query->fetch();
                //ha encontrado al usuario
                if ($row != null) {
                    //comprueba si la contraseña es válida
                    if (password_verify($password, $row['password'])) {

                        $user->setLogin($row['login']);
                        $user->setPassword($row['password']);
                        $user->setIdUsuario($row['idUSUARIO']);

                        //obtengo roles
                        $query = $this->db->connect()->prepare('SELECT rol.rol, rol.idROL, usuario_rol.idUSUARIO FROM rol LEFT JOIN usuario_rol ON usuario_rol.idROL = rol.idROL WHERE usuario_rol.idUSUARIO = :idUSUARIO');

                        $query->execute(['idUSUARIO' => $user->getIdUsuario()]);
                        $roles = [];
                        while ($row = $query->fetch()) {

                            $rol = array("id" => $row['idROL'], "rol" => $row['rol']);
                            array_push($roles, $rol);
                        }
                        $user->setRoles($roles);

                        //obtengo empresa
                        $query = $this->db->connect()->prepare('SELECT empresa.* FROM empresa LEFT JOIN usuario_empresa ON usuario_empresa.idEmpresa = empresa.idEMPRESA WHERE usuario_empresa.idUSUARIO = :idUsuario');
                        $query->execute(['idUsuario' => $user->getIdUsuario()]);
                        $empresas = [];
                        while ($row = $query->fetch()) {

                            $empresa = array("id" => $row['idEMPRESA'], "nombre" => $row['nombre']);
                            array_push($empresas, $empresa);
                        }
                        $user->setEmpresas($empresas);
                        return $user;
                    } else {
                        //en caso de que la contraseña no sea válida devuelve 401
                        return "401";
                    }
                } else {
                    //si no encuentra el login devuelve 404
                    return "404";
                }
            } catch (PDOException $e) {
                return null;
            }
        }
    }
}
