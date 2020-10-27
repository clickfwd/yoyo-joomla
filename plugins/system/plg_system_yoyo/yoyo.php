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

use Clickfwd\Yoyo\Joomla\JoomlaComponentResolver;
use Clickfwd\Yoyo\Services\Configuration as YoyoConfig;
use Clickfwd\Yoyo\Services\Request as YoyoRequest;
use Clickfwd\Yoyo\Yoyo;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

class plgSystemYoyo extends CMSPlugin
{
	protected $yoyo;

	protected $app;

	protected $yoyoAssetsLoaded = false;

	public function onAfterInitialise()
	{
		define('YOYO_FRAMEWORK', 1);
		
		require_once __DIR__.'/src/helpers.php';
		require_once JPATH_LIBRARIES.'/yoyo/vendor/autoload.php';
		require_once __DIR__.'/src/JoomlaRequest.php';
		require_once __DIR__.'/src/JoomlaComponentResolver.php';

		$this->yoyo = new Yoyo();
		
		// $url = '/index.php?option=com_ajax&group=system&plugin=yoyo&format=raw';
		
		$url = '/index.php?yoyo&format=raw';

		$this->yoyo->configure([
		  'url' => rtrim(Uri::base(),'/').$url,
		  'scriptsPath' => rtrim(Uri::root(),'/').'/media/lib_yoyo/js/',
		  // Disabled until a better history caching solution can be implemented
		  'historyEnabled' => false,
		]);
		
		$this->yoyo->registerComponentResolver('joomla', JoomlaComponentResolver::class);

		$this->yoyo->bindRequest(new Clickfwd\Yoyo\Joomla\JoomlaRequest());

		// If not a Yoyo update, we are done
		$request = Yoyo::request();

		if (! $request->isYoyoRequest()) {
			return;
		}

		$output = $this->yoyo->update();

		die($output);
	}

	/**
	 * While this would be the Joomla-way to process ajax requests
	 * It is much slower to produce a response than the above solution
	 */
	/*
	public function onAjaxYoyo()
	{
		$request = YoyoRequest::getInstance();

		// If not a Yoyo update, we are done
		if (! $request->isYoyoRequest()) {
			return;
		}

		$output = $this->yoyo->update();

		return $output;	
	}
	*/

	public function onYoyoRender()
	{
		if ($this->yoyoAssetsLoaded) {
			return;
		}
	
		$doc = $this->app->getDocument();
		
		$doc->addScript(YoyoConfig::htmxSrc());

		$doc->addScript(YoyoConfig::yoyoSrc());
		
		$doc->addScriptDeclaration(YoyoConfig::javascriptInitCode(false));
		
		$doc->addStyleDeclaration(YoyoConfig::cssStyle(false));

		$this->yoyoAssetsLoaded = true;
	}
}