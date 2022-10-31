<?php
/******************************
 PDOラッパークラス
*******************************/
namespace TM;

abstract class DBConf
{
    /**
    * queryのアクセスタイプ
    * @access_type array
    */
    private $access_type = [
        'query',
        'update',
        'update_id',
        'insert',
        'delete',
        'duplicate',
        'fullsql',
    ];

    /**
    *  pdoアクセスリソース
    * @dbh
    */
    protected $dbh;

    /**
    *  トランザクションを利用　デフォルト（利用しない）
    * @transaction
    */
    private $transaction = false;

    /**
    *  取得したレコードの形式　（1= stdClass  2= array） デフォルト 1
    * @fetch_style
    */
    protected $fetch_style = 1;

    public function __construct()
    {
        try {
            $this->dbh = new \PDO(
                DATABASE_TYPE.':dbname='.DATABASE_NAME.';host='.DATABASE_HOST.';port=3306',DATABASE_USER,DATABASE_PASS
            );
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbh->query('SET NAMES UTF8');
            $this->dbh->query('SET group_concat_max_len = 65535');
        } catch (\PDOException $e) {
            throw new \Exception('DBERROR');
        }
    }

    public function __destruct()
    {
        $this->dbh = '';
    }

    public function setTransaction($boolean)
    {
        $this->transaction = $boolean;
    }

    public function setFetchStyle($value)
    {
        $this->fetch_style = $value;
    }

    /**
    *  queryの実行
    * @final
    * @execute
    * @param string type 'query', 'update', 'insert', 'delete' ,'duplicate'
    * @param string sql
    * @param array placeHolder
    * @return query -> stdClass | array  other -> int
    */
    final protected function execute($type, $sql, array $place_holder)
    {
        try {

            $type = mb_strtolower($type);
            $result = 0;
            if ($type && in_array($type, $this->access_type)) {
                if ($this->transaction && $type != 'query') {
                    $this->dbh->beginTransaction();
                }
                $sth = $this->dbh->prepare($sql);
                $ret = $sth->execute($place_holder);
                if ($type == 'query') {
                    $result = $this->fetch_style == 1 ?
                    $sth->fetchAll(\PDO::FETCH_CLASS, 'stdClass') : $sth->fetchAll(\PDO::FETCH_ASSOC);
                } else {
                    if ($this->transaction) {
                        $ret ? $this->dbh->commit() : $this->dbh->rollBack();
                    }
                    if ($type == 'insert' || $type == 'duplicate') {
                        $result = $ret ? $this->dbh->lastInsertId() : 0;
                    } elseif ($type == 'update') {
                        $result = $ret ? $sth->rowCount() : 0;
                    } elseif ($type == 'update_id') {
                        $result = $ret ? $this->dbh->lastInsertId() : 0;
                    } else {
                        $result = $ret ? $ret : 0;
                    }
                }
            }
            return $result;

        } catch (\PDOException $e) {

            if ($this->transaction && $type != 'query') {
                $this->dbh->rollBack();
            }
            throw new \Exception($e->getMessage() . ' : ' . $sql);

        }

    }
}
