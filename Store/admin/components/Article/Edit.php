<?php

/**
 * Edit page for Articles
 *
 * @package   Store
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class StoreArticleEdit extends SiteArticleEdit
{
	// {{{ protected properties

	/**
	 * @var string
	 */
	protected $ui_xml = 'Store/admin/components/Article/edit.xml';

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->mapClassPrefixToPath('Store', 'Store');
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		parent::saveDBData();

		$this->saveRegions();
	}

	// }}}
	// {{{ protected function saveRegions()

	protected function saveRegions()
	{
		$region_list = $this->ui->getWidget('regions');

		SwatDB::updateBinding($this->app->db, 'ArticleRegionBinding',
			'article', $this->edit_article->id, 'region', $region_list->values,
			'Region', 'id');
	}

	// }}}
	// {{{ protected function saveArticle()

	protected function saveArticle()
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'bodytext',
			'description', 'visible', 'searchable'));

		$this->edit_article->title       = $values['title'];
		$this->edit_article->shortname   = $values['shortname'];
		$this->edit_article->bodytext    = $values['bodytext'];
		$this->edit_article->description = $values['description'];
		$this->edit_article->visible     = $values['visible'];
		$this->edit_article->searchable  = $values['searchable'];

		$this->edit_article->save();
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$regions = $this->ui->getWidget('regions');
		$regions_options = SwatDB::getOptionArray($this->app->db,
			'Region', 'text:title', 'integer:id');

		$regions->addOptionsByArray($regions_options);

		// default region visibility
		if ($this->id === null)
			$regions->values = array_flip($regions_options);
	}

	// }}}
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		parent::loadDBData();

		$this->loadRegions();
	}

	// }}}
	// {{{ protected function loadRegions()

	protected function loadRegions()
	{
		$regions_list = $this->ui->getWidget('regions');

		$sql = sprintf('select region
			from ArticleRegionBinding where article = %s',
			$this->app->db->quote($this->id, 'integer'));

		$bindings = SwatDB::query($this->app->db, $sql);

		foreach ($bindings as $binding)
			$regions_list->values[] = $binding->region;
	}

	// }}}
}

?>
