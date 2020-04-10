<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files for decoding jwt will be here

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
 * Description of usuario
 *
 * @author jcpm0
 */
class usuarios extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los usuarios
     * GET
     */
    function usuarios($token)
    {
        try {
            //comprobamos que el token esté ok
            $decoded = JWT::decode($token, constant('key'), array('HS256'));
            //extraemos los datos del modelo
            $usuarios = $this->model->getUsuarios();
            //si vienen datos
            if ($usuarios != null) {
                //establecemos el código de estado 200->ok
                http_response_code(200);
                //formateamos la salida
                $salida = [];
                foreach ($usuarios as $key => $value) {
                    array_push($salida, ["id" => $value->getIdUsuario(), "login" => $value->getLogin(), "roles" => $value->getRoles(), "empresas" => $value->getEmpresas()]);
                }
                echo json_encode($salida);
            } else {
                //si no hay usuarios mando código 404
                http_response_code(404);
                echo json_encode(array("mensaje" => "No se han encontrado usuarios"));
            }
        } catch (Exception $e) {
            // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
            http_response_code(401);

            // show error message
            echo json_encode(array(
                "message" => 'Acceso denegado',
                "error" => $e->getMessage()
            ));
        }
    }

    /**
     * Obtiene un usuario por id
     * GET
     */
    function usuario($param, $token)
    {
        //obtengo el id que viene en el array $param
        $id = $param[0];
        try {
            //comprobamos que el token esté ok
            $decoded = JWT::decode($token, constant('key'), array('HS256'));
            //obtengo el usuario
            $usuarioDB = $this->model->getUserById($id);

            if ($usuarioDB != null) {
                //establecemos el código de estado 200->ok
                http_response_code(200);
                //formateamos la salida
                $salida = [];

                array_push($salida, ["id" => $usuarioDB->getIdUsuario(), "login" => $usuarioDB->getLogin(), "roles" => $usuarioDB->getRoles(), "empresas" => $usuarioDB->getEmpresas()]);

                echo json_encode($salida);
            } else {
                //si no existe usuario mando código 404
                http_response_code(404);
                echo json_encode(array("mensaje" => "No se han encontrado el usuario"));
            }
        } catch (Exception $e) {
            // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
            http_response_code(401);

            // show error message
            echo json_encode(array(
                "message" => 'Acceso denegado',
                "error" => $e->getMessage()
            ));
        }
    }

    /**
     * Crea un usuario
     */
    function crear($token)
    {

        $json = file_get_contents('php://input');

       // echo $json;
        $data = json_decode($json,true);
        $usuario= $data['usuario'];
        $login= $usuario['login'];
        $password= password_hash($usuario['password'],PASSWORD_DEFAULT);

        $nuevoUsuario= new Usuario();
        $nuevoUsuario->setLogin($login);
        $nuevoUsuario->setPassword($password);
        $nuevoUsuario->setRoles($usuario['roles']);
        $nuevoUsuario->setEmpresas($usuario['empresas']);


       try {
            //comprobamos que el token esté ok
            $decoded = JWT::decode($token, constant('key'), array('HS256'));
            //compruebo si el login ya existe
            $usuarioDB = $this->model->getUserByLogin($login);
            if (!$usuarioDB instanceof Usuario){

            
            //intento insertar el usuario
            $usuarioDB = $this->model->insertaUsuario($nuevoUsuario);
         
            if ( $usuarioDB instanceof Usuario) {
                //establecemos el código de estado 200->ok
                http_response_code(200);
                //formateamos la salida
                $salida = [];

                array_push($salida, ["id" => $usuarioDB->getIdUsuario(), "login" => $usuarioDB->getLogin(), "roles" => $usuarioDB->getRoles(), "empresas" => $usuarioDB->getEmpresas()]);

                echo json_encode($salida);
            } else {
                //si no existe usuario mando código 404
                http_response_code(500);
                echo json_encode(array("mensaje" => "No se ha podido insertar el usuario"));
            }
        }else{
            // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
            http_response_code(400);

            // show error message
            echo json_encode(array(
                "message" => 'El login ya existe.'
            ));
        }
        } catch (Exception $e) {
            // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
            http_response_code(401);

            // show error message
            echo json_encode(array(
                "message" => 'Acceso denegado',
                "error" => $e->getMessage()
            ));
        }
    }

/**
 * Borrar un usuario de la bbdd
 */
function delete($param,$token){
    //obtengo el id que viene en el array $param

    $id = $param[0];
    echo $id;
    try {
        //comprobamos que el token esté ok
        $decoded = JWT::decode($token, constant('key'), array('HS256'));
        //obtengo el usuario
        $respuesta = $this->model->eliminaUsuario($id);
  
        switch ($respuesta){
            case "200": 
                echo json_encode(array("mensaje" => "Usuario eliminado correctamente"));
            break;
            case "400": 
                echo json_encode(array("mensaje" => "No se ha podido borrar el usuario"));
            break;
        }
    }catch(Exception $e){
        // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
        http_response_code(401);

        // show error message
        echo json_encode(array(
            "message" => 'Acceso denegado',
            "error" => $e->getMessage()
        ));
    }
}

}
