<?php

require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');
$cookie = new Cookie('mautic', '', time() + (3600 / 4));
$d = Tools::jsonDecode(file_get_contents("php://input"), true);
$sessionId = $_COOKIE['mautic_session_id'];
$leadId      = $_COOKIE[$sessionId];
echo $leadId;
print_R($_COOKIE);
die();
if (!is_array($d))
    die(print_r($_COOKIE));
if (!$cookie->__isset('lead_id')) {
    $lead_id = $d['mautic.page_on_hit']['hit']['lead']['id'];
    $email = $d['mautic.page_on_hit']['hit']['lead']['fields']['core']['email']['value'];
    if (ValidateCore::isEmail($email))
        $cookie->__set('email', $email);
    $cookie->__set('lead_id', $lead_id);
    $cookie->write();
    setcookie("test","value",time()+3600);
    file_put_contents(dirname(__FILE__) .'/test.txt', print_r($_COOKIE,true));
}else {
    echo 'test';
    echo $cookie->__get('lead_id');
    echo $cookie->__get('email');
    $cookie->__unset('lead_id');
}
