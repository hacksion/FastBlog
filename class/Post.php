<?php
namespace TM;

class Post
{
    private $inputValue = ['type' => 'input', 'value' => ''];
    private $selectValue = ['type' => 'select', 'value' => ''];
    private $radioValue = ['type' => 'radio', 'value' => ''];
    private $checkboxValue = ['type' => 'checkbox', 'value' => ''];
    private $textareaValue = ['type' => 'textarea', 'value' => ''];
    private $idValue = ['type' => 'id', 'value' => ''];
    private $inputarrayValue = ['type' => 'input_array', 'value' => ''];

    public function gen($key, $null=null)
    {
        $result = $null;
        if ($key && (!empty($_POST[$key]) || $_POST[$key] == 0)){
            if(is_array($_POST[$key])){
                $result = !empty($_POST[$key]) ? implode(',', $_POST[$key]):$null;
            }else{
                $result = trim($_POST[$key]);
                $result = preg_replace('/\t/s', ' ', $result);
            }
        }
        return $result;
    }

    public function getTable($table)
    {
        return $this->getColumn($table);
    }

    public function setTable($table)
    {
        return $this->setColumn($table);
    }

    public function addColumn($options)
    {
        $result = [
            'id' => ['type' => 'input', 'value' => $options['id']],
            'delete_record' => 1,
            'table_name' => $options['table'],
            'result' => 1,
            'msg' => 'Records Fetched'
        ];
        return $result;
    }

    private function getColumn($table)
    {
        $col = [
            'setting' => [
                'site_name' => $this->inputValue,
                'index_title' => $this->inputValue,
                'index_description' => $this->inputValue,
                'copyright_url' => $this->inputValue,
                'lang' => $this->selectValue,
                'facebook' => $this->inputValue,
                'instagram' => $this->inputValue,
                'linkedin' => $this->inputValue,
                'twitter' => $this->inputValue,
                'google_tag' => $this->textareaValue,
                'google_tag_no' => $this->textareaValue,
                'site_logo' => $this->inputValue,
                'site_f_logo' => $this->inputValue,
                'f_image' => $this->inputValue,
                'site_icon' => $this->inputValue,
                'withdrawal_modal' => $this->radioValue,
                'noindex' => $this->radioValue,
                'contact' => $this->radioValue,
                'index_count' => $this->inputValue,
                'footer_text' => $this->textareaValue,
                'f_color' => $this->inputValue,
                'ft_color' => $this->inputValue,
                'bg_color' => $this->inputValue,
            ],
            'category' => [
                'page' => $this->inputValue,
                'subject' => $this->inputValue,
                'h1' => $this->inputValue,
                'title' => $this->inputValue,
                'description' => $this->textareaValue,
                'main_nav' => $this->radioValue
            ],
            'content' => [
                'author' => $this->selectValue,
                'category' => $this->selectValue,
                'page' => $this->inputValue,
                'title' => $this->inputValue,
                'description' => $this->textareaValue,
                'html' => $this->textareaValue,
                'thumbnail' => $this->inputValue,
                'banner' => $this->inputValue,
                'banner_link' => $this->inputValue,
                'banner_timer' => $this->selectValue,
                'publishing' => $this->radioValue,
                'release_date' => $this->inputValue
            ],
            'withdrawal_modal' => [
                'html' => $this->textareaValue,
            ],
            'sidenav' => [
                'sidenav_status' => $this->radioValue,
                'html' => $this->textareaValue,
            ],
            'account' => [
                'account' => $this->inputValue,
                'name' => $this->inputValue,
                'del' => $this->radioValue,
                'icon' => $this->inputValue,
                'auth' => $this->radioValue,
            ],
            'smtp' => [
                'host' => $this->inputValue,
                'port' => $this->inputValue,
                'encrypt' => $this->inputValue,
                'user' => $this->inputValue,
                'passwd' => $this->inputValue,
                'email' => $this->inputValue,
                'from_name' => $this->inputValue,
                'encoding' => $this->inputValue,
                'charset' => $this->inputValue,
                'html' => $this->textareaValue,
            ],
        ];
        return $col[$table];
    }

    private function setColumn($table)
    {
        $col = [
            'setting' => [
                'site_name' => null,
                'index_title' => null,
                'index_description' => null,
                'site_name' => null,
                'copyright_url' => null,
                'lang' => null,
                'facebook' => null,
                'instagram' => null,
                'linkedin' => null,
                'twitter' => null,
                'google_tag' => null,
                'google_tag_no' => null,
                'withdrawal_modal' => 0,
                'noindex' => 0,
                'contact' => 0,
                'index_count' => 5,
                'footer_text' => null,
                'f_color' => '#1d212f',
                'ft_color' => '#e8e8e8',
                'bg_color' => '#ffffff'
            ],
            'category' => [
                'page' => null,
                'subject' => null,
                'h1' => null,
                'title' => null,
                'description' => null,
                'main_nav' => 0
            ],
            'content' => [
                'author' => 1,
                'category' => 2,
                'page' => null,
                'title' => null,
                'description' => null,
                'html' => null,
                'publishing' => 1,
                'banner_link' => null,
                'banner_timer' => null,
                'release_date' => null
            ],
            'withdrawal_modal' => [
                'html' => null,
            ],
            'sidenav' => [
                'sidenav_status' => 1,
                'html' => null,
            ],
            'account' => [
                'account' => null,
                'name' => null,
                'del' => 0,
                'auth' => 1
            ],
            'smtp' => [
                'host' => null,
                'port' => null,
                'encrypt' => null,
                'user' => null,
                'passwd' => null,
                'email' => null,
                'from_name' => null,
                'encoding' => null,
                'charset' => null,
                'html' => null,
            ],
        ];
        return $col[$table];
    }

}
