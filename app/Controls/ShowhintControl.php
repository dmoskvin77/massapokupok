<?php
/**
 * Компонент хинт
 */
class ShowhintControl extends BaseControl implements IComponent
{
	private $alias;

	function __construct($alias = null)
	{
		$this->alias = $alias;
	}

	// рендер
	public function render()
	{
		if ($this->alias)
		{
			$hm = new HintManager();
			$hint = $hm->getByAlias($this->ownerSiteId, $this->ownerOrgId, $this->alias);
			if ($hint)
			{
				$this->addData("hint", $hint);
				$this->addData("flname", Configurator::get("application:hintsFolder").$this->tplFolder."_".$hint->alias.".html");
			}

		}

	}	
}
