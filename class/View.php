<?php
namespace TM;

class View
{
    private $replace = [];
    private $duplicate = [];

    public function __construct($options)
    {
        $this->replace = $options;
        foreach($this->replace['callFunction'] as $function => $option){
            if(method_exists(get_class(), $function)){
                $this->replace[$function] = $this->$function($option);
            }
        }
    }

    private function resourceReplace($html)
    {
        if($html){
            preg_match_all('/\{.*?\}/u', $html, $matches);
            if($matches[0]){
                foreach($matches[0] as $m){
                    $val = ltrim($m, '{');
                    $val = rtrim($val, '}');
                    if(isset($this->replace['callFunction'][$val])){
                        $rep[$m] = method_exists(get_class(), $val) ? $this->$val($this->replace['callFunction'][$val]):'';
                        array_push($this->duplicate, $val);
                    }
                }
                $html = str_replace( array_keys( $rep ), array_values( $rep ), $html);
            }
        }
        return $html;
    }

    public function display()
    {
        $source = file_get_contents($this->replace['tpl']);
        preg_match_all('/\{.*?\}/u', $source, $matches);
        if($matches[0]){
            $rep = [];
            foreach($matches[0] as $m){
                $val = ltrim($m, '{');
                $val = rtrim($val, '}');
                if(in_array($val, $this->duplicate)){
                    $source = str_replace($m, '', $source);
                }else{
                    $rep[$m] = array_key_exists($val, $this->replace) ? $this->replace[$val]:$m;
                }
            }
        }
        echo str_replace( array_keys( $rep ), array_values( $rep ), $source);
    }

    public function head()
    {
        $add_script = '';
        if(isset($this->replace['add_script'])){
            foreach($this->replace['add_script'] as $value){
                $add_script .= '<script defer src="'.$value.'"></script>';
            }
        }
        $add_css = '';
        if(isset($this->replace['add_css'])){
            foreach($this->replace['add_css'] as $value){
                $add_css .= '<link href="'.$value.'" rel="stylesheet" type="text/css">';
            }
        }
        $link_icon = '';
        if($this->replace['icon']){
            $link_icon .= '
            <link rel="icon" type="image/png" href="'.$this->replace['icon'].'" sizes="16x16 24x24 32x32 48x48 64x64">
            <link rel="apple-touch-icon" href="'.$this->replace['icon'].'">';
        }

        $result = '
        <meta charset="UTF-8">
        '.($this->replace['noindex_flag'] ? '<meta name="robots" content="noindex">':'').'
        <title>'.$this->replace['title'].'</title>
        <meta name="description" content="'.$this->replace['description'].'">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        '.($this->replace['article_modified'] ? '<meta property="article:modified_time" content="'.date('Y-m-d', strtotime($this->replace['article_modified'])).'T'.date('H:i:s+09:00', strtotime($this->replace['article_modified'])).'">':'').'
        <meta property="og:locale" content="'.$this->replace['og:lang'].'">
        <meta property="og:type" content="website">
        <meta property="og:title" content="'.($this->replace['title'] ?? '').'">
        <meta property="og:description" content="'.($this->replace['description'] ?? '').'">
        <meta property="og:url" content="'.$this->replace['url'].'">
        <meta property="og:site_name" content="'.($this->replace['site_name'] ?? '').'">
        <meta property="og:image" content="'.(isset($this->replace['thumbnail_url']) && $this->replace['thumbnail_url'] ? $this->replace['thumbnail_url']:$this->replace['logo']).'">
        <meta name="twitter:card" content="summary_large_image">
        '.$link_icon.'

        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <script defer src="https://kit.fontawesome.com/79dd3834cf.js" crossorigin="anonymous"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'function.js" data-asyncurl="'.$this->replace['PUBLIC_URL']['ASYNC'].'" data-url="'.$this->replace['url'].'" data-imagesurl="'.$this->replace['PUBLIC_URL']['IMG'].'"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'RecordList.js"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'common.js"></script>
        '.$add_script.'
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        '.$add_css.'
        <link href="'.$this->replace['PUBLIC_URL']['CSS'].'style.min.css" rel="stylesheet" type="text/css">
        <link href="'.$this->replace['PUBLIC_URL']['CSS'].'custom.css" rel="stylesheet" type="text/css">
        '.$this->replace['google_tag'];

        return $result;
    }

