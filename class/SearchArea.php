<?php
namespace TM;

class SearchArea
{
    public function createCategory($options)
    {
        $SearchForms = new SearchForms($options['page']);
        $search_list = [
            'hidden' => [
                'w' => 'w',
                'sp' => ['sp', 5],
                'p' => ['p', $SearchForms->getSS('p', 0)],
                'c' => ['c', $SearchForms->getSS('c', 'release_date')],
                's' => ['s', $SearchForms->getSS('s', 'DESC')]
            ],
            'btn' => [
                'add' => false,
                'csv' => false
            ]
        ];
        return $SearchForms->searchFormHidden($search_list);
    }

    public function createNavSearch($options)
    {
        $SearchForms = new SearchForms('public_keyword_search');
        $search_list = [
            'search_text' => $options['search_text'],
            'btn_class' => 'nav_',
            'hidden' => [
                'w' => 'nw',
                'sp' => ['nsp', 5],
                'p' => ['np', $SearchForms->getSS('np', 0)],
                'c' => ['nc', $SearchForms->getSS('nc', 'release_date')],
                's' => ['ns', $SearchForms->getSS('ns', 'DESC')]
            ],
            'btn' => [
                'add' => false,
                'csv' => false
            ]
        ];
        return $SearchForms->searchFormNav($search_list);
    }
}
