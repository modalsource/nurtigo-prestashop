<?php

/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_'))
    exit;

class Mauticprestashop extends Module {

    CONST baseUrl = 'http://www.tuli.sk/sub/m122';
    CONST apiUrl = 'http://www.tuli.sk/sub/m122/api/';
    CONST clientKey = '17trt23ayoys4s4kgkkkgkgkgo0kkc08kkgw4wc0o4sgg44w4o';
//    CONST clientKey = '4ydz61otfpk404go8skws4w44ww8c8okw8wo8sco4ok4kk4cok';
    CONST clientSecret = '4u3ewa225a80kc0s0og0wgw80cwskck0owcks0w0g0kscswo8w';
    //  CONST clientSecret = '4jiqmamkq8mc4ss84880088gc0w0ccs40cco4s84s04g004k88';
    CONST callback = 'http://localhost/tuli/cms/modules/mauticprestashop/authorization.php';

    public $_fields = array('MAUTICPRESTASHOP_CLIENT_KEY', 'MAUTICPRESTASHOP_CLIENT_SECRET', 'MAUTICPRESTASHOP_BASE_URL');

    //'MAUTICPRESTASHOP_ACCESS_TOKEN', 'MAUTICPRESTASHOP_ACCESS_TOKEN_EXPIRES', 'MAUTICPRESTASHOP_REFRESH_TOKEN',);

    public function __construct() {
        $this->name = 'mauticprestashop';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'kuzmany.biz/prestashop';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Mautic for Prestashop');
        $this->description = $this->l('Integration Mautic & Mautic API for Prestashop.');
    }

    public function install() {

        return parent::install() &&
                $this->registerHook('actionCustomerAccountAdd') &&
                $this->registerHook('displayFooter');
    }

