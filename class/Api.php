<?php
namespace TM;
class Api
{
    private $DB;
    private $options = [];

    public function __construct($options)
    {
        $this->DB = new DB;
        $this->DB->setFetchStyle(2);
        $this->options = $options;
    }

    public function get()
    {
        $method = isset($this->options[0]) ? array_shift($this->options):'unknown';
        if(method_exists(get_class(), $method)){
            $this->$method($this->options);
        }
    }

    public function content($options)
    {
        $result = [
            'date' => '',
            'title' => '',
            'thumbnail' => '',
            's_thumbnail' => '',
            'author' => '',
            'category' => '',
            'body' => ''
        ];
        $sql = 'SELECT
        `content`.`id`,
        `content`.`release_date`,
        `content`.`title`,
        `content`.`thumbnail`,
        `account`.`name` AS `author_name`,
        `category`.`page` AS `category_page`,
        `category`.`subject` AS `category_subject`,
        `content`.`html`
        FROM `content`
        LEFT OUTER JOIN `account` ON (`content`.`author` = `account`.`id`)
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)
        WHERE
        `content`.`publishing` = 1 AND
        DATE_FORMAT(`content`.`release_date`, "%Y%m%d%H%i") < DATE_FORMAT(NOW(), "%Y%m%d%H%i") AND
        `content`.`page` = ?
        ';
        $record = $this->DB->query($sql, [$options[0]]);
        if($record){
            $result['date'] = $record[0]['release_date'];
            $result['title'] = $record[0]['title'];
            if($record[0]['thumbnail']){
                $result['thumbnail'] = PUBLIC_URL['IMG'].'content/'.$record[0]['id'].'/'.$record[0]['thumbnail'];
                $result['s_thumbnail'] = PUBLIC_URL['IMG'].'content/'.$record[0]['id'].'/s_'.$record[0]['thumbnail'];
            }
            $result['author'] = $record[0]['author_name'];
            $result['category'] = $record[0]['category_page'];
            $result['body'] = htmlspecialchars($record[0]['html']);
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function category($options)
    {
        $page = $options[0] ?? 'index';
        $limit = $options[1] ?? 10;
        $result = [
            'page' => '',
            'subject' => '',
            'h1' => '',
            'contents' => []
        ];

        $category = $this->DB->query('SELECT `id`,`subject`,`h1` FROM `category` WHERE `page` = ?', [$page]);
        if($category){
            $cat = $page == 'index' ? '':' AND `content`.`category` = ?';
            $pls = $page == 'index' ? []:[$category[0]['id']];
            $result['page'] = $page;
            $result['subject'] = $category[0]['subject'];
            $result['h1'] = $category[0]['h1'];
            $sql = 'SELECT
            `content`.`id`,
            `content`.`page`,
            `content`.`release_date`,
            `content`.`title`,
            `content`.`thumbnail`,
            `account`.`name` AS `author_name`
            FROM `content`
            LEFT OUTER JOIN `account` ON (`content`.`author` = `account`.`id`)
            WHERE
            `content`.`publishing` = 1 AND
            DATE_FORMAT(`content`.`release_date`, "%Y%m%d%H%i") < DATE_FORMAT(NOW(), "%Y%m%d%H%i")'.$cat.'
            ORDER BY `content`.`release_date` DESC LIMIT '.$limit;
            $record = $this->DB->query($sql, $pls);
            foreach($record as $value){
                $result['contents'][] = [
                    'date' => $value['release_date'],
                    'page' => $value['page'],
                    'title' => $value['title'],
                    'author' => $value['author_name'],
                    'thumbnail' => $value['thumbnail'] ? PUBLIC_URL['IMG'].'content/'.$value['id'].'/'.$value['thumbnail']:'',
                    's_thumbnail' => $value['thumbnail'] ? PUBLIC_URL['IMG'].'content/'.$value['id'].'/s_'.$value['thumbnail']:'',
                    'url' => URL.$value['page']
                ];
            }
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}