    public function admin_head()
    {
        $add_script = '';
        if(isset($this->replace['add_script'])){
            foreach($this->replace['add_script'] as $value){
                $add_script .= '<script defer src="'.$value.'"></script>';
            }
        }
        $add_css = '';
        if(isset($this->replace['add_css'])){
            foreach($this->replace['add_css'] as $value){
                $add_css .= '<link href="'.$value.'" rel="stylesheet" type="text/css">';
            }
        }
        $result = '
        <meta charset="UTF-8">
        <title>'.SYSTEM_NAME.'</title>
        <meta name="robots" content="noindex,nofollow">
        <meta name="robots" content="noarchive">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/png" href="'.$this->replace['icon'].'" sizes="16x16 24x24 32x32 48x48 64x64">
        <link rel="apple-touch-icon" href="'.$this->replace['icon'].'">
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <script defer src="https://kit.fontawesome.com/79dd3834cf.js" crossorigin="anonymous"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'function.js" data-asyncurl="'.$this->replace['PUBLIC_URL']['ASYNC'].'" data-adminurl="'.ADMIN_DIR.'/" data-url="'.$this->replace['url'].'" data-imagesurl="'.$this->replace['PUBLIC_URL']['IMG'].'"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'RecordList.js"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'Sortable.js"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'admin_edit.js"></script>
        <script defer src="'.$this->replace['PUBLIC_URL']['JS'].'admin_common.js"></script>
        '.$add_script.'
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        '.$add_css.'
        <link href="'.$this->replace['PUBLIC_URL']['CSS'].'admin_style.min.css" rel="stylesheet" type="text/css">
        ';

        return $result;
    }

    public function nav()
    {
        $nav = '';
        foreach($this->replace['nav_list']['nav'] as $n){
            if($n['main_nav'] == 1){
                $nav .= '<li class="nav-item"><a class="nav-link" href="'.$this->replace['url'].$n['category_page'].'" data-id="'.$n['category_page'].'">'.$n['subject'].'</a></li>';
            }
        }
        $logo_bg = $this->replace['logo'] ? ' style="background-image:url('.$this->replace['logo'].')"':'';
        $site_name = empty($logo_bg) ? $this->replace['site_name']:'';
        $result = $this->replace['google_tag_no'].'
        <nav class="navbar navbar-light fixed-top navbar-expand-lg">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#p_nav" aria-controls="p_nav" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a href="'.$this->replace['url'].'" class="logo me-lg-5"'.$logo_bg.'>'.$site_name.'</a>
                <div class="collapse navbar-collapse justify-content-start" id="p_nav">
                    <ul class="navbar-nav">
                    '.$nav.'
                    </ul>
                </div>
                <a href="#" id="nav_search_btn"><i class="fas fa-search"></i></a>
                <div id="nav_search_area" class="shadow">
                    '.$this->replace['nav_form'].'
                    <nav aria-label="Page navigation" class="search_result_nav"></nav>
                    <div id="search_result"></div>
                </div>
            </div>
        </nav>
        ';
        return $result;
    }

