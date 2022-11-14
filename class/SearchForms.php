<?php
namespace TM;

class SearchForms
{
    private $session_name = 'list';

    public function __construct($session_name)
    {
        $this->session_name = $session_name;
    }

    public function getSS($key, $default='')
    {
        return isset($_SESSION[$this->session_name][$key]) ? $_SESSION[$this->session_name][$key]:$default;
    }

    public function setSS($key, $value='')
    {
        $_SESSION[$this->session_name][$key] = $value;
    }

    public function searchFormHidden($options)
    {
        $add = isset($options['btn']['add']) && $options['btn']['add'] ? '<i class="btn btn-sm btn-success me-2 i_btn_add new_create" role="button" data-bs-toggle="modal" data-bs-target="#edit_modal" data-table="'.$options['btn']['table'].'"> 新規追加</i>':'';
        if($add && isset($options['btn']['edit_url']) && $options['btn']['edit_url']){
            $add = '<a href="'.$options['btn']['edit_url'].'" class="btn btn-sm btn-success me-2">新規追加</a>';
        }
        $result = '
        <form name="'.$this->session_name.'">'.$add;
        $result .= $this->hiddenTag($options['hidden']);
        $result .= '
        <input type="hidden" name="'.$options['hidden']['sp'][0].'" value="'.$options['hidden']['sp'][1].'">
        <input type="hidden" name="'.$options['hidden']['p'][0].'" value="'.$options['hidden']['p'][1].'">
        <input type="hidden" name="'.$options['hidden']['c'][0].'" value="'.$options['hidden']['c'][1].'">
        <input type="hidden" name="'.$options['hidden']['s'][0].'" value="'.$options['hidden']['s'][1].'">
        <a href="" class="'.($options['btn_class'] ?? '').'search_reset d-none" data-reset="'.$this->session_name.'" title="Reset"></a>
        <a href="" class="'.($options['btn_class'] ?? '').'search_btn d-none" title="Search"></a>
        </form>';
        return $result;
    }

    public function searchFormNav($options)
    {
        $result = '
        <form name="'.$this->session_name.'">';
        $result .= $this->hiddenTag($options['hidden']);
        $result .= '
        <div class="label_area">
        <label for="nav_search" class="form-label">'.$options['search_text'].'</label>
        <i class="fas fa-times-circle" id="search_close" role="button" aria-hidden="true"></i>
        </div>
        <input type="text" name="nw" class="form-control" id="nav_search" placeholder="">
        <input type="hidden" name="'.$options['hidden']['sp'][0].'" value="'.$options['hidden']['sp'][1].'">
        <input type="hidden" name="'.$options['hidden']['p'][0].'" value="'.$options['hidden']['p'][1].'">
        <input type="hidden" name="'.$options['hidden']['c'][0].'" value="'.$options['hidden']['c'][1].'">
        <input type="hidden" name="'.$options['hidden']['s'][0].'" value="'.$options['hidden']['s'][1].'">
        <a href="" class="'.($options['btn_class'] ?? '').'search_reset d-none" data-reset="'.$this->session_name.'" title="Reset"></a>
        <a href="" class="'.($options['btn_class'] ?? '').'search_btn d-none" title="Search"></a>
        </form>';
        return $result;
    }

    public function searchForm($options)
    {
        $add = isset($options['btn']['add']) && $options['btn']['add'] ? '<i class="btn btn-sm btn-success me-2 i_btn_add new_create" role="button" data-bs-toggle="modal" data-bs-target="#edit_modal" data-table="'.$options['btn']['table'].'"> 新規追加</i>':'';
        if($add && isset($options['btn']['edit_url']) && $options['btn']['edit_url']){
            $add = '<a href="'.$options['btn']['edit_url'].'" class="btn btn-sm btn-success me-2">新規追加</a>';
        }
        $result = '<form name="'.$this->session_name.'" class="mb-3 d-none d-sm-block">';
        $result .= '<div class="d-flex">';
        $result .= $this->searchFormBtn($add);
        if(isset($options['parts'])){
            foreach ($options['parts'] as $title => $value) {
                $result .= $value;
            }
            $result .= '<a href="" class="btn btn-secondary btn-sm search_btn mx-2" title="Search"><i class="fa-solid fa-magnifying-glass"></i></a>';
        }
        $result .= '</div>';
        $result .= $this->hiddenTag($options['hidden']);
        $result .= '
        <input type="hidden" name="'.$options['hidden']['sp'][0].'" value="'.$options['hidden']['sp'][1].'">
        <input type="hidden" name="'.$options['hidden']['p'][0].'" value="'.$options['hidden']['p'][1].'">
        <input type="hidden" name="'.$options['hidden']['c'][0].'" value="'.$options['hidden']['c'][1].'">
        <input type="hidden" name="'.$options['hidden']['s'][0].'" value="'.$options['hidden']['s'][1].'">
        </form>';
        return $result;
    }

