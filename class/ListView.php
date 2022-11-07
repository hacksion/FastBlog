<?php
namespace TM;
class ListView
{
    private $records;
    private $all_count;
    private $sql_session;
    private $lang;
    private $lang_list;
    private $date_format;
    private $category_id;
    private $url;

    public function __construct($options = [])
    {
        $this->records = $options['records'] ?? null;
        $this->all_count = $options['all_count'] ?? 0;
        $this->sql_session = $options['sql_session'] ?? [];
        $this->lang = $options['lang'] ?? 'ja';
        $this->date_format = $options['date_format'] ?? 'Y年m月d日';
        $this->lang_list = $options['lang_list'] ?? [];
        $this->category_id = $options['category_id'] ?? '';
        $this->url = $options['url'];
        $this->imagesurl = $options['imagesurl'];
    }

    public function lists()
    {
        try {
            if(!$this->category_id){
                throw new \Exception($this->lang_list['NOT_CONTENT'][$this->lang]);
            }
            $list_html = '<div class="content_list">';
            foreach ($this->records as $list) {
                $rep_value = ['{table_of_contents}' => ''];
                $desc = str_replace( array_keys( $rep_value ), array_values( $rep_value ), strip_tags($list->html));
                $thumb = $list->thumbnail ? '<a href="'.$this->url.$list->category_page.'/'.$list->page.'" class="thumb" style="background-image:url('.$this->imagesurl.'content/'.$list->id.'/'.$list->thumbnail.')"></a>':'';
                $list_html .= '
                <article>
                <h2><a href="'.$this->url.$list->category_page.'/'.$list->page.'">'.$list->title.'</a></h2>
                <div class="editor">
                <a href="'.$this->url.$list->category_page.'" class="category">'.$list->category_subject.'</a>
                <span class="author">
                <span class="author_name">'.$list->account_name.'</span>
                <span class="author_date">'.date($this->date_format, strtotime($list->release_date)).'</span>
                </span>
                </div>
                '.$thumb.'
                <p class="description">'.strWidth($desc, 200).'</p>
                <a href="'.$this->url.$list->category_page.'/'.$list->page.'" class="btn btn-outline-primary detail">'.$this->lang_list['DETAIL'][$this->lang].'</a>
                </article>
                ';
            }
            $list_html .= '</div>
            <span id="all_records" class="d-none">'.$this->all_count.'</span>
            <span id="current_page" class="d-none">'.$this->sql_session['page_num'].'</span>
            <span id="set_view_count" class="d-none">'.$this->sql_session['view_count'].'</span>';

            echo $list_html;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function search()
    {
        $list_html = '';
        $list_html .= $this->records ? '':'<p class="text-center">'.$this->lang_list['NOT_CONTENT'][$this->lang].'</p>';
        foreach ($this->records as $list) {
            $thumb = $list->thumbnail ? '<div class="thumb" style="background-image:url('.$this->imagesurl.'content/'.$list->id.'/s_'.$list->thumbnail.')"></div>':'';
            $list_html .= '
            <div class="row mt-3 mb-2">
                <div class="col-3">
                    '.$thumb.'
                </div>
                <div class="col-9">
                    <h4><a href="'.$this->url.$list->category_page.'/'.$list->page.'">'.strWidth($list->title, 200).'</a></h4>
                    <span class="date">'.date($this->date_format, strtotime($list->release_date)).'</span>
                </div>
            </div>
            ';
        }
        $list_html .= '
        <span id="all_records" class="d-none">'.$this->all_count.'</span>
        <span id="current_page" class="d-none">'.$this->sql_session['page_num'].'</span>
        <span id="set_view_count" class="d-none">'.$this->sql_session['view_count'].'</span>';

        echo $list_html;
    }
}
