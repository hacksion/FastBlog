<?php
require('../init/config.php');
$method = isset($_POST['table']) ? $_POST['table']:'';
$type = isset($_POST['type']) ? $_POST['type']:'';
if($method && $type){
    try {
        file_put_contents(SERVER_DIR[$type].$method, $_POST['file']);
        echo '{"result":1,"msg":"SUCCESS","class":"true"}';
    } catch (Exception $e) {
        echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';
    }
}else{
    echo '{"result":0,"msg":"404 Not Found method","class":"false"}';
}
