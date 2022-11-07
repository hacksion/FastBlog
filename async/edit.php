<?php
require('../init/config.php');
$method = $_POST['table'] ?? '';
$options = [
    'lang' => ($_POST['html_lang'] ?? 'ja'),
    'url' => ($_POST['url'] ?? '')
];
if($method){
    try {
        $Edit = new TM\Edit($options);
        if(method_exists($Edit, $method)){
            $Edit->$method();
        }else{
            throw new Exception('Error');
        }
    } catch (Exception $e) {
        echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false!"}';
    }
}else{
    echo '{"result":0,"msg":"Not Found Edit method","class":"false"}';
}
