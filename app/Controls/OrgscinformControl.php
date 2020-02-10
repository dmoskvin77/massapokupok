<?php
/**
 * Формоска для сообщения об оплате заказа пользователем
 *
 */
class OrgscinformControl extends AuthorizedOrgControl
{
	public $pageTitle = "Оплата счета сайту";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$gotError = Request::getInt("error");
		if ($gotError == 1)
			Context::setError("Оплата не была произведена");

		$gotOk = Request::getInt("ok");
		if ($gotOk == 1)
			Context::setError("Оплата произведена, обновите страницу для проверки статуса счёта");

		$scid = Request::getInt("scid");
		if (!$scid)
			Enviropment::redirectBack("Не задан ID");

		$scm = new SiteCommisionManager();
		$scObj = $scm->getById($scid);
		if (!$scObj)
			Enviropment::redirectBack("Не найден счет");

		if ($scObj->orgId != $actor->id || $this->ownerSiteId != $scObj->ownerSiteId || $this->ownerOrgId != $scObj->ownerOrgId)
			Enviropment::redirectBack("Не достаточно прав для заданного действия");

		$this->addData("scObj", $scObj);

		// -----------------------------------------------------------------------

		// что касается оплаты на кошелёк
		$secretWord = 'elIxWXRTc15pcllgbXg2YUx1a2I1MW9RTWBf';
		$vendorId = '193537155072';

		// сформируем данные формы W1
		$fields = array();
		$pstypes = array();

		$this->addData("returnSuccess", 'http://'.$this->host.'/orgcommision/ok/1');

		$fields['WMI_MERCHANT_ID'] = $vendorId;
		$fields['WMI_PAYMENT_AMOUNT'] = $scObj->needAmount;
		$fields['WMI_PAYMENT_NO'] = 10000+$scObj->id;
		$fields['WMI_AUTO_ACCEPT'] = '1';
		$fields['WMI_SUCCESS_URL'] = 'http://'.$this->host.'/orgcommision/ok/1';
		$fields['WMI_FAIL_URL'] = 'http://'.$this->host.'/orgcommision/error/1';

		$fields['WMI_CULTURE_ID'] = 'ru-RU';
		$fields['WMI_CURRENCY_ID'] = '643';
		$fields['WMI_DESCRIPTION'] = 'BASE64:'.base64_encode('Оплата счёта N'.$scObj->id.' от '.$actor->nickName);

		// Сортировка значений внутри полей
		foreach ($fields as $name => $val)
		{
			if (is_array($val))
			{
				usort($val, 'strcasecmp');
				$fields[$name] = $val;
			}
		}

		// Формирование сообщения, путем объединения значений формы,
		// отсортированных по именам ключей в порядке возрастания.
		uksort($fields, 'strcasecmp');
		$fieldValues = '';
		foreach ($fields as $value)
		{
			if (is_array($value))
			{
				foreach($value as $v)
				{
					// Конвертация из текущей кодировки (UTF-8)
					// необходима только если кодировка магазина отлична от Windows-1251
					$v = iconv('utf-8', 'windows-1251', $v);
					$fieldValues .= $v;
				}
			}
			else
			{
				// Конвертация из текущей кодировки (UTF-8)
				// необходима только если кодировка магазина отлична от Windows-1251
				$value = iconv('utf-8', 'windows-1251', $value);
				$fieldValues .= $value;
			}
		}

		// Формирование значения параметра WMI_SIGNATURE, путем
		// вычисления отпечатка, сформированного выше сообщения,
		// по алгоритму MD5 и представление его в Base64
		$signature = base64_encode(pack('H*', md5($fieldValues.$secretWord)));

		//Добавление параметра WMI_SIGNATURE в словарь параметров формы
		$fields['WMI_SIGNATURE'] = $signature;

		$this->addData('fields', $fields);

	}
}
