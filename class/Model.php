<?php
namespace TM;

class Model
{
    private $replace = [];
    private $DB;
    private $setting;

    public function __construct($options)
    {
        $this->DB = new DB;
        $this->replace = $options;
        $this->replace['site_id'] = 1;
        $this->setting = $this->DB->query('SELECT * FROM `setting` WHERE `id` = ?', [$this->replace['site_id']])[0];
        $this->replace['lang'] = explode('_', $this->setting->lang)[0];
        $this->replace['url'] = URL;
        $this->replace['logo'] = $this->setting->site_logo ? PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->site_logo:'';
        $this->replace['f_logo'] = $this->setting->site_f_logo ? PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->site_f_logo:'';
        $this->replace['icon'] = $this->setting->site_icon ? PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->site_icon:'';
        $this->replace['contact_flag'] = $this->setting->contact;
        $this->replace['withdrawal_modal_flag'] = $this->setting->withdrawal_modal;
        
    }

    private function status200()
    {
        $this->replace['status_code'] = 200;
        if(isset($this->replace['callFunction']['html'])){
            $method = $this->replace['callFunction']['html'].'Html';
            if(method_exists(get_class(), $method)){
                $this->$method();
            }
            $this->replace['html,'.$this->replace['callFunction']['html']] = '';
        }else{
            $this->publicHtml();
        }
        foreach($this->replace['callFunction'] as $function => $option){
            if(method_exists(get_class(), $function)){
                $this->replace[$function] = $this->$function($option);
            }
        }
    }

    private function tokenCheck($redirect = '') : void
    {
        if(!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_token']){
            $this->redirect($redirect);
        }
    }

