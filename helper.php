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
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ModuleHelper;

class ModPhocaCartFilterHelper
{
	public static function getAjax() {

		jimport('joomla.application.module.helper');


        $app = Factory::getApplication();
        $document = Factory::getDocument();

        if (!ComponentHelper::isEnabled('com_phocacart')) {
            echo '<div class="alert alert-error alert-danger">'.Text::_('Phoca Cart Error') . ' - ' . Text::_('Phoca Cart is not installed on your system').'</div>';
			return;
        }
        if (file_exists(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php')) {
            // Joomla 5 and newer
            require_once(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php');
        } else {
            // Joomla 4
            JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');
        }


		$lang = Factory::getLanguage();
		$lang->load('com_phocacart');

		$module = ModuleHelper::getModule('phocacart_filter');

		if (!$module || (isset($module->id) && (int)$module->id < 1)) {
		    // Module is not published
            return "";
        }

		$params = new Registry();
		$params->loadString($module->params);

		$filter								    = new PhocacartFilter();
        $filter->category					    = $params->get( 'filter_category', 0 );
        $filter->tag 						    = $params->get( 'filter_tag', 1 );
        $filter->label 						    = $params->get( 'filter_label', 1 );
        $filter->parameter						= $params->get( 'filter_parameter', 1 );
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

        $filter->ajax                           = 1;

        echo $filter->renderList();

	}
}
