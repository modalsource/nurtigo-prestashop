<?php

session_start();

include(dirname(__FILE__) . '/../../config/config.inc.php');
require(dirname(__FILE__) . '/lib/api/vendor/autoload.php');

$module = ModuleCore::getInstanceByName("mauticprestashop");
$auth = $module->mautic_auth(true);
if ($auth->validateAccessToken()) {
    if ($auth->accessTokenUpdated()) {
        $accessTokenData = $auth->getAccessTokenData();
        Configuration::updateGlobalValue('MAUTICPRESTASHOP_ACCESS_TOKEN_DATA', serialize($accessTokenData));
    }
}

Tools::redirect(Tools::getValue('redirect_uri'));

