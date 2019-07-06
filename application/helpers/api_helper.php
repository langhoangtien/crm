<?php
/**
 * Created by PhpStorm.
 * User: DELL XPS
 * Date: 2/11/2019
 * Time: 4:22 PM
 */

class google_api
{
    protected $recaptcha;

    function __construct()
    {
        $this->recaptcha->url = GOOGLE_RECAPTCHA_AUTH_URL;
        $this->recaptcha->secret_key = GOOGLE_RECAPTCHA_SECRET_KEY;
        $this->recaptcha->site_key = GOOGLE_RECAPTCHA_SITE_KEY;
    }

    function validate_captcha($response, $url = null, $secret_key = null)
    {
        if (empty($url)) {
            $url = $this->recaptcha->url;
        }
        if (empty($secret_key)) {
            $secret_key = $this->recaptcha->secret_key;
        }
        $data = array(
            'secret' => $secret_key,
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @$result = json_decode(curl_exec($ch));
        curl_close($ch);
        return $result;
    }
}