<?php
/*
* 2007-2014 PrestaShop 
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
*         DISCLAIMER   *
* *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
* @category   Opensum
* @package    opensumManufacturerSlider
* @author    vivek kumar tripathi <vivek@opensum.com>
* @site    http://opensum.com
* @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.opensum.com)
*/

if (!defined('_PS_VERSION_')){
	exit;
}

define('_OS_ITEM_MANUFACTURERSLIDER_', 10);
class os_manufacturerslider extends Module
{
	private $_html;
	
	public function __construct(){
		$this->name = 'os_manufacturerslider';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'vivek kumar tripathi';
		$this->need_instance = 0;
                $this->bootstrap = true;

		parent::__construct();
		
		$this->displayName = $this->l('Manufacturer/Brands Slider');
		$this->description = $this->l('Display slider with manufacturers/Brand on your site.');
	}

	public function install(){
		if (!parent::install() OR
			!Configuration::updateValue('BMS_DISPLAY_ON_MOBILE', 1) OR 
			!Configuration::updateValue('BMS_COUNT', '20') OR 
			!$this->registerHook('home') OR
			!$this->registerHook('header')
		) {
			return false;
		}
		
		$this->updatePosition(Hook::get('home'), 0, 1);
		return true;
	}
	
	public function uninstall(){
		if (!parent::uninstall())
			return false;
		return true;
	}
	
	function hookHeader(){
		if(	Configuration::get('BMS_DISPLAY_ON_MOBILE') && $this->checkMobileDevice() ){
			return false;
		}
		$this->context->controller->addJS(__PS_BASE_URI__ . 'modules/os_manufacturerslider/js/jcarousellite.js');
		$this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/os_manufacturerslider/css/skin.css');
	}
	
	function hookHome($params){ 
		global $smarty, $cookie;
		if(	Configuration::get('BMS_DISPLAY_ON_MOBILE') && $this->checkMobileDevice() ){
			return false;
		}
		$only_active = TRUE;
		$n = 0;
		$p = Configuration::get('BMS_COUNT') ? Configuration::get('BMS_COUNT') : 999;
		$manufacturers = Manufacturer::getManufacturers(true, $cookie->id_lang, $only_active, $n, $p);
		foreach ($manufacturers AS &$row){
			$row['image'] = (!file_exists(_PS_MANU_IMG_DIR_.'/'.$row['id_manufacturer'].'-medium_default.jpg')) ? Language::getIsoById((int)$cookie->id_lang).'-default' : $row['id_manufacturer'];
                }
		$smarty->assign(array(
			'manufacturers' => $manufacturers,
			'img_manu_dir' => _THEME_MANU_DIR_,
			'nbManufacturers' => count($manufacturers),
			'mediumSize' => Image::getSize('medium'),
		));
            return $this->display(__FILE__, '/tpl/front/slider.tpl');		
	}
	
	public function getContent(){
		global $smarty, $cookie;
		if(Tools::isSubmit('submitUpdate')) {
			Configuration::updateValue('BMS_DISPLAY_ON_MOBILE', Tools::getValue('display_on_mobile'));
			Configuration::updateValue('BMS_COUNT', Tools::getValue('count'));
			
			$smarty->assign(array(
				'save_ok' => true
			));
		}
		$this->_html .= $this->_displayForm();
		return $this->_html;
	}
           private function _displayForm()
        {
            $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Show on mobile devices'),
						'name' => 'display_on_mobile',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
						),
                                    
					array(
						'type' => 'text',
						'label' => $this->l('No of Manufacturers in slider'),
						'name' => 'count',
                                                'class'=>'fixed-width-xs',
						'desc' => $this->l('Leave blank for no limit')
					),
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitUpdate';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		$this->_html .=  $helper->generateForm(array($fields_form));
        }
        
         public function getConfigFieldsValues()
	{
		return array(
			'display_on_mobile' => Tools::getValue('display_on_mobile',  Configuration::get('BMS_DISPLAY_ON_MOBILE')),
			'count' => Tools::getValue('count', Configuration::get('BMS_COUNT'))
		);
	}
	public function checkMobileDevice(){
		if (preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipaq|ipod|j2me|java|midp|mini|mmp|mobi\s|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|zte)/i', $_SERVER['HTTP_USER_AGENT'], $out))
			return true;
	}
}
