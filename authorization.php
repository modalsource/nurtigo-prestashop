<?php

include(dirname(__FILE__) . '/../../config/config.inc.php');
session_name("mauticprestashop");
session_start();
require(dirname(__FILE__) . '/lib/api/vendor/autoload.php');

if (Tools::getIsset('reset')) {
    unset($_SESSION['oauth']);
}

$error = false;
$module = ModuleCore::getInstanceByName("mauticprestashop");

try {
    $auth = $module->mautic_auth(Tools::getIsset('reset'));
    if ($auth) {
        if ($auth->validateAccessToken()) {
            if ($auth->accessTokenUpdated()) {
                $accessTokenData = $auth->getAccessTokenData();
                Configuration::updateValue('MAUTICPRESTASHOP_ACCESS_TOKEN_DATA', serialize($accessTokenData), false, Tools::getValue('id_shop_group'), Tools::getValue('id_shop'));
            }
        }
    }
} catch (Exception $e) {
    $error = true;
}
if (!Tools::getIsset('back')) {
    $redirect = Tools::secureReferrer($_SERVER['HTTP_REFERER']);
} else {
    $redirect = unserialize(base64_decode(urldecode(Tools::getValue('back'))));
}
if ($error) {
    $redirect .= '&error=1';
}
Tools::redirect($redirect);
