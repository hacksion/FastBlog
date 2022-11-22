<?php
require('../init/config.php');
$method = $_POST['table'] ?? '';
if($method){
    try {
        $KeyUpEdit = new TM\KeyUpEdit;
        if(method_exists($KeyUpEdit, $method)){
            $KeyUpEdit->$method();
        }else{
            throw new Exception('Error');
        }
        
    } catch (Exception $e) {
        echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false!"}';
    }
}else{
    echo '{"result":0,"msg":"404 Not Found Edit method","class":"false"}';
}
