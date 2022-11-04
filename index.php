<?php
require('./init/config.php');
$set_options['method'] = $method = 'index';
$set_options['category_page'] = 'index';
$options = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));
$options = defined('CURRENT_DIR') ? array_values(array_diff($options, explode('/', CURRENT_DIR))):$options;
if($options){
    if($options[0] == 'api'){
        $method = 'api';
        array_shift($options);
        $set_options = $options;
    }elseif($options[0] == ADMIN_DIR){
        $method = 'admin';
        array_shift($options);
        if(isset($options[0])){
            $set_options['method'] = $options[0];
            array_shift($options);
        }
        $set_options['category_page'] = $options;
    }else{
        $set_options['method'] = $method = count($options) > 1 ? 'content':'category';
        $set_options['category_page'] = $options[0];
        if(count($options) > 1){
            $set_options['content_page'] = $options[1];
        }
    }
}
(new TM\Controller($set_options))->$method();
