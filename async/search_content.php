<?php
require_once('../init/config.php');

$keyword = $_POST['nw'] ?? null;
if(!empty($keyword)){
    $ListController = new TM\ListController([
        'lang' => $_POST['lang'] ?? 'ja',
        'form_name' => 'public_keyword_search',
        'url' => $_POST['url'],
        'imagesurl' => $_POST['imagesurl']
    ]);
    $ListController->search();
}
