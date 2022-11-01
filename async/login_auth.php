<?php
require('../init/config.php');
try {
    if (isset($_POST['token']) && $_SESSION['csrf_token'] == $_POST['token']) {
        echo (new TM\Auth)->loginExec($_POST['account'], $_POST['passwd'], $_POST['redir_url']);
        exit;
    }
    throw new Exception('認証エラー');
} catch (Exception $e) {
    echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';
}
