<?php 
require('./init/resource.php');
require('./init/config.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo SYSTEM_NAME;?></title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="robots" content="noarchive">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <script defer src="https://kit.fontawesome.com/79dd3834cf.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script defer src="<?php echo PUBLIC_URL['JS'];?>admin_function.js" data-asyncurl="<?php echo PUBLIC_URL['ASYNC'];?>" data-adminurl="<?php echo ADMIN_DIR;?>/" data-url="<?php echo PUBLIC_URL['URL'];?>"></script>
    <script defer src="<?php echo PUBLIC_URL['JS'];?>admin_install.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href="<?php echo PUBLIC_URL['CSS'];?>admin_style.min.css" rel="stylesheet" type="text/css">
</head>

<body class="signin">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center _100">
            <form autocomplete="off" name="install">
                <div class="row bg-white rounded shadow">
                    <div class="col-12 pt-5">
                        <div class="text-center">
                            <img src="<?php echo PUBLIC_URL['URL'];?>images/setting/1/site_logo.svg" class="w-25">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-5">
                        <h4>データベース設定</h4>
                        <label for="" class="">データベースホスト名</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="DATABASE_HOST" value="localhost" required="">
                        </div>
                        <label for="" class="">データベース名</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="DATABASE_NAME" required="">
                        </div>
                        <label for="" class="">データベースユーザー名</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="DATABASE_USER" required="">
                        </div>
                        <label for="" class="">データベースパスワード</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="DATABASE_PASS" required="">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-5">
                        <h4>初期アカウント設定</h4>
                        <label for="" class="">ログインアカウント</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="account" value="admin" required="">
                        </div>
                        <label for="" class="">ログインパスワード</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="passwd" required="">
                        </div>
                        <label for="" class="">アカウント名</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="name" value="管理者" required="">
                        </div>
                        <button type="submit" class="btn btn-outline-primary mt-5 mx-auto d-block" id="btn_submit">設定</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
