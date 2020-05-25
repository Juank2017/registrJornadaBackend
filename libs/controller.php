<?php


class Controller{

    function __construct(){

    }

    function loadModel($model){
        $url = 'model/'.$model.'model.php';

        if(file_exists($url)){
            require $url;
            
            $modelName = $model.'Model';
            $this->model = new $modelName();
        }
    }
}

