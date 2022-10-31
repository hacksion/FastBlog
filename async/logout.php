<?php
require('../init/config.php');
try {
    $token = !empty($_POST['token']) ? $_POST['token']:null;
    if($token){
        (new TM\Auth)->logout();
        echo '{"result":1,"msg":"Logout OK","class":"false"}';
        exit;
    }
    throw new Exception('ブラウザーをリロードしてください');
} catch (Exception $e) {
    echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';
}
