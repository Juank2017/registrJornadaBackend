<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,PUT,GET,DELETE");
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
    function index($token, $pagina)
    {        //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {

            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //extraemos los datos del modelo
                $usuarios = $this->model->getUsuarios($pagina);
                //si vienen datos
                if ($usuarios != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    array_push($salida,array("paginacion"=>$usuarios['paginacion']));
                    foreach ($usuarios['usuarios'] as $key => $value) {

                        array_push($salida, ["id" => $value->getIdUsuario(), "login" => $value->getLogin(), "roles" => $value->getRoles(), "empresas" => $value->getEmpresas()]);
                    }
                    //a print_r($salida);
                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado usuarios"));
                }
            }catch(PDOException $e){
                http_response_code(500);

                // show error message
                echo json_encode(array(
                    "message" => 'Error en la BBDD',
                    "error" => $e->getMessage()
                ));
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
    }

    /**
     * Obtiene un usuario por id
     * GET
     */
    function usuario( $token,$param=[])
    {    //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
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
            } catch(PDOException $e){
                http_response_code(500);

                // show error message
                echo json_encode(array(
                    "message" => 'Error en la BBDD',
                    "error" => $e->getMessage()
                ));
            }catch (Exception $e) {
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

    /**
     * Crea un usuario
     */
    function create($token)
    {
        //comprueba que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            // echo $json;
            $data = json_decode($json, true);
            if ($data != null && array_key_exists('login', $data)) {
               // $usuario = array_key_exists('usuario', $data) ?  $data['usuario'] : '';

                $login = array_key_exists('login', $data) ? $data['login'] : '';
                $password = array_key_exists('password', $data) ? password_hash($data['password'], PASSWORD_DEFAULT) : '';
                if (empty($login) || empty($password)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {


                    $nuevoUsuario = new Usuario();
                    $nuevoUsuario->setLogin($login);
                    $nuevoUsuario->setPassword($password);
                    $nuevoUsuario->setRoles($data['roles']);
                    $nuevoUsuario->setEmpresas($data['empresas']);

                   // print_r($nuevoUsuario);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));
                        //compruebo si el login ya existe
                        $usuarioDB = $this->model->getUserByLogin($login);
                        if (!$usuarioDB instanceof Usuario) {


                            //intento insertar el usuario
                            $usuarioDB = $this->model->createUser($nuevoUsuario);

                            if ($usuarioDB instanceof Usuario) {
                                //establecemos el código de estado 200->ok
                                http_response_code(200);
                                //formateamos la salida
                                

                                $salida =  ["id" => $usuarioDB->getIdUsuario(), "login" => $usuarioDB->getLogin(), "roles" => $usuarioDB->getRoles(), "empresas" => $usuarioDB->getEmpresas()];

                                echo json_encode($salida);
                            } else {
                                //si no existe usuario mando código 404
                                http_response_code(500);
                                echo json_encode(array("mensaje" => "No se ha podido insertar el usuario"));
                            }
                        } else {

                            http_response_code(400);

                            // show error message
                            echo json_encode(array(
                                "message" => 'El login ya existe.'
                            ));
                        }
                    } catch(PDOException $e){
                        http_response_code(500);

                        // show error message
                        echo json_encode(array(
                            "message" => 'Error en la BBDD',
                            "error" => $e->getMessage()
                        ));
                    }catch (Exception $e) {
                        // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
                        http_response_code(401);

                        // show error message
                        echo json_encode(array(
                            "message" => 'Acceso denegado',
                            "error" => $e->getMessage()
                        ));
                    }
                }
            } else {
                http_response_code(400);
                echo json_encode((array("mensaje" => 'Los datos recibidos no tienen el formato correcto.')));
            }
        }
    }

    /**
     * Borrar un usuario de la bbdd
     */
    function delete($token, $param = [])
    {
        if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param

            $id = count($param) > 0 ? $param[0] : "";

            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el usuario
                $respuesta = $this->model->deleteUser($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Usuario eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el usuario"));
                        break;
                }
            } catch(PDOException $e){
                http_response_code(500);

                // show error message
                echo json_encode(array(
                    "message" => 'Error en la BBDD',
                    "error" => $e->getMessage()
                ));
            }catch (Exception $e) {
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

    /**
     * Actualiza un usuario en la bdd
     */
    function update($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            // echo $json;
            $data = json_decode($json, true);
            if ($data != null && array_key_exists('login', $data)) {
               // $usuario = array_key_exists('usuario', $data) ?  $data['usuario'] : '';

                $login = array_key_exists('login', $data) ? $data['login'] : '';
                $roles =array_key_exists('roles', $data) ? $data['roles'] : '';
                $empresas = array_key_exists('empresas', $data) ? $data['empresas'] : '';
                if (empty($login)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    $usuarioActualizado = new Usuario();
                    $usuarioActualizado->setIdUsuario($data['idUSUARIO']);
                    $usuarioActualizado->setLogin($login);
                    $usuarioActualizado->setPassword($data['password']);
                    $usuarioActualizado->setRoles($roles);
                    $usuarioActualizado->setEmpresas($empresas);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $usuarioDB = $this->model->updateUser($usuarioActualizado);

                        if ($usuarioDB instanceof Usuario) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $usuarioDB->getIdUsuario(), "login" => $usuarioDB->getLogin(), "roles" => $usuarioDB->getRoles(), "empresas" => $usuarioDB->getEmpresas()]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido actualizar el usuario"));
                        }
                    } catch(PDOException $e){
                        http_response_code(500);

                        // show error message
                        echo json_encode(array(
                            "message" => 'Error en la BBDD',
                            "error" => $e->getMessage()
                        ));
                    }catch (Exception $e) {
                        // si viene mal el token, devolvemos status 401 y mensaje de acceso denegado
                        http_response_code(401);

                        // show error message
                        echo json_encode(array("message" => 'Acceso denegado', "error" => $e->getMessage()));
                    }
                }
            } else {
                http_response_code(400);
                echo json_encode((array("mensaje" => 'Los datos recibidos no tienen el formato correcto.')));
            }
        }
    }
}
