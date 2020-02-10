<?php
/**
 * Формочка для сообщения об оплате заказа пользователем
 *
 */
class UserpayControl extends AuthorizedUserControl
{
	public $pageTitle = "Оплата заказа";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$oheadid = Request::getInt('oheadid');
		if (!$oheadid)
			Enviropment::redirectBack("Не указан ID заказа");

		$ohm = new OrderHeadManager();
		$oheadObj = $ohm->getById($oheadid);
		if (!$oheadObj)
			Enviropment::redirectBack("Не найден заказ");

		if ($this->ownerSiteId != $oheadObj->ownerSiteId || $this->ownerOrgId != $oheadObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение заданного действия");

		$this->addData("oheadid", $oheadid);

		// закупка
		$zhm = new ZakupkaHeaderManager();
		$zakHeadObj = $zhm->getById($oheadObj->headId);
		if (!$zakHeadObj)
			Enviropment::redirectBack("Не найдена закупка");

        $um = new UserManager();
        $orgObj = $um->getById($zakHeadObj->orgId);
        $this->addData("org", $orgObj);
        $this->addData("ts", time());

        // сумма с орг сбором
		$orderAmount = $oheadObj->optAmount + round($oheadObj->optAmount*$zakHeadObj->orgRate/100, 2);

		// сколько человек должен денег
		$needAmount = round($orderAmount - $oheadObj->payAmount + $oheadObj->payBackAmount + $oheadObj->opttoorgDlvrAmount, 2);

        // плюсом надо добавить стоимость доставк в офис
        $officePayHold = 0;
        $officePrice = 0;
        $officePayAmount = 0;
        $officePayBackAmount = 0;

        // получим доставку и сумму по ней
        $oom = new OfficeOrderManager();
        $officeOrderObj = $oom->getByOrderIdUserId($oheadid, $actor->id);
        if ($officeOrderObj)
        {
            $officePayHold = $officeOrderObj->payHold;
            $officePrice = $officeOrderObj->price;
            $officePayAmount = $officeOrderObj->payAmount;
            $officePayBackAmount = $officeOrderObj->payBackAmount;
        }

        $needPayOffice = $officePrice - $officePayHold - $officePayAmount + $officePayBackAmount;

        // конечная сумма к оплате
        $needAmount = $needAmount + $needPayOffice;

        $payCommision = Configurator::get("application:paySystemCommisionPercent");
        $this->addData("payCommision", $payCommision);
        if ($payCommision > 0)
            $paySysAmount = $needAmount + round( $needAmount / 100 * $payCommision , 2 );
        else
            $paySysAmount = $needAmount;

		$this->addData("needAmount", $needAmount);

		// задача показать формочку с вводом суммы, даты оплаты и инфы откуда человек оплатил
		// для удобства находим предыдущую оплату и показываем информацию из неё
		// prevPayInfo

		// сформируем параметры для кнопки оплаты
		$pum = new PurseManager();
		$purseObj = $pum->getByOrgId($zakHeadObj->orgId);
		if ($purseObj)
		{
			// если есть кошелёк, надо выставить неоплаченный счёт
			// на нужную сумму
			// не оплаченные счета пусть болтаются пока тоже,
			// как нибудь потом можно удалять счета сборщиком мусора
			$pm = new PayManager();
			$payObj = new Pay();
			$payObj->userId = $actor->id;
			$payObj->orgId = $zakHeadObj->orgId;
			$payObj->headId = $oheadObj->headId;
			$payObj->amount = $needAmount;
			$payObj->dateCreate = time();
			$payObj->userInfo = "Счёт выставлен ".strftime('%d.%m.%Y в %H:%M', time());
			// за заказ
			$payObj->type = Pay::TYPE_ORDER;
			// ручной перевод
			$payObj->way = Pay::WAY_EK;
			// статус - новая операция (в обработке)
			$payObj->status = Pay::STATUS_NEW;
			$payObj->ownerSiteId = $this->ownerSiteId;
			$payObj->ownerOrgId = $this->ownerOrgId;
			$pm->save($payObj);

			// есть Единый кошелёк
			// что касается оплаты на кошелёк
			$secretWord = base64_decode($purseObj->skey);
			$vendorId = $purseObj->merchantId;

			// сформируем данные формы W1
			$fields = array();

			$this->addData("returnSuccess", 'http://'.$this->host.'/finanses/ok/1');

			$fields['WMI_MERCHANT_ID'] = $vendorId;
			$fields['WMI_PAYMENT_AMOUNT'] = $paySysAmount;
			$fields['WMI_PAYMENT_NO'] = 1000000+$payObj->id;
			$fields['WMI_AUTO_ACCEPT'] = '1';
			$fields['WMI_SUCCESS_URL'] = 'http://'.$this->host.'/finanses/ok/1';
			$fields['WMI_FAIL_URL'] = 'http://'.$this->host.'/finanses/error/1';

			$fields['WMI_CULTURE_ID'] = 'ru-RU';
			$fields['WMI_CURRENCY_ID'] = '643';
			$fields['WMI_DESCRIPTION'] = 'BASE64:'.base64_encode('Оплата счета N'.$payObj->id);

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

		// если есть Яндеск кнопка
        $ybm = new YakassabuttonManager();
        $yabutObj = $ybm->getApprovedByOrgId($zakHeadObj->orgId);
        if ($yabutObj) {
            $this->addData("yakassacode", str_replace("&quot;", '"', htmlspecialchars_decode($yabutObj->buttonCode, ENT_NOQUOTES)));
        }

	}

}
