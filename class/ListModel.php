<?php
namespace TM;
class ListModel
{
    private $SearchForms;
    private $Retrieval;
    private $replace = [];

    public function __construct($options)
    {
        $this->Retrieval = new Retrieval;
        $this->SearchForms = new SearchForms($options['form_name']);
        $category = '';
        if(isset($options['category_page'])){
            $category = (new DB)->query('SELECT `id` FROM `category` WHERE `page` = ?', [$options['category_page']]);
        }
        $this->replace['category_id'] = $category ? $category[0]->id:1;
        $this->replace['lang'] = $options['lang'] ?? 'ja';
        $this->replace['lang_list'] = (new LangList)->getLangList();
        $this->replace['date_format'] = $this->replace['lang'] == 'ja' ? 'Y年m月d日':'F j, Y';
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

    public function index()
    {
        $sql_session = $this->SearchForms->setPostSqlSession([
            'post' => $_POST,
            'table' => 'content',
            'conditions' => [
                "`content`.`publishing` = 1",
                "DATE_FORMAT(`content`.`release_date`, '%Y%m%d%H%i') < DATE_FORMAT(NOW(), '%Y%m%d%H%i')"
            ]
        ]);

        $sql = 'SELECT
        `content`.`id`,
        `content`.`release_date`,
        `content`.`category`,
        `content`.`page`,
        `content`.`title`,
        `content`.`html`,
        `content`.`thumbnail`,
        `account`.`name` AS `account_name`,
        `category`.`subject` AS `category_subject`,
        `category`.`page` AS `category_page`
        FROM `content`
        LEFT OUTER JOIN `account` ON (`content`.`author` = `account`.`id`)
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)';

        $this->replace['records'] = $this->getListRecord(
            [
                'sql' => $sql,
                'sql_session' => $sql_session,
                'like_target' => [
                    "`content`.`title`"
                ]
            ]
        );
        $this->replace['sql_session'] = $sql_session;
        $this->replace['all_count'] = $this->Retrieval->getRecordsCount();

        return $this->replace;
    }

    public function category()
    {
        $id = $this->replace['category_id'];

        $sql_session = $this->SearchForms->setPostSqlSession([
            'post' => $_POST,
            'table' => 'content',
            'conditions' => [
                "`content`.`category` = $id",
                "`content`.`publishing` = 1",
                "DATE_FORMAT(`content`.`release_date`, '%Y%m%d%H%i') < DATE_FORMAT(NOW(), '%Y%m%d%H%i')"
            ]
        ]);

        $sql = 'SELECT
        `content`.`id`,
        `content`.`release_date`,
        `content`.`category`,
        `content`.`page`,
        `content`.`title`,
        `content`.`html`,
        `content`.`thumbnail`,
        `account`.`name` AS `account_name`,
        `category`.`subject` AS `category_subject`,
        `category`.`page` AS `category_page`
        FROM `content`
        LEFT OUTER JOIN `account` ON (`content`.`author` = `account`.`id`)
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)';

        $this->replace['records'] = $this->getListRecord(
            [
                'sql' => $sql,
                'sql_session' => $sql_session,
                'like_target' => [
                    "`content`.`title`"
                ]
            ]
        );
        $this->replace['sql_session'] = $sql_session;
        $this->replace['all_count'] = $this->Retrieval->getRecordsCount();

        return $this->replace;
    }

    public function search()
    {
        $sql_session = $this->SearchForms->setPostSqlSession([
            'post' => $_POST,
            'table' => 'content',
            'conditions' => [
                "`content`.`publishing` = 1",
                "`content`.`publishing` = 1",
                "DATE_FORMAT(`content`.`release_date`, '%Y%m%d%H%i') < DATE_FORMAT(NOW(), '%Y%m%d%H%i')"
            ]
        ]);

        $sql = 'SELECT
        `content`.`id`,
        `content`.`release_date`,
        `content`.`page`,
        `content`.`title`,
        `content`.`thumbnail`,
        `category`.`page` AS `category_page`
        FROM `content`
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)
        ';

        $this->replace['records'] = $this->getListRecord(
            [
                'sql' => $sql,
                'sql_session' => $sql_session,
                'like_target' => [
                    "`content`.`title`"
                ]
            ]
        );
        $this->replace['sql_session'] = $sql_session;
        $this->replace['all_count'] = $this->Retrieval->getRecordsCount();

        return $this->replace;
    }
}
