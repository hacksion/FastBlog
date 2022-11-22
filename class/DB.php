<?php
namespace TM;

class DB extends DBConf
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function duplicate(string $sql, array $place_holder):int
    {
        return $this->execute(__FUNCTION__, $sql, $place_holder);
    }

    public function query(string $sql, array $place_holder)
    {
        return $this->execute(__FUNCTION__, $sql, $place_holder);
    }

    public function updateCondition(string $sql, array $place_holder=[]):int
    {
        return $this->execute('update', $sql, $place_holder);
    }

    public function updateConditionId(string $sql, array $place_holder=[]):int
    {
        return $this->execute('update_id', $sql, $place_holder);
    }

    public function fullsql(string $sql, array $place_holder=[])
    {
        return $this->execute(__FUNCTION__, $sql, $place_holder);
    }
    public function fullsqlInsert(string $sql, array $place_holder=[])
    {
        return $this->execute('insert', $sql, $place_holder);
    }

    public function insert(string $table, array $records):int
    {
        $colmun = [];
        $place = [];
        $data = [];
        $place_holder = [];
        $result = 0;
        foreach ($records as $key => $value) {
            $colmun[] = $key;
            $place[] = '?';
            $place_holder[] = $value;
        }
        $colmun = $colmun ? implode(',', $colmun) : '';
        $place = $place ? implode(',', $place) : '';
        if ($colmun && $place) {
            $sql = 'INSERT IGNORE INTO ' . $table . ' (' . $colmun . ') VALUE (' . $place . ')';
            $result = $this->execute(__FUNCTION__, $sql, $place_holder);
        }
        return $result;
    }

    public function update(string $table, array $target, array $records):int
    {
        $set_column = [];
        $place_holder = [];
        $target_set = [];
        $result = 0;
        foreach ($records as $key => $value) {
            $set_column[] = $key . '=?';
            $place_holder[] = $value;
        }
        foreach ($target as $key => $value) {
            $target_set[] = $key . '=?';
            $place_holder[] = $value;
        }
        $where = $target_set ? ' WHERE ' . implode(' AND ', $target_set) : '';
        $set_column = $set_column ? implode(',', $set_column) : '';
        if ($set_column && $where) {
            $sql = 'UPDATE ' . $table . ' SET ' . $set_column . $where;
            $result = $this->execute(__FUNCTION__, $sql, $place_holder);
        }
        return $result;
    }

    public function update_id(string $table, array $target, array $records):int
    {
        $set_column = [];
        $place_holder = [];
        $target_set = [];
        $result = 0;
        foreach ($records as $key => $value) {
            $set_column[] = $key . '=?';
            $place_holder[] = $value;
        }
        foreach ($target as $key => $value) {
            $target_set[] = $key . '=?';
            $place_holder[] = $value;
        }
        $where = $target_set ? ' WHERE ' . implode(' AND ', $target_set) : '';
        $set_column = $set_column ? implode(',', $set_column) : '';
        if ($set_column && $where) {
            $sql = 'UPDATE ' . $table . ' SET ' . $set_column . $where;
            $result = $this->execute(__FUNCTION__, $sql, $place_holder);
        }
        return $result;
    }

    public function delete(string $table, array $target, $add_target=null):int
    {
        $result = 0;
        if ($table && $target) {
            foreach ($target as $key => $value) {
                $target_set[] = $key . '=?';
                $place_holder[] = $value;
            }
            $sql = 'DELETE FROM ' . $table . ' WHERE ' . implode(' AND ', $target_set);
            if($add_target){
                $sql .= ' AND '.$add_target;
            }
            $result = $this->execute(__FUNCTION__, $sql, $place_holder);
            $this->execute(__FUNCTION__, "ALTER TABLE $table auto_increment = 1", []);
        }
        return $result;
    }

    public function createColumn(array $column, $table=null)
    {
        $result = [];
        foreach($column as $value){
            $result[] = $table ? '`'.$table.'`.`'.$value.'`':'`'.$value.'`';
        }
        return implode(',', $result);
    }

    public function getUniqNum(string $table_name, string $target_column, string $conditions):int
    {
        $result = 0;
        if ($table_name && $target_column) {
            $where_new = !empty($conditions) ? " WHERE $conditions" : '';
            $where_plus = !empty($conditions) ? $conditions .= " AND " : '';
            $sql = "SELECT IF(
                (SELECT count($target_column) FROM {$table_name}{$where_new}) = 0,1,(if((SELECT MIN($target_column)
                FROM {$table_name}{$where_new}) <> 1,1,MIN($target_column + 1)))) AS $target_column
                FROM $table_name
                WHERE $where_plus($target_column + 1) NOT IN (SELECT $target_column FROM {$table_name}{$where_new})";
            $uniqid = $this->execute('query', $sql, []);
            return $result = $uniqid[ 0 ]->$target_column;
        }
        return $result;
    }
}
