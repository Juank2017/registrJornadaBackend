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
class turnos extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los turnos
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
                $turnos = $this->model->getTurnos();
                if ($turnos != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    foreach ($turnos as $key => $value) {

                        array_push($salida, ["id" => $value['id'], "turno" => $value['turno']]);
                    }
                    //a print_r($salida);
                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado turnos"));
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
     * Obtiene un turno por el id
     */
    function turno( $token,$param)
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
                $turnoDB = $this->model->getTurnoById($id);

                if ($turnoDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];

                    array_push($salida, ["id" => $turnoDB['id'], "turno" => $turnoDB['turno']]);

                    echo json_encode($salida);
                } else {
                    //si no existe usuario mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el turno"));
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
     * Crea un turno
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
            if ($data != null && array_key_exists('turno', $data)) {
                $turno = array_key_exists('turno', $data) ?  $data['turno'] : '';


                if (empty($turno)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        //intento insertar el usuario
                        $turnoDB = $this->model->createTurno($turno);

                        if ($turnoDB != null) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $turnoDB['id'], "turno" => $turnoDB['turno']]);

                            echo json_encode($salida);
                        } else {
                            //si no existe usuario mando código 404
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el turno"));
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
     * Borra un turno
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
                $respuesta = $this->model->deleteTurno($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "turno eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el turno"));
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
            if ($data != null && array_key_exists('turno', $data)) {
                $turno = array_key_exists('turno', $data) ?  $data['turno'] : '';

                $id = array_key_exists('id', $data) ?  $data['id'] : '';

                if (empty($turno) || empty($id)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $turnoDB = $this->model->updateTurno($turno,$id);

                        if ($turnoDB !=null ) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $turnoDB['id'], $turnoDB['turno']]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el turno"));
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