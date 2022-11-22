<?php
require('../init/config.php');
try {
    $result = 0;
    $msg = 'access error';
    $page = !empty($_POST['page']) ? $_POST['page']:'';
    if (!empty($page)) {
        $DB = new TM\DB;
        $result = $DB->updateConditionId('UPDATE `content` SET `id` = LAST_INSERT_ID(`id`),`access` = `access` + 1 WHERE `page` = ?', [$page]);
        if (is_numeric(trim($result)) && trim($result) > 0) {
            $DB->insert('access_log', ['content_id' => $result, 'page' => $page, 'access_datetime' => date('Y-m-d H:i:s'), 'ip' => $_SERVER['REMOTE_ADDR']]);
            $result = 1;
            $msg = 'Counted';
        }
    }
    echo '{"result":'.$result.',"msg":"'.$msg.'","table":"'.$result.'"}';
    exit;
} catch (Exception $e) {
    echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';
}
