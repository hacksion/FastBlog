<?php
require('../init/config.php');
$method = isset($_POST['table']) ? $_POST['table']:'';
$KeyUpEdit = new TM\KeyUpEdit;
if($method && method_exists($KeyUpEdit, $method)){
    try {
        $KeyUpEdit->$method();
    } catch (Exception $e) {
        echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false!"}';
    }
}else{
    echo '{"result":0,"msg":"404 Not Found Edit method","class":"false"}';
}
