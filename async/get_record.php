<?php
require('../init/config.php');

try {
    if(!isset($_POST['id']) || !isset($_POST['table'])){
        throw new Exception('Error');
    }
    $Auth = new TM\Auth('array');
    $login = $Auth->checkExec();
    if ($login['result'] == 0) throw new Exception($login['msg']);
    $Post = new TM\Post;
    $id = $Post->gen('id');
    $table = $Post->gen('table');
    $result = 2;
    $msg = 'No Records';
    if(!empty($id)){
        $data = $Post->getTable($table);
        $colmns = [];
        foreach($data as $key => $value){
            $colmns[] = "`{$table}`.`{$key}`";
        }
        $sql = 'SELECT ';
        $sql .= implode(',', $colmns);
        if($table == 'category'){
            $sql .= ",`{$table}`.`created`,(SELECT COUNT(`content`.`id`) FROM `content` WHERE `content`.`category` = `category`.`id`) AS `content_count` FROM `{$table}` WHERE `{$table}`.`id` = ?";
        }elseif($table == 'account'){
            $sql .= ",`{$table}`.`created`,(SELECT COUNT(`content`.`id`) FROM `content` WHERE `content`.`author` = `account`.`id`) AS `content_count` FROM `{$table}` WHERE `{$table}`.`id` = ?";
        }else{
            $sql .= ",`{$table}`.`created` FROM `{$table}` WHERE `{$table}`.`id` = ?";
        }

        $DB = new TM\DB;
        $record = $DB->query($sql, [$id]);
        if(!empty($record)){
            foreach($data as $key => $value){
                $data[$key]['value'] = $record[0]->{$key};
            }
            $add = $Post->addColumn([
                'id' => $id,
                'table' => $table,
                'date' => $record[0]->created
            ]);
            if($table == 'category'){
                $add['delete_record'] = $record[0]->content_count == 0 ? 1:0;
            }elseif($table == 'account'){
                $add['delete_record'] = $record[0]->content_count == 0 ? 1:0;
            }

            $data = array_merge($data, $add);
            echo json_encode($data);
            exit;
        }
    }
    echo '{"result":'.$result.',"msg":"Not Found"}';

} catch (Exception $e) {

    echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';

}
