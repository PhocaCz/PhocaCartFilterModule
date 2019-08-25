<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;// no direct access

if (!JComponentHelper::isEnabled('com_phocacart', true)) {
	$app = JFactory::getApplication();
	$app->enqueueMessage(JText::_('Phoca Cart Error'), JText::_('Phoca Cart is not installed on your system'), 'error');
	return;
}

JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');

/*
if (! class_exists('PhocacartLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocacart/libraries/loader.php');
}

phocacartimport('phocacart.utils.settings');
phocacartimport('phocacart.filter.filter');
phocacartimport('phocacart.path.route');
phocacartimport('phocacart.path.path');
phocacartimport('phocacart.render.renderjs');
phocacartimport('phocacart.ordering.ordering');*/

$lang = JFactory::getLanguage();
//$lang->load('com_phocacart.sys');
$lang->load('com_phocacart');


$moduleclass_sfx 					= htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');




$filter								    = new PhocacartFilter();
$filter->category					    = $params->get( 'filter_category', 0 );
$filter->tag 						    = $params->get( 'filter_tag', 1 );
$filter->label 						    = $params->get( 'filter_label', 1 );
$filter->manufacturer 				    = $params->get( 'filter_manufacturer', 1 );
$filter->manufacturer_title 		    = $params->get( 'manufacturer_title', '' );
$filter->price 						    = $params->get( 'filter_price', 1 );
$filter->attributes 				    = $params->get( 'filter_attributes', 0 );
$filter->specifications 			    = $params->get( 'filter_specifications', 0 );
$filter->enable_color_filter 		    = $params->get( 'enable_color_filter', 0 );
$filter->enable_image_filter 		    = $params->get( 'enable_image_filter', 0 );
$filter->image_style_image_filter 	    = $params->get( 'image_style_image_filter', 0 );
$filter->enable_color_filter_spec	    = $params->get( 'enable_color_filter_spec', 0 );
$filter->enable_image_filter_spec	    = $params->get( 'enable_image_filter_spec', 0 );
$filter->image_style_image_filter_spec 	= $params->get( 'image_style_image_filter_spec', 0 );
$filter->ordering_tag 				    = $params->get( 'ordering_tag', 1 );
$filter->ordering_label 				= $params->get( 'ordering_label', 1 );
$filter->ordering_manufacturer 		    = $params->get( 'ordering_manufacturer', 1 );
$filter->ordering_attribute 		    = $params->get( 'ordering_attribute', 1 );
$filter->ordering_specification 	    = $params->get( 'ordering_specification', 1 );
$filter->filter_language			    = $params->get( 'filter_language', 0 );
$filter->open_filter_panel			    = $params->get( 'open_filter_panel', 1 );

$language = '';
if ($filter->filter_language == 1) {
	//$lang 		= JFactory::getLanguage();
	$language	= $lang->getTag();
}

$p									= array();
$p['remove_parameters_cat']			= $params->get( 'remove_parameters_cat', 0 );
$p['load_component_media']			= $params->get( 'load_component_media', 0 );



$document					= JFactory::getDocument();
// Price FROM Price TO - Input Range

if ($filter->price == 2 || $filter->price == 3) {


	$document->addScript(JURI::root(true).'/media/com_phocacart/js/ui/jquery-ui.slider.min.js');
	JHTML::stylesheet('media/com_phocacart/js/ui/jquery-ui.slider.min.css' );

	$currency 	= PhocacartCurrency::getCurrency();
	PhocacartRenderJs::getPriceFormatJavascript($currency->price_decimals, $currency->price_dec_symbol, $currency->price_thousands_sep, $currency->price_currency_symbol, $currency->price_prefix, $currency->price_suffix, $currency->price_format);
	$price_from	= $filter->getArrayParamValues('price_from', 'string');
	$price_to	= $filter->getArrayParamValues('price_to', 'string');
	$min		= PhocacartProduct::getProductPrice(2, 1, $language);// min price
	$max		= PhocacartProduct::getProductPrice(1, 1, $language);// max price

	if (!$min) {
		$min = 0;
	}
	if (!$max) {
		$max = 0;
	}

	if ($price_to[0] == '') {
		$price_to[0] = $max;
	}
	if ($price_from[0] == '') {
		$price_from[0] = $min;
	}

	PhocacartRenderJs::renderFilterRange($min, $max, $price_from[0], $price_to[0]);
}


