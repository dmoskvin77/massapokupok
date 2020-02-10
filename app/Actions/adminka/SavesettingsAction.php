<?php
/**
 * Действие БО сохранение основных настроек системы
 */

class SavesettingsAction extends AdminkaAction
{
	public function execute()
	{
		$st = new SettingsManager();		
		$fullList = $st->getSettingsList($this->ownerSiteId, $this->ownerOrgId);
		// сохраним все остальные параметры
		foreach ($fullList as $key => $value)
		{
			$choosen = FilterInput::add(new StringFilter("{$key}", false, ""));
			$choosen = str_replace('"', "&lt;", $choosen);
			$st->updateValue($this->ownerSiteId, $this->ownerOrgId, $key, $choosen);
		}
				
		Adminka::redirect("settingsmanager", "Настройки успешно сохранены");

	}

}
