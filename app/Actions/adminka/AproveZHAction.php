<?php
/**
* Действие БО для одобрения закупки
*/
class AproveZHAction extends AdminkaAction
{
	public function execute()
	{
		$headid = Request::getInt("headid");
		if (!$headid)
			Adminka::redirect("managezh", "Не задана закупка");

		$zhm = new ZakupkaHeaderManager();
		$curZH = $zhm->getById($headid);
		if (!$curZH)
			Adminka::redirect("managezh", "Не задана закупка");

		if ($this->ownerSiteId != $curZH->ownerSiteId || $this->ownerOrgId != $curZH->ownerOrgId)
			Adminka::redirect("managezh", "Нет прав на выполнение данного действия");

		$om = new UserManager();
		$curOrg = $om->getById($curZH->orgId);
		if (!$curOrg)
			Adminka::redirectBack("Не найден организатор");

		$curZH->entityStatus = ZakupkaHeader::ENTITY_STATUS_ACTIVE;
		$curZH->status = ZakupkaHeader::STATUS_ACTIVE;
		$zhm->save($curZH);

		$this->sendEmail($curOrg, $curZH);

		Adminka::redirectBack("Закупка открыта, уведомление отправлено");
	}


	/**
	* Функция отправляет письмо на е-майл
	*
	* @param Opt $curOrg орг
	*/
	protected function sendEmail($curOrg, $curZH)
	{
		$shortTitle = "Ваша закупка открыта";

		$host = 'http://'.$this->host;
		$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
		$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
		$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

		$orgName = '';
		if ($curOrg->firstName || $curOrg->lastName)
			$orgName = ',';

		if ($curOrg->firstName)
			$orgName .= ' '.$curOrg->firstName;

		if ($curOrg->lastName)
			$orgName .= ' '.$curOrg->lastName;

		$vars = array(
			"MESSAGE_TITLE" => $shortTitle,
			"ORG_LOGIN" => $curOrg->login,
			"ZH_ID" => $curZH->id,
			"ZH_NAME" => $curZH->name,
			"NAME" => $orgName,
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Utility::prepareStringForMail(MailTextHelper::parse("header.html", $vars));
		$body = Utility::prepareStringForMail(MailTextHelper::parse("zhapproved.html", $vars));
		$footer = Utility::prepareStringForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $curOrg->login, $fromEmail, $fromName);

	}

}
