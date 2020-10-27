<?php

namespace Yoyo\Modules\Yoyo;

use Clickfwd\Yoyo\Component;
use ContentHelperRoute;
use JAccess;
use JComponentHelper;
use JModelLegacy;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use JRoute;

class Articles extends Component
{
	public $page = 1;

	public $limit = 10;

	protected $article;

	protected $app;

	public function mount()
	{
		$this->loadArticleClasses();

		$this->app = Factory::getApplication();	

		$this->article = JModelLegacy::getInstance('Articles', 'ContentModel', [
			'ignore_request' => true
		]);

		$appParams = $this->app->getParams();

		$this->article->setState('params', $appParams);
		$this->article->setState('list.start', (int) $this->start);
		$this->article->setState('list.limit', (int) $this->limit);
		$this->article->setState('filter.published', 1);
		$this->article->setState('load_tags', false);

		// $this->article->setState('filter.category_id', $params->get('catid', []));

		$this->article->setState('filter.condition', 1);	
		$this->article->setState('filter.language', $this->app->getLanguageFilter());

	    $order_map = array(
	        'm_dsc' => 'a.modified DESC, a.created',
	        'c_dsc' => 'a.created',
	        'p_dsc' => 'a.publish_up',
	        'h_dsc' =>  'a.hits',
	    );

    	$ordering = ArrayHelper::getValue($order_map, 'c_dsc', 'a.publish_up');

		$this->article->setState('list.ordering', $ordering);
		$this->article->setState('list.direction', 'DESC');
	}

	protected function getArticlesProperty()
	{
		// Access filter
		$access     = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		$this->article->setState('filter.access', $access);

		$items = $this->article->getItems();

		foreach ($items as &$item)
		{
			$item->slug = $item->id . ':' . $item->alias;

			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised)) {

				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language));
			} else {

				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

		return $items;	
	}

    protected function getStartProperty()
    {
        return 1 + (($this->page - 1) * $this->limit);
    }

    protected function getNextProperty()
    {
        return $this->page + 1;
    }

    protected function getPreviousProperty()
    {
        return $this->page > 1 ? $this->page - 1 : false;
    }	

	protected function loadArticleClasses() 
	{
		if (class_exists('ContentHelperRoute')) {
			return;
		}

		$path = JPATH_SITE . '/components/com_content/models/';
    	
    	JModelLegacy::addIncludePath($path);
    	
    	require_once $path.'articles.php';
	
		require_once JPATH_SITE . '/components/com_content/helpers/route.php';
	}
}