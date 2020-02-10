<?php
/**
 * Компонент отображает контент заданной информационной страницы
 *
 */
class PageEmbabedControl extends BaseControl implements IComponent
{
	private $alias;

	function __construct($alias = null)
	{
		$this->alias = $alias;
	}

	// рендер
	public function render()
	{
		$gotContent = null;
		if ($this->alias)
		{
			$cm = new ContentManager();
			$contentObj = $cm->getByAlias($this->ownerSiteId, $this->ownerOrgId, $this->alias);
			if ($contentObj)
				$gotContent = $contentObj->text;

		}

		if ($gotContent)
			$this->addData("content", str_replace("&quot;", '"', htmlspecialchars_decode($gotContent, ENT_NOQUOTES)));

	}

}

