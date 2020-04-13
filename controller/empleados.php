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
 * Description of empleado
 *
 * @author jcpm0
 */
class empleados extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los empleados
     * GET
     */
    function index($token)
    {        //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {

            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //extraemos los datos del modelo
                $empleados = $this->model->getEmpleados();
                //si vienen datos
                if ($empleados != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    foreach ($empleados as $key => $value) {
                        $usuario = $value->getIdUsuario();
                        array_push($salida, [
                            "id" => $value->getIdEmpleado(),
                            "nombre" => $value->getNombre(),
                            "apellidos" => $value->getApellidos(),
                            "dni" => $value->getDni(),
                            "usuario" => [
                                'id' => $usuario->getIdUsuario(),
                                'login' => $usuario->getLogin(),
                                'roles' => $usuario->getRoles(),
                                'empresas' => $usuario->getEmpresas()
                            ],
                            "turno" => $value->getIdTurno(),
                            "sede" => $value->getIdSede()
                        ]);
                    }

                    echo json_encode($salida);
                } else {
                    //si no hay empleados mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado empleados"));
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
     * Obtiene un empleado por id
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
                //obtengo el empleado
                $empleadoDB = $this->model->getEmpleadoById($id);

                if ($empleadoDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    $usuario = $empleadoDB->getIdUsuario();

                    array_push($salida, [
                        "id" => $empleadoDB->getIdEmpleado(),
                        "nombre" => $empleadoDB->getNombre(),
                        "apellidos" => $empleadoDB->getApellidos(),
                        "dni" => $empleadoDB->getDni(),
                        "empleado" => [
                            'id' => $usuario->getIdUsuario(),
                            'login' => $usuario->getLogin(),
                            'roles' => $usuario->getRoles(),
                            'empresas' => $usuario->getEmpresas()
                        ],
                        "turno" => $empleadoDB->getIdTurno(),
                        "sede" => $empleadoDB->getIdSede()
                    ]);

                    echo json_encode($salida);
                } else {
                    //si no existe empleado mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el empleado"));
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
     * Obtiene un empleado por id
     * GET
     */
    function findByLogin($token, $param = [])
    {    //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            //obtengo el id que viene en el array $param
            $login = count($param) > 0 ? $param[0] : "";
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el empleado
                $empleadoDB = $this->model->getEmpleadoByUserLogin($login);

                if ($empleadoDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    echo json_encode($empleadoDB);
                } else {
                    //si no existe empleado mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el empleado"));
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
     * Crea un empleado
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
            if ($data != null && array_key_exists('empleado', $data)) {
                $empleado = array_key_exists('empleado', $data) ?  $data['empleado'] : '';

                $nombre = array_key_exists('nombre', $empleado) ? $empleado['nombre'] : '';
                $apellidos = array_key_exists('apellidos', $empleado) ? $empleado['apellidos'] : '';
                $dni = array_key_exists('dni', $empleado) ? $empleado['dni'] : '';
                $idUsuario = array_key_exists('idUsuario', $empleado) ? $empleado['idUsuario'] : '';
                $idTurno = array_key_exists('idTurno', $empleado) ? $empleado['idTurno'] : '';
                $idSede = array_key_exists('idSede', $empleado) ? $empleado['idSede'] : '';
                if (empty($nombre) || empty($apellidos) || empty($dni) || empty($idUsuario) || empty($idTurno) || empty($idSede)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {


                    $nuevoEmpleado = new Empleado;
                    $nuevoEmpleado->setNombre($nombre);
                    $nuevoEmpleado->setApellidos($apellidos);
                    $nuevoEmpleado->setDni($dni);
                    $nuevoEmpleado->setIdUsuario($idUsuario);
                    $nuevoEmpleado->setIdTurno($idTurno);
                    $nuevoEmpleado->setIdSede($idSede);


                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));
                        //intento insertar el empleado
                        $empleadoDB = $this->model->createEmpleado($nuevoEmpleado);

                        if ($empleadoDB instanceof Empleado) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $usuario = $empleadoDB->getIdUsuario();

                            array_push($salida, [
                                "id" => $empleadoDB->getIdEmpleado(),
                                "nombre" => $empleadoDB->getNombre(),
                                "apellidos" => $empleadoDB->getApellidos(),
                                "dni" => $empleadoDB->getDni(),
                                "usuario" => [
                                    'id' => $usuario->getIdUsuario(),
                                    'login' => $usuario->getLogin(),
                                    'roles' => $usuario->getRoles(),
                                    'empresas' => $usuario->getEmpresas()
                                ],
                                "turno" => $empleadoDB->getIdTurno(),
                                "sede" => $empleadoDB->getIdSede()
                            ]);

                            echo json_encode($salida);
                        } else {
                            //si no existe empleado mando código 500
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el empleado"));
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
     * Borrar un empleado de la bbdd
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
                //obtengo el empleado
                $respuesta = $this->model->deleteEmpleado($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Empleado eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el empleado"));
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
     * Actualiza un empleado en la bdd
     */
    function update($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            $data = json_decode($json, true);
            if ($data != null && array_key_exists('empleado', $data)) {
                $empleado = array_key_exists('empleado', $data) ?  $data['empleado'] : '';

                $nombre = array_key_exists('nombre', $empleado) ? $empleado['nombre'] : '';
                $apellidos = array_key_exists('apellidos', $empleado) ? $empleado['apellidos'] : '';
                $dni = array_key_exists('dni', $empleado) ? $empleado['dni'] : '';
               
                $idTurno = array_key_exists('idTurno', $empleado) ? $empleado['idTurno'] : '';
                $idSede = array_key_exists('idSede', $empleado) ? $empleado['idSede'] : '';
                if (empty($nombre) || empty($apellidos) || empty($dni) || empty($idTurno) || empty($idSede)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    $empleadoActualizado = new Empleado;
                    $empleadoActualizado->setNombre($nombre);
                    $empleadoActualizado->setApellidos($apellidos);
                    $empleadoActualizado->setDni($dni);
                   
                    $empleadoActualizado->setIdTurno($idTurno);
                    $empleadoActualizado->setIdSede($idSede);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $empleadoDB = $this->model->updateUser($empleadoActualizado);

                        if ($empleadoDB instanceof Empleado) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $usuario = $empleadoDB->getIdUsuario();

                            array_push($salida, [
                                "id" => $empleadoDB->getIdEmpleado(),
                                "nombre" => $empleadoDB->getNombre(),
                                "apellidos" => $empleadoDB->getApellidos(),
                                "dni" => $empleadoDB->getDni(),
                                "empleado" => [
                                    'id' => $usuario->getIdUsuario(),
                                    'login' => $usuario->getLogin(),
                                    'roles' => $usuario->getRoles(),
                                    'empresas' => $usuario->getEmpresas()
                                ],
                                "turno" => $empleadoDB->getIdTurno(),
                                "sede" => $empleadoDB->getIdSede()
                            ]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el empleado"));
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
