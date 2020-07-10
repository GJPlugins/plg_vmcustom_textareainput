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

$title = $viewData[0];
$item = $viewData[1];
?>

<span>
	<u><?php echo JText::_($title); ?>:</u>
	<br /><?php echo nl2br($item['comment']); ?>
</span>
