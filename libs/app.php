<?php

//require_once 'controllers/errores.php';

class App{

    public function __construct(){
       // $url = isset($_GET['url'])? $_GET['url']: null;
        $url = (string)filter_input(INPUT_GET, 'url');
        $token= (string)filter_input(INPUT_GET,'token');
      
        $url = explode('/', rtrim($url, '/'));

        if(empty($url[0])){
//            $archivoController = 'controllers/index.php';
//            require $archivoController;
//            $controller = new Index();
//           // $controller->render();
//            $controller->loadModel('index');
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
                   
               
                    $controller->{$url[1]}($param,$token);
                }else{
                   
                    // solo se llama al método
                    $controller->{$url[1]}($token);
                }
            }else{
                // si se llama a un controlador
                $controller->render();  
            }
        }else{
            //$controller = new Errores();
        }
    }
    
}
?>
