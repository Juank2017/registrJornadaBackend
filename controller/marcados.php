<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,PUT,GET");
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
 * Description of sede
 *
 * @author jcpm0
 */
class marcados extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los marcados
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
                $marcados = $this->model->getMarcados($pagina);
                //si vienen datos
                if ($marcados != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    array_push($salida,array("paginacion"=>$marcados['paginacion']));
                    foreach ($marcados['marcados'] as $key => $value) {
                        $empleado = $value->getIdEMPLEADO();
                        $tipo_marcaje= $value->getIdTipo_Marcaje();
                        array_push($salida,array("marcados"=> [
                            "id" => $value->getIdMarcado(),
                            "fecha" => $value->getfecha(),
                            "longitud" => $value->getLongitud(),
                            "latitud" => $value->getLatitud(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ],
                            "tipo_marcaje" =>$tipo_marcaje

                        ]));
                    }

                    echo json_encode($salida);
                } else {
                    //si no hay marcados mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado marcados"));
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
    function marcado($token, $param = [])
    {    //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el sede
                $marcadoDB = $this->model->getMarcadoById($id);

                if ($marcadoDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    $empleado = $marcadoDB->getIdEMPLEADO();
                        $tipo_marcaje= $marcadoDB->getIdTipo_Marcaje();
                        array_push($salida, [
                            "id" => $marcadoDB->getIdMarcado(),
                            "fecha" => $marcadoDB->getfecha(),
                            "longitud" => $marcadoDB->getLongitud(),
                            "latitud" => $marcadoDB->getLatitud(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ],
                            "tipo_marcaje" =>$tipo_marcaje

                        ]);
                    echo json_encode($salida);
                } else {
                    //si no existe sede mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado la sede"));
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
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el sede
                $marcados = $this->model->getMarcadoByEmpleadoId($id);

                if ($marcados != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                   
                    foreach ($marcados as $key => $value) {
                        $empleado = $value->getIdEMPLEADO();
                        $tipo_marcaje= $value->getIdTipo_Marcaje();
                        array_push($salida,[
                            "id" => $value->getIdMarcado(),
                            "fecha" => $value->getfecha(),
                            "horaInicio" => $value->getHora_inicio(),
                            "horaFinal" => $value->getHora_final(),
                            "longitud" => $value->getLongitud(),
                            "latitud" => $value->getLatitud(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ],
                            "tipo_marcaje" =>$tipo_marcaje

                        ]);
                    }

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
     * Crea un sede
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
            if ($data != null && array_key_exists('fecha', $data)) {
                

                $fecha = array_key_exists('fecha', $data) ? $data['fecha'] : '';
                $longitud = array_key_exists('longitud', $data) ? $data['longitud'] : '';
                $latitud = array_key_exists('latitud', $data) ? $data['latitud'] : '';
                $hora_inicio = array_key_exists('horaInicio', $data) ? $data['horaInicio'] : '';
                $hora_final = array_key_exists('horaFinal', $data) ? $data['horaFinal'] : '';
                $idTipo_marcaje = array_key_exists('idTipoMarcaje', $data) ? $data['idTipoMarcaje'] : '';
                $idEmpleado = array_key_exists('idEmpleado', $data) ? $data['idEmpleado'] : '';
                
                if (empty($fecha)  || empty($hora_inicio)   || empty($idTipo_marcaje) || empty($idEmpleado)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {


                    $nuevoMarcado = new Marcado;
                    $nuevoMarcado->setFecha($fecha);
                    $nuevoMarcado->setLongitud($longitud);
                    $nuevoMarcado->setLatitud($latitud);
                    $nuevoMarcado->setHora_inicio($hora_inicio);
                    $nuevoMarcado->setHora_final($hora_final);
                    $nuevoMarcado->setIdTipo_Marcaje($idTipo_marcaje);
                    $nuevoMarcado->setIdEMPLEADO($idEmpleado);

                    


                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));
                        //intento insertar el sede
                        $marcadoDB = $this->model->createMarcado($nuevoMarcado);

                        if ($marcadoDB instanceof Marcado) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $empleado = $marcadoDB->getIdEMPLEADO();
                        $tipo_marcaje= $marcadoDB->getIdTipo_Marcaje();
                        $salida= [
                            "id" => $marcadoDB->getIdMarcado(),
                            "fecha" => $marcadoDB->getfecha(),
                            "horaInicio" => $marcadoDB->getHora_inicio(),
                            "horaFinal" => $marcadoDB->getHora_final(),
                            "longitud" => $marcadoDB->getLongitud(),
                            "latitud" => $marcadoDB->getLatitud(),
                            "empleado" => [
                                'id' => $empleado->getIdEmpleado(),
                                'nombre' => $empleado->getNombre(),
                                'apellidos' => $empleado->getApellidos()
                            ],
                            "tipo_marcaje" =>$tipo_marcaje

                        ];

                            echo json_encode($salida);
                        } else {
                            //si no existe sede mando código 500
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el marcado"));
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
     * Borrar un sede de la bbdd
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
                //obtengo el sede
                $respuesta = $this->model->deleteMarcado($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Marcado eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el marcado"));
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
     * Actualiza un sede en la bdd
     */
    function update($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            $data = json_decode($json, true);
            if ($data != null && array_key_exists('id', $data)) {
               $empleado = array_key_exists('empleado', $data) ? $data['empleado'] : '';
               $tipo_marcaje =array_key_exists('tipo_marcaje', $data) ? $data['tipo_marcaje'] : '';
                $idMarcado= array_key_exists('id', $data) ? $data['id'] : '';
                $fecha = array_key_exists('fecha', $data) ? $data['fecha'] : '';
                $longitud = array_key_exists('longitud', $data) ? $data['longitud'] : '';
                $latitud = array_key_exists('latitud', $data) ? $data['latitud'] : '';
                $hora_inicio = array_key_exists('horaInicio', $data) ? $data['horaInicio'] : '';
                $hora_final = array_key_exists('horaFinal', $data) ? $data['horaFinal'] : '';
                $idTipo_marcaje = array_key_exists('id', $data) ? $data['id'] : '';
                $idEmpleado = array_key_exists('id', $empleado) ? $empleado['id'] : '';
                
                if (empty($idMarcado) ||empty($fecha)  || empty($hora_inicio) || empty($hora_final) || empty($idTipo_marcaje) || empty($idEmpleado)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                   
                    $marcadoActualizado = new Marcado;
                   
                    $marcadoActualizado->setFecha($fecha);
                    $marcadoActualizado->setLongitud($longitud);
                    $marcadoActualizado->setLatitud($latitud);
                    $marcadoActualizado->setHora_inicio($hora_inicio);
                    $marcadoActualizado->setHora_final($hora_final);
                    $marcadoActualizado->setIdTipo_Marcaje($idTipo_marcaje);
                    $marcadoActualizado->setIdEMPLEADO($idEmpleado);
                    $marcadoActualizado->setIdMarcado($idMarcado);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $marcadoDB = $this->model->updateMarcado($marcadoActualizado);

                        if ($marcadoDB instanceof Marcado) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                           
                            $empleado = $marcadoDB->getIdEMPLEADO();
                            $tipo_marcaje= $marcadoDB->getIdTipo_Marcaje();
                            array_push($salida, [
                                "id" => $marcadoDB->getIdMarcado(),
                                "fecha" => $marcadoDB->getfecha(),
                                "longitud" => $marcadoDB->getLongitud(),
                                "latitud" => $marcadoDB->getLatitud(),
                                "empleado" => [
                                    'id' => $empleado->getIdEmpleado(),
                                    'nombre' => $empleado->getNombre(),
                                    'apellidos' => $empleado->getApellidos()
                                ],
                                "tipo_marcaje" =>$tipo_marcaje
    
                            ]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar la sede"));
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