    public function hiddenTag($options)
    {
        $result = !empty($options['w']) ? '<input type="hidden" name="input_w" value="'.$options['w'].'">':'';
        $result .= '
        <input type="hidden" name="input_sp" value="'.$options['sp'][0].'">
        <input type="hidden" name="input_p" value="'.$options['p'][0].'">
        <input type="hidden" name="input_c" value="'.$options['c'][0].'">
        <input type="hidden" name="input_s" value="'.$options['s'][0].'">
        ';
        return $result;
    }

    public function searchFormBtn($add)
    {
        return '
        <a href="" class="btn btn-secondary btn-sm me-2 search_reset" data-reset="'.$this->session_name.'" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>'.$add;
    }

    public function setPostSqlSession($options)
    {
        $post = $options['post'];
        $table_name = $options['table'];
        $conditions = $options['conditions'] ?? [];
        $like_value =  $options['like_value'] ?? '';
        unset($_SESSION[$this->session_name]);
        $date_column = 'modified';
        $re_table_name = $table_name;
        $result = [
            'order_by' => '`'.$re_table_name.'`.`'.$date_column.'`',
            'order_sort' => 'DESC',
            'view_count' => 30,
            'page_num' => 0,
            'conditions' => $conditions,
            'conditions_or' => [],
            'conditions_gor' => [],
            'like_value' => $like_value
        ];
        //sort column
        if(!empty($post[$post['input_c']])){
            $result['order_by'] = '`'.$table_name.'`.`'.$post[$post['input_c']].'`';
            $this->setSS($post['input_c'], $post[$post['input_c']]);
        }
        //sort set
        if(!empty($post[$post['input_s']])){
            $result['order_sort'] = $post[$post['input_s']];
            $this->setSS($post['input_s'], $post[$post['input_s']]);
        }
        //Set the number of items to display on one page
        if(!empty($post[$post['input_sp']])){
            $result['view_count'] = $post[$post['input_sp']];
            $this->setSS($post['input_sp'], $post[$post['input_sp']]);
        }
        //set current page number
        if(!empty($post[$post['input_p']])){
            $result['page_num'] = $post[$post['input_p']];
            $this->setSS($post['input_p'], $post[$post['input_p']]);
        }
        //Set if requested by keyword search
        if(!empty($post[$post['input_w']])){
            $result['like_value'] = $post[$post['input_w']];
            $this->setSS($post['input_w'], $post[$post['input_w']]);
        }
        if(isset($post['cat']) && $post['cat']){
            array_push($result['conditions'], '`'.$table_name.'`.`category` = '.$post['cat']);
            $this->setSS('cat', $post[ 'cat' ]);
        }
        return $result;
    }

    public function w($options=[])
    {
        return '<input type="text" class="form-control form-control-sm w-25" placeholder="'.(isset($options['placeholder']) ? $options['placeholder']:'').'" name="w" value="'.$this->getSS('w', (isset($options['default']) ? $options['default']:'')).'">';
    }
    
    public function cat($options=[])
    {
        $pls = isset($options['placeholder']) ? $options['placeholder']:'';
        $selected = $this->getSS('cat', (isset($options['default']) ? $options['default']:''));
        $options = '';
        $DB = new DB;
        $DB->setFetchStyle(2);
        $category = $DB->query('SELECT `id`,`subject` FROM `category` WHERE `id` > 1 ORDER BY `num` ASC', []);
        foreach($category as $value){
            $options .= '<option value="'.$value['id'].'"'.($value['id'] == $selected ? ' selected':'').'>'.$value['subject'].'</option>';
        }

        $result = '<select name="cat" class="form-control form-control-sm w-25 me-1">
        <option value="">- '.$pls.' -</option>
        '.$options.'
        </select>';
        return $result;
    }
}
