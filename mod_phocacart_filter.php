<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;// no direct access

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ModuleHelper;

$app = Factory::getApplication();

if (!ComponentHelper::isEnabled('com_phocacart', true)) {

	$app->enqueueMessage(Text::_('Phoca Cart Error'), Text::_('Phoca Cart is not installed on your system'), 'error');
	return;
}
if (file_exists(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php')) {
	// Joomla 5 and newer
	require_once(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php');
} else {
	// Joomla 4
	JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');
}


$s = PhocacartRenderStyle::getStyles();
if ($s['c']['class-type'] != 'uikit') {
	HTMLHelper::_('bootstrap.collapse', '');
}

$document 	= Factory::getDocument();
$lang 		= Factory::getLanguage();
//$lang->load('com_phocacart.sys');
$lang->load('com_phocacart');

$moduleclass_sfx 					= htmlspecialchars((string)$params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$filter								    = new PhocacartFilter();
$filter->category					    = $params->get( 'filter_category', 0 );
$filter->tag 						    = $params->get( 'filter_tag', 1 );
$filter->label 						    = $params->get( 'filter_label', 1 );
$filter->parameter						= $params->get( 'filter_parameter', 1 );
$filter->manufacturer 				    = $params->get( 'filter_manufacturer', 1 );
$filter->manufacturer_title 		    = $params->get( 'manufacturer_title', '' );
$filter->price 						    = $params->get( 'filter_price', 1 );
$filter->ignore_zero_price 			    = $params->get( 'ignore_zero_price', 0 );
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
$filter->ordering_parameter 			= $params->get( 'ordering_parameter', 1 );
$filter->ordering_manufacturer 		    = $params->get( 'ordering_manufacturer', 1 );
$filter->ordering_attribute 		    = $params->get( 'ordering_attribute', 1 );
$filter->ordering_specification 	    = $params->get( 'ordering_specification', 1 );
$filter->ordering_category 	    		= $params->get( 'ordering_category', 1 );
$filter->filter_language			    = $params->get( 'filter_language', 0 );
$filter->open_filter_panel			    = $params->get( 'open_filter_panel', 1 );
$filter->force_category			    	= $params->get( 'force_category', 0 );
$filter->limit_attributes_category		= $params->get( 'limit_attributes_category', 0 );
$filter->limit_tags_category			= $params->get( 'limit_tags_category', 0 );
$filter->limit_labels_category			= $params->get( 'limit_labels_category', 0 );
$filter->limit_parameters_category		= $params->get( 'limit_parameters_category', 0 );
$filter->limit_price_category			= $params->get( 'limit_price_category', 0 );
$filter->limit_manufacturers_category	= $params->get( 'limit_manufacturers_category', 0 );
$filter->limit_specifications_category	= $params->get( 'limit_specifications_category', 0 );
$filter->limit_category_count			= $params->get( 'limit_category_count', -1 );
$filter->display_category_count			= $params->get( 'display_category_count', 0 );
$filter->limit_tag_count				= $params->get( 'limit_tag_count', -1 );
$filter->display_tag_count				= $params->get( 'display_tag_count', 0 );
$filter->limit_parameter_count			= $params->get( 'limit_parameter_count', -1 );
$filter->display_parameter_count		= $params->get( 'display_parameter_count', 0 );
$filter->limit_manufacturer_count		= $params->get( 'limit_manufacturer_count', -1 );
$filter->display_manufacturer_count		= $params->get( 'display_manufacturer_count', 0 );
$filter->check_available_products		= $params->get( 'check_available_products', 1 );
$filter->remove_parameters_cat			= $params->get( 'remove_parameters_cat', 0 );
$filter->load_component_media			= $params->get( 'load_component_media', 1 );

$language = '';
if ($filter->filter_language == 1) {
	$language	= $lang->getTag();
}

$isItemsView 				= PhocacartRoute::isItemsView();

$urlItemsView 				= PhocacartRoute::getJsItemsRoute($filter->category);
$urlItemsViewWithoutParams 	= PhocacartRoute::getJsItemsRouteWithoutParams();
$config 					= $app->getConfig();
$sef						= $config->get('sef', 1);

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

/*
 * IF parameter $p['remove_parameters_cat'] == 1
 * If set in parameters to YES, when deselecting category, all other params will be removed too
 * RECOMMENDED as mostly the parameters can be assigned to category
 * ELSE
 * If set in parameters to NO, when deleting category, all other parameters will stay in URL
 * NOT RECOMMENDED as mostly all other parameters are assigned to category
 * If we are not in items views (isItemsView = 0), there cannot be even any parameters,
 * so we can reload to pure clean items view (urlItemsView) when we remove the category
 *
 * Remove category parameter from GET
 * IF NO ITEMS VIEW - we can remove everything and go back to items view (e.g. we are in category view)
 * IF SEF DISABLED - category id: id=5 is standard parameter and we need to remove it with help of phRemoveFilter
 * IF SEF ENABLED - category id: 5-category is not standard parameter but alias, remove it with querystring
 */


/*
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
/*
//$jsPart2 = PhocacartRenderJs::renderLoaderFullOverlay();
/*
$js   = array();

$js[] = ' ';
$js[] = '/* Function phChangeFilter ';
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

$document->addScriptDeclaration(implode("\n", $js));*/

// If we limit some parametr to active category, we need to inform the Javascript because in JS
// it is decided if the parametr list is reloaded by ajax (e.g. if you change category, and parameters are limited by active category,
// the list of parameters - listed in filter module needs to be ajax reloaded)

$limitToActiveCategory = 0;
if ($filter->limit_attributes_category == 1 ||
	$filter->limit_tags_category == 1 ||
	$filter->limit_labels_category == 1 ||
	$filter->limit_parameters_category == 1 ||
	$filter->limit_price_category == 1 ||
	$filter->limit_manufacturers_category == 1 ||
	$filter->limit_specifications_category  == 1 ) {
	$limitToActiveCategory = 1;
}

$document->addScriptOptions('phVarsModPhocacartFilter', array('isItemsView' => (int)$isItemsView, 'urlItemsView' => $urlItemsView, 'urlItemsViewWithoutParams' => $urlItemsViewWithoutParams, 'isSEF' => $sef ));
$document->addScriptOptions('phParamsModPhocacartFilter', array('removeParametersCat' => (int)$filter->remove_parameters_cat, 'limitToActiveCategory' => (int)$limitToActiveCategory));

$s = PhocacartRenderStyle::getStyles();
if ($filter->load_component_media == 1) {
	$media = PhocacartRenderMedia::getInstance('main');
	$media->loadBase();
	$media->loadBootstrap();
	$media->loadSpec();
}

require(ModuleHelper::getLayoutPath('mod_phocacart_filter', $params->get('layout', 'default')));
?>
