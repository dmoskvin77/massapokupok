<?php
/**
* Действие БО для отмены кнопки
*/
class RemoveyabuttonAction extends AdminkaAction
{
	public function execute()
	{
        $id = Request::getInt("id");
        if (!$id)
            Adminka::redirect("manageboard", "Не задан ID кнопки");

        if ($this->ownerSiteId != 1) {
            Adminka::redirectBack("Нет прав на выполнение данного действия");
        }

        $ybm = new YakassabuttonManager();
        $buttonObject = $ybm->getById($id);
        if (!$buttonObject)
            Adminka::redirect("manageyakassa", "Кнопка не найдена");

        if ($buttonObject->status == 0)
            Adminka::redirect("manageyakassa", "Статус кнопки не позволяет её отклонить");

        $buttonObject->status = 0;
        $buttonObject = $ybm->save($buttonObject);

        // отправим сообщение на е-майл "Кнопка отклонена"
        $um = new UserManager();
        $userObj = $um->getById($buttonObject->userId);
        $this->sendEmail($userObj);

        Adminka::redirect("manageyakassa", "Объявление опубликовано");

    }


    // уведомляем автора
    protected function sendEmail($userObj)
    {
        $shortTitle = "Ваша кнопка для оплаты Яндекс отклонена";

        $host = 'http://'.$this->host;
        $fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
        $fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
        $signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

        if ($userObj->firstName || $userObj->secondName)
            $userObj->firstName = ', '.$userObj->firstName;

        if ($userObj->secondName)
            $userObj->secondName = ' '.$userObj->secondName;

        if ($userObj->lastName)
            $userObj->lastName = ' '.$userObj->lastName;

        $vars = array(
            "MESSAGE_TITLE" => $shortTitle,
            "USER_LOGIN" => $userObj->login,
            "FIRST_NAME" => $userObj->firstName,
            "SECOND_NAME" => $userObj->secondName,
            "LAST_NAME" => $userObj->lastName,
            "MESSAGE_SIGN" => $signMessage,
            "HOST" => $host
        );

        $header = Adminka::prepareForMail(MailTextHelper::parse("header.html", $vars));
        $body = Adminka::prepareForMail(MailTextHelper::parse("yakassadeclined.html", $vars));
        $footer = Adminka::prepareForMail(MailTextHelper::parse("footer.html", $vars));

        require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
        Mail::send($shortTitle, $header.$body.$footer, $userObj->login, $fromEmail, $fromName);

    }

}
