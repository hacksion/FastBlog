<?php
require('./init/resource.php');
require('./init/config.php');
$resource = [
    'PUBLIC_URL' => PUBLIC_URL,
    'method' => 'index',
    'category_page' => 'index'
];
$method = 'index';
$options = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));
$options = $resource['PUBLIC_URL']['CURRENT_DIR'] ? array_values(array_diff($options, explode('/', $resource['PUBLIC_URL']['CURRENT_DIR']))):$options;
if($options){
    if($options[0] == 'api'){
        $method = 'api';
        array_shift($options);
        $resource['method'] = $options[0];
        array_shift($options);
        $resource['type'] = $options[0];
        array_shift($options);
        $resource['options'] = $options;
    }elseif($options[0] == ADMIN_DIR){
        $method = 'admin';
        array_shift($options);
        if(isset($options[0])){
            $resource['method'] = $options[0];
            array_shift($options);
        }
        $resource['category_page'] = $options;
    }else{
        $resource['method'] = $method = count($options) > 1 ? 'content':'category';
        $resource['category_page'] = $options[0];
        if(count($options) > 1){
            $resource['content_page'] = $options[1];
        }
    }
}
(new TM\Controller($resource))->$method();
