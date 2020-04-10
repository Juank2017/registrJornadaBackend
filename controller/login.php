<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'config/config.php';
include_once 'vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once 'vendor/firebase/php-jwt/src/ExpiredException.php';
include_once 'vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once 'vendor/firebase/php-jwt/src/JWT.php';

use \Firebase\JWT\JWT;

/**
 * Description of loginController
 *
 * @author jcpm0
 */
class login extends controller {

    function __construct() {
        parent::__construct();
    }

    

    function login($token) {
       
        $login = $_POST['login'];
        $password = $_POST['password'];
        // var_dump($param);
        $user = $this->model->getUsuario($login, $password);

        if ($user instanceof Usuario) {
            $token = array(
                "iss" => constant('iss'),
                "aud" => constant('aud'),
                "iat" => constant('iat'),
                "nbf" => constant('nbf'),
               // "exp" => constant('exp'),
                "usuario" => array("id" => $user->getIdUsuario(),
                    "login" => $user->getLogin(),
                    "roles" => $user->getRoles(),
                    "empresas" => $user->getEmpresas())
            );
            $jwt = JWT::encode($token, constant('key'));
            http_response_code(200);
            echo json_encode(array("mensaje" => 'true',
                "usuario" => array("id" => $user->getIdUsuario(),
                    "login" => $user->getLogin(),
                    "roles" => $user->getRoles(),
                    "empresas" => $user->getEmpresas()),
                "token" => $jwt
            ));
        } else {
            
            switch($user){
                case "401": 
                    echo json_encode(array("mensaje" => 'Acceso denegado. Password incorrecto'));
                break;
                case "404": 
                    echo json_encode(array("mensaje" => 'Usuario no encontrado.'));
                break;
            }
           
        }
    }

}
