<?php
namespace TM;

class Edit
{
    private $login = [];
    private $lang = 'ja';
    private $url = '';
    private $DB = '';
    private $records = [];
    private $result = [
        'result' => 0,
        'msg' => 'Update Failed',
        'finish' => 0,
        'id' => 0,
        'id_num' => 0,
        'data_id' => 0,
        'files_upload' => 0,
        'flg' => true,
        'table' => '',
        'edit' => 'add',
        'dir' => ''
    ];
    public function __construct($options)
    {
        try {
            $this->login = (new Auth('array'))->checkExec();
            if ($this->login['result'] == 0) throw new \Exception($this->login['msg']);
            $this->lang = $options['lang'];
            $this->url = $options['url'];
            $Post = new Post;
            $this->result['table'] = $Post->gen('table');
            $this->result['dir'] = $this->result['table'];
            $this->result['id'] = $Post->gen('id');

            $keys = $Post->setTable($this->result['table']);
            foreach($keys as $key => $null){
                $this->records[$key] = $Post->gen($key, $null);
            }
            if(empty($this->result['id'])){
                if(empty($this->records['created_id'])){
                    $this->records['created_id'] = $this->login['id'];
                }
                $this->records['modified_id'] = $this->login['id'];
            }

            if($this->result['table'] == 'account' && $_POST['passwd']){
                $this->records['passwd'] = password_hash($_POST['passwd'], PASSWORD_BCRYPT);
            }
            if($this->result['table'] == 'content' && empty($_POST['release_date'])){
                $this->records['release_date'] = date('Y-m-d H:i:s');
            }
            if($this->result['table'] == 'content' || $this->result['table'] == 'category'){
                $this->records['page'] = preg_replace("/( |　)/", "", $this->records['page']);
            }

            $this->DB = new DB;
        } catch (\Exception $e) {
            echo '{"result":0,"msg":"'. $e->getMessage() . '","class":"false"}';
            exit;
        }
    }

    private function finish()
    {
        if($this->result['finish'] == 1){
            if($this->result['id']){
                //更新
                $this->result['result'] = $this->DB->update($this->result['table'], ['id' => $this->result['id']], $this->records);
                $this->result['id_num'] = $this->result['id'];
                $this->result['data_id'] = $this->result['id'];
                $this->result['edit'] = 'update';
            }else{
                $this->result['result'] = $this->DB->insert($this->result['table'], $this->records);
                $this->result['id_num'] = $this->result['result'];
                $this->result['data_id'] = $this->result['result'];
            }
        }else{
            throw new \Exception('データ送信エラー');
        }
    }

    private function filesUpload()
    {
        if($this->result['files_upload'] == 1){
            $FileCtl = new FileCtl;
            $upload = 0;
            $file_key = [
                'site_logo' => 'site_logo',
                'site_f_logo' => 'site_f_logo',
                'f_image' => 'f_image',
                'site_icon' => 'site_icon',
                'thumbnail' => 'thumbnail',
                'banner' => 'banner',
                'side_img' => 'side_img',
                'icon' => 'icon'
            ];
            foreach($file_key as $f_key => $new_name){
                if(isset($_FILES[$f_key]) && !empty($_FILES[$f_key]['name'][0])){
                    $ret = $FileCtl->uploadMulti(
                        SERVER_DIR['IMG'].$this->result['table'].'/' . $this->result['id_num'] . '/',
                        $f_key,
                        0,
                        $new_name,
                        (1048576 * 5)
                    );
                    $file_name = $new_name.'.'.$ret[2];
                    $ori_file = SERVER_DIR['IMG'].$this->result['table'].'/' . $this->result['id_num'] . '/' .$file_name;
                    $Imagick = new \Imagick();
                    $Imagick->readImage($ori_file);
                    $format = strtolower($Imagick->getImageFormat());
                    if($format == 'jpeg'){
                        $exif_data = @exif_read_data($ori_file);
                        if(isset($exif_data['Orientation'])){
                            imageOrientation($ori_file, $exif_data['Orientation']);
                        }
                    }
                    if($new_name != 'site_logo' && $new_name != 'site_f_logo' && $new_name != 'f_image' && $new_name != 'side_img' && $new_name != 'banner'){
                        $Imagick = new \Imagick($ori_file);
                        if($new_name == 'site_icon' || $new_name == 'icon'){
                            $width = 144;
                            $height = 144;
                        }elseif($new_name == 'thumbnail'){
                            $width = 300;
                            $height = 300;
                        }
                        // オリジナルのサイズ取得
                        $width_org = $Imagick->getImageWidth();
                        $height_org = $Imagick->getImageHeight();
                        // 縮小比率を計算
                        $ratio = $width_org / $height_org;
                        if ($width / $height > $ratio) {
                            $width = $height * $ratio;
                        } else {
                            $height = $width / $ratio;
                        }
                        // 縮小実行
                        $Imagick->scaleImage($width, $height);
                        // 保存
                        //既存の画像圧縮は
                        // $im = new Imagick('test.jpg');
                        // $im->setImageCompressionQuality(10);
                        // $im->writeImage('test_1.jpg');
                        $Imagick->setCompressionQuality(80);
                        if($new_name == 'thumbnail'){
                            $thumb_s = 's_'.$new_name.'.'.$ret[2];
                            $Imagick->writeImage(SERVER_DIR['IMG'].$this->result['table'].'/' . $this->result['id_num'].'/'.$thumb_s);
                        }else{
                            $Imagick->writeImage($ori_file);
                        }
                        $Imagick->destroy();
                    }
                    $this->DB->update($this->result['table'], ['id' => $this->result['id_num']], [ $f_key => $file_name ]);
                    $this->result['result'] = 1;
                }
            }
        }
    }

