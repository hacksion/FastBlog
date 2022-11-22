<?php
require('../init/config.php');
if(!empty($_POST['reset'])){
    unset($_SESSION[$_POST['reset']]);
    echo '{"result":1,"msg":"reset","class":"true"}';
    exit;
}
echo '{"result":0,"msg":"error","class":"true"}';
