<?php
require_once('../init/config.php');
$category_name = $_POST['form_name'] ?? null;
$page = $_POST['page'] ?? 'public';
if(empty($category_name)){
    echo '404 Not Found';
    exit;
}

$method = $page == 'public' ? 'lists':'admin';
$ListController = new TM\ListController([
    'category_name' => $category_name,
    'category_page' => $category_name,
    'form_name' => $category_name,
    'lang' => $_POST['lang'],
    'method' => $_POST['method'],
]);
if(method_exists($ListController, $method)){
    $ListController->$method();
}else{
    echo '404 Not Found';
    exit;
}
