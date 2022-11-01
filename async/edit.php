<?php
require('../init/config.php');
$method = $_POST['table'] ?? '';
$lang = $_POST['html_lang'] ?? '';
if($method && $lang){
    try {
        $Edit = new TM\Edit($lang);
        if(method_exists($Edit, $method)){
            $Edit->$method();
        }else{
            throw new Exception('Error');
        }
    } catch (Exception $e) {
        echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false!"}';
    }
}else{
    echo '{"result":0,"msg":"404 Not Found Edit method","class":"false"}';
}
