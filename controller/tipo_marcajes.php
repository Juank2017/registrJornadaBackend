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
class tipo_marcajes extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los tipo_marcajes
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
                $tipo_marcajes = $this->model->getTipo_marcajes();
                if ($tipo_marcajes != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    foreach ($tipo_marcajes as $key => $value) {

                        array_push($salida, ["id" => $value['id'], "tipo_marcaje" => $value['tipo_marcaje']]);
                    }
                    //a print_r($salida);
                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado tipo_marcajes"));
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
     * Obtiene un tipo_marcaje por el id
     */
    function tipo_marcaje( $token,$param)
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
                $tipo_marcajeDB = $this->model->getTipo_marcajeById($id);

                if ($tipo_marcajeDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];

                    array_push($salida, ["id" => $tipo_marcajeDB['id'], "tipo_marcaje" => $tipo_marcajeDB['tipo_marcaje']]);

                    echo json_encode($salida);
                } else {
                    //si no existe usuario mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el tipo_marcaje"));
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
     * Crea un tipo_marcaje
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
            if ($data != null && array_key_exists('tipo_marcaje', $data)) {
                $tipo_marcaje = array_key_exists('tipo_marcaje', $data) ?  $data['tipo'] : '';


                if (empty($tipo_marcaje)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        //intento insertar el usuario
                        $tipo_marcajeDB = $this->model->createTurno($tipo_marcaje);

                        if ($tipo_marcajeDB != null) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $tipo_marcajeDB['id'], "tipo_marcaje" => $tipo_marcajeDB['tipo_marcaje']]);

                            echo json_encode($salida);
                        } else {
                            //si no existe usuario mando código 404
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el tipo_marcaje"));
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
     * Borra un tipo_marcaje
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
                $respuesta = $this->model->deleteTipo_marcaje($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "tipo_marcaje eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el tipo_marcaje"));
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
            if ($data != null && array_key_exists('tipo_marcaje', $data)) {
                $tipo_marcaje = array_key_exists('tipo_marcaje', $data) ?  $data['tipo'] : '';

                $id = array_key_exists('id', $data) ?  $data['id'] : '';

                if (empty($tipo_marcaje) || empty($id)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $tipo_marcajeDB = $this->model->updateTipo_marcaje($tipo_marcaje,$id);

                        if ($tipo_marcajeDB !=null ) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $tipo_marcajeDB['id'], $tipo_marcajeDB['tipo_marcaje']]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el tipo_marcaje"));
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