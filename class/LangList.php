<?php
namespace TM;

class LangList
{
    public function __construct()
    {

    }
    public function getLangList()
    {
        $result = [];
        $dictionary = (new DB)->query('SELECT `key_name`,`key_value` FROM `dictionary`', []);
        foreach($dictionary as $value){
            $result[$value->key_name] = json_decode($value->key_value, true);
        }
        return $result;
    }
}
