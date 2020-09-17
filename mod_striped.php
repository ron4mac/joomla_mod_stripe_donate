<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the helper functions only once
JLoader::register('modStripedHelper', __DIR__ . '/helper.php');

$moduleName = basename(dirname(__FILE__));

$input = JFactory::getApplication()->input;
if ($input->get('session_id', '', 'string')) {
	require JModuleHelper::getLayoutPath($moduleName, 'default_yay');
} else {

	$document = JFactory::getDocument();
	$document->addStylesheet(JURI::base(true) . '/modules/'.$moduleName.'/assets/' . $moduleName . '.css');

	require JModuleHelper::getLayoutPath($moduleName);
}