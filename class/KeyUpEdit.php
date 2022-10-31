<?php
namespace TM;

class KeyUpEdit
{
    private $login = [];
    private $DB = '';
    private $records = [];
    private $result = [
        'result' => 0,
        'msg' => 'Update Failed',
        'name' => '',
        'key' => '',
        'id' => '',
        'value' => '',
    ];
    public function __construct()
    {
        try {
            $this->login = (new Auth('array'))->checkExec();
            if ($this->login['result'] == 0) throw new \Exception($this->login['msg']);
            $this->result['table'] = $_POST['table'];
            $name_tmp = explode('_', $_POST['name']);
            $this->result['key'] = $name_tmp[0];
            $this->result['id'] = $name_tmp[1];
            $this->result['value'] = $_POST['value'];
            $this->DB = new DB;
        } catch (\Exception $e) {
            echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';
            exit;
        }
    }

    private function resultJson()
    {
        $this->result['msg'] = 'No Change In Contents';
        if (is_numeric(trim($this->result['result'])) && trim($this->result['result']) > 0) {
            $this->result['result'] = 1;
            $this->result['msg'] = 'Created';
            if($this->result['id']){
                $this->DB->update(
                    $this->result['table'],
                    ['id' => $this->result['id']],
                    ['modified' => date('Y-m-d H:i:s'), 'modified_id' => $this->login['id']]
                );
                $this->result['msg'] = 'Updated';
            }
        }else{
            $this->result['result'] = 2;
        }
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    public function dictionary()
    {
        if($this->result['value']){
            $this->result['result'] = $this->DB->fullsql("UPDATE `dictionary` SET `key_value` = JSON_SET(`key_value`, '$.".$this->result['key']."', ?) WHERE id = ?", [$this->result['value'], $this->result['id']]);
        }
        $this->resultJson();
    }

}
