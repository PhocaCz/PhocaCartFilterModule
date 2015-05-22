<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
defined('_JEXEC') or die('Restricted access');// no direct access

if (!JComponentHelper::isEnabled('com_phocacart', true)) {
	return JError::raiseError(JText::_('Phoca Cart Error'), JText::_('Phoca Cart is not installed on your system'));
}
if (! class_exists('PhocaCartLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocacart/libraries/loader.php');
}

phocacartimport('phocacart.utils.settings');
phocacartimport('phocacart.filter.filter');
phocacartimport('phocacart.path.route');
phocacartimport('phocacart.render.renderjs');

$lang 						= JFactory::getLanguage();
$lang->load('com_phocacart');
$document					= JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/jquery.ba-bbq.min.js');
$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/filter.js');
$isItemsView 				= PhocaCartRoute::isItemsView();
$urlItemsView 				= PhocaCartRoute::getJsItemsRoute();
$urlItemsViewWithoutParams 	= PhocaCartRoute::getJsItemsRouteWithoutParams();

$filter						= new PhocaCartFilter();
$filter->category			= $params->get( 'filter_category', 0 );
$filter->tag 				= $params->get( 'filter_tag', 1 );
$filter->manufacturer 		= $params->get( 'filter_manufacturer', 1 );
$filter->price 				= $params->get( 'filter_price', 1 );
$filter->attributes 		= $params->get( 'filter_attributes', 0 );
$filter->specifications 	= $params->get( 'filter_specifications', 0 );
$p['remove_parameters_cat']	= $params->get( 'filter_specifications', 0 );

/*
 * param 		... param name, for example tag
 * value 		... param value, for example new (tag=new)
 * formAction	... form action - depends on form type, can be 0, 1 if form type is text or checked if form type is checked
 * formType		... TEXT - input field is text (e.g. 0, 1)
 *                  CHECKED - input field is checked
 *                  CATEGORY - input field is checked but there is specific rule for category
 *                   - if we uncheck category - the urlItemsView is without category URL part
 *                   - if we check category - the urlItemsView is with this URL part, but in fact
 *                   there is possible only uncheck the category as if it is not loaded, the filter is not displayed
 *                  or category (category is checked but specific rules)
 * uniqueValue	... param can be an array tag=new,old or unique value price_from=100 ... arrays can be joined, unique values are replaced
 * wait			... if we change two params at once - e.g. price_from, price_to - we change price_from and we need to wait for second
 *				    parameter (price_to), so we don't reload the site but we build the url with hel of global variable
 */
 

// Specific case for deselecting categories
if ($p['remove_parameters_cat'] == 1) {
	// If set in parameters to YES, when deselecting category, all other params will be removed too
	// RECOMMENDED as mostly the parameters can be assigned to category
	$jsPart1 = 'document.location 		= urlItemsView';
} else {
	// If set in parameters to NO, when deleting category, all other parameters will stay in URL
	// NOT RECOMMENDED as mostly all other parameters are assigned to category
	$jsPart1 = 'var currentUrlParams	= jQuery.param.querystring();'
			  .'document.location 		= jQuery.param.querystring(urlItemsView, currentUrlParams, 2);';
}

$jsPart2 = PhocaCartRenderJs::renderLoaderFullOverlay();


$js   = array();
$js[] = 'function phChangeFilter(param, value, formAction, formType, uniqueValue, wait) {';
$js[] = '   var isItemsView		= '.(int)$isItemsView.';';
$js[] = '	var urlItemsView	= \''.$urlItemsView.'\';';
$js[] = ' 	';
$js[] = '	if (formType == \'text\') {';
//$js[] = '      value = phEncode(value);';
$js[] = '      if (formAction == 1) {';
$js[] = '         phSetFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      } else {';
$js[] = '         phRemoveFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      }';
$js[] = '   } else if (formType == \'category\') {';
$js[] = '      urlItemsView			= \''.$urlItemsViewWithoutParams.'\';';
$js[] = '      ' . $jsPart1 ;
$js[] = '   } else {';
$js[] = '      if (formAction.checked) {';
$js[] = '         phSetFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      } else {';
$js[] = '         phRemoveFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      }';
$js[] = '   }';
$js[] = '   '.$jsPart2;
$js[] = '}';

$document->addScriptDeclaration(implode("\n", $js));

require(JModuleHelper::getLayoutPath('mod_phocacart_filter'));
?>