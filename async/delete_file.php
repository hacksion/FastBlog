<?php
require('../init/config.php');
try {
    $Auth = new TM\Auth('array');
    $login = $Auth->checkExec();
    if ($login['result'] == 0) throw new Exception($login['msg']);
    $result = 0;
    $msg = 'Could Not Delete';
    $id = !empty($_POST['id']) ? $_POST['id']:null;
    $name = !empty($_POST['name']) ? $_POST['name']:null;
    $method = !empty($_POST['method']) ? $_POST['method']:null;
    $table = !empty($_POST['table']) ? $_POST['table']:null;
    $column = !empty($_POST['col']) ? $_POST['col']:null;
    $type = !empty($_POST['type']) ? $_POST['type']:null;
    if(empty($id) || empty($name) || empty($type) || empty($table) || empty($column) || $method != 'delete_file'){
        throw new Exception($msg);
    }
    $DB = new TM\DB;
    $file_path = $table.'/'.$id.'/'.$name;
    $path = $type == 'images' ? SERVER_DIR['IMG'].$file_path:SERVER_DIR['FILE'].$file_path;
    $record = $DB->query('SELECT '.$column.' FROM '.$table.' WHERE id = ?', [$id]);
    if($record){
        $files = !empty($record[0]->{$column}) ? explode(',', $record[0]->{$column}):[];
        $files_reset = [];
        foreach($files as $value){
            if($value != $name) $files_reset[] = $value;
        }
        $files_reset = !empty($files_reset) ? implode(',', $files_reset):null;
        $DB->update($table, ['id' => $id], [$column => $files_reset]);
        unlink($path);
        if($type == 'images' && file_exists(SERVER_DIR['IMG'].$table.'/'.$id.'/s_'.$name)){
            unlink(SERVER_DIR['IMG'].$table.'/'.$id.'/s_'.$name);
        }
        $result = 1;
        $msg = 'Deleted';
    }
    echo '{"result":"'.$result.'","msg":"'.$msg.'","column":"'.$column.'","files":"'.$files_reset.'","table":"'.$table.'"}';
    exit;

} catch (Exception $e) {

    echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';

}
