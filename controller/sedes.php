<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,PUT");
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
class sedes extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los sedes
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
                $sedes = $this->model->getSedes($pagina);
                //si vienen datos
                if ($sedes != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $sedes1=[];
                   
                    foreach ($sedes['sedes'] as $key => $value) {
                        $empresa = $value->getIdEmpresa();
                        array_push($sedes1,array(
                            "id" => $value->getIdSede(),
                            "nombre" => $value->getNombre(),
                            "direccion" => $value->getDireccion(),
                            "longitud" => $value->getLongitud(),
                            "latitud" => $value->getLatitud(),
                            "empresa" => [
                                'id' => $empresa['id'],
                                'nombre' => $empresa['nombre'],
                                'cif' => $empresa['cif']
                            ]

                        ));
                    }
                    
                    $salida=array("paginacion"=>$sedes['paginacion'],'sedes'=> $sedes1);
                    echo json_encode($salida);
                } else {
                    //si no hay sedes mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado sedes"));
                }
            } catch(PDOException $e){
                http_response_code(500);

                // show error message
                echo json_encode(array(
                    "message" => 'Error en la BBDD',
                    "error" => $e->getMessage()
                ));
            }
            catch (Exception $e) {
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
    function sede($token, $param = [])
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
                $sedeDB = $this->model->getSedeById($id);

                if ($sedeDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    
                    $empresa = $sedeDB->getIdEmpresa();
                    $salida= array('sede'=> [
                        "id" => $sedeDB->getIdSede(),
                        "nombre" => $sedeDB->getNombre(),
                        "direccion" => $sedeDB->getDireccion(),
                        "longitud" => $sedeDB->getLongitud(),
                        "latitud" => $sedeDB->getLatitud(),
                        "empresa" => [
                            'id' => $empresa['id'],
                            'nombre' => $empresa['nombre'],
                            'cif' => $empresa['cif']
                        ]

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
            }
             catch (Exception $e) {
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
     * Obtiene un sede por id de empresa
     * GET
     */
    function empresa($token, $param = [])
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
                $sedes = $this->model->getSedesByEmpresaId($id);

                if ($sedes != null) {
                   //establecemos el código de estado 200->ok
                   http_response_code(200);
                   //formateamos la salida
                   $sedes1=[];
                  
                   foreach ($sedes as $key => $value) {
                       $empresa = $value->getIdEmpresa();
                       array_push($sedes1,array(
                           "id" => $value->getIdSede(),
                           "nombre" => $value->getNombre(),
                           "direccion" => $value->getDireccion(),
                           "longitud" => $value->getLongitud(),
                           "latitud" => $value->getLatitud(),
                           "empresa" => [
                               'id' => $empresa['id'],
                               'nombre' => $empresa['nombre'],
                               'cif' => $empresa['cif']
                           ]

                       ));
                   }
                   
                   
                   echo json_encode($sedes1);
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
            }
             catch (Exception $e) {
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
            if ($data != null && array_key_exists('longitud', $data)) {
                $empresa = array_key_exists('empresa', $data) ?  $data['empresa'] : '';

                $nombre = array_key_exists('nombre', $data) ? $data['nombre'] : '';
                $longitud = array_key_exists('longitud', $data) ? $data['longitud'] : '';
                $latitud = array_key_exists('latitud', $data) ? $data['latitud'] : '';
                $direccion = array_key_exists('direccion', $data) ? $data['direccion'] : '';
                $idEmpresa = array_key_exists('id', $empresa) ? $empresa['id'] : '';
            
                
                if (empty($nombre) || empty($longitud) || empty($latitud) || empty($direccion) || empty($idEmpresa)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {


                    $nuevoSede = new Sede;
                    $nuevoSede->setNombre($nombre);
                    $nuevoSede->setLongitud($longitud);
                    $nuevoSede->setLatitud($latitud);
                    $nuevoSede->setDireccion($direccion);
                    $nuevoSede->setIdEMPRESA($idEmpresa);
                    


                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));
                        //intento insertar el sede
                        $sedeDB = $this->model->createSede($nuevoSede);

                        if ($sedeDB instanceof Sede) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $empresa = $sedeDB->getIdEmpresa();
                            array_push($salida, [
                                "id" => $sedeDB->getIdSede(),
                                "nombre" => $sedeDB->getNombre(),
                                "longitud" => $sedeDB->getLongitud(),
                                "latitud" => $sedeDB->getLatitud(),
                                "empresa" => [
                                    'id' => $empresa['id'],
                                    'nombre' => $empresa['nombre'],
                                    'cif' => $empresa['cif']
                                ]
        
                            ]);

                            echo json_encode($salida);
                        } else {
                            //si no existe sede mando código 500
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar el sede"));
                        }
                    }catch(PDOException $e){
                        http_response_code(500);

                        // show error message
                        echo json_encode(array(
                            "message" => 'Error en la BBDD',
                            "error" => $e->getMessage()
                        ));
                    }
                     catch (Exception $e) {
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
                $respuesta = $this->model->deleteSede($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Sede eliminado correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar el sede"));
                        break;
                }
            }catch(PDOException $e){
                http_response_code(500);

                // show error message
                echo json_encode(array(
                    "message" => 'Error en la BBDD',
                    "error" => $e->getMessage()
                ));
            }
             catch (Exception $e) {
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
            if ($data != null && array_key_exists('longitud', $data)) {
              
                $idSede= array_key_exists('id', $data) ? $data['id'] : '';
                $empresa = array_key_exists('empresa', $data) ?  $data['empresa'] : '';

                $nombre = array_key_exists('nombre', $data) ? $data['nombre'] : '';
                $longitud = array_key_exists('longitud', $data) ? $data['longitud'] : '';
                $latitud = array_key_exists('latitud', $data) ? $data['latitud'] : '';
                $direccion = array_key_exists('direccion', $data) ? $data['direccion'] : '';
                $idEmpresa = array_key_exists('id', $empresa) ? $empresa['id'] : '';
                
                if (empty('idSede') || empty($nombre) || empty($longitud) || empty($latitud) || empty($direccion) || empty($idEmpresa)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                   
                    $sedeActualizado = new Sede;
                    $sedeActualizado->setNombre($nombre);
                    $sedeActualizado->setLongitud($longitud);
                    $sedeActualizado->setLatitud($latitud);
                    $sedeActualizado->setDireccion($direccion);
                    $sedeActualizado->setIdEMPRESA($idEmpresa);
                    $sedeActualizado->setIdSede($idSede);

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $sedeDB = $this->model->updateSede($sedeActualizado);

                        if ($sedeDB instanceof Sede) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            $salida = [];
                            $empresa = $sedeDB->getIdEmpresa();
                            array_push($salida, [
                                "id" => $sedeDB->getIdSede(),
                                "nombre" => $sedeDB->getNombre(),
                                "longitud" => $sedeDB->getLongitud(),
                                "latitud" => $sedeDB->getLatitud(),
                                "empresa" => [
                                    'id' => $empresa['id'],
                                    'nombre' => $empresa['nombre'],
                                    'cif' => $empresa['cif']
                                ]
        
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
                    }
                    catch (Exception $e) {
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
