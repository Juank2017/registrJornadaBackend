<?php

class App{

    public function __construct(){
       // $url = isset($_GET['url'])? $_GET['url']: null;
        $url = (string)filter_input(INPUT_GET, 'url');
        $token= (string)filter_input(INPUT_GET,'token');
        $pagina= (string)filter_input(INPUT_GET,'pagina');
        $url = explode('/', rtrim($url, '/'));
       
        if(empty($url[0])){

            return false;
        }else{
            $archivoController = 'controller/' . $url[0] . '.php';
        }
 
        if(file_exists($archivoController)){
            require $archivoController;

            $controller = new $url[0];
            $controller->loadModel($url[0]);
            // Se obtienen el número de param
            $nparam = sizeof($url);
            // si se llama a un método
            if($nparam > 1){
                // hay parámetros
                if($nparam > 2){
                    $param = [];
                    for($i = 2; $i < $nparam; $i++){
                        array_push($param, $url[$i]);
                    }
                    $controller->{$url[1]}($token,$param);
                }else{
                    // solo se llama al método
                    $controller->{$url[1]}($token);
                }
            }else{
                // si se llama a un controlador
                $controller->index($token,$pagina);  
            }
        }else{
            http_response_code(404);
            echo json_encode(array("mensaje" => "No puedo encontrar ese recurso"));
        }
    }
    
}
