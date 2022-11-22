<?php
namespace TM;

class AdminListSql
{
    public function category()
    {
        $sql = 'SELECT
        `category`.`id`,
        `category`.`page`,
        `category`.`subject`,
        `category`.`title`,
        `category`.`description`,
        `category`.`main_nav`,
        `category`.`num`,
        (SELECT COUNT(`content`.`id`) FROM `content` WHERE `content`.`category` = `category`.`id`) AS `content_count`
        FROM `category`
        ';
        return $sql;
    }

    public function content()
    {
        $sql = 'SELECT
        `content`.`id`,
        `content`.`created`,
        `content`.`page`,
        `content`.`title`,
        `content`.`publishing`,
        `content`.`release_date`,
        `content`.`access`,
        `category`.`subject` AS category_subject,
        `content`.`author`,
        `account`.`auth` AS auth,
        `account`.`name` AS author_name
        FROM `content`
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)
        LEFT OUTER JOIN `account` ON (`content`.`author` = `account`.`id`)
        ';
        return $sql;
    }

    public function account()
    {
        $sql = 'SELECT
        `account`.`id`,
        `account`.`created`,
        `account`.`account`,
        `account`.`name`,
        `account`.`icon`,
        `account`.`auth`,
        `account`.`del`
        FROM `account`
        ';
        return $sql;
    }
}
