<?php

namespace Yoyo\Modules\YoyoAdmin;

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

		$this->article->setState('list.start', (int) $this->start);
		$this->article->setState('list.limit', (int) $this->limit);
		$this->article->setState('filter.published', 1);
		$this->article->setState('load_tags', false);

		// $this->article->setState('filter.category_id', $params->get('catid', []));

		$this->article->setState('filter.condition', 1);	

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

		$user = Factory::getuser();

		foreach ($items as &$item)
		{
			if ($user->authorise('core.edit', 'com_content.article.' . $item->id))
			{
				$item->link = JRoute::_('index.php?option=com_content&task=article.edit&id=' . $item->id);
			}
			else
			{
				$item->link = '';
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

		$path = JPATH_ADMINISTRATOR . '/components/com_content/models/';

    	JModelLegacy::addIncludePath($path);
    	
    	require_once $path.'articles.php';
	}
}