<?php
/**
 * Контрол для представления формы общих настроек системы
 */
class SettingsManagerControl extends BaseAdminkaControl
{
	public $menu = BoMenu::MENU_MAINSETTINGS;
	
	public function render()
	{	
		$sm = new SettingsManager();
		$settings = $sm->getSettingsList($this->ownerSiteId, $this->ownerOrgId);
		// список всех установок
		$this->addData("settingslist", $settings);

	}

}
