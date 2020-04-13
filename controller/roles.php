<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


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
class roles extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los roles
     */
    function index($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //extraemos los datos del modelo
                $roles = $this->model->getRoles();
                if ($roles != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    foreach ($roles as $key => $value) {

                        array_push($salida, ["id" => $value['id'], "rol" => $value['rol']]);
                    }
                    //a print_r($salida);
                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado roles"));
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
    }

    /**
     * Obtiene un rol por el id
     */
    function rol( $token,$param)
    {
        //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                $rolDB = $this->model->getRolById($id);

                if ($rolDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];

                    array_push($salida, ["id" => $rolDB['id'], "rol" => $rolDB['rol']]);

                    echo json_encode($salida);
                } else {
                    //si no existe usuario mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el rol"));
                }

                //code...
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
     * Crea un rol
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
            if ($data != null && array_key_exists('rol', $data)) {
                $rol = array_key_exists('rol', $data) ?  $data['rol'] : '';


                if (empty($rol)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        //intento insertar el usuario
                        $rolDB = $this->model->createRol($rol);

                        if ($rolDB != null) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $rolDB['idROL'], "rol" => $rolDB['rol']]);

                            echo json_encode($salida);
                        } else {
                            //si no existe usuario mando código 404
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el rol"));
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
            } else {
                http_response_code(400);
                echo json_encode((array("mensaje" => 'Los datos recibidos no tienen el formato correcto.')));
            }
        }
    }

    /**
     * Borra un rol
     */
    function delete($token,$param){
        if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param

            $id = count($param) > 0 ? $param[0] : "";

            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el usuario
                $respuesta = $this->model->deleteRol($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Rol eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el rol"));
                        break;
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
            if ($data != null && array_key_exists('rol', $data)) {
                $rol = array_key_exists('rol', $data) ?  $data['rol'] : '';

                $id = array_key_exists('id', $data) ?  $data['id'] : '';

                if (empty($rol) || empty($id)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $rolDB = $this->model->updateRol($rol,$id);

                        if ($rolDB !=null ) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $rolDB['id'], $rolDB['rol']]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el rol"));
                        }
                    } catch (Exception $e) {
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
