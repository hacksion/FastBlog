<?php
$CURRENT_DIR = trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$URL = (empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER['HTTP_HOST'].'/'.($CURRENT_DIR ? $CURRENT_DIR.'/':'');
/***************** public url ********************/
define('PUBLIC_URL', [
	'URL' => $URL,
	'CURRENT_DIR' => $CURRENT_DIR,
    'ASYNC' => $URL.'async/',
    'JS' => $URL.'js/',
    'CSS' => $URL.'css/',
    'IMG' => $URL.'images/'
]);