    public function admin_nav()
    {
        $url = $this->replace['url'].ADMIN_DIR;
        $auth = isset($this->replace['login']['auth']) ? $this->replace['login']['auth']:'';

        $icon_img = isset($this->replace['login']['icon']) && $this->replace['login']['icon'] ? '<img src="'.$this->replace['PUBLIC_URL']['IMG'].'account/'.$this->replace['login']['id'].'/'.$this->replace['login']['icon'].'">':'<i class="fas fa-user-circle me-1"></i>';
        $icon = isset($this->replace['login']) && $this->replace['login'] ? $icon_img.$this->replace['login']['name']:'';
        $icon .= '<span class="d-none" id="authority_id">'.$auth.'</span>';
        $withdrawal_modal = '';
        if(isset($this->replace['withdrawal_modal_flag']) && $this->replace['withdrawal_modal_flag'] == 1 && $auth == 1){
            $withdrawal_modal = '
            <li class="nav-item">
                <a href="'.$url.'/withdrawal_modal" class="nav-link" data-id="withdrawal_modal">離脱モーダル設定</a>
            </li>';
        }
        $contact = '';
        if(isset($this->replace['contact_flag']) && $this->replace['contact_flag'] == 1 && $auth == 1){
            $contact = '
            <li class="nav-item">
                <a href="'.$url.'/smtp" class="nav-link" data-id="smtp">メール送信設定</a>
            </li>';
        }

        $category = $auth == 1 ? '<li class="nav-item"><a href="'.$url.'/category" class="nav-link" data-id="category">カテゴリー</a></li>':'';
        $sidenav = $auth == 1 ? '<li class="nav-item"><a href="'.$url.'/sidenav" class="nav-link" data-id="sidenav">サイドナビ</a></li>':'';
        $template = '';
        if($auth == 1){
            $template = '
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-id="html">
                HTMLテンプレート
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="'.$url.'/html/template/index">ホーム</a></li>
                <li><a class="dropdown-item" href="'.$url.'/html/template/category">カテゴリー</a></li>
                <li><a class="dropdown-item" href="'.$url.'/html/template/content">コンテンツ</a></li>
            </ul>
            </li>
            ';
        }
        $css = $auth == 1 ? '<li class="nav-item"><a href="'.$url.'/css" class="nav-link" data-id="css">カスタムCSS</a></li>':'';
        $dictionary = $auth == 1 ? '<li class="nav-item"><a href="'.$url.'/dictionary" class="nav-link" data-id="dictionary">言語設定</a></li>':'';
        $account = $auth == 1 ? '<li class="nav-item"><a href="'.$url.'/account" class="nav-link" data-id="account">アカウント</a></li>':'';
        $setting = $auth == 1 ? '<li class="nav-item"><a href="'.$url.'/setting" class="nav-link" data-id="setting">基本設定</a></li>':'';
        $result = '
        <nav class="mt-2">
            <div class="text-center">
                <img src="'.$this->replace['logo'].'" class="w-50">
            </div>
            <div class="user">'.$icon.'</div>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="'.$url.'" class="nav-link" data-id="index">ダッシュボード</a>
                </li>
                '.$category.'
                <li class="nav-item">
                    <a href="'.$url.'/content" class="nav-link" data-id="content">コンテンツ</a>
                </li>
                '.$sidenav.'
                '.$withdrawal_modal.'
                '.$contact.'
                '.$template.'
                '.$css.'
                '.$dictionary.'
                '.$account.'
                '.$setting.'
                <li class="nav-item">
                    <a href="'.$this->replace['url'].'" class="nav-link" target="_blank">公開サイト</a>
                </li>
                <li class="nav-item mt-5">
                    <p class="logout_btn btn btn-sm btn-secondary">ログアウト</p>
                </li>
            </ul>
            <div id="sp_nav">
                <div class="btn-trigger" id="btn01">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
        ';
        return $result;
    }

    public function footer() : string
    {
        $nav = '';
        foreach($this->replace['nav_list']['nav'] as $n){
            if($n['category_page'] != 'index' && $n['main_nav'] == 1){
                $nav .= '<li class="nav-item caret"><a class="nav-link" href="'.$this->replace['url'].$n['category_page'].'" data-id="'.$n['category_page'].'" style="'.$this->replace['ft_color'].'">'.$n['subject'].'</a></li>';
            }
        }

        $result = '
        <footer style="'.$this->replace['ft_color'].$this->replace['f_color'].$this->replace['f_image'].'">
            <div class="container">
                <div class="row g-md-5">
                    <div class="col-12 col-lg-6" style="border-right: 1px solid rgba('.implode(',', $this->replace['ft_color_rgb']).',.3);">
                        '.($this->replace['f_logo'] ? '<img src="'.$this->replace['f_logo'].'" class="logo">':'').'
                        <p>'.nl2br($this->replace['footer_text']).'</p>
                        <ul class="sns">
                        '.($this->replace['facebook'] ? '<li class="list-inline-item"><a href="'.$this->replace['facebook'].'" title="Facebook" class="social_item" target="_blank"><img src="'.$this->replace['url'].'images/facebook.png" alt="facebook"></a></li>':'').'
                        '.($this->replace['instagram'] ? '<li class="list-inline-item"><a href="'.$this->replace['instagram'].'" title="Instagram" class="social_item" target="_blank"><img src="'.$this->replace['url'].'images/instagram.png" alt="instagram"></a></li>':'').'
                        '.($this->replace['linkedin'] ? '<li class="list-inline-item"><a href="'.$this->replace['linkedin'].'" title="Linkedin" class="social_item" target="_blank"><img src="'.$this->replace['url'].'images/linkedin.png" alt="linkedin"></a></li>':'').'
                        '.($this->replace['twitter'] ? '<li class="list-inline-item"><a href="'.$this->replace['twitter'].'" title="Twitter" class="social_item" target="_blank"><img src="'.$this->replace['url'].'images/Twitter.png" alt="Twitter"></a></li>':'').'
                        </ul>
                    </div>
                    <div class="col-12 col-lg-6">
                        <nav>
                            <h4 class="mb-3">カテゴリー</h4>
                            <ul>
                                '.$nav.'
                            </ul>
                        </nav>
                    </div>
                </div>
                <small style="'.$this->replace['ft_color'].'" class="text-center">&copy; '.date('Y') .' <a href="'.($this->replace['copyright_url'] ? $this->replace['copyright_url']:$this->replace['url']).'" target="_blank" style="'.$this->replace['ft_color'].'">'.$this->replace['site_name'].'</a></small>
            </div>
        </footer>
        <i id="to_top" class="fas fa-chevron-up bg-dark text-white p-3 rounded-circle" role="button" style="font-size:1rem;"></i><noscript><p class="js_error">Please give permission for Javascript in your browser</p></noscript>';

        if(isset($this->replace['withdrawal_modal']) && $this->replace['withdrawal_modal']){
            $result .= '
            <div class="modal fade" id="withdrawal_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            '.$this->replace['withdrawal_modal'].'
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            ';
        }

        return $result;
    }

