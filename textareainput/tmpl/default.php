<?php
/*------------------------------------------------------------------------
# plgVmCustomTextareainput - Custom textarea field for Virtuemart
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
#-------------------------------------------------------------------------
#
#
#    DO NOT MODIFY THIS FILE. YOU WOULD LOOS YOUR CHANGES WHEN UPDATING
#
#    Instead, make a copy oif this file in your template folder :
#    /templates/YOUR_TEMPLATE/html/vmcustom/textareainput/
#
-------------------------------------------------------------------------*/
defined('_JEXEC') or die();

$class='vmcustom-textareainput';

$product 	= $viewData[0];
$params 	= $viewData[1];
$isVM3 		= $viewData[2];
if ($isVM3) {
	$name = 'customProductData['.$product->virtuemart_product_id.']['.$params->virtuemart_custom_id.']['.$params->virtuemart_customfield_id .'][comment]';
} else {
	$name = 'customPlugin['.$params->virtuemart_customfield_id.']['.$params->custom_element.'][comment]';
}

vmJsApi::JvalideForm();
$maxlength = '';

if ($params->min_char || $params->max_char) {
	$validate = ' validate[';
	if ($params->min_char) {
		$validate .= 'required,minSize['.$params->min_char.']';
	}
	if ($params->max_char) {
		$validate .= ($params->min_char) ? ',' : '';
		$validate .= 'maxSize['.$params->max_char.']';
		$maxlength = ' maxlength="'.$params->max_char.'" ';
	}
	$validate .= ']';
}

$app = JFactory::getApplication();
$default_value = '';
if ($params->custom_defaultsource == 'text') {
	$default_value = JText::_($params->custom_defaultvalue);
} else if ($params->custom_defaultsource == 'get') {
	$inputvariable = $params->custom_defaultvalue;
	$default_value = $app->input->get->getString($inputvariable,'');
} else if ($params->custom_defaultsource == 'post') {
	$inputvariable = $params->custom_defaultvalue;
	$default_value = $app->input->post->getString($inputvariable,'');
}

//Readon only
$readonly = ($params->custom_readonly) ? 'readonly' : '';
?>

    <textarea 
    	id="<?php echo $params->virtuemart_customfield_id; ?>"
    	class="<?php echo $class.$validate; ?> field" 
        rows="<?php echo $params->custom_rows ?>" 
        cols="<?php echo $params->custom_cols ?>" 
        <?php echo $maxlength; ?> 
        name="<?php echo $name ?>" placeholder="<?php echo $params->custom_emptytext ?>" <?php echo $readonly ?>><?php echo $default_value ?></textarea>
		<div class="floatright right paddingtop">
			<?php echo JText::_('COM_VIRTUEMART_ASK_COUNT')  ?><span id="counter_<?php echo $params->virtuemart_customfield_id; ?>">0</span>
        </div>
<?php
	// preventing 2 x load javascript
	static $textareainputjs;
	if (defined('textareainputjs')) return true;
	define('textareainputjs',true);
	//javascript to update price
	$document = JFactory::getDocument();
	$js = '
		jQuery(document).ready( function($) {
			var form = jQuery(".vmcustom-textareainput").parents("form");
			form.addClass("form-validate");
			if (!form.attr("id")) {
				//Make sure the form has an id for validation purpose
				form.attr("id","add-to-cart");
			}
			form.validationEngine("attach");
            
            //Unbind the click event to the add to cart button
            var addtocart = form.find(\'input[type="submit"]\');
            if(!addtocart.length) {
                var addtocart = form.find(".addtocart-button");
            }
            addtocart.off("click");
            
            //Rebind the add to cart button withour own function
            addtocart.click(function(e) {
                //e.preventDefault();
                var isValid = form.validationEngine("validate");
                if(isValid) { 
                    Virtuemart.sendtocart(form);
                } else {
                    e.stopSendtocart = true;
                }
                return false;
            });
            
			
			countChars = function(elt) {
			  	var formProduct = $(elt).parents("form.product");
				var id = $(elt).attr(\'id\');
				virtuemart_product_id = formProduct.find(\'input[name="virtuemart_product_id[]"]\').val();
				Virtuemart.setproducttype(formProduct,virtuemart_product_id);
				var result = $(elt).val();
				result = result.trim();
				br_regex = /[\r\n]+/g;
				result = result.replace(br_regex, "");					
				$("#counter_"+id).html( result.length );
			}
			jQuery(".vmcustom-textareainput")
			  .keyup(function() {
				  countChars($(this));
			  })
			  
			jQuery(".vmcustom-textareainput").each(function(){
				  countChars($(this));
			});
			  
		});
	';

    //vm 3 -templates alter the default order of the scripts loading, hence we have to reload the scripts in the correct order
    vmJsApi::jPrice();
    echo vmJsApi::writeJS();

    vmJsApi::JvalideForm();
    vmJsApi::addJScript('addtocart',$js); 
