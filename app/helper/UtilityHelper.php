<?php

/**
 * @var array
 */
$global_captcha = [];

/**
 * @param $captcha
 * @return void
 */
function setGlobalCaptcha($captcha)
{
    global $global_captcha;
    $global_captcha = $captcha;
}

/**
 * @return array
 */
function getGlobalCaptcha()
{
    global $global_captcha;
    return $global_captcha;
}

/**
 * @return mixed
 */
function auth()
{
    return AuthModel::getAuthUser();
}

/**
 * @return array
 */
function mail_properties()
{
    $result = [];

    if ($_ENV['SMTP_ENCRYPTION'] === 'none') {
        $_ENV['SMTP_ENCRYPTION'] = 'tls';

        $result = [
            'SMTPSecure' => false,
            'SMTPAutoTLS' => false
        ];
    }

    return $result;
}