    public function admin_footer()
    {
        return '
        <footer>
        <small class="text-center">&copy; '.SYSTEM_NAME.'</small>
        </footer>
        <div class="modal fade" id="memo_modal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header pt-2 pb-0 border-bottom-0">
        <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"></div>
        </div>
        </div>
        </div>
        <i id="to_top" class="fas fa-chevron-up bg-dark text-white p-3 rounded-circle" role="button" style="font-size:1rem;"></i><noscript><p class="js_error">Please give permission for Javascript in your browser</p></noscript>
        ';
    }

    private function contact_form($option = null)
    {
        $result = '';

        if($this->replace['contact_flag']){
            $result = '
            <form name="contact_form" class="bg-white rounded">
                <div class="mb-3">'.$option['html'].'</div>
                <h4>'.$this->replace['CONTACT_TTL'].'</h4>
                <div class="mb-3">
                    <label for="lbl_name" class="form-label">'.$this->replace['NAME_TTL'].'</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="" id="lbl_name" required>
                </div>
                <div class="mb-3">
                    <label for="lbl_email" class="form-label">'.$this->replace['EMIAL_TTL'].'</label>
                    <input type="email" name="email" class="form-control form-control-sm" value="" id="lbl_email" required>
                </div>
                <div class="mb-3">
                    <label for="lbl_inquiry" class="form-label">'.$this->replace['INQUIRY_TTL'].'</label>
                    <textarea class="form-control form-control-sm" rows="5" name="inquiry" id="lbl_inquiry" required></textarea>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-sm btn-primary" id="contact_form">'.$this->replace['SEND_TTL'].'</button>
                </div>
                <input type="hidden" name="id" value="'.$this->replace['site_id'].'">
                <input type="hidden" name="lang" value="'.$this->replace['lang'].'">
                <div id="contact_form_check" class="d-none">'.$this->replace['SEND_CHECK'].'</div>
            </form>
            ';
        }
        return $result;
    }

    public function content_area($record)
    {
        $result = '';
        if($record){
            $date_format = $this->replace['lang'] == 'ja' ? 'Y年m月d日':'F j, Y';
            $thumb = $record->thumbnail ? '<img src="'.$this->replace['PUBLIC_URL']['IMG'].'content/'.$record->id.'/'.$record->thumbnail.'" class="mb-4">':'';

            $icon = $record->account_icon ? '<span class="icon me-1" style="background-image:url('.$this->replace['PUBLIC_URL']['IMG'].'account/'.$record->account_id.'/'.$record->account_icon.')"></span>':'';

            $sns = '<a class="facebook" href="https://www.facebook.com/sharer.php?u='.urlencode($this->replace['url'].$record->page).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
            $sns .= '<a class="twitter" href="https://twitter.com/intent/tweet?text='.urlencode($this->replace['url'].$record->title).'&url='.urlencode($this->replace['url'].$record->page).'&via=" target="_blank"><i class="fab fa-twitter"></i></a>';
            $description = $this->replace['url'].$record->title;
            $sns .= '<a class="pinterest" href="https://www.pinterest.jp/pin/create/button/?url='.urlencode($this->replace['url'].$record->page).'&media='.urlencode($this->replace['PUBLIC_URL']['IMG'].'content/'.$record->id.'/'.$record->thumbnail).'&description='.urlencode($description).'" target="_blank" target="_blank"><i class="fab fa-pinterest-p"></i></a>';
            $sns .= '<a class="whatsapp" href="https://api.whatsapp.com/send?text='.urlencode($this->replace['url'].$record->title).'%20%0A%0A%20'.urlencode($this->replace['url'].$record->page).'" target="_blank"><i class="fab fa-whatsapp"></i></a>';

            $table_of_contents = '';
            if($record->table_of_contents){
                $table_of_contents = '
                <div class="table_of_contents">
                    <p>'.$this->replace['TABLE_OF_CONTENTS'].'</p>
                    '.$record->table_of_contents.'
                </div>';
            }
            $rep_value = ['{table_of_contents}' => $table_of_contents];
            $html = str_replace( array_keys( $rep_value ), array_values( $rep_value ), $record->html);
            $html = $this->resourceReplace($html);

            $result .= '
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="'.$this->replace['url'].'">'.$this->replace['HOME'].'</a></li>
                    <li class="breadcrumb-item"><a href="'.$this->replace['url'].$record->category_page.'">'.$record->category_subject.'</a></li>
                    
                </ol>
            </nav>
            <article>
                <h1>'.$record->title.'</h1>
                <div class="editor row d-lg">
                    <div class="col-12">
                        <div class="d-flex justify-content-start">
                        '.$icon.'<p>'.$record->account_name.'</p>
                        <p class="mx-2">|</p>
                        <p>'.date($date_format, strtotime($record->release_date)).'</p>
                        </div>
                    </div>
                </div>
                '.$thumb.'
                <div class="content_body">
                    <div class="sns">
                        <span class="share"><i class="fas fa-share-alt"></i> Share</span>
                        '.$sns.'
                    </div>
                    '.$html.'
                </div>
            </article>
            ';
            $result .= $this->otherArticles();
            if($record->banner){
                $result .= '
                <div id="admodal_elm" class="d-none" data-timer="'.$record->banner_timer.'">
                <i class="fa-solid fa-circle-xmark" id="admodal_close"></i>';
                $img = '<img src="'.$this->replace['PUBLIC_URL']['IMG'].'content/'.$record->id.'/'.$record->banner.'" id="admodal_img">';
                if($record->banner_link){
                    $result .= '<a href="'.$record->banner_link.'" target="_blank" id="admodal_linkurl">';
                    $result .= $img;
                    $result .= '</a>';
                }else{
                    $result .= $img;
                }
                $result .= '</div>';
            }
        }
        return $result;
    }

