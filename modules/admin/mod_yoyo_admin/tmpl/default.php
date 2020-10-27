<?php
/**
 * @package    Yoyo
 *
 * @author     ClickFWD, LLC <info@clickfwd.com>
 * @copyright  Copyright (c) 2020 ClickFWD, LLC All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://getyoyo.dev
 */

defined('_JEXEC') or die;

$yoyoComponents = [
	['heading' => 'Articles', 'name' => 'articles'],
	['heading' => 'Counter', 'name' => 'counter'],
	['heading' => 'Image Upload', 'name' => 'upload'],
];
?>
<div style="margin: 20px; display: flex; flex-flow: row wrap;     justify-content: space-between;">

	<?php foreach($yoyoComponents as $component): ?>

		<div style="flex: 0 0 33%; padding: 0 1rem;">

			<h3><?php echo $component['heading']; ?></h3>

			<div style="margin: 2rem 0 3rem 0;">
				<?php echo Yoyo\yoyo_render($component['name'],['yoyo:source'=>'module.yoyo_admin']); ?>
			</div>

		</div>

	<?php endforeach; ?>

</div>