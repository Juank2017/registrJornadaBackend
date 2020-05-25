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
class empresas extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los empresas
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
                $empresas = $this->model->getEmpresas($pagina);
                if ($empresas != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = array("paginacion" => $empresas['paginacion'], "empresas" => $empresas['empresas']);

                    echo json_encode($salida);
                } else {
                    //si no hay usuarios mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado empresas"));
                }
            } catch (PDOException $e) {
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
     * Obtiene una empresa por el id
     */
    function empresa($token, $param)
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
                $empresaDB = $this->model->getEmpresaById($id);

                if ($empresaDB != null) {
                    //establecemos el código de estado 200->ok
                    http_response_code(200);
                    //formateamos la salida
                    $salida = [];

                    array_push($salida, ["id" => $empresaDB['id'], "nombre" => $empresaDB['nombre'], "cif" => $empresaDB['cif']]);

                    echo json_encode($salida);
                } else {
                    //si no existe usuario mando código 404
                    http_response_code(404);
                    echo json_encode(array("mensaje" => "No se han encontrado la empresa"));
                }
            } catch (PDOException $e) {
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
     * Crea una empresa
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
                $nombre = array_key_exists('nombre', $data) ?  $data['nombre'] : '';
                $cif = array_key_exists('cif', $data) ?  $data['cif'] : '';


                if (empty($nombre) || empty($cif)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {

                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        //intento insertar el usuario
                        $empresaDB = $this->model->createEmpresa($nombre, $cif);

                        if ($empresaDB != null) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida


                            $salida = array("id" => $empresaDB['id'], "nombre" => $empresaDB['nombre'], "cif" => $empresaDB['cif']);
                            echo json_encode($salida);
                        } else {
                            //si no existe usuario mando código 404
                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido insertar la empresa"));
                        }
                    } catch (PDOException $e) {
                        http_response_code(500);

                        // show error message
                        echo json_encode(array(
                            "message" => 'Error en la BBDD',
                            "error" => $e->getMessage()
                        ));
                    } catch (PDOException $e) {
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
     * Borra un rol
     */
    function delete($token, $param)
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
                $respuesta = $this->model->deleteEmpresa($id);

                switch ($respuesta) {
                    case "200":
                        echo json_encode(array("mensaje" => "Empresa eliminada correctamente"));
                        break;
                    case "400":
                        echo json_encode(array("mensaje" => "No se ha podido borrar la empresa"));
                        break;
                }
            } catch (PDOException $e) {
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
     * Actualiza una empresa en la bdd
     */
    function update($token)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            echo json_encode(array("mensaje" => 'Método no admitido'));
        } else {
            $json = file_get_contents('php://input');

            // echo $json;
            $data = json_decode($json, true);
            if ($data != null && array_key_exists('nombre', $data)) {
                $nombre = array_key_exists('nombre', $data) ?  $data['nombre'] : '';
                $cif = array_key_exists('cif', $data) ?  $data['cif'] : '';
                $id = array_key_exists('id', $data) ?  $data['id'] : '';

                if (empty($nombre) || empty($id) || empty($cif)) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "Faltan datos"));
                } else {



                    try {
                        //comprobamos que el token esté ok
                        $decoded = JWT::decode($token, constant('key'), array('HS256'));

                        $empresaDB = $this->model->updateEmpresa($id, $nombre, $cif);

                        if ($empresaDB != null) {
                            //establecemos el código de estado 200->ok
                            http_response_code(200);
                            //formateamos la salida
                            $salida = [];

                            array_push($salida, ["id" => $empresaDB['id'], "nombre" => $empresaDB['nombre'], "cif" => $empresaDB['cif']]);

                            echo json_encode($salida);
                        } else {

                            http_response_code(500);
                            echo json_encode(array("mensaje" => "No se ha podido actualizar la empresa"));
                        }
                    } catch (PDOException $e) {
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
}
