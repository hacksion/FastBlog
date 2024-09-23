<?php
/***************** server current path ********************/
define('PRIVATE_DIR', dirname(__DIR__).'/');
/***************** ini setting ********************/
ini_set( 'display_errors', 'Off' );
ini_set( 'error_reporting', E_ALL);
ini_set( 'error_log', PRIVATE_DIR . 'php.log');
/*****************  session start ********************/
if(!isset($_SESSION))session_start();
/***************** language ********************/
mb_language('Japanese');
mb_internal_encoding('utf-8');
/***************** system name ********************/
define('SYSTEM_NAME', 'Fast Blog');
/***************** admin dir ********************/
define('ADMIN_DIR', 'sys_admin');
/***************** template extension ********************/
define('TPL_EXT', '.html');
/***************** version ********************/
define('VERSION', '1.0.0');
/***************** key name ********************/
define('KEY_NAME', [
    'SESSION' => $_SERVER['HTTP_HOST'].'FastBlogAuth'
]);
/*****************  server path  ********************/
define('SERVER_DIR', [
    'CLASS' => PRIVATE_DIR.'class/',
    'INIT' => PRIVATE_DIR.'init/',
    'HTML' => PRIVATE_DIR.'html/',
    'CSS' => PRIVATE_DIR.'css/',
    'IMG' => PRIVATE_DIR.'images/',
    'JS' => PRIVATE_DIR.'js/',
]);
/***************** database ********************/
if(file_exists(SERVER_DIR['INIT'].'db.php')){
    require('db.php');
}
/***************** functions ********************/
require('functions.php');
/***************** composer autoload ********************/
require(PRIVATE_DIR . 'vendor/autoload.php');
/*****************  original class auto loader ********************/
spl_autoload_register('autoloadClass');
