<?php
require('../init/config.php');
try {
    $Auth = new TM\Auth('array');
    $login = $Auth->checkExec();
    if ($login['result'] == 0) throw new Exception($login['msg']);
    $msg = 'Could Not Delete';
    $id = !empty($_POST['id']) ? $_POST['id']:null;
    $table = !empty($_POST['table']) ? $_POST['table']:null;
    $method = !empty($_POST['method']) ? $_POST['method']:null;
    $accept_table = [
        'category',
        'account',
        'content'
    ];
    if(!in_array($table, $accept_table, true) || empty($id) || $method != 'delete'){
        throw new Exception($msg);
    }
    $DB = new TM\DB;
    $result = $DB->delete($table, [ 'id' => $id ]);
    if (is_numeric(trim($result)) && trim($result) > 0) {
        if($table == 'content' || $table == 'account'){
            $FileCtl = new TM\FileCtl;
            $FileCtl->setFilePath(SERVER_DIR['IMG'].$table.'/'.$id);
            $FileCtl->deleteDir();
            if($table == 'content'){
                //access_log delete
                $DB->delete('access_log', [ 'content_id' => $id ]);
            }elseif($table == 'account'){
                $tbls = ['account','category','content','dictionary','setting','sidenav','withdrawal_modal'];
                $cols = ['created_id','modified_id'];
                //id change 1
                foreach($tbls as $tbl){
                    foreach($cols as $col){
                        $ret = $DB->query("SELECT $col FROM $tbl WHERE $col = ?", [$id]);
                        if($ret){
                            $DB->update($tbl, [$col => $id], [$col => 1]);
                        }
                    }
                }
            }
        }

        $msg = 'Deleted';
    }
    echo '{"result":"'.$result.'","msg":"'.$msg.'","class":"true","table":"'.$table.'","id":"'.$id.'","dir":"'.$table.'"}';
    exit;
} catch (Exception $e) {
    echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false+"}';
}
