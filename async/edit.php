<?php
require('../init/config.php');
$method = isset($_POST['table']) ? $_POST['table']:'';
$lang = isset($_POST['html_lang']) ? $_POST['html_lang']:'en';
$Edit = new TM\Edit($lang);
if($method && method_exists($Edit, $method)){
    try {
        $Edit->$method();
    } catch (Exception $e) {
        echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false!"}';
    }
}else{
    echo '{"result":0,"msg":"404 Not Found Edit method","class":"false"}';
}