    public function uninstall() {
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent() {

        if (((bool) Tools::isSubmit('submitMauticprestashopModule')) == true) {
            $this->postProcess();
        }


        return $this->renderForm();
    }

    protected function renderForm() {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMauticprestashopModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues() {
        $ret = array();
        foreach ($this->_fields as $field) {
            $ret[$field] = Configuration::get($field);
        }
        return $ret;
    }

    protected function getConfigForm() {
        die(_PS_ADMIN_DIR_);
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Base URL of the Mautic instance'),
                        'name' => 'MAUTICPRESTASHOP_BASE_URL',
                        'description' => $this->l('Example: http://my-mautic-server.com'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Client/Consumer key from Mautic'),
                        'name' => 'MAUTICPRESTASHOP_CLIENT_KEY',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Client/Consumer secret key from Mautic'),
                        'name' => 'MAUTICPRESTASHOP_CLIENT_SECRET',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'html_data',
                        'html_content' => '<hr><a href="' . $this->_path . '/authorization.php"><button  type="button" class="btn btn-default">' . $this->l('Authorize APP') . '</button></a><hr>'
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function postProcess() {
        foreach ($this->_fields as $field)
            Configuration::updateValue($field, Tools::getValue($field));
    }

    public function mautic_auth($reauthorize = false) {
        require_once (dirname(__FILE__) . '/lib/api/vendor/autoload.php');
        $settings = array(
            'baseUrl' => Configuration::get('MAUTICPRESTASHOP_BASE_URL'),
            'version' => 'OAuth1a',
            'clientKey' => Configuration::get('MAUTICPRESTASHOP_CLIENT_KEY'),
            'clientSecret' => Configuration::get('MAUTICPRESTASHOP_CLIENT_SECRET'),
            'callback' => $this->context->link->getAdminLink('AdminModules', true)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
        );

        if ($reauthorize == false) {
            $accessTokenData = Tools::unSerialize(Configuration::getGlobalValue('MAUTICPRESTASHOP_ACCESS_TOKEN_DATA'), false);
            if (!$accessTokenData && !is_array($accessTokenData))
                return;
            $settings['accessToken'] = $accessTokenData['access_token'];
            $settings['accessTokenSecret'] = $accessTokenData['access_token_secret'];
        }

        return Mautic\Auth\ApiAuth::initiate($settings);
    }

    public function hookActionCustomerAccountAdd($params) {
        $newsletter = $params['newCustomer']->newsletter;
        $auth = $this->mautic_auth();
        $leadApi = Mautic\MauticApi::getContext("leads", $auth, self::apiUrl);
        $data = array(
            'email' => $params['newCustomer']->email,
            'firstname' => $params['newCustomer']->firstname,
            'lastname' => $params['newCustomer']->lastname,
            'domain' => Context::getContext()->shop->domain
        );
        if ($newsletter)
            $data['newsletter'] = 1;
        $lead = $leadApi->create($data);
        $leadId = $lead['lead']['id'];
        if ($leadId) {
            $listApi = Mautic\MauticApi::getContext("lists", $auth, self::apiUrl);
            $response = $listApi->addLead(11, $leadId);
        }
    }

    public function getLeadId() {
        $sessionId = $_COOKIE['mautic_session_id'];
        $leadId = $_COOKIE[$sessionId];
        return $leadId;
    }

    public function hookActionCartSave($params) {

        $cart = $params['cart'];
        $leadId = $this->getLeadId();
        $leads_product = array();

        if ($cart->id)
            if (Tools::getIsset('add'))
                $leads_product[] = 'Cart|' . $cart->id;

        //elseif (Tools::getIsset('delete'))
        //    $leads_product[] = '-Cart|' . $cart->id;
        //$leads_product[] = 'Cart|' . Tools::getValue('id_product') . '|' . Tools::getValue('ipa', 0) . '|' . $cart->date_add;

        $auth = $this->mautic_auth();
        $leadApi = Mautic\MauticApi::getContext("leads", $auth, self::apiUrl);
        $updatedData = array(
            'tags' => implode(',', $leads_product),
            'domain' => Context::getContext()->shop->domain
        );
        $result = $leadApi->edit($leadId, $updatedData);
        //if(Context::getContext()->shop->domain == 'www.tulishop.de')

        $listApi = Mautic\MauticApi::getContext("lists", $auth, self::apiUrl);
        $response = $listApi->addLead(30, $leadId);
    }

    public function hookActionValidateOrder($params) {
        $leadId = $this->getLeadId();
        $order = $params['order'];
        $cart = $params['cart'];
        $products = $order->getProducts();
        $leads_product = array();
        foreach ($products as $product)
            $leads_product[] = '-Cart|' . $cart->id;
        // $leads_product[] = '-Cart|' . $product['id_product'] . '|' . ($product['product_attribute_id'] ? $product['product_attribute_id'] : 0 ) . '|' . $cart->date_add;

        $auth = $this->mautic_auth();
        $leadApi = Mautic\MauticApi::getContext("leads", $auth, self::apiUrl);

        $updatedData = array(
            'tags' => implode(',', $leads_product),
            'domain' => Context::getContext()->shop->domain
        );
        $leadApi->edit($leadId, $updatedData);
        $listApi = Mautic\MauticApi::getContext("lists", $auth, self::apiUrl);
        $response = $listApi->removeLead(30, $leadId);
    }

    public function get_tracking_code($email = null) {
// tracking code
        $data = array();
        $data['page_url'] = $_SERVER['REQUEST_URI'];
        $data['page_title'] = $this->context->smarty->getTemplateVars('meta_title');
        $data['page_language'] = Context::getContext()->language->iso_code;
        if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER'] != '')
            $data['page_referrer'] = $_SERVER['HTTP_REFERER'];
        if ($email != null)
            $data['email'] = $email;
        elseif (isset(Context::getContext()->customer->email))
            $data['email'] = Context::getContext()->customer->email;
        $d = urlencode(base64_encode(serialize($data)));
        return '<img src="http://' . Context::getContext()->shop->domain . '/sub/m/mtracking.gif?d=' . $d . '" style="display: none;" />';
    }

    public function hookDisplayFooter() {
        if (!Cache::isStored('mautic_tracking_code'))
            return $this->get_tracking_code();
    }

    public function get_public_content() {
        $content = array();
        foreach ($this->_fields as $field)
            $content[$field] = Configuration::get($field);
        return $content;
    }

}
