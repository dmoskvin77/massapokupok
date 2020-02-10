<?php
/**
 * Компонент отображает лого проекта
 */
class PrologoControl extends BaseControl implements IComponent
{
	function __construct($current = null)
	{

	}

	// рендер
	public function render()
	{
		$this->addData("city", SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city"));
	}
}

