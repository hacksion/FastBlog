<?php
namespace TM;
class AdminListView
{
    private $records;
    private $all_count;
    private $sql_session;
    private $lang;
    private $auth;
    private $systxt = [];

    public function __construct($options = [])
    {
        $this->records = $options['records'] ?? null;
        $this->all_count = $options['all_count'] ?? 0;
        $this->sql_session = $options['sql_session'] ?? [];
        $this->lang = $options['lang'] ?? 'ja';
        $this->auth = $options['auth'] ?? '';
    }

    public function category()
    {
        $list_html = $this->records ? '':'<tr><td colspan="7" class="text-center">レコードがありません</td></tr>';
        foreach ($this->records as $value) {
            $contents_count = $value->id > 1 ? number_format($value->content_count):'';
            $list_html .= '
            <tr class="sort_target" data-id="'.$value->id.'">
                <td class="text-nowrap align-middle">
                    <i class="fas fa-arrows-alt-v arrow me-1"></i><span class="sort_num">'.$value->num.'</span>
                </td>
                <td class="text-nowrap align-middle">
                    <i class="fas fa-edit" role="button" data-id="'.$value->id.'"  data-table="category" data-bs-toggle="modal" data-bs-target="#edit_modal"></i>
                </td>
                <td class="text-nowrap align-middle">'.$value->subject.'</td>
                <td class="text-nowrap align-middle">'.$value->page.'</td>
                <td class="text-nowrap align-middle">'.strWidth($value->title, 50).'</td>
                <td class="text-nowrap align-middle">'.$contents_count.'</td>
                <td class="text-nowrap align-middle">'.($value->main_nav == 1 ? 'ON':'OFF').'</td>
            </tr>
            ';
        }
        $list_html .= '<span id="all_records" class="d-none">'.$this->all_count.'</span>
        <span id="current_page" class="d-none">'.$this->sql_session['page_num'].'</span>
        <span id="set_view_count" class="d-none">'.$this->sql_session['view_count'].'</span>';

        echo $list_html;
    }

    public function content()
    {
        $list_html = $this->records ? '':'<tr><td colspan="7" class="text-center">レコードがありません</td></tr>';
        $url = URL.ADMIN_DIR.'/content/edit';
        foreach ($this->records as $value) {
            $publishing = '<span class="text-danger">非公開<span>';
            if($value->publishing == 1){
                $publishing = '公開<br>'.date('Y-m-d H:i', strtotime($value->release_date));
            }
            $edit = $this->auth != 2 ? '<a href="'.$url.'/'.$value->id.'"><i class="fas fa-edit"></i></a>':'';
            $list_html .= '
            <tr>
                <td class="text-nowrap align-middle">'.$edit.'</td>
                <td class="text-nowrap align-middle">'.strWidth($value->title, 30).'</td>
                <td class="text-nowrap align-middle">'.$value->page.'</td>
                <td class="text-nowrap align-middle">'.$value->category_subject.'</td>
                <td class="text-nowrap align-middle">'.$publishing.'</td>
                <td class="text-nowrap align-middle">'.$value->author_name.'</td>
                <td class="text-nowrap align-middle">'.number_format($value->access).'</td>
            </tr>
            ';
        }
        $list_html .= '<span id="all_records" class="d-none">'.$this->all_count.'</span>
        <span id="current_page" class="d-none">'.$this->sql_session['page_num'].'</span>
        <span id="set_view_count" class="d-none">'.$this->sql_session['view_count'].'</span>';

        echo $list_html;
    }

    public function account()
    {
        $table = __FUNCTION__;
        $list_html = $this->records ? '':'<tr><td colspan="7" class="text-center">レコードがありません</td></tr>';
        $date_format = $this->lang == 'ja' ? 'Y年m月d日':'F j, Y';
        foreach ($this->records as $value) {
            $del = $value->del == 1 ? '<span class="text-danger">無効</span>':'有効';
            $auth = $value->auth == 1 ? '<span class="text-danger">管理者</span>':'ライター';
            $icon = $value->icon ? '<img src="'.PUBLIC_URL['IMG'].'account/'.$value->id.'/'.$value->icon.'" style="width:30px">':'';
            $list_html .= '
            <tr>
                <td class="text-nowrap align-middle">
                    <i class="fas fa-edit" role="button" data-id="'.$value->id.'"  data-table="account" data-bs-toggle="modal" data-bs-target="#edit_modal" role="button"></i>
                </td>
                <td class="text-nowrap align-middle">'.$icon.'</td>
                <td class="text-nowrap align-middle">'.date($date_format, strtotime($value->created)).'</td>
                <td class="text-nowrap align-middle">'.$value->account.'</td>
                <td class="text-nowrap align-middle">'.$value->name.'</td>
                <td class="text-nowrap align-middle">'.$auth.'</td>
                <td class="text-nowrap align-middle">'.$del.'</td>
            </tr>
            ';
        }
        $list_html .= '<span id="all_records" class="d-none">'.$this->all_count.'</span>
        <span id="current_page" class="d-none">'.$this->sql_session['page_num'].'</span>
        <span id="set_view_count" class="d-none">'.$this->sql_session['view_count'].'</span>';

        echo $list_html;
    }
}
