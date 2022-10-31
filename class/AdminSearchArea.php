<?php
namespace TM;

class AdminSearchArea
{
    private $replace = [];

    public function __construct($options = [])
    {
        $this->replace = $options;
    }

    private function excute($options)
    {
        $SearchForms = new SearchForms($options['table']);
        $search_list = [
            'hidden' => [
                'w' => 'w',
                'sp' => [$options['sp'][0], $options['sp'][1]],
                'p' => [$options['p'][0], $SearchForms->getSS($options['p'][0], $options['p'][1])],
                'c' => [$options['c'][0], $SearchForms->getSS($options['c'][0], $options['c'][1])],
                's' => [$options['s'][0], $SearchForms->getSS($options['s'][0], $options['s'][1])]
            ],
            'btn' => [
                'add' => $options['btn_add'],
                'edit_url' => $options['edit_url'],
                'table' => $options['table'],
                'csv' => $options['btn_csv']
            ],
            'replace' => $this->replace
        ];
        $parts = [];
        if(isset($options['parts'])){
            foreach($options['parts'] as $method => $options){
                $parts['parts'][$method] = $SearchForms->$method($options);
            }
        }
        return $SearchForms->searchForm($search_list += $parts);
    }

    public function category()
    {
        $options = [
            'table' => 'category',
            'sp' => ['sp', 10],
            'p' => ['p', 0],
            'c' => ['c', 'num'],
            's' => ['s', 'ASC'],
            'btn_add' => true,
            'btn_csv' => false,
            'edit_url' => '',
            'parts' => ['w' => []]
        ];
        return $this->excute($options);
    }

    public function content()
    {
        $options = [
            'table' => 'content',
            'sp' => ['sp', 10],
            'p' => ['p', 0],
            'c' => ['c', 'release_date'],
            's' => ['s', 'DESC'],
            'edit_url' => 'content/edit',
            'btn_add' => true,
            'btn_csv' => false,
            'parts' => [ 'cat' => ['placeholder' => 'カテゴリー'], 'w' => []]
        ];
        return $this->excute($options);
    }

    public function account()
    {
        $options = [
            'table' => __FUNCTION__,
            'sp' => ['sp', 10],
            'p' => ['p', 0],
            'c' => ['c', 'created'],
            's' => ['s', 'DESC'],
            'edit_url' => '',
            'btn_add' => true,
            'btn_csv' => false,
            'parts' => ['w' => []]
        ];
        return $this->excute($options);
    }
}
