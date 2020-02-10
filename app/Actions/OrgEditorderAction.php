<?php
/**
 * Редактирование заказа участника организатором
 *
*/

class OrgEditorderAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", false, "ID закупки"));
		$orderid = FilterInput::add(new IntFilter("orderid", false, "ID заказа"));

		$comment = Request::getVar("comment");
		if (mb_strlen($comment) > 10000000)
			FilterInput::addMessage("Слишком большой текст комментария");

		if (!$headid && !$orderid)
			FilterInput::addMessage("Не указаны параметры заказа");

		$headObj = null;
		$zhm = new ZakupkaHeaderManager();
		$olm = new OrderListManager();
		if ($orderid)
		{
			$olObj = $olm->getById($orderid);
			if (!$olObj)
				FilterInput::addMessage("Не найден заказ");
			else
			{
				if ($this->ownerSiteId != $olObj->ownerSiteId || $this->ownerOrgId != $olObj->ownerOrgId)
					FilterInput::addMessage("Нет прав на выполнение данного действия");

				if (!$headid)
				{
					$headObj = $zhm->getById($olObj->headId);
					if ($headObj)
					{
						if ($headObj->orgId != $this->actor->id || $this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
							FilterInput::addMessage("Нет прав на выполнение данного действия");
					}
				}
			}
		}
		else
			Enviropment::redirectBack("Не найдена строка заказа");

		// на сколько меняется кол-во
		$deltaCount = 0;
		$firstCount = 0;

		// данные ряда
		$zlm = new ZakupkaLineManager();
		$zlObj = null;

        $prodName = FilterInput::add(new StringFilter("prodName", true, "Название"));
        $prodArt = FilterInput::add(new StringFilter("prodArt", false, "Артикул"));
        $optPrice = FilterInput::add(new StringFilter("optPrice", true, "Цена"));
        $count = FilterInput::add(new StringFilter("count", false, "Кол-во"));
        $size = FilterInput::add(new StringFilter("size", false, "Размер"));
        $color = FilterInput::add(new StringFilter("color", false, "Цвет"));

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		if (!$headObj && $headid)
			$headObj = $zhm->getById($headid);

		if (!$headObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($headObj->orgId != $this->actor->id || $this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		if ($headObj->status == ZakupkaHeader::STATUS_DELIVERED || $headObj->status == ZakupkaHeader::STATUS_CLOSED)
			Enviropment::redirectBack("Статус закупки не позволяет редактировать заказ");

		$ts = time();
		$ohm = new OrderHeadManager();

		$orderHeadObj = $ohm->getByUserZakId($olObj->userId, $headObj->id);
		if (!$orderHeadObj)
			Enviropment::redirectBack("Не найден заказ");

		if ($this->ownerSiteId != $orderHeadObj->ownerSiteId || $this->ownerOrgId != $orderHeadObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		// начало транзакции
		$ohm->startTransaction();
		try
		{
			// запишем в лог сделанное изменение
			$olchlogman = new OrderlistchangelogManager();
			$olchObj = new OrderListChangeLog();

			$olchObj->orderId = $olObj->orderId;
			$olchObj->userId = $olObj->userId;
			$olchObj->orgId = $olObj->orgId;
			$olchObj->headId = $olObj->headId;
			if ($olObj->zlId) {
                $olchObj->zlId = $olObj->zlId;
            }

			$olchObj->prodNameOld = $olObj->prodName;
			$olchObj->prodArtOld = $olObj->prodArt;
			$olchObj->optPriceOld = $olObj->optPrice;
			$olchObj->countOld = $olObj->count;
			$olchObj->sizeOld = $olObj->size;
			$olchObj->colorOld = $olObj->color;
			$olchObj->commentOld = $olObj->comment;

			$olchObj->prodNameNew = $prodName;
			$olchObj->prodArtNew = $prodArt;
			$olchObj->optPriceNew = $optPrice;
			$olchObj->countNew = $count;
			$olchObj->sizeNew = $size;
			$olchObj->colorNew = $color;
			$olchObj->commentNew = $comment;

			$olchObj->dateCreate = time();

			$olchObj->ownerSiteId = $this->ownerSiteId;
			$olchObj->ownerOrgId = $this->ownerOrgId;

			$olchlogman->save($olchObj);


			// сохраняем сам заказ
			$olObj->orderId = $orderHeadObj->id;
			$olObj->userId = $olObj->userId;
			$olObj->orgId = $headObj->orgId;
			$olObj->headId = $headid;

			$olObj->prodName = $prodName;
			$olObj->prodArt = $prodArt;
			$olObj->optPrice = $optPrice;
			$olObj->count = $count;

			$olObj->size = $size;
			$olObj->color = $color;
			$olObj->comment = $comment;

			$olObj->status = OrderList::STATUS_NEW;
			$olObj->dateCreate = $ts;
			$olObj->dateUpdate = $ts;
			$olm->save($olObj);

			// увеличим кол-во заказов в закупке
			$headObj->orderCount = $headObj->orderCount + $count - $firstCount;
			if ($headObj->orderCount < 0)
				$headObj->orderCount = 0;

			$zhm->save($headObj);

			// кол-во у ряда по кол-ву так же уточним
			if ($zlObj && $zlObj->minValue)
			{
				$zlObj->orderedValue = $zlObj->orderedValue + $count - $firstCount;
				if ($zlObj->orderedValue < 0)
					$zlObj->orderedValue = 0;

				$zlm->save($zlObj);
			}

			// проапдейтить заказ
			// для этого запустим ф-ю полного пересчёта данных
			$ohm->rebuildOrder($olObj->userId, $headObj->id);

			// в очередь добавим задание пересчитать набранность закупки
			$qm = new QueueMysqlManager();
			$qm->savePlaceTask("calcnarate", null, null, $headObj->id);

			// напишем сообщение участнику о том, что его заказ был отредактирован
			// ставим рассылать сообщения на почту
			$um = new UserManager();
			$oneUser = $um->getById($olObj->userId);
			$oneOrg = $um->getById($headObj->orgId);

			$host = 'http://'.$this->host;
			$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
			$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
			$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

			$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html"));
			$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html"));

			$viewLink = $host."/viewcollection/id/".$headObj->id;
			$orgLink = $host."/vieworg/id/".$headObj->orgId;

			$shortTitle = "Изменение заказа организатором";
			$messageToEmail = 'Организатор <a href="'.$orgLink.'">'.$oneOrg->nickName.'</a> отредактировала Ваш заказ в закупке <a href="'.$viewLink.'">'.$headObj->name.'</a>. Проверьте корзину. Если возникнут вопросы, отправьте их организатору личным сообщением.';
			$messagePublic = 'Организатор [url="'.$orgLink.'"]'.$oneOrg->nickName.'[/url] отредактировала Ваш заказ в закупке [url="'.$viewLink.'"]'.$headObj->name.'[/url]. Проверьте корзину. Если возникнут вопросы, отправьте их организатору личным сообщением.';

			if ($oneUser->firstName || $oneUser->secondName)
				$oneUser->firstName = ', '.$oneUser->firstName;

			if ($oneUser->secondName)
				$oneUser->secondName = ' '.$oneUser->secondName;

			if ($oneUser->lastName)
				$oneUser->lastName = ' '.$oneUser->lastName;

			$vars = array(
				"MESSAGE_TITLE" => $shortTitle,
				"USER_LOGIN" => $oneUser->nickName,
				"FIRST_NAME" => $oneUser->firstName,
				"SECOND_NAME" => $oneUser->secondName,
				"LAST_NAME" => $oneUser->lastName,
				"MAIN_MESSAGE" => $messageToEmail,
				"MESSAGE_SIGN" => $signMessage,
				"HOST" => $host
			);

			// уведомление закинем
			$pem = new PublicEventManager();
			$pem->buildInsertOnZakupka($this->ownerSiteId, $this->ownerOrgId, array($oneUser->id), $oneOrg->id, $oneOrg->nickName, $headObj->id, $headObj->name, $messagePublic);

		}
		catch (Exception $e)
		{
			$ohm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirectBack("Не удалось записать заказ");
		}

		$body = Enviropment::prepareForMail(MailTextHelper::parse("onlytext.html", $vars));
		// ставим письмо в очередь
		allMailManager::addMailMessage($shortTitle, $header.$body.$footer, $oneUser->login, $fromEmail, $fromName);

		$ohm->commitTransaction();

		Enviropment::redirectBack("Данные сохранены, уведомление участнику отправлено");

	}

}
