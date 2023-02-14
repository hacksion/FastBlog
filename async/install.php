<?php
try {
    $DATABASE_NAME = $_POST['DATABASE_NAME'] ?? '';
    $DATABASE_HOST = $_POST['DATABASE_HOST'] ?? '';
    $DATABASE_USER = $_POST['DATABASE_USER'] ?? '';
    $DATABASE_PASS = $_POST['DATABASE_PASS'] ?? '';
    $result = 'Error';
    $result_number = 0;
    if($DATABASE_NAME && $DATABASE_HOST && $DATABASE_USER && $DATABASE_PASS){
        $dbh = new \PDO(
            'mysql:dbname='.$DATABASE_NAME.';host='.$DATABASE_HOST.';port=3306',$DATABASE_USER,$DATABASE_PASS
        );
        $DBFILE = "<?php
    define('DATABASE_TYPE', /* データベースタイプ　 => */ 'mysql');
    define('DATABASE_NAME', /* データベース名 => */ '".$DATABASE_NAME."');
    define('DATABASE_HOST', /* データベースホスト名　 => */ '".$DATABASE_HOST."');
    define('DATABASE_USER', /* データベースユーザー名 => */ '".$DATABASE_USER."');
    define('DATABASE_PASS', /* データベースパスワード => */ '".$DATABASE_PASS."');";
        $result = file_put_contents(dirname(__DIR__).'/init/db.php', $DBFILE);

        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->query('SET NAMES UTF8');
        $dbh->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');
        $dbh->query('SET AUTOCOMMIT = 0');
        $dbh->query('SET time_zone = "+00:00"');

        $dbh->query("CREATE TABLE `access_log` (
            `content_id` int(11) NOT NULL,
            `access_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `page` varchar(100) NOT NULL,
            `ip` varchar(15) DEFAULT NULL
            ) ENGINE=InnoDB 
            DEFAULT CHARSET=utf8mb4");

        $dbh->query("CREATE TABLE `account` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL,
            `modified` datetime NOT NULL,
            `created_id` int(11) NOT NULL DEFAULT '1',
            `modified_id` int(11) NOT NULL DEFAULT '1',
            `name` varchar(30) NOT NULL,
            `account` varchar(50) NOT NULL,
            `passwd` varchar(255) NOT NULL,
            `icon` varchar(255) DEFAULT NULL,
            `auth` tinyint(2) UNSIGNED NOT NULL DEFAULT '1',
            `del` tinyint(1) NOT NULL DEFAULT '0'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $dbh->query("CREATE TABLE `category` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `article_modified` datetime DEFAULT NULL,
            `created_id` int(11) NOT NULL DEFAULT '1' COMMENT '登録者ID',
            `modified_id` int(11) NOT NULL DEFAULT '1' COMMENT '更新者ID',
            `page` varchar(100) NOT NULL,
            `subject` varchar(30) NOT NULL,
            `h1` varchar(100) DEFAULT NULL,
            `title` varchar(100) NOT NULL,
            `description` text NOT NULL,
            `main_nav` tinyint(1) NOT NULL DEFAULT '1',
            `num` int(2) NOT NULL DEFAULT '1'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $dbh->query("CREATE TABLE `content` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_id` int(11) NOT NULL DEFAULT '1' COMMENT '登録者ID',
            `modified_id` int(11) NOT NULL DEFAULT '1' COMMENT '更新者ID',
            `author` int(11) NOT NULL DEFAULT '1',
            `category` int(11) NOT NULL DEFAULT '1',
            `page` varchar(100) NOT NULL,
            `title` varchar(255) NOT NULL,
            `description` text,
            `html` longtext,
            `table_of_contents` text,
            `thumbnail` varchar(50) DEFAULT NULL,
            `publishing` tinyint(1) NOT NULL DEFAULT '0',
            `release_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `banner` varchar(50) DEFAULT NULL,
            `banner_link` varchar(255) DEFAULT NULL,
            `banner_timer` decimal(2,0) NOT NULL DEFAULT '0',
            `access` int(10) UNSIGNED NOT NULL DEFAULT '0'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $dbh->query("CREATE TABLE `dictionary` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_id` int(11) NOT NULL DEFAULT '1',
            `modified_id` int(11) NOT NULL DEFAULT '1',
            `key_name` varchar(50) NOT NULL,
            `key_value` json DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $dbh->query("CREATE TABLE `setting` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_id` int(11) NOT NULL DEFAULT '1',
            `modified_id` int(11) NOT NULL DEFAULT '1',
            `site_name` varchar(255) NOT NULL DEFAULT 'Web Site Name',
            `index_title` varchar(255) DEFAULT NULL,
            `index_description` varchar(255) DEFAULT NULL,
            `index_count` decimal(2,0) NOT NULL DEFAULT '5',
            `blog_url` varchar(255) DEFAULT NULL,
            `copyright_url` varchar(255) DEFAULT NULL,
            `site_logo` varchar(20) NOT NULL DEFAULT 'logo.png',
            `site_f_logo` varchar(20) DEFAULT NULL,
            `site_icon` varchar(20) NOT NULL DEFAULT 'icon.png',
            `lang` varchar(20) NOT NULL DEFAULT 'ja_JP',
            `facebook` varchar(255) DEFAULT NULL,
            `instagram` varchar(255) DEFAULT NULL,
            `linkedin` varchar(255) DEFAULT NULL,
            `twitter` varchar(255) DEFAULT NULL,
            `google_tag` text,
            `google_tag_no` text,
            `withdrawal_modal` tinyint(1) NOT NULL DEFAULT '0',
            `noindex` tinyint(1) NOT NULL DEFAULT '0',
            `contact` tinyint(1) NOT NULL DEFAULT '0',
            `footer_text` text,
            `ft_color` varchar(7) NOT NULL DEFAULT '#e8e8e8',
            `f_color` varchar(7) NOT NULL DEFAULT '#1d212f',
            `f_image` varchar(20) DEFAULT NULL,
            `bg_color` varchar(7) NOT NULL DEFAULT '#ffffff'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $dbh->query("CREATE TABLE `sidenav` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_id` int(11) NOT NULL DEFAULT '1' COMMENT '登録者ID',
            `modified_id` int(11) NOT NULL DEFAULT '1' COMMENT '更新者ID',
            `sidenav_status` tinyint(1) NOT NULL DEFAULT '1',
            `side_img` varchar(50) DEFAULT NULL,
            `html` text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $dbh->query("CREATE TABLE `smtp` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_id` int(11) NOT NULL DEFAULT '1',
            `modified_id` int(11) NOT NULL DEFAULT '1',
            `host` varchar(255) DEFAULT NULL,
            `port` int(5) UNSIGNED NOT NULL DEFAULT '587',
            `encrypt` varchar(10) NOT NULL DEFAULT 'tls',
            `user` varchar(100) DEFAULT NULL,
            `passwd` varchar(100) DEFAULT NULL,
            `email` varchar(100) DEFAULT NULL,
            `from_name` varchar(100) DEFAULT NULL,
            `encoding` varchar(10) NOT NULL DEFAULT 'base64',
            `charset` varchar(10) NOT NULL DEFAULT 'UTF-8',
            `html` text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $dbh->query("CREATE TABLE `withdrawal_modal` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_id` int(11) NOT NULL DEFAULT '1',
            `modified_id` int(11) NOT NULL DEFAULT '1',
            `html` text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $dbh->query("ALTER TABLE `access_log`
        ADD UNIQUE KEY `content_id` (`content_id`,`access_datetime`,`page`) USING BTREE");
        $dbh->query("ALTER TABLE `account`
        ADD PRIMARY KEY (`id`)");
        $dbh->query("ALTER TABLE `category`
        ADD PRIMARY KEY (`id`),
        ADD KEY `page` (`page`)");
        $dbh->query("ALTER TABLE `content`
        ADD PRIMARY KEY (`id`),
        ADD KEY `category` (`category`),
        ADD KEY `page` (`page`),
        ADD KEY `release_date` (`release_date`)");
        $dbh->query("ALTER TABLE `dictionary`
        ADD PRIMARY KEY (`id`),
        ADD UNIQUE KEY `key_name` (`key_name`)");
        $dbh->query("ALTER TABLE `setting`
        ADD PRIMARY KEY (`id`)");
        $dbh->query("ALTER TABLE `sidenav`
        ADD PRIMARY KEY (`id`)");
        $dbh->query("ALTER TABLE `smtp`
        ADD PRIMARY KEY (`id`)");
        $dbh->query("ALTER TABLE `withdrawal_modal`
        ADD PRIMARY KEY (`id`)");

        require('../init/config.php');
        $today = date('Y-m-d H:i:s');
        $DB = new TM\DB;
        $DB->insert('account', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'name' => $_POST['name'],
            'account' => $_POST['account'],
            'passwd' => password_hash($_POST['passwd'], PASSWORD_BCRYPT),
            'icon' => 'default_account_icon.png',
            'auth' => 1,
            'del' => 0,
        ]);
        
        $DB->insert('category', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'article_modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'page' => 'sample_category',
            'subject' => 'サンプルカテゴリー',
            'h1' => 'サンプルカテゴリー',
            'title' => 'サンプルカテゴリー',
            'description' => '',
            'main_nav' =>1,
            'num' => 1
        ]);

        $DB->insert('content', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'author' => 1,
            'category' => 1,
            'page' => 'welcome-page',
            'title' => 'ようこそ Fast Blogへ',
            'description' => NULL,
            'html' => '<p>ようこそ Fast Blogへ</p>',
            'table_of_contents' => NULL,
            'thumbnail' => NULL,
            'publishing' => 1,
            'release_date' => $today,
            'banner' => NULL,
            'banner_link' => NULL,
            'banner_timer' => 0,
            'access' => 0,
        ]);

        $dic = [
            1 => ['DETAIL', '{"en": "Detail", "ja": "詳細はこちら..."}'],
            2 => ['HOME', '{"en": "Home", "ja": "ホーム"}'],
            3 => ['NEXT', '{"en": "Next", "ja": "次の記事"}'],
            4 => ['BEFORE', '{"en": "Before", "ja": "前の記事"}'],
            5 => ['RECOMMEND', '{"en": "Recommend", "ja": "おすすめの記事"}'],
            6 => ['SEARCH_TEXT', '{"en": "Keyword Search", "ja": "キーワード検索"}'],
            7 => ['NOT_FOUND', '{"en": "Not Found", "ja": "見つかりません"}'],
            8 => ['NOT_CONTENT', '{"en": "Content not found", "ja": "コンテンツが見つかりません"}'],
            9 => ['TABLE_OF_CONTENTS', '{"en": "Table of Contents", "ja": "目次"}'],
            10 => ['SEND_TRUE', '{"en": "Sent", "ja": "送信しました"}'],
            11 => ['SEND_FALSE', '{"en": "Transmission Failure", "ja": "送信失敗"}'],
            12 => ['SEND_CHECK', '{"en": "Do you want to send with this content?", "ja": "この内容で送信しますか？"}'],
            13 => ['NAME_TTL', '{"en": "Your Name", "ja": "お名前"}'],
            14 => ['EMIAL_TTL', '{"en": "E-Mail", "ja": "メールアドレス"}'],
            15 => ['INQUIRY_TTL', '{"en": "Inquiry", "ja": "お問合せ"}'],
            16 => ['SEND_TTL', '{"en": "Send", "ja": "送信"}'],
            17 => ['CONTACT_TTL', '{"en": "Contact Form", "ja": "お問合せフォーム"}'],
        ];

        foreach($dic as $key => $value){
            $DB->insert('dictionary', [
                'id' => $key,
                'created' => $today,
                'modified' => $today,
                'created_id' => 1,
                'modified_id' => 1,
                'key_name' => $value[0],
                'key_value' => $value[1],
            ]);
        }

        $DB->insert('setting', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'site_name' => 'Fast Blog',
            'index_title' => 'Fast Blog',
            'index_description' => NULL,
            'index_count' => 5,
            'copyright_url' => NULL,
            'site_logo' => 'site_logo.svg',
            'site_f_logo' => 'site_logo_f.svg',
            'site_icon' => 'site_icon.png',
            'lang' => 'ja_JP',
            'facebook' => NULL,
            'instagram' => NULL,
            'linkedin' => NULL,
            'twitter' => NULL,
            'google_tag' => NULL,
            'google_tag_no' => NULL,
            'withdrawal_modal' => 0,
            'noindex' => 1,
            'contact' => 0,
            'footer_text' => '<h3>Fast Blog</h3>',
            'ft_color' => '#d9d9d9',
            'f_color' => '#303030',
            'f_image' => NULL,
            'bg_color' => '#ffffff',
        ]);

        $DB->insert('sidenav', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'sidenav_status' => 0,
            'side_img' => NULL,
            'html' => NULL,
        ]);

        $DB->insert('smtp', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'host' => NULL,
            'port' => 587,
            'encrypt' => 'tls',
            'user' => NULL,
            'passwd' => NULL,
            'email' => NULL,
            'from_name' => NULL,
            'encoding' => 'base64',
            'charset' => 'UTF-8',
            'html' => NULL,
        ]);

        $DB->insert('withdrawal_modal', [
            'id' => 1,
            'created' => $today,
            'modified' => $today,
            'created_id' => 1,
            'modified_id' => 1,
            'html' => NULL,
        ]);
        //unlink(dirname(__DIR__).'/install.php');
        $result = 'インストールは完了しました。<br>'.dirname(__DIR__).'/install.phpのファイルを削除してください。';
        $result_number = 1;
    }

    echo '{"result":'.$result_number.',"msg":"'.$result.'"}';
} catch (\PDOException $e) {
    echo '{"result":0,"msg":"データベースが存在しないか<br>もしくはすでにテーブルが存在していますので<br>新しくデータベースを設定して<br>インストールしてください"}';
}
