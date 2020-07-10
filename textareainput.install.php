<?php
/*------------------------------------------------------------------------
# plg_system_vmsendcart - Virtuemart Send Cart Plugin
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgVmcustomTextareainputInstallerScript {

	/**
	* Called on installation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function install($adapter) {
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/version.php' )) { 
			include_once (JPATH_ADMINISTRATOR.'/components/com_virtuemart/version.php' );
			$VMVERSION = new vmVersion();
			if (isset($VMVERSION->RELEASE)) {
				$version = $VMVERSION->RELEASE;
			} else {
				$version = vmVersion::$RELEASE;
			}

			if (version_compare($version,'2.0.0','<')) {
				//Wrong version of Virtuemart
				$msg = 'The plugin Vm2 finalize order is designed for Virtuemart 2.x or newer';
				JFactory::getApplication()->enqueueMessage($msg, 'error');
				return false;
			}
		} else {
			//Virtuemart not found
			$msg = 'The plugin Vm2 finalize order requires Virtuemart 2.x or newer';
			JFactory::getApplication()->enqueueMessage($msg, 'error');
			return false;
		}
	}

	/**
	* Called on uninstallation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*/
	function uninstall($adapter) {
		//echo '<p>'. JText::_('1.6 Custom uninstall script') .'</p>';
	}

	/**
	* Called on update
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function update($adapter) {
		//echo '<p>'. JText::_('1.6 Custom update script') .'</p>';
	}

	/**
	* Called before any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function preflight($route, $adapter) {
		//echo '<p>'. JText::sprintf('1.6 Preflight for %s', $route) .'</p>';
	}

	/**
	* Called after any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function postflight($route, $adapter) {
		if ($route=='install' || $route=='update') {

			/*
			$oldfolders = array();
			$oldfolders[] = JPATH_SITE."/plugins/system/vmsendcart/vmsendcart";
			foreach ($oldfolders as $oldfolder) {
				if (JFolder::exists($oldfolder))
					JFolder::delete($oldfolder);
			}
	
			$oldfiles = array();
			$oldfiles[] = JPATH_ADMINISTRATOR."/language/en-GB/en-GB.plg_system_vmsendcart.ini";
			$oldfiles[] = JPATH_ADMINISTRATOR."/language/en-GB/en-GB.plg_system_vmsendcart.sys.ini";
			foreach ($oldfiles as $oldfile) {
				if (JFile::exists($oldfile))
					JFile::delete($oldfile);
			}
			*/
			
			$lang = JFactory::getLanguage();
			$lang->load('plg_vmcustom_textareainput',JPATH_SITE.'/plugins/vmcustom/textareainput');
			
			?>
            <div class="well clearfix">
                <h2><img src="../plugins/vmcustom/textareainput/images/textareainput-48.png" width="48" height="48" alt="Virtuemart Textarea Input"/>&nbsp; Virtuemart Textarea Input</h2>
                <p class="lead">Plugin installed</p>
                <div class="row-fluid">
                    <a class="btn btn-large btn-primary pull-left span5" href="index.php?option=com_plugins&filter_folder=vmcustom&filter_search=Textarea"><?php echo JText::_('PLG_DAYCOUNTS_CONFIGURE_PLUGIN'); ?></a>
                    <a href="https://www.daycounts.com/" target="new" class="pull-right span5"><img src="../plugins/vmcustom/textareainput/images/daycounts.png" style="" alt="Daycounts.com"/></a>
                </div>
            </div>
            <br />
        	<?php
		}
	}
}