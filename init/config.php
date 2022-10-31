<?php
/***************** ini setting ********************/
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL);
ini_set( 'error_log', 'php.log');
/*****************  session start ********************/
if(!isset($_SESSION))session_start();
/***************** language ********************/
mb_language('Japanese');
mb_internal_encoding('utf-8');
/***************** private dir ********************/
define('PRIVATE_DIR', dirname(__DIR__).'/');
/***************** CURRENT_DIR ********************/
define('CURRENT_DIR', 'blog');
/***************** URL ********************/
define('URL', (empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER['HTTP_HOST'].(CURRENT_DIR ? '/'.CURRENT_DIR:'').'/');
/***************** admin dir ********************/
define('ADMIN_DIR', 'sys_admin');
define('SYSTEM_NAME', 'Fast Blog');
/***************** template extension ********************/
define('TPL_EXT', '.html');
/***************** version ********************/
define('VERSION', '2.0.0');
/***************** key name ********************/
define('KEY_NAME', [
    'SESSION' => $_SERVER['HTTP_HOST'].'FastBlogAuth'
]);
/*****************  server path  ********************/
define('SERVER_DIR', [
    'CLASS' => PRIVATE_DIR.'class/',
    'INIT' => PRIVATE_DIR.'init/',
    'HTML' => PRIVATE_DIR.'html/',
    'ADMIN_HTML' => PRIVATE_DIR.'html/'.ADMIN_DIR.'/',
    'CSS' => PRIVATE_DIR.'css/',
    'IMG' => PRIVATE_DIR.'images/',
    'JS' => PRIVATE_DIR.'js/',
    'ASYNC' => PRIVATE_DIR.'async/'
]);
/*****************  public URL ********************/
define('PUBLIC_URL', [
    'ADMIN_ERROR' => URL . ADMIN_DIR . '/login',
    'ASYNC' => URL.'async/',
    'JS' => URL.'js/',
    'CSS' => URL.'css/',
    'IMG' => URL.'images/'
]);
/***************** database ********************/
require('db.php');
/***************** functions ********************/
require('functions.php');
/***************** composer autoload ********************/
require(PRIVATE_DIR.'vendor/autoload.php');
/*****************  original class auto loader ********************/
spl_autoload_register('autoloadClass');
