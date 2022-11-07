<?php
namespace TM;
class AdminListModel
{
    private $SearchForms;
    private $Retrieval;
    private $replace = [];

    public function __construct($options = [])
    {
        $this->Retrieval = new Retrieval;
        $this->SearchForms = new SearchForms($options['form_name']);
        $this->replace['lang'] = $options['lang'] ?? 'ja';
        $this->replace['url'] = $options['url'];
        $this->replace['imagesurl'] = $options['imagesurl'];
    }

    private function getListRecord($options)
    {
        return $this->Retrieval->setLikeValue($options['sql_session']['like_value'])
        ->setConditions($options['sql_session']['conditions'])
        ->setConditionsOR($options['sql_session']['conditions_or'])
        ->setConditionsGOR($options['sql_session']['conditions_gor'])
        ->setLikeColumn($options['like_target'] ?? [])
        ->setOrderBy($options['sql_session']['order_by'])
        ->setOrderSort($options['sql_session']['order_sort'])
        ->setLimit(true)
        ->setRecordsNum($options['sql_session']['view_count'])
        ->setPageNum($options['sql_session']['page_num'])
        ->getRecords($options['sql'], []);
    }

    private function excute($options)
    {
        $method = $options['table'];
        $sql_session = $this->SearchForms->setPostSqlSession([
            'post' => $_POST,
            'table' => $method,
            'conditions' => $options['conditions'] ?? []
        ]);
        $this->replace['records'] = $this->getListRecord(
            [
                'sql' => (new AdminListSql)->$method(),
                'sql_session' => $sql_session,
                'like_target' => $options['like_target'] ?? []
            ]
        );
        $this->replace['sql_session'] = $sql_session;
        $this->replace['all_count'] = $this->Retrieval->getRecordsCount();

        return $this->replace;
    }

    public function category()
    {
        $method = __FUNCTION__;
        $options = [
            'table' => $method,
            'like_target' => [
                $method.'.page',
                $method.'.subject',
                $method.'.title'
            ]
        ];
        return $this->excute($options);
    }

    public function content()
    {
        $method = __FUNCTION__;
        $options = [
            'table' => $method,
            'like_target' => [
                $method.'.page',
                $method.'.title'
            ]
        ];
        return $this->excute($options);
    }

    public function account()
    {
        $method = __FUNCTION__;
        $options = [
            'table' => $method,
            'like_target' => [
                $method.'.name',
                $method.'.account'
            ]
        ];
        return $this->excute($options);
    }
}
