<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,PUT,GET,DELETE");
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
class horarios extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los horarios
     */
    function index($token, $pagina)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //extraemos los datos del modelo
                $horarios = $this->model->getHorarios($pagina);
                
                if ($horarios != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    array_push($salida,array("paginacion"=>$horarios['paginacion']));
                    foreach ($horarios['horarios'] as $key => $value) {

                        array_push($salida, array("horarios"=>["id" => $value['id'], "hora_entrada" => $value['hora_entrada'],"hora_salida"=>$value['hora_salida'],"idEmpleado"=>$value['idEmpleado']]));
                    }

                    //a print_r($salida);}
                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado horarios"));
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
     * Obtiene todos los horarios
     */
    function empleado($token, $param)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //extraemos los datos del modelo
                $horarios = $this->model->getHorarioByEmpleadoId($id);
                
                if ($horarios != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = array();
                   
                    foreach ($horarios as $key => $value) {

                        array_push($salida, ["id" => $value['id'], "hora_entrada" => $value['hora_entrada'],"hora_salida"=>$value['hora_salida'],"idEmpleado"=>$value['idEmpleado']]);
                    }

                    //a print_r($salida);}
                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado horarios"));
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
     * Obtiene un horario por el id
     */
    function horario( $token,$param)
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
                $horarioDB = $this->model->getHorarioById($id);

                if ($horarioDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];

                    array_push($salida, ["id" => $horarioDB['id'], "hora_entrada" => $horarioDB['hora_entrada'],"hora_salida"=>$horarioDB['hora_salida'],"idEmpleado"=>$horarioDB['idEmpleado']]);

                    echo json_encode($salida);
                } else {
                    //si no existe usuario mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el horario"));
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
     * Crea un horario
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
            if ($data != null && array_key_exists('hora_entrada', $data)) {
                $empleado = array_key_exists('empleado', $data) ?  $data['empleado'] : '';
                $hora_entrada=array_key_exists('hora_entrada', $data) ?  $data['hora_entrada'] : '';
                $hora_salida=array_key_exists('hora_salida', $data) ?  $data['hora_salida'] : '';
                $idEMPLEADO=array_key_exists('id', $empleado) ?  $empleado['id'] : '';

                if (empty($hora_entrada)||empty($hora_salida)||empty($idEMPLEADO)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        //intento insertar el usuario
                        $horarioDB = $this->model->createHorario($hora_entrada,$hora_salida,$idEMPLEADO);

                        if ($horarioDB != null) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $horarioDB['id'], "hora_entrada" => $horarioDB['hora_entrada'],"hora_salida"=>$horarioDB['hora_salida'],"idEmpleado"=>$horarioDB['idEmpleado']]);

                            echo json_encode($salida);
                        } else {
                            //si no existe usuario mando código 404
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el horario"));
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
     * Borra un horario
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
                $respuesta = $this->model->deleteHorario($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "horario eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el horario"));
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
            if ($data != null && array_key_exists('horario', $data)) {
                $horario = array_key_exists('horario', $data) ?  $data['horario'] : '';

                $id = array_key_exists('id', $horario) ?  $horario['id'] : '';
            
                $hora_entrada=array_key_exists('hora_entrada', $horario) ?  $horario['hora_entrada'] : '';
                $hora_salida=array_key_exists('hora_salida', $horario) ?  $horario['hora_salida'] : '';
                $idEMPLEADO=array_key_exists('idEMPLEADO', $horario) ?  $horario['idEMPLEADO'] : '';

                if (empty($horario) || empty($id)||empty($hora_entrada)||empty($hora_salida)||empty($idEMPLEADO)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $horarioDB = $this->model->updateHorario($id,$hora_entrada,$hora_salida,$idEMPLEADO);

                        if ($horarioDB !=null ) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $horarioDB['id'], "hora_entrada" => $horarioDB['hora_entrada'],"hora_salida"=>$horarioDB['hora_salida'],"idEmpleado"=>$horarioDB['idEmpleado']]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el horario"));
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