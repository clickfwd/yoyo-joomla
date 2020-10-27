<?php
/**
 * @package    Yoyo
 *
 * @author     ClickFWD, LLC <info@clickfwd.com>
 * @copyright  Copyright (c)2020 ClickFWD, LLC All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://getyoyo.dev
 */

use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require ModuleHelper::getLayoutPath('mod_yoyo_admin', $params->get('layout', 'default'));
