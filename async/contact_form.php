<?php
require('../init/config.php');
try {
    $lang = $_POST['lang'] ?? 'en';
    $replace = [];
    $LangList = (new TM\LangList)->getLangList();
    foreach($LangList as $key => $value){
        $replace[$key] = $value[$lang];
    }
    $msg = $replace['SEND_FALSE'];
    $result = 0;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $inquiry = $_POST['inquiry'] ?? '';
    $id = $_POST['id'] ?? '';
    if($name && $email && $inquiry && $id){
        $DB = new TM\DB;
        $site = $DB->query("SELECT `site_name` FROM `setting` WHERE `id` = ?", [$id]);
        $smtp = $DB->query("SELECT `host`,`port`,`encrypt`,`user`,`passwd`,`email`,`from_name`,`encoding`,`charset` FROM `smtp` WHERE `id` = ?", [$id]);
        if($smtp[0]->host && $smtp[0]->user && $smtp[0]->passwd && $smtp[0]->email){

            $subject = $site[0]->site_name.' '.$replace['INQUIRY_TTL'];
            $mail_body = $replace['NAME_TTL']."\n";
            $mail_body .= $name."\n\n";
            $mail_body .= $replace['EMIAL_TTL']."\n";
            $mail_body .= $email."\n\n";
            $mail_body .= $replace['INQUIRY_TTL']."\n";
            $mail_body .= $inquiry;
            $PHPMailer = new \PHPMailer\PHPMailer\PHPMailer();
            //$PHPMailer->SMTPDebug = 2;
            $PHPMailer->isSMTP();
            $PHPMailer->Host = $smtp[0]->host;
            $PHPMailer->Username = $smtp[0]->user;
            $PHPMailer->Password = $smtp[0]->passwd;
            $PHPMailer->setFrom($email);
            $PHPMailer->SMTPAuth = true;
            $PHPMailer->SMTPSecure = $smtp[0]->encrypt ?? 'tls';
            $PHPMailer->Port = $smtp[0]->port ?? 587;
            $PHPMailer->Encoding = $smtp[0]->encoding ?? 'base64';
            $PHPMailer->CharSet = $smtp[0]->charset ?? 'UTF-8';
            $PHPMailer->Subject = $subject;
            $PHPMailer->Body = $mail_body;
            $PHPMailer->addAddress($smtp[0]->email);
            $PHPMailer->send();
            $PHPMailer->clearAddresses();
            $msg = $replace['SEND_TRUE'];
            $result = 1;

            //送信内容確認メール
            $PHPMailer->Subject     = '[ '.$site[0]->site_name.' ]　お問い合わせがありがとうございます';
            $PHPMailer->Body        = $mail_body;
            $PHPMailer->setFrom($smtp[0]->email, $site[0]->site_name);
            $PHPMailer->addAddress($email);
            $PHPMailer->send();
            $PHPMailer->clearAddresses();
        }
    }
    echo '{"result":"'.$result.'","msg":"'.$msg.'"}';
    exit;
} catch (Exception $e) {
    echo '{"result":0,"msg":"'. $msg .'"}';
}
