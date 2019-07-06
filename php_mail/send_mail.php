<?php
require_once ('phpmailer/class.phpmailer.php');
$mail = new PHPMailer();

$post = $_POST;

if($post['type'] == 'sequence')
    $post['Body'] = unserialize($post['Body']);
$post['address_list'] = unserialize($post['address_list']);

$mail->CharSet   = 'UTF-8';
$mail->IsSMTP();  // telling the class to use SMTP
$mail->Mailer    = "smtp";
$mail->Host      = "ssl://smtp.gmail.com";
$mail->Port      = 465;
$mail->SMTPDebug = 1;
$mail->SMTPAuth  = true; // turn on SMTP authentication
$mail->Username  = $post['Username']; // SMTP username
$mail->Password  = $post['Password']; // SMTP password
//$mail->From     = $options['From'];
$mail->FromName  = $post['FromName'];
$mail->Subject   = $post['Subject'];

$mail->IsHTML(true);
$mail->WordWrap = 50;

if($post['type'] == 'sequence') {
    if(!empty($post['address_list'])){
        foreach($post['address_list'] as $key => $item) {
            $mail->AddAddress($item['AddAddress'], $item['AddAddress_name']);
            $mail->Body      = $post['Body'][$key];

            if(!empty($_FILES))
                $mail->AddAttachment($_FILES['filedata']['tmp_name'], $post['file_name']);

            return @$mail->Send();
            $mail->ClearAddresses();
        }
    }
}elseif($post['type'] == 'at_the_same_time') {
    if(!empty($post['address_list'])){
        foreach($post['address_list'] as $item) {
            $mail->AddAddress($item['AddAddress'], $item['AddAddress_name']);
        }

        $mail->Body      = $post['Body'];
        if(!empty($_FILES))
            $mail->AddAttachment($_FILES['filedata']['tmp_name'], $post['file_name']);

        return @$mail->Send();
    }
}