    private function resultJson()
    {
        $this->result['msg'] = '内容に変更はありません';
        if (is_numeric(trim($this->result['result'])) && trim($this->result['result']) > 0) {
            $this->result['result'] = 1;
            $this->result['msg'] = 'Created';
            if($this->result['id']){
                $modified_date = date('Y-m-d H:i:s');
                $this->DB->update(
                    $this->result['table'],
                    ['id' => $this->result['id']],
                    ['modified' => $modified_date, 'modified_id' => $this->login['id']]
                );
                if($this->result['table'] == 'content'){
                    $this->DB->update(
                        'category',
                        ['id' => $this->records['category']],
                        ['article_modified' => $modified_date]
                    );
                    $this->DB->update(
                        'category',
                        ['id' => 1],
                        ['article_modified' => $modified_date]
                    );
                    // h2 type
                    //preg_match_all('/<h2(.*?)>(.*?)<\/h2>/i', $this->records['html'], $matches);

                    // h2,h3 type
                    preg_match_all('/<h[1-3]>(.*?)<\/h[1-3]>/i', $this->records['html'], $matches);
                    if($matches){
                        // h2,h3 type
                        $rep_value = [];
                        $h = 2;
                        $i = 0;
                        $tag = '<ul class="table_of_contents">';
                        foreach($matches[0] as $value){
                            if(strpos($value,'</h3>') && $h == 2){
                                $tag .= '<ul>';
                            }
                            if(strpos($value,'</h2>') && $h == 3){
                                $tag .= '</ul>';
                            }
                            $h = strpos($value,'</h2>') ? 2:3;
                            $tag .= '<li class="p_link" data-id="p_link_'.$i.'">'.$matches[1][$i].'</li>';
                            $rep_value[$value] = '<h'.$h.' id="p_link_'.$i.'">'.$matches[1][$i].'</h'.$h.'>';
                            $i++;
                        }
                        $tag .= '</ul>';

                        $this->DB->update(
                            $this->result['table'],
                            ['id' => $this->result['id']],
                            [
                                'html' => str_replace( array_keys( $rep_value ), array_values( $rep_value ), $this->records['html']),
                                'table_of_contents' => $tag
                            ]
                        );
                    }
                    //sitemap更新
                    //category content
                    $sitemap = new SitemapGenerator;
                    $site_records = $this->DB->query('SELECT `modified`,`page` FROM `category`', []);
                    if($site_records){
                        $sitemap->add([
                            'loc'        => $this->url,
                            'lastmod'    => date('c'),
                            'priority'   => '1.0'
                        ]);
                        foreach($site_records as $value){
                            $sitemap->add([
                                'loc'        => $this->url.$value->page,
                                'lastmod'    => date('c', strtotime($value->modified)),
                                'priority'   => '0.80'
                            ]);
                        }
                    }
                    $site_records = $this->DB->query('SELECT `content`.`modified`,`content`.`page`,`category`.`page` AS `category_page` FROM `content` LEFT OUTER JOIN `category` ON (`content`.`category` = `category`.`id`) WHERE `content`.`publishing` = 1', []);
                    if($site_records){
                        foreach($site_records as $value){
                            $sitemap->add([
                                'loc'        => $this->url.$value->category_page.'/'.$value->page,
                                'lastmod'    => date('c', strtotime($value->modified)),
                                'priority'   => '0.80'
                            ]);
                        }
                    }
                    $sitemap->generate(PRIVATE_DIR.'sitemap.xml');

                }
                $this->result['msg'] = '更新しました';
            }
        }else{
            $this->result['result'] = 2;
        }
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    public function setting()
    {
        if(!empty($this->records['site_name'])){
            $this->result['finish'] = 1;
            $this->result['files_upload'] = 1;
        }
        $this->finish();
        $this->filesUpload();
        $this->resultJson();
    }

    public function smtp()
    {
        $this->result['finish'] = 1;
        $this->finish();
        $this->resultJson();
    }

    public function content()
    {
        if(!empty($this->records['page']) && !empty($this->records['title'])){
            $this->result['finish'] = 1;
            $this->result['files_upload'] = 1;
        }
        $this->finish();
        $this->filesUpload();
        $this->resultJson();
    }

    public function sidenav()
    {
        $this->result['finish'] = 1;
        $this->result['files_upload'] = 1;
        $this->finish();
        $this->filesUpload();
        $this->resultJson();
    }

    public function withdrawal_modal()
    {
        $this->result['finish'] = 1;
        $this->finish();
        $this->resultJson();
    }

    public function account()
    {
        if(!empty($this->records['account']) && !empty($this->records['name'])){
            $this->result['finish'] = 1;
            $this->result['files_upload'] = 1;
        }
        $this->finish();
        $this->filesUpload();
        $this->resultJson();
    }

    public function category()
    {
        $this->result['finish'] = 1;
        $this->finish();
        $this->resultJson();
    }

    public function dictionary()
    {

    }

}