$app						= JFactory::getApplication();
$isItemsView 				= PhocacartRoute::isItemsView();

$urlItemsView 				= PhocacartRoute::getJsItemsRoute($filter->category);
$urlItemsViewWithoutParams 	= PhocacartRoute::getJsItemsRouteWithoutParams();
$config 					= JFactory::getConfig();
$sef						= $config->get('sef', 1);


if ($p['load_component_media'] == 1) {
	$media = new PhocacartRenderMedia();
	$media->loadBase();
	$media->loadBootstrap();
	$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/jquery.ba-bbq.min.js');
	$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/filter.js');
	$media->loadSpec();
} else {

	$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/jquery.ba-bbq.min.js');
	$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/filter.js');

}
$s = PhocacartRenderStyle::getStyles();

/* Difference between - Active category vs. All categories
 * Active category - Url gets ID parameter and it can be only one ID: id=1:abc
 * All categories - Url gets c parameter and there can be more categories filters: c=1:abc,2def
 * When we use all categories, we need clean urlItemsView
 */
if ($filter->category == 2) {
	$urlItemsView = $urlItemsViewWithoutParams;
}

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


// Specific case for deselecting categories (ACTIVE CATEGORY ONLY)
if ($p['remove_parameters_cat'] == 1) {
	// If set in parameters to YES, when deselecting category, all other params will be removed too
	// RECOMMENDED as mostly the parameters can be assigned to category
	$jsPart1 = 'document.location 		= urlItemsView;';
} else {
	// If set in parameters to NO, when deleting category, all other parameters will stay in URL
	// NOT RECOMMENDED as mostly all other parameters are assigned to category
	// If we are not in items views (isItemsView = 0), there cannot be even any parameters,
	// so we can reload to pure clean items view (urlItemsView) when we remove the category
	//
	// Remove category parameter from GET
	// IF NO ITEMS VIEW - we can remove everything and go back to items view (e.g. we are in category view)
	// IF SEF DISABLED - category id: id=5 is standard parameter and we need to remove it with help of phRemoveFilter
	// IF SEF ENABLED - category id: 5-category is not standard parameter but alias, remove it with querystring
	$jsPart1 = 'var currentUrlParams	= jQuery.param.querystring();'
		.' if (isItemsView == 1) {';
	if ($sef) {
		$jsPart1 .= '   document.location 		= jQuery.param.querystring(urlItemsView, currentUrlParams, 2);';
	} else {
		$jsPart1 .= '   phRemoveFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
	}
	$jsPart1 .= ' } else {'
		.'   document.location 		= urlItemsView;'
		.' }';
}

$jsPart2 = PhocacartRenderJs::renderLoaderFullOverlay();

$js   = array();

$js[] = ' ';
$js[] = '/* Function phChangeFilter */';
$js[] = 'function phChangeFilter(param, value, formAction, formType, uniqueValue, wait) {';
$js[] = '   var isItemsView		= '.(int)$isItemsView.';';
$js[] = '	var urlItemsView	= \''.$urlItemsView.'\';';
$js[] = '	var phA = 1;';
$js[] = ' 	';
$js[] = '	if (formType == \'text\') {';
//$js[] = '      value = phEncode(value);';
$js[] = '      if (formAction == 1) {';
$js[] = '         phA = phSetFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      } else {';
$js[] = '         phA = phRemoveFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      }';
$js[] = '   } else if (formType == \'category\') {';
$js[] = ' 		urlItemsView = \''.$urlItemsViewWithoutParams.'\';';
$js[] = '      ' . $jsPart1 ;
$js[] = '   } else {';
$js[] = '      if (formAction.checked) {';
$js[] = '         phA = phSetFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      } else {';
$js[] = '         phA = phRemoveFilter(param, value, isItemsView, urlItemsView, uniqueValue, wait);';
$js[] = '      }';
$js[] = '   }';
$js[] = '   '.$jsPart2;
$js[] = '}';
$js[] = ' ';

$document->addScriptDeclaration(implode("\n", $js));

require(JModuleHelper::getLayoutPath('mod_phocacart_filter'));
?>