    private function redirect($redirect = '') : void
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location:" . ($redirect ? $redirect : URL));
        exit;
    }

    private function publicHtml()
    {
        $this->getLangList($this->replace['lang']);
        $this->replace['index_count'] = $this->setting->index_count;
        $this->replace['noindex_flag'] = $this->setting->noindex;
        $this->replace['site_name'] = $this->setting->site_name;
        $this->replace['footer_text'] = $this->setting->footer_text;
        $this->replace['copyright'] = date('Y').' '.$this->setting->site_name;
        $this->replace['copyright_url'] = $this->setting->copyright_url ? $this->setting->copyright_url:URL;
        $this->replace['bg_color'] = $this->setting->bg_color ? 'background-color:'.$this->setting->bg_color.';':'';
        $this->replace['f_color'] = $this->setting->f_color ? 'background-color:'.$this->setting->f_color.';':'';
        $this->replace['ft_color'] = $this->setting->ft_color ? 'color:'.$this->setting->ft_color.';':'';
        $this->replace['ft_color_rgb'] = $this->setting->ft_color ? hex2rgb($this->setting->ft_color):'';
        $this->replace['f_image'] = $this->setting->f_image ? 'background-image:url('.PUBLIC_URL['IMG'].'setting/'.$this->setting->id.'/'.$this->setting->f_image.')':'';
        $this->replace['facebook'] = $this->setting->facebook;
        $this->replace['instagram'] = $this->setting->instagram;
        $this->replace['linkedin'] = $this->setting->linkedin;
        $this->replace['twitter'] = $this->setting->twitter;
        $this->replace['og:lang'] = $this->setting->lang;
        $this->replace['google_tag'] = $this->setting->google_tag;
        $this->replace['google_tag_no'] = $this->setting->google_tag_no;
        $this->replace['nav_form'] = $this->navForm();
        $this->replace['title'] = $this->setting->index_title;
        $this->replace['description'] = $this->setting->index_description;
        $this->replace['article_modified'] = '';
        $this->replace['h1'] = '';
        $this->withdrawalModal();
        $this->publicNav();
    }

    private function publicNav()
    {
        $category = $this->DB->query('SELECT `id`,`article_modified`,`page`,`h1`,`subject`,`title`,`description`,`main_nav` FROM `category` ORDER BY `num` ASC', []);
        
        foreach($category as $cat){
            $this->replace['nav_list']['nav'][] = [
                'id' => $cat->id,
                'category_page' => $cat->page,
                'subject' => $cat->subject,
                'main_nav' => $cat->main_nav,
            ];
            if($cat->page == $this->replace['category_page']){
                $this->replace['title'] = $cat->title;
                $this->replace['description'] = $cat->description;
                $this->replace['article_modified'] = $cat->article_modified;
                $this->replace['h1'] = $cat->h1;
            }
        }
    }

    private function adminHtml()
    {
        if($this->replace['method'] != 'login'){
            $this->replace['login'] = (new Auth('array'))->checkExec();
            if($this->replace['login']['result'] == 0){
                $this->redirect(PUBLIC_URL['ADMIN_ERROR']);
            }
        }
    }

    private function getLangList($site_lang)
    {
        $dictionary = $this->DB->query('SELECT `key_name`,`key_value` FROM `dictionary`', []);
        foreach($dictionary as $value){
            $this->replace[$value->key_name] = json_decode($value->key_value, true)[$site_lang];
        }
    }

    private function search_area($option = null)
    {
        $SearchForms = new SearchForms($this->replace['category_page']);
        $search_list = [
            'hidden' => [
                'w' => 'w',
                'sp' => ['sp', 5],
                'p' => ['p', $SearchForms->getSS('p', 0)],
                'c' => ['c', $SearchForms->getSS('c', 'release_date')],
                's' => ['s', $SearchForms->getSS('s', 'DESC')]
            ],
            'btn' => [
                'add' => false,
                'csv' => false
            ]
        ];
        return $SearchForms->searchFormHidden($search_list);
    }

    private function navForm()
    {
        $SearchForms = new SearchForms('public_keyword_search');
        $search_list = [
            'search_text' => $this->replace['SEARCH_TEXT'],
            'btn_class' => 'nav_',
            'hidden' => [
                'w' => 'nw',
                'sp' => ['nsp', 5],
                'p' => ['np', $SearchForms->getSS('np', 0)],
                'c' => ['nc', $SearchForms->getSS('nc', 'release_date')],
                's' => ['ns', $SearchForms->getSS('ns', 'DESC')]
            ],
            'btn' => [
                'add' => false,
                'csv' => false
            ]
        ];
        return $SearchForms->searchFormNav($search_list);
    }

    private function contact_form()
    {
        $result = ['host' => '', 'user' => '', 'passwd' => '', 'email' => ''];
        $smtp = $this->DB->query('SELECT `host`,`user`,`passwd`,`email`,`html` FROM `smtp` WHERE `id` = ?', [$this->replace['site_id']]);
        $view = 1;
        if($smtp){
            foreach($result as $key => $value){
                if(empty($smtp[0]->$key))$view = 0;
                $result[$key] = $smtp[0]->$key;
            }
            $result['html'] = $smtp[0]->html;
        }
        $this->replace['callFunction']['contact_form'] = $view ? $result:null;
    }

    private function recommend()
    {
        $sql = 'SELECT
        `content`.`id`,
        `content`.`page`,
        `content`.`title`,
        `content`.`thumbnail`,
        `category`.`page` AS `category_page`
        FROM `content`
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)
        WHERE
        `content`.`publishing` = 1 AND
        DATE_FORMAT(`content`.`release_date`, "%Y%m%d%H%i") < DATE_FORMAT(NOW(), "%Y%m%d%H%i")
        ORDER BY `content`.`access` DESC
        LIMIT '.$this->replace['index_count'];
        $this->replace['callFunction']['recommend'] = $this->DB->query($sql, []);
    }

    private function withdrawalModal($option = null)
    {
        $this->replace['withdrawal_modal'] = '';
        if($this->replace['withdrawal_modal_flag']){
            $sql = 'SELECT `withdrawal_modal`.`html` FROM `withdrawal_modal` WHERE id = ?';
            $result = $this->DB->query($sql, [$this->replace['site_id']]);
            if($result && $result[0]->html){
                $this->replace['withdrawal_modal'] = $result[0]->html;
            }
        }
    }

    private function side_nav($option = null)
    {
        $sidenav = $this->DB->query('SELECT `id`,`side_img`,`html`,`sidenav_status` FROM `sidenav` WHERE `id` = 1', []);
        $this->replace['callFunction']['side_nav'] = $sidenav && $sidenav[0]->sidenav_status ? $sidenav[0]:'';
    }

    public function index()
    {
        $this->status200();
        return $this->replace;
    }

    public function category()
    {
        $result = '';
        if($this->replace['category_page']){
            $result = $this->DB->query('SELECT `id` FROM `category` WHERE page = ?', [$this->replace['category_page']]);
        }
        if(empty($result)){
            $this->replace['status_code'] = 404;
        }else{
            $this->status200();
        }
        return $this->replace;
    }

    public function content()
    {
        $result = '';
        if($this->replace['content_page']){
            $sql = 'SELECT
            `content`.`id`,
            `content`.`release_date`,
            `content`.`category`,
            `content`.`page`,
            `content`.`title`,
            `content`.`description`,
            `content`.`html`,
            `content`.`table_of_contents`,
            `content`.`thumbnail`,
            `content`.`banner`,
            `content`.`banner_link`,
            `content`.`banner_timer`,
            `account`.`id` AS `account_id`,
            `account`.`name` AS `account_name`,
            `account`.`icon` AS `account_icon`,
            `category`.`page` AS `category_page`,
            `category`.`subject` AS `category_subject`,
            `category`.`description` AS `category_description`
            FROM `content`
            LEFT OUTER JOIN `account` ON (`content`.`author` = `account`.`id`)
            LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)
            WHERE
            `content`.`page` = ? AND
            `content`.`publishing` = 1 AND
            DATE_FORMAT(`content`.`release_date`, "%Y%m%d%H%i") < DATE_FORMAT(NOW(), "%Y%m%d%H%i")';
            $result = $this->DB->query($sql, [$this->replace['content_page']]);
        }
        if(empty($result)){
            $this->replace['status_code'] = 404;
        }else{
            $this->status200();
            $this->replace['title'] = $result[0]->title;
            $this->replace['description'] = $result[0]->description;
            $this->replace['callFunction']['content_area'] = $result[0];
            $img_path = 'content/'.$result[0]->id.'/s_'.$result[0]->thumbnail;
            $this->replace['thumbnail_url'] = file_exists(SERVER_DIR['IMG'].$img_path) ? PUBLIC_URL['IMG'].$img_path:$this->replace['logo'];
            $this->replace['content_id'] = $result[0]->id;
            $this->otherArticles();
        }
        return $this->replace;
    }

    public function otherArticles()
    {
        $sql = 'SELECT
        `content`.`id`,
        `content`.`created`,
        `content`.`category`,
        `content`.`page`,
        `content`.`title`,
        `content`.`thumbnail`,
        `category`.`page` AS `category_page`
        FROM `content`
        LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`)
        WHERE
        `content`.`publishing` = 1 AND
        DATE_FORMAT(`content`.`release_date`, "%Y%m%d%H%i") < DATE_FORMAT(NOW(), "%Y%m%d%H%i") AND ';
        $before = '
        `content`.`created` < (SELECT `created` FROM `content` WHERE `id` = ?)
        ORDER BY `content`.`created` DESC LIMIT 1
        ';
        $after = '
        `content`.`created` > (SELECT `created` FROM `content` WHERE `id` = ?)
        ORDER BY `content`.`created` ASC LIMIT 1
        ';
        $this->replace['other_articles_records']['before'] = $this->DB->query($sql.$before, [$this->replace['content_id']]);
        $this->replace['other_articles_records']['after'] = $this->DB->query($sql.$after, [$this->replace['content_id']]);
    }

    public function admin_index()
    {
        $this->status200();
        return $this->replace;
    }

    public function admin_category()
    {
        $this->status200();
        $this->replace['admin_search_area'] = (new AdminSearchArea)->category();
        return $this->replace;
    }

    public function admin_account()
    {
        $this->status200();
        $this->replace['admin_search_area'] = (new AdminSearchArea)->account();
        return $this->replace;
    }

    public function admin_content()
    {
        $this->status200();
        $this->replace['admin_search_area'] = (new AdminSearchArea)->content();
        return $this->replace;
    }

    public function admin_content_edit()
    {
        $this->status200();
        array_unshift($this->replace['add_script'], URL.'plugin/ckeditor/ckeditor.js', PUBLIC_URL['JS'].'flatpickr.js');
        $this->replace['add_css'] = [
            PUBLIC_URL['CSS'].'flatpickr.css',
        ];
        $form = [
            'form_id' => '',
            'form_author' => '',
            'form_category' => '',
            'form_category_name' => '',
            'form_page' => '',
            'form_title' => '',
            'form_description' => '',
            'form_html' => '',
            'form_thumbnail' => '',
            'form_publishing' => 1,
            'form_release_date' => date('Y-m-d H:i'),
            'form_access' => 0,
            'form_author_options' => '',
            'form_publishing_radio' => '',
            'form_category_options' => '<option value="">---</oprion>',
            'form_thumbnail_img' => '',
            'form_delete' => '',
            'back_url' => URL.ADMIN_DIR.'/content',
            'public_content_url' => '',
            'form_banner_link' => '',
            'form_banner' => '',
            'form_banner_img' => '',
            'form_banner_timer' => '',
            'form_banner_timer_options' => '<option value="">---</oprion>',
        ];

        if($this->replace['id']){
            $col = [
                'id',
                'author',
                'category',
                'page',
                'title',
                'description',
                'html',
                'thumbnail',
                'publishing',
                'release_date',
                'banner',
                'banner_link',
                'banner_timer',
                'access',
            ];

            $sql = 'SELECT '.colFactory($col, 'content').',`category`.`page` AS `category_name` FROM `content` LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`) WHERE `content`.`id` = ?';
            $content = $this->DB->query($sql, [$this->replace['id']]);
            if($content){
                foreach($col as $value){
                    $form['form_'.$value] = $content[0]->$value;
                }
                $form['form_category_name'] = $content[0]->category_name;
                $form['form_delete'] = '<button type="button" class="btn btn-sm btn-danger" id="delete_edit_record" data-id="'.$this->replace['id'].'" data-table="content">削除</button>';
                $form['public_content_url'] = '
                <label for="" class="form-label">公開コンテンツ</label>
                <p class="m-0"><a href="'.URL.$form['form_category_name'].'/'.$form['form_page'].'" class="btn btn-sm btn-secondary" target="_blank">ページ確認</a></p>';
            }
        }
        $LVL_PUBLISHING = [0 => '非公開', 1 => '公開'];
        for($i = 0; $i < 2; $i++){
            $checked = $form['form_publishing'] == $i ? ' checked':'';
            $form['form_publishing_radio'] .= '<input type="radio" class="btn-check" name="publishing" id="publishing_'.$i.'" value="'.$i.'"'.$checked.'>';
            $form['form_publishing_radio'] .= '<label class="btn btn-sm btn-outline-secondary" for="publishing_'.$i.'">'.$LVL_PUBLISHING[$i].'</label>';
        }

        $category = $this->DB->query('SELECT `id`,`subject` FROM `category` ORDER BY `num` ASC', []);
        foreach($category as $value){
            $selected = $form['form_category'] == $value->id ? ' selected':'';
            $form['form_category_options'] .= '<option value="'.$value->id.'"'.$selected.'>'.$value->subject.'</option>';
        }

        for($i = 5; $i < 100; $i+=5){
            $selected = $form['form_banner_timer'] == $i ? ' selected':'';
            $form['form_banner_timer_options'] .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
        }

        $account = $this->DB->query('SELECT `id`,`name`,`auth` FROM `account`', []);
        foreach($account as $value){
            $selected = $form['form_author'] == $value->id ? ' selected':'';
            if($this->replace['login']['auth'] == 2){
                if($this->replace['login']['id'] == $value['id']){
                    $form['form_author_options'] .= '<option value="'.$value->id.'"'.$selected.'>'.$value->name.'</option>';
                }
            }else{
                $form['form_author_options'] .= '<option value="'.$value->id.'"'.$selected.'>'.$value->name.'</option>';
            }
        }

        if($form['form_thumbnail']){
            $form['form_thumbnail_img'] = '
            <div class="thumbnail_file_area">
            <img src="'.PUBLIC_URL['IMG'].'content/'.$form['form_id'].'/'.$form['form_thumbnail'].'">
            <div class="text-danger"><i class="fas fa-trash file_trash" data-id="'.$this->replace['id'].'" data-name="'.$form['form_thumbnail'].'" data-table="content" data-type="images" data-col="thumbnail" role="button"></i></div>
            </div>
            ';
        }
        if($form['form_banner']){
            $form['form_banner_img'] = '
            <div class="banner_file_area">
            <img src="'.PUBLIC_URL['IMG'].'content/'.$form['form_id'].'/'.$form['form_banner'].'">
            <div class="text-danger"><i class="fas fa-trash file_trash" data-id="'.$this->replace['id'].'" data-name="'.$form['form_banner'].'" data-table="content" data-type="images" data-col="banner" role="button"></i></div>
            </div>
            ';
        }

        return $this->replace += $form;
    }

    public function admin_setting()
    {
        $this->status200();
        array_unshift($this->replace['add_script'], URL.'plugin/ckeditor/ckeditor.js');
        
        foreach($this->setting as $key => $value){
            if($key == 'lang'){
                $this->replace['form_option_lang'] = '
                <option value="ja_JP"'.($this->setting->lang == 'ja_JP' ? ' selected':'').'>日本語</option>
                <option value="en_US"'.($this->setting->lang == 'en_US' ? ' selected':'').'>English</option>
                ';
            }elseif($key == 'site_logo'){
                $this->replace['form_site_logo'] = $this->setting->site_logo;
                $this->replace['form_logo_upload_area'] = $this->setting->site_logo ? '<img src="'.PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->site_logo.'" style="width:160px" class="me-1"><div class="text-danger"><i class="fas fa-trash file_trash" data-id="1" data-name="'.$this->setting->site_logo.'" data-table="setting" data-type="images" data-col="site_logo" role="button"></i></div>':'';
            }elseif($key == 'site_f_logo'){
                $this->replace['form_site_f_logo'] = $this->setting->site_f_logo;
                $this->replace['form_f_logo_upload_area'] = $this->setting->site_f_logo ? '<img src="'.PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->site_f_logo.'" style="width:160px;background-color:'.$this->setting->f_color.'" class="me-1"><div class="text-danger"><i class="fas fa-trash file_trash" data-id="1" data-name="'.$this->setting->site_f_logo.'" data-table="setting" data-type="images" data-col="site_f_logo" role="button"></i></div>':'';
            }elseif($key == 'f_image'){
                $this->replace['form_f_image'] = $this->setting->f_image;
                $this->replace['f_image_upload_area'] = $this->setting->f_image ? '<img src="'.PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->f_image.'" style="width:100%;background-color:'.$this->setting->f_color.'" class="me-1"><div class="text-danger"><i class="fas fa-trash file_trash" data-id="1" data-name="'.$this->setting->f_image.'" data-table="setting" data-type="images" data-col="f_image" role="button"></i></div>':'';
            }elseif($key == 'site_icon'){
                $this->replace['form_site_icon'] = $this->setting->site_icon;
                $this->replace['form_icon_upload_area'] = $this->setting->site_icon ?  '<img src="'.PUBLIC_URL['IMG'].'setting/'.$this->replace['site_id'].'/'.$this->setting->site_icon.'" style="width:72px" class="me-1"><div class="text-danger"><i class="fas fa-trash file_trash" data-id="1" data-name="'.$this->setting->site_icon.'" data-table="setting" data-type="images" data-col="site_icon" role="button"></i></div>':'';
            }elseif($key == 'withdrawal_modal'){
                $this->replace['form_withdrawal_modal'] = '
                <input type="radio" class="btn-check" name="withdrawal_modal" id="withdrawal_modal_0" value="0"'.($this->setting->withdrawal_modal == 0 ? ' checked':'').'>
                <label class="btn btn-sm btn-outline-secondary" for="withdrawal_modal_0">無効</label>
                <input type="radio" class="btn-check" name="withdrawal_modal" id="withdrawal_modal_1" value="1"'.($this->setting->withdrawal_modal == 1 ? ' checked':'').'>
                <label class="btn btn-sm btn-outline-secondary" for="withdrawal_modal_1">有効</label>';
            }elseif($key == 'noindex'){
                $this->replace['form_noindex'] = '
                <input type="radio" class="btn-check" name="noindex" id="noindex_0" value="0"'.($this->setting->noindex == 0 ? ' checked':'').'>
                <label class="btn btn-sm btn-outline-secondary" for="noindex_0">有効</label>
                <input type="radio" class="btn-check" name="noindex" id="noindex_1" value="1"'.($this->setting->noindex == 1 ? ' checked':'').'>
                <label class="btn btn-sm btn-outline-secondary" for="noindex_1">無効</label>';
            }elseif($key == 'contact'){
                $this->replace['form_contact'] = '
                <input type="radio" class="btn-check" name="contact" id="contact_0" value="0"'.($this->setting->contact == 0 ? ' checked':'').'>
                <label class="btn btn-sm btn-outline-secondary" for="contact_0">無効</label>
                <input type="radio" class="btn-check" name="contact" id="contact_1" value="1"'.($this->setting->contact == 1 ? ' checked':'').'>
                <label class="btn btn-sm btn-outline-secondary" for="contact_1">有効</label>';
            }else{
                $this->replace['form_'.$key] = $value;
            }
        }
        return $this->replace;
    }

    public function admin_smtp()
    {
        $this->status200();
        array_unshift($this->replace['add_script'], URL.'plugin/ckeditor/ckeditor.js');
        $site = $this->DB->query('SELECT * FROM `smtp` WHERE `id` = ?', [$this->replace['site_id']]);
        foreach($site[0] as $key => $value){
                $this->replace['form_'.$key] = $value;
        }
        return $this->replace;
    }

    public function admin_sidenav()
    {
        $this->status200();
        array_unshift($this->replace['add_script'], URL.'plugin/ckeditor/ckeditor.js');
        $site = $this->DB->query('SELECT * FROM `sidenav` WHERE `id` = ?', [$this->replace['site_id']]);
        foreach($site[0] as $key => $value){
            if($key == 'side_img') {
                $this->replace['form_side_img'] = $value;
                $this->replace['form_logo_upload_area'] = $value ? '<img src="'.PUBLIC_URL['IMG'].'sidenav/'.$this->replace['site_id'].'/'.$value.'"><div class="text-danger"><i class="fas fa-trash file_trash" data-id="'.$this->replace['site_id'].'" data-name="'.$value.'" data-table="sidenav" data-type="images" data-col="side_img" role="button"></i></div>':'';
            }elseif($key == 'sidenav_status'){
                $this->replace['form_sidenav_status'] = '
                    <input type="radio" class="btn-check" name="sidenav_status" id="sidenav_status_0" value="0"'.($value == 0 ? ' checked':'').'>
                    <label class="btn btn-sm btn-outline-secondary" for="sidenav_status_0">サイドナビ非表示</label>
                    <input type="radio" class="btn-check" name="sidenav_status" id="sidenav_status_1" value="1"'.($value == 1 ? ' checked':'').'>
                    <label class="btn btn-sm btn-outline-secondary" for="sidenav_status_1">サイドナビ表示</label>
                ';
            }else{
                $this->replace['form_'.$key] = $value;
            }

        }
        return $this->replace;
    }

    public function admin_html_template()
    {
        $this->status200();
        $this->replace['tpl_ext'] = TPL_EXT;
        $this->replace['form_template'] = file_get_contents(SERVER_DIR['HTML'].$this->replace['id'].$this->replace['tpl_ext']);
        return $this->replace;
    }

    public function admin_css()
    {
        $this->status200();
        $this->replace['form_custom_css'] = file_get_contents(SERVER_DIR['CSS'].'custom.css');
        return $this->replace;
    }

    public function admin_withdrawal_modal()
    {
        $this->status200();
        array_unshift($this->replace['add_script'], URL.'plugin/ckeditor/ckeditor.js');
        $form = [
            'form_id' => '',
            'form_html' => ''
        ];
        $sql = 'SELECT
            `withdrawal_modal`.`id`,
            `withdrawal_modal`.`html`
            FROM `withdrawal_modal` WHERE `withdrawal_modal`.`id` = ?
            ';
        $result = $this->DB->query($sql, [$this->replace['site_id']]);
        foreach($result[0] as $key => $value){
            $form['form_'.$key] = $value;
        }

        return $this->replace += $form;
    }

    public function admin_dictionary()
    {
        $this->status200();
        $dictionary = $this->DB->query('SELECT `id`,`key_value` FROM `dictionary`', []);
        $this->replace['dictionary_list'] = '';
        
        foreach($dictionary as $value){
            
            $lang = json_decode($value->key_value, true);
            $this->replace['dictionary_list'] .= '
            <div class="row mb-3">
                <div class="col-6">
                    <input type="text" name="ja_'.$value->id.'" value="'.$lang['ja'].'" class="form-control form-control-sm keyup_edit" data-table="dictionary" required>
                </div>
                <div class="col-6">
                    <input type="text" name="en_'.$value->id.'" value="'.$lang['en'].'" class="form-control form-control-sm keyup_edit" data-table="dictionary" required>
                </div>
            </div>
            ';
        }
        return $this->replace;
    }

    public function admin_login()
    {
        $this->status200();
        $this->replace['token'] = $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
        $this->replace['redir_url'] = URL.ADMIN_DIR;
        return $this->replace;
    }

    private function totalNumber()
    {
        $result = $this->DB->query('SELECT SUM(`access`) AS `total` FROM `content`', []);
        return number_format($result[0]->total);
    }

    private function monthToMonthAnalysis()
    {
        //access_log
        $ratio = 0;
        $this_m = "DATE_FORMAT(`access_datetime`, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')";
        $last_m = "DATE_FORMAT(`access_datetime`, '%Y%m') = DATE_FORMAT( ADDDATE( CURDATE() , INTERVAL -1 MONTH) , '%Y%m')";
        $sql = 'SELECT COUNT(`content_id`) AS `count` FROM `access_log` WHERE ';
        $result_t = 0;
        $result_l = 0;
        $result = $this->DB->query($sql.$this_m, []);
        if($result){
            $result_t = $result[0]->count;
        }
        $result = $this->DB->query($sql.$last_m, []);
        if($result){
            $result_l = $result[0]->count;
        }
        if($result_t && $result_l){
            $ratio = round($result_t / $result_l * 100) - 100;
        }elseif($result_t && $result_l == 0){
            $ratio = 100;
        }
        return $ratio;
    }

    private function dayOfTheWeekAnalysis()
    {
        $sql = "SELECT DATE_FORMAT(`access_datetime`, '%w') as `w`,COUNT(`content_id`) as `count` FROM `access_log` WHERE `access_datetime` > ( NOW() - INTERVAL 30 DAY )
        GROUP BY DATE_FORMAT(`access_datetime`, '%w')";
        $dofw_analysis = $this->DB->query($sql, []);
        $week = [];
        for($i = 0; $i < 7; $i++)$week[$i] = 0;
        foreach($dofw_analysis as $value)$week[$value->w] = $value->count;
        $result = trth([0, 7], dayOfWeek('ja'));
        $result .= trtd($week, [0, 7], true);
        return $result;
    }

    private function hourlyAnalysis()
    {
        $sql = "SELECT
        DATE_FORMAT(`access_datetime`, '%k') as `hour`,COUNT(`content_id`) as `count` FROM `access_log`
        WHERE `access_datetime` > ( NOW() - INTERVAL 30 DAY )
        GROUP BY DATE_FORMAT(`access_datetime`, '%k')";
        $hourly_analysis = $this->DB->query($sql, []);
        $hour = [];
        for($i = 1; $i < 25; $i++)$hour[$i] = 0;
        foreach($hourly_analysis as $value)$hour[$value->hour] = $value->count;
        $result = trth([1, 7],null,'時');
        $result .= trtd($hour, [1, 7], true);
        $result .= trth([7, 13],null,'時');
        $result .= trtd($hour, [7, 13], true);
        $result .= trth([13, 19],null,'時');
        $result .= trtd($hour, [13, 19], true);
        $result .= trth([19, 25],null,'時');
        $result .= trtd($hour, [19, 25], true);
        return $result;
    }

    private function top10List()
    {
        $accessTop = $this->DB->query('SELECT `id`,`title`,`access` FROM `content` WHERE `access` > 0 ORDER BY `access` DESC', []);
        $result = '<ul class="top_number">';
        foreach($accessTop as $value){
            $link =  $this->replace['login']['auth'] != 2 ? '<a href="'.URL.ADMIN_DIR.'/content/edit/'.$value->id.'">'.strWidth($value->title, 50).'</a>':strWidth($value->title, 50);
            $result .= '<li><span class="float-start">'.$link.'</span><span class="badge bg-danger float-end">'.number_format($value->access).'</span></li>';
        }
        $result .= '</ul>';
        return $result;
    }
}