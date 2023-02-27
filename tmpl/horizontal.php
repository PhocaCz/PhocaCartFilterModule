<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.dropdown', '.dropdown', []);

echo '<div class="ph-filter-box-horizontal'.$moduleclass_sfx .'">';
echo $filter->renderList([
  'layout' => 'form_filter_horizontal',
  'wrapper_role' => '',
]);
echo '</div>';
