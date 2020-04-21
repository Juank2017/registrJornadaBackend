<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/x-www-form-urlencoded");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
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
class login extends controller
{

    function __construct()
    {
        parent::__construct();
    }



    function index($token)
    {
        //comprueba que sea una petición post
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');
          //  var_dump($json);
            $array = json_decode($json );
          //  var_dump( $array);
            //Extrae los datos que vengan en la petición.
            $login = $array->login;
            $password = $array->password;
            //si alguno viene vacio devulve bad request con un mensaje
            if (empty($login) || empty($password)) {
                http_response_code(400);
                echo json_encode(array("mensaje" => "falta alguno de los campos en el body",'post'=>$_POST));
            } else {
                //obtiene el usuario
                $user = $this->model->getUsuario($login, $password);
                //si lo ha obtenido genera el token JWT
                if ($user instanceof Usuario) {
                    $token = array(
                        "iss" => constant('iss'),
                        "aud" => constant('aud'),
                        "iat" => constant('iat'),
                        "nbf" => constant('nbf'),
                        // "exp" => constant('exp'),
                        "usuario" => array(
                            "id" => $user->getIdUsuario(),
                            "login" => $user->getLogin(),
                            "roles" => $user->getRoles(),
                            "empresas" => $user->getEmpresas()
                        )
                    );
                    $jwt = JWT::encode($token, constant('key'));
                    http_response_code(200);
                    echo json_encode(array(
                        "mensaje" => 'true',
                        "usuario" => array(
                            "id" => $user->getIdUsuario(),
                            "login" => $user->getLogin(),
                            "roles" => $user->getRoles(),
                            "empresas" => $user->getEmpresas()
                        ),
                        "token" => $jwt
                    ));
                } else {

                    switch ($user) {
                        case "401":
                            http_response_code(401);
                            echo json_encode(array("mensaje" => 'Acceso denegado. Password incorrecto'));
                            break;
                        case "404":
                            http_response_code(404);
                            echo json_encode(array("mensaje" => 'Usuario no encontrado.'));
                            break;
                    }
                }
            }
        }
    }
}
