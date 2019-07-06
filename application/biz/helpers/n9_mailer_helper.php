<?php
function biz_send_mail($post, $file = null) {
    $url = 'http://4biz.vn/php_mail/send_mail.php';
    $CI =& get_instance();
		$CI->load->library('email'); 
    if(!empty($file)) {
        $filename = $file['file_upload']['name'];
        $filedata = $file['file_upload']['tmp_name'];
        $filesize = $file['file_upload']['size'];
    }
    $param = array(
        'Username'        => $CI->config->item('config_email_account'),
        'Password'        => $CI->config->item('config_email_pass'),
        'FromName'        => $post['from_name'],
        'address_list'    => $post['address_list'],
        'Subject'         => $post['subject'],
        'Body'            => $post['body'],
				'file_name'       => isset($post['file_name'])?$post['file_name']:'',
        'type'            => $post['type'],
    );

    if(!empty($file)) {
        $param["filedata"] = "@$filedata";
        $headers = array("Content-Type:multipart/form-data");
    }

    $ch = curl_init($url);
    if(!empty($file)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);

    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, count($param));

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		if(!empty($filesize))
			{
				 curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);
			}
   
    $result = curl_exec($ch);

    curl_close($ch);
		return $result;

}