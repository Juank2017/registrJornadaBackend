<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,GET,PUT,DELETE");
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
 * Description of notificacion
 *
 * @author jcpm0
 */
class notificaciones extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los notificaciones
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
                $notificaciones = $this->model->getNotificaciones();
                //si vienen datos
                if ($notificaciones != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                   
                    $not = [];
                   
                    foreach ($notificaciones['notificaciones'] as $key => $value) {
                        $empleado = $value->getIdEMPLEADO();

                        array_push($not, [
                            "id" => $value->getIdNotificacion(),
                            "fecha" => $value->getfecha(),
                            "texto_notificacion" => $value->getTexto_notificacion(),
                            "texto_respuesta" => $value->getTexto_respuesta(),
                            "leida" => $value->getLeida(),
                            'loginEmisor' => $value->getLoginEmisor(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ]


                        ]);
                    }

                    $salida=array("paginacion"=>$notificaciones['paginacion'],'notificaciones'=> $not);
                    echo json_encode($salida);
                } else {
                    //si no hay notificaciones mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado notificaciones"));
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
     * Obtiene un sede por id
     * GET
     */
    function empleado($token, $param = [])
    {    //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
            $pagina= (string)filter_input(INPUT_GET,'pagina');
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el sede
                $notificaciones = $this->model->getNotificacionesByEmpleadoId($pagina,$id);

                if ($notificaciones != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    $not = [];
                 //   array_push($salida,array("paginacion"=>$notificaciones['paginacion']));
                    foreach ($notificaciones['notificaciones'] as $key => $value) {
                        $empleado = $value->getIdEMPLEADO();

                        array_push($not, [
                            "id" => $value->getIdNotificacion(),
                            "fecha" => $value->getfecha(),
                            "texto_notificacion" => $value->getTexto_notificacion(),
                            "texto_respuesta" => $value->getTexto_respuesta(),
                            "leida" => $value->getLeida(),
                            'loginEmisor' => $value->getLoginEmisor(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ]


                        ]);
                    }

                    $salida=array("paginacion"=>$notificaciones['paginacion'],'notificaciones'=> $not);
                    echo json_encode($salida);
                } else {
                    //si no existe sede mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado la sede"));
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
     * Obtiene un notificacion por id
     * GET
     */
    function notificacion($token, $param = [])
    {    //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el notificacion
                $notificacionDB = $this->model->getNotificacionById($id);

                if ($notificacionDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    $empleado = $notificacionDB->getIdEMPLEADO();

                    $salida= [
                        "id" => $notificacionDB->getIdNotificacion(),
                            "fecha" => $notificacionDB->getfecha(),
                            "texto_notificacion" => $notificacionDB->getTexto_notificacion(),
                            "texto_respuesta" => $notificacionDB->getTexto_respuesta(),
                            "leida" => $notificacionDB->getLeida(),
                            'loginEmisor' => $notificacionDB->getLoginEmisor(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ]


                    ];
                    echo json_encode($salida);
                } else {
                    //si no existe notificacion mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado la notificacion"));
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
     * Crea un notificacion
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
           //var_dump($data);
            if ($data != null && array_key_exists('id', $data)) {
                 
                      $fecha = array_key_exists('fecha', $data) ? $data['fecha'] : '';
                      $texto_notificacion = array_key_exists('texto_notificacion', $data) ? $data['texto_notificacion'] : '';
                      $texto_respuesta = array_key_exists('texto_respuesta', $data) ? $data['texto_respuesta'] : '';
                      $leida = array_key_exists('leida', $data) ? $data['leida'] : '';
                      $idEMPLEADO = array_key_exists('idEMPLEADO', $data) ? $data['idEMPLEADO'] : '';
                      $loginEmisor = array_key_exists('loginEmisor', $data) ? $data['loginEmisor'] : '';


                if (empty($fecha) || empty($texto_notificacion)   || empty($idEMPLEADO)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {


                    $nuevoNotificacion = new Notificacion;
                    $nuevoNotificacion->setFecha($fecha);
                    $nuevoNotificacion->setTexto_notificacion($texto_notificacion);
                    $nuevoNotificacion->setTexto_respuesta($texto_respuesta);
                    $nuevoNotificacion->setLeida($leida);
                    $nuevoNotificacion->setIdEMPLEADO($idEMPLEADO);
                    $nuevoNotificacion->setLoginEmisor($loginEmisor);





                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));
                        //intento insertar el notificacion
                        $notificacionDB = $this->model->createNotificacion($nuevoNotificacion);

                        if ($notificacionDB instanceof Notificacion) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $empleado = $notificacionDB->getIdEMPLEADO();

                            array_push($salida, [
                                "id" => $notificacionDB->getIdNotificacion(),
                                "fecha" => $notificacionDB->getfecha(),
                                "texto_notificacion" => $notificacionDB->getTexto_notificacion(),
                                "texto_respuesta" => $notificacionDB->getTexto_respuesta(),
                                "leida" => $notificacionDB->getLeida(),
                                'loginEmisor' => $notificacionDB->getLoginEmisor(),
                                "empleado" => [
                                    'id' => $empleado->getIdEmpleado(),
                                    'nombre' => $empleado->getNombre(),
                                    'apellidos' => $empleado->getApellidos()
                                ]


                            ]);

                            echo json_encode($salida);
                        } else {
                            //si no existe notificacion mando código 500
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el notificacion"));
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
            } else {
                http_response_code(400);
                echo json_encode((array("mensaje" => 'Los datos recibidos no tienen el formato correcto.')));
            }
        }
    }

    /**
     * Borrar un notificacion de la bbdd
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
                //obtengo el notificacion
                $respuesta = $this->model->deleteNotificacion($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Notificacion eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el notificacion"));
                        break;
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
     * Actualiza un notificacion en la bdd
     */
    function update($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            $data = json_decode($json, true);
            if ($data != null && array_key_exists('id', $data)) {
              
                $idNotificacion = array_key_exists('id', $data) ? $data['id'] : '';
                $fecha = array_key_exists('fecha', $data) ? $data['fecha'] : '';
                $texto_notificacion = array_key_exists('texto_notificacion', $data) ? $data['texto_notificacion'] : '';
                $texto_respuesta = array_key_exists('texto_respuesta', $data) ? $data['texto_respuesta'] : '';
                $leida = array_key_exists('leida', $data) ? $data['leida'] : '';
                $idEMPLEADO = array_key_exists('idEMPLEADO', $data) ? $data['idEMPLEADO'] : '';
                $loginEmisor = array_key_exists('loginEmisor', $data) ? $data['loginEmisor'] : '';


                if (empty($fecha) || empty($texto_notificacion) || empty($texto_respuesta) || empty($leida) || empty($idEMPLEADO)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {


                    $notificacionActualizado = new Notificacion;

                    $notificacionActualizado->setFecha($fecha);
                    $notificacionActualizado->setTexto_notificacion($texto_notificacion);
                    $notificacionActualizado->setTexto_respuesta($texto_respuesta);
                    $notificacionActualizado->setLeida($leida);
                    $notificacionActualizado->setIdEMPLEADO($idEMPLEADO);
                    $notificacionActualizado->setIdNotificacion($idNotificacion);
                    $notificacionActualizado->setLoginEmisor($loginEmisor);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $notificacionDB = $this->model->updateNotificacion($notificacionActualizado);

                        if ($notificacionDB instanceof Notificacion) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];


                            $empleado = $notificacionDB->getIdEMPLEADO();

                            array_push($salida, [
                                "id" => $notificacionDB->getIdNotificacion(),
                                "fecha" => $notificacionDB->getfecha(),
                                "texto_notificacion" => $notificacionDB->getTexto_notificacion(),
                                "texto_respuesta" => $notificacionDB->getTexto_respuesta(),
                                "leida" => $notificacionDB->getLeida(),
                                'loginEmisor' => $notificacionDB->getLoginEmisor(),
                                "empleado" => [
                                    'id' => $empleado->getIdEmpleado(),
                                    'nombre' => $empleado->getNombre(),
                                    'apellidos' => $empleado->getApellidos()
                                ]
                            ]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar la notificacion"));
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
