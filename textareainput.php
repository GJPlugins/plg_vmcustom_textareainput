<?php
/*------------------------------------------------------------------------
# plgVmCustomTextareainput - Custom textarea field for Virtuemart
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');

class plgVmCustomTextareainput extends vmCustomPlugin {

	protected $isVM3 = false;

	function __construct(& $subject, $config) {

		$lang = JFactory::getLanguage();
		$lang->load('plg_vmcustom_textareainput',dirname(__FILE__));

		parent::__construct($subject, $config);

		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$this->isVM3 = false;
			$this->customFieldColumn = 'custom_params';
		} else {
			$this->isVM3 = true;
			$this->customFieldColumn = 'customfield_params';
		}

		$varsToPush = array(	'custom_rows'=>array(0.0,'int'),
								'custom_cols'=>array(0.0,'int'),
								'custom_emptytext'=>array('','string'),
								'custom_defaultsource'=>array('text','string'),
								'custom_defaultvalue'=>array('','string'),
						    	'custom_readonly'=>array(0.0,'int'),
						    	'custom_price_by_letter'=>array(0.0,'bool'),
								'min_char'=>array(0.0,'int'),
								'max_char'=>array(0.0,'int')
		);

		$this->setConfigParameterable($this->customFieldColumn,$varsToPush);

	}

	public function parseCustomParams (&$field, $xParams = 'custom_params') {
		
		if (!$this->isVM3) {
			return parent::parseCustomParams($field);
		}

		if (empty($field->custom_element)) {
			return 0;
		}
		$custom_param = array();
		if (!empty($field->{$xParams}) && is_string ($field->{$xParams})) {
			$custom_param = json_decode ($field->{$xParams}, TRUE);
		}
		else {
			return;
		}

		if (is_array($custom_param)) {
			foreach ($custom_param as $k => $v) {
				$field->$k = $v;
			}
		}
	}

	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {


		if ($field->custom_element != $this->_name) return '';
		$this->parseCustomParams($field,$this->customFieldColumn);

		if(!defined('VM_VERSION') or VM_VERSION < 3){
			//$this->parseCustomParams ($field);
		  	$fieldName = 'custom_param['.$row.']';
			$this->parseCustomParams ($field,$this->customFieldColumn);
		} else {
			$fieldName = 'customfield_params['.$row.']';
		}

		$priceOptions = array();
		$priceOptions[] = JHTML::_('select.option', '0',JText::_('VMCUSTOM_TEXTAREAINPUT_PRICE_BY_INPUT'));
		$priceOptions[] = JHTML::_('select.option', '1',JText::_('VMCUSTOM_TEXTAREAINPUT_PRICE_BY_LETTER'));

		$sourceOptions = array();
		$sourceOptions[] = JHTML::_('select.option', 'value',JText::_('VMCUSTOM_TEXTAREAINPUT_DEFAULT_TEXT_VALUE'));
		$sourceOptions[] = JHTML::_('select.option', 'get',JText::_('VMCUSTOM_TEXTAREAINPUT_DEFAULT_TEXT_GET'));
		$sourceOptions[] = JHTML::_('select.option', 'post',JText::_('VMCUSTOM_TEXTAREAINPUT_DEFAULT_TEXT_POST'));

		$yesNoOptions = array();
		$yesNoOptions[] = JHTML::_('select.option', '0',JText::_('JNO'));
		$yesNoOptions[] = JHTML::_('select.option', '1',JText::_('JYES'));

		$html ='
			<table class="adminform">'.
				VmHTML::row('input','VMCUSTOM_TEXTAREAINPUT_ROWS',$fieldName.'[custom_rows]',$field->custom_rows).
				VmHTML::row('input','VMCUSTOM_TEXTAREAINPUT_COLS',$fieldName.'[custom_cols]',$field->custom_cols).
				VmHTML::row('input','VMCUSTOM_TEXTAREAINPUT_MIN_CHAR',$fieldName.'[min_char]',$field->min_char).
				VmHTML::row('input','VMCUSTOM_TEXTAREAINPUT_MAX_CHAR',$fieldName.'[max_char]',$field->max_char).
				VmHtml::row('genericlist','VMCUSTOM_TEXTAREAINPUT_PRICE_BY_LETTER_OR_INPUT',$priceOptions,$fieldName.'[custom_price_by_letter]','','value','text',$field->custom_price_by_letter).
				VmHTML::row('input','VMCUSTOM_TEXTAREAINPUT_EMPTY',$fieldName.'[custom_emptytext]',$field->custom_emptytext,'class="inputbox"',50).
				VmHtml::row('genericlist','VMCUSTOM_TEXTAREAINPUT_DEFAULT_SOURCE',$sourceOptions,$fieldName.'[custom_defaultsource]','text','value','text',$field->custom_defaultsource).
				VmHTML::row('input','VMCUSTOM_TEXTAREAINPUT_DEFAULT_VALUE',$fieldName.'[custom_defaultvalue]',$field->custom_defaultvalue,'class="inputbox"',50).
				VmHtml::row('genericlist','VMCUSTOM_TEXTAREAINPUT_READONLY',$yesNoOptions,$fieldName.'[custom_readonly]','0','value','text',$field->custom_readonly).
				'
			</table>';
		$retValue .= $html;
		$row++;
		return true ;
	}

	function plgVmOnStoreProduct($data,$plugin_param){
	}

	function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
		return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table){
		return $this->setOnTablePluginParams($name, $id, $table);
	}

	function plgVmDeclarePluginParamsCustom($psType,$name,$id, &$data){
		return $this->declarePluginParams('custom', $name, $id, $data);
	}
	function plgVmDeclarePluginParamsCustomVM3(&$data){
		return $this->declarePluginParams('custom', $data);
	}


	/*******************/
	/* Product Display */
	/*******************/
	function plgVmOnDisplayProductFEVM3(&$product,&$group) {
		$idx=0;
		$this->plgVmOnDisplayProductFE($product,$idx,$group);
	}
	function plgVmOnDisplayProductFE($product,&$idx,&$group) {
		if (empty($group->custom_element) or $group->custom_element != $this->_name) return false;
		//vmJsApi::JvalideForm();
		$group->display .= $this->renderByLayout('default',array(&$product,&$group,$this->isVM3) );
		return true;
	}
	function plgVmOnDisplayProductVariantFE($field,&$idx,&$group) {
		if ($field->custom_element != $this->_name) return '';
		$this->getCustomParams($field);
		//vmJsApi::JvalideForm();
		$group->display .= $this->renderByLayout('default',array($field,&$field,$this->isVM3) );
		return true;
    }

	/****************/
	/* Cart Display */
	/****************/
	function plgVmOnViewCartVM3(&$product, &$productCustom, &$html) {

		if (empty($productCustom->custom_element) or $productCustom->custom_element != $this->_name) return false;

		if(empty($product->customProductData[$productCustom->virtuemart_custom_id][$productCustom->virtuemart_customfield_id])) return false;
		foreach( $product->customProductData[$productCustom->virtuemart_custom_id] as $k =>$item ) {
			if($productCustom->virtuemart_customfield_id == $k) {
				if(isset($item['comment'])){
					$html .= $this->renderByLayout('cart',array($productCustom->custom_title,$item) );
					//$html .='<span>'.vmText::_($productCustom->custom_title).' '.$item['comment'].'</span>';
				}
			}
		}
		return true;
	}

	function plgVmOnViewCart($product,$row,&$html) {
		if (empty($product->productCustom->custom_element) or $product->productCustom->custom_element != $this->_name) return '';
		
		if (!$plgParam = $this->GetPluginInCart($product)) return '' ;

		foreach($plgParam as $k => $item){

			if(!empty($item['comment']) ){
				if($product->productCustom->virtuemart_customfield_id==$k){
					$html .= $this->renderByLayout('cart',array($product->productCustom->custom_title,$item) );
					//$html .='<span>'.JText::_($product->productCustom->custom_title).' '.$item['comment'].'</span>';
				}
			}
		 }

		return true;
    }

	
	/************************/
	/* Cart  Module Display */
	/************************/
	function plgVmOnViewCartModuleVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}
	function plgVmOnViewCartModule( $product,$row,&$html) {
		$this->plgVmOnViewCart($product,$row,$html);
    }

	/**************************/
	/* Frontend Order Display */
	/**************************/
	function plgVmDisplayInOrderFEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}
	function plgVmDisplayInOrderFE($product, $row, &$html) {
		$this->plgVmOnViewCart($product,$row,$html);
	}

	/*************************/
	/* Backend Order Display */
	/*************************/
	function plgVmDisplayInOrderBEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}
	function plgVmDisplayInOrderBE($product, $row, &$html) {
		$this->plgVmOnViewCart($product,$row,$html);
	}


	function plgVmOnSelfCallFE($type,$name,&$render) {
		$render->html = '';
	}
	/******************************/
	/* Backend Edit Field Display */
	/******************************/
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}


	/***************************/
	/* Calculate product price */
	/***************************/

	//VM3 Only - Get the override price based on the Bonus rules that are applied and valid.
	public function plgVmPrepareCartProduct(&$product, &$customfield,$selected,&$modificatorSum){

		if ($customfield->custom_element !==$this->_name) return ;

		if (!empty($selected['comment'])) {
			if ($customfield->custom_price_by_letter ==1) {
				$pure_chars =  html_entity_decode ($selected['comment']);
				$pure_chars = trim($pure_chars);
				$pure_chars = preg_replace('/[\r\n]+/','', $pure_chars); //Do not count carriage return
				$charcount = strlen ($pure_chars);
			} else {
				$charcount = 1.0;
			}
			$modificatorSum += $charcount * $customfield->customfield_price ;
		} else {
			$modificatorSum += 0.0;
		}

		return true;
	}

	public function plgVmCalculateCustomVariant($product, &$productCustomsPrice,$selected){
		if ($productCustomsPrice->custom_element !==$this->_name) return ;
		$customVariant = $this->getCustomVariant($product, $productCustomsPrice,$selected);
		if (!empty($productCustomsPrice->custom_price)) {
			//TODO adding % and more We should use here $this->interpreteMathOp
			// eg. to calculate the price * comment text length

			if (!empty($customVariant['comment'])) {
				if ($productCustomsPrice->custom_price_by_letter ==1) {
					$pure_chars =  $customVariant['comment'];
					$pure_chars = trim($pure_chars);
					$pure_chars = preg_replace('/[\r\n]+/','', $pure_chars); //Do not count carriage return
					$charcount = strlen ($pure_chars);
				} else {
					$charcount = 1.0;
				}
				$productCustomsPrice->custom_price = $charcount * $productCustomsPrice->custom_price ;
			} else {
				$productCustomsPrice->custom_price = 0.0;
			}

		}
		return true;
	}

	public function plgVmOnAddToCart(&$product) {
		//This will do server side validation in case the javascript validation was bypassed
		//Retreive the value sent to the cart
		if (!isset($product->customPlugin)) {
			return;
		}
		$custom_plugin_json = $product->customPlugin;
		$custom_plugins = json_decode($custom_plugin_json);
		$app = JFactory::getApplication();
		$errors = array();
		$db	= JFactory::getDBO();
		
		foreach ($custom_plugins as $customfield_id => $customfield_value) {
			if (isset($customfield_value->textareainput) && isset($customfield_value->textareainput->comment)) {

				$sql = $db->getQuery(true)
					->select('cfp.*,cf.custom_title')
					->from('#__virtuemart_product_customfields AS cfp')
					->join('INNER', '#__virtuemart_customs AS cf ON (cfp.virtuemart_custom_id = cf.virtuemart_custom_id)')
					->where('cfp.virtuemart_customfield_id = '.$db->q($customfield_id));
				$db->setQuery($sql);
		
				$customfield = $db->loadObject();
				$field_params_json = $customfield->custom_param;
				$custom_params = json_decode($field_params_json);
				$custom_title = $customfield->custom_title;
				
				$found_textarea = true;
				$comment = $customfield_value->textareainput->comment;
				if ($comment == $custom_params->custom_emptytext) {
					$comment = '';
				}
				$length = strlen($comment);
				if ($custom_params->min_char && $length < $custom_params->min_char) {
					$errors[] = JText::sprintf('VMCUSTOM_TEXTAREAINPUT_MIN_CHAR_ERROR', $custom_params->min_char, $custom_title );
				}
				if ($custom_params->max_char && $length > $custom_params->max_char) {
					$errors[] = JText::sprintf('VMCUSTOM_TEXTAREAINPUT_MAX_CHAR_ERROR', $custom_params->min_char, $custom_title );
				}
			}
		}
		if (count($errors)) {
			if (VmConfig::get('addtocart_popup',1)) {
				$this->json = new stdClass();
				$this->json->msg = implode('<br/>',$errors);
				$this->json->stat = '2';
				echo json_encode($this->json);
				jExit();
			}
			foreach ($errors as $error) {
				$app->enqueueMessage($error,'error');
			}
			return false;
		}
	}

}

// No closing tag