<?php
require('../init/config.php');
try {
    $method = $_POST['method'] ?? '';
    $table = $_POST['table'] ?? '';
    if($method && $table){
        $DB = new TM\DB;
        if($method == 'sort'){
            if($_POST['id']){
                $ids = explode(',',$_POST['id']);
                $num = 1;
                foreach($ids as $id){
                    $DB->update($table, ['id' => $id ], ['num' => $num]);
                    $num++;
                }
                echo '{"result":1,"msg":"Updated","class":"true"}';
                exit;
            }
        }
    }
    throw new Exception('Error');
} catch (Exception $e) {
    echo '{"result":0,"msg":"' .$method.' : '.$table.' : '. $e->getMessage() . '","class":"false"}';
}