    public function otherArticles()
    {
        $date_format = $this->replace['lang'] == 'ja' ? 'Y年m月d日':'F j, Y';
        $before = $this->replace['other_articles_records']['before'] ? '<a href="'.$this->replace['url'].$this->replace['other_articles_records']['before'][0]->category_page.'/'.$this->replace['other_articles_records']['before'][0]->page.'">'.$this->replace['other_articles_records']['before'][0]->title.'</a>':'';
        $after = $this->replace['other_articles_records']['after'] ? '<a href="'.$this->replace['url'].$this->replace['other_articles_records']['after'][0]->category_page.'/'.$this->replace['other_articles_records']['after'][0]->page.'">'.$this->replace['other_articles_records']['after'][0]->title.'</a>':'';
        $result = '
        <section class="other_articles">
            <div class="row">
                <div class="col-6">
                    <span><i class="me-1 fas fa-chevron-left"></i>'.$this->replace['BEFORE'].'</span>
                    <p>'.$before.'</p>
                </div>
                <div class="col-6">
                    <span>'.$this->replace['NEXT'].'<i class="ms-1 fas fa-chevron-right"></i></span>
                    <p>'.$after.'</p>
                </div>
            </div>
        </section>
        ';
        return $result;
    }

    public function side_nav($options = null)
    {
        $result = '';
        if($options){
            $result = $options->side_img ? '<img src="'.$this->replace['PUBLIC_URL']['IMG'].'sidenav/'.$options->id.'/'.$options->side_img.'">':'';
            $result .= $options->html ? $options->html:'';
        }
        return $result;
    }

    /**
    *public recommend部分
    *
    * @recommend
    *
    * @return HTML
    */
    public function recommend($record)
    {
        $result = '';
        foreach($record as $list){
            $col_3 = '';
            if($list->thumbnail){
                $col_3 = '<div class="col-3"><div class="thumb" style="background-image:url('.$this->replace['PUBLIC_URL']['IMG'].'content/'.$list->id.'/s_'.$list->thumbnail.')"></div></div>':'';
            }
            //$thumb = $list->thumbnail ? '<div class="thumb" style="background-image:url('.$this->replace['PUBLIC_URL']['IMG'].'content/'.$list->id.'/s_'.$list->thumbnail.')"></div>':'';
            //$col_3 = $thumb ? '<div class="col-3">'.$thumb.'</div>':'';
            $result .= '
            <div class="row mb-4">
                '.$col_3.'
                <div class="col-'.($col_3 ? 9:12).'">
                    <h3><a href="'.$this->replace['url'].$list->category_page.'/'.$list->page.'">'.strWidth($list->title, 100).'</a></h3>
                </div>
            </div>
            ';
        }

        return $result;
    }

}
