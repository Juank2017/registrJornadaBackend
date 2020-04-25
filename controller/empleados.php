<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,GET,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require_once 'config/config.php';
include_once 'vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once 'vendor/firebase/php-jwt/src/ExpiredException.php';
include_once 'vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once 'vendor/firebase/php-jwt/src/JWT.php';

use \Firebase\JWT\JWT;

/**
 * Resuelve las peticiones a la API y devuelve el contenido de la BBDD en formato JSON
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
    function index($token, $pagina)
    {        //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //extraemos los datos del modelo
                $empleados = $this->model->getEmpleados($pagina);
                //si vienen datos
                if ($empleados != null) {
               // var_dump($empleados);
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];
                    $empl = [];
                    array_push($salida,array("paginacion"=>$empleados['paginacion']));
                    foreach ($empleados['empleados'] as $key => $value) {
                        //var_dump($value);
                        $usuario = $value->getIdUsuario();
                        $sede = $value->getIdSede();
                        array_push($empl, [
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
                            "sede" => [
                                'id' => $sede->getIdSede(),
                                'nombre' => $sede->getNombre(),
                                
                            ]
                        ]);
                    }
                    $salida=array("paginacion"=>$empleados['paginacion'],'empleados'=> $empl);
                    echo json_encode($salida);
                } else {
                    //si no hay empleados mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado empleados"));
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
    function empresa($token, $param = [])
    {    //comprueba que sea una petición get
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $pagina= (string)filter_input(INPUT_GET,'pagina');
            //obtengo el id que viene en el array $param
            $id = count($param) > 0 ? $param[0] : "";
            try {
                //comprobamos que el token esté ok
                $decoded = JWT::decode($token, constant('key'), array('HS256'));
                //obtengo el empleado
                $empleados = $this->model->getEmpleadosByEmpresaId($pagina,$id);

                if ($empleados != null) {
                   //establecemos el código de estado 200->ok
                   http_response_code(200);
                   //formateamos la salida
                   $salida = [];
                   $empl = [];
                   array_push($salida,array("paginacion"=>$empleados['paginacion']));
                   foreach ($empleados['empleados'] as $key => $value) {
                       //var_dump($value);
                       $usuario = $value->getIdUsuario();
                     //  var_dump($usuario);
                       $sede = $value->getIdSede();
                       array_push($empl, [
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
                           "sede" => [
                               'id' => $sede->getIdSede(),
                               'nombre' => $sede->getNombre(),
                               
                           ]
                       ]);
                   }
                   $salida=array("paginacion"=>$empleados['paginacion'],'empleados'=> $empl);
                   echo json_encode($salida);
                } else {
                    //si no existe empleado mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el empleado"));
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
                    $sede = $empleadoDB->getIdSede();
                   $salida = array( 
                        "id" => $empleadoDB->getIdEmpleado(),
                        "nombre" => $empleadoDB->getNombre(),
                        "apellidos" => $empleadoDB->getApellidos(),
                        "dni" => $empleadoDB->getDni(),
                        "usuario" => [
                            'idUSUARIO' => $usuario->getIdUsuario(),
                            'login' => $usuario->getLogin(),
                            'password' =>$usuario->getPassword(),
                            'roles' => $usuario->getRoles(),
                            'empresas' => $usuario->getEmpresas()
                        ],
                        "turno" => $empleadoDB->getIdTurno(),
                        "sede" => [
                            'id' => $sede->getIdSEDE(),
                            'nombre'=> $sede->getNombre()
                        ]
                    );

                    echo json_encode($salida);
                } else {
                    //si no existe empleado mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado el empleado"));
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
            if ($data != null && array_key_exists('nombre', $data)) {
               // $empleado = array_key_exists('empleado', $data) ?  $data['empleado'] : '';
                $usuarioURL = $data['usuario'];
                $turno= $data['turno'];
                $sede= $data['sede'];
                $nombre = array_key_exists('nombre', $data) ? $data['nombre'] : '';
                $apellidos = array_key_exists('apellidos', $data) ? $data['apellidos'] : '';
                $dni = array_key_exists('dni', $data) ? $data['dni'] : '';
                $idUsuario = array_key_exists('idUSUARIO', $usuarioURL) ? $usuarioURL['idUSUARIO'] : '';
                $idTurno = array_key_exists('id', $turno) ? $turno['id'] : '';
                $idSede = array_key_exists('id', $sede) ? $sede['id'] : '';
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

                            array_push($salida, array(
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
                            ));

                            echo json_encode($salida);
                        } else {
                            //si no existe empleado mando código 500
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el empleado"));
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
     * Actualiza un empleado en la bdd
     */
    function update($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            $data = json_decode($json, true);
            if ($data != null && array_key_exists('id', $data)) {
                $idEmpleado = array_key_exists('id', $data) ?  $data['id'] : '';
                $turno = array_key_exists('turno', $data) ?  $data['turno'] : '';
                $sede = array_key_exists('sede', $data) ?  $data['sede'] : '';
                $nombre = array_key_exists('nombre', $data) ? $data['nombre'] : '';
                $apellidos = array_key_exists('apellidos', $data) ? $data['apellidos'] : '';
                $dni = array_key_exists('dni', $data) ? $data['dni'] : '';
               
                $idTurno = array_key_exists('id', $turno) ? $turno['id'] : '';
                $idSede = array_key_exists('id', $sede) ? $sede['id'] : '';
                if (empty($nombre) || empty($apellidos) || empty($dni) || empty($idTurno) || empty($idSede)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    $empleadoActualizado = new Empleado;
                    $empleadoActualizado->setIdEmpleado($idEmpleado);
                    $empleadoActualizado->setNombre($nombre);
                    $empleadoActualizado->setApellidos($apellidos);
                    $empleadoActualizado->setDni($dni);
                   
                    $empleadoActualizado->setIdTurno($idTurno);
                    $empleadoActualizado->setIdSede($idSede);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $empleadoDB = $this->model->updateEmpleado($empleadoActualizado);

                        if ($empleadoDB instanceof Empleado) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $usuario = $empleadoDB->getIdUsuario();

                            $salida = [
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
                            ];

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el empleado","empleadobd"=>$empleadoDB));
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
