<?php
/**
* Действие БО для подтверждения оплаты комиссии
*/
class AprovecommAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("manageservices", "Не задан ID платежа");

        $scm = new SiteCommisionManager();
        $commObj = $scm->getById($id);
        if (!$commObj)
            Adminka::redirect("manageservices", "Платеж не найден");

		// нужен контроль актора на соответствие!!!
        if ($commObj->type == 'TYPE_ZAK') {
            // комиссию по закупке может подствердить только админ текущего магазина
            if ($commObj->ownerSiteId != $this->ownerSiteId || $commObj->ownerOrgId != $this->ownerOrgId) {
                Adminka::redirect("manageservices", "Нет прав на выполнение данного действия");
            }
        }
        else {
            // остальные платежи может подсверждать только главный аккаунт
            if ($this->ownerSiteId != 1) {
                Adminka::redirect("manageservices", "Нет прав на выполнение данного действия");
            }
        }

        $commObj->status = SiteCommision::STATUS_SUCCED;
        $commObj->dateConfirm = time();
        $commObj = $scm->save($commObj);

		$om = new UserManager();
		$curOrg = $om->getById($commObj->orgId);
		if (!$curOrg) {
            Adminka::redirectBack("Не найден организатор");
        }

		$this->sendEmail($curOrg, $commObj);

		Adminka::redirectBack("Платеж подтвержден, уведомление отправлено");
	}


	/**
	* Функция отправляет письмо на е-майл
	*
	* @param Opt $curOrg орг
	*/
	protected function sendEmail($curOrg, $commObj)
	{
		$shortTitle = "Ваш платеж подтвержден";

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
			"COMM_ID" => $commObj->id,
			"NAME" => $orgName,
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Utility::prepareStringForMail(MailTextHelper::parse("header.html", $vars));
		$body = Utility::prepareStringForMail(MailTextHelper::parse("commapproved.html", $vars));
		$footer = Utility::prepareStringForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $curOrg->login, $fromEmail, $fromName);

	}

}
