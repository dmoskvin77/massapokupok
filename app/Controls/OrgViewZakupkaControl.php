<?php
/**
 * Контрол для просмотра содержимого закупки
 *
 */
class OrgViewZakupkaControl extends AuthorizedOrgControl
{
	public $pageTitle = "Закупка";

	public function render()
	{
		$actor = Context::getActor();

		$ts = time();
		$this->addData("ts", $ts);

		// id закупки
		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		// id ряда если редактируется
		$id = Request::getInt("id");

		// режим просмотра закупки оргом
		$mode = Request::getVar("mode");
		$this->addData("mode", $mode);

		$zhm = new ZakupkaHeaderManager();
		$curZHM = $zhm->getByIdAndOrgId($headid, $actor->id);
		// есть закупка
		if ($curZHM)
		{
			if ($curZHM->orgId != $actor->id || $this->ownerSiteId != $curZHM->ownerSiteId || $this->ownerOrgId != $curZHM->ownerOrgId)
				Enviropment::redirectBack("Нет прав на редактирование закупки");

			// это действительно закупка данного орга
			$this->addData("headid", $headid);
			$this->addData("headobj", $curZHM);
			$this->addData("headname", $curZHM->name);
			$this->addData("headstatus", $curZHM->status);
			$this->addData("headstatusname", ZakupkaHeader::getStatusDesc($curZHM->status));

			// остальные данные
			$this->addData("zakListStatusNames", OrderList::getStatusDesc());

			$um = new UserManager();
			$orgList = $um->getByIds(array($curZHM->orgId));
			$this->addData("orgList", $orgList);
			// список пользователей
			$userIds = array();
			// заказы участников в рядах
			$orderRowsArray = array();
			// заказы пользователей
			$olm = new OrderListManager();

			$orders = null;
			if ($mode == 'order' || $curZHM->status)
			{
				$orders = $olm->getByHeadId($headid);
				// получить список участников
				if (count($orders))
				{
					$this->addData("orders", $orders);
					foreach ($orders AS $oneOrder)
					{
						$userIds[] = $oneOrder->userId;
						if ($oneOrder->zlId && $oneOrder->rp)
							$orderRowsArray[$oneOrder->zlId.'_'.$oneOrder->rp.'_'.$oneOrder->num] = $oneOrder;
					}
				}
			}

			// кол-во новых оплат
			$payMan = new PayManager();
			$newPayCnt = $payMan->countNewPaysByHeadId($headid);
			$newPayCount = "";
			if ($newPayCnt)
				$newPayCount = " <b>({$newPayCnt})</b>";

			$this->addData("newPayCount", $newPayCount);

			// что касается рядов
			$zlm = new ZakupkaLineManager();
			$pm = new ProductManager();

			if ($id)
			{
				$editZline = $zlm->getById($id);
				if (!$editZline)
					Enviropment::redirectBack("Ряд не найден");

				if ($editZline->orgId != $actor->id)
					Enviropment::redirectBack("Нет прав на редактирование ряда");

				$this->addData("editZline", $editZline);

				// поиск товара
				if ($editZline->productId)
				{
					$editProduct = $pm->getById($editZline->productId);
					$this->addData("editProduct", $editProduct);
				}

			}
			else
			{
				$lineList = null;

				// поднимаем с базы ряды этой закупки
				if (!$mode)
					$lineList = $zlm->getByHeadId($headid);

				// сбор id товаров
				if (count($lineList))
				{
					$lineListConverted = array();
					$prodIds = array();
					foreach ($lineList AS $oneLine)
					{
						if ($oneLine->productId)
							$prodIds[] = $oneLine->productId;

						// ключ сортировки
						// поможет организовать любую сортировку рядов
						// например по цене
						$sortKey = $oneLine->id;

						$buildRow = "";
						if ($oneLine->sizes)
						{
							// транслируем ряды сразу в список <ul><li>
							// со вставкой ников куда следует
							$getSizesArray = @unserialize($oneLine->sizes);
							// размещенные заказы участников
							$getPlacedArray = @unserialize($oneLine->sizesChoosen);
							// строка с готовым списком <ul></li>
							if (count($getSizesArray) && is_array($getSizesArray))
							{
								asort($getSizesArray);
								$arrayUnique = array_unique($getSizesArray);

								// построение линий ряда
								for ($i = 1; $i <= $oneLine->rowNumbers; $i++)
								{
									if (count($arrayUnique) == 1)
										$buildRow = $buildRow.'<div class="zlinedatahorizontal">';
									else
										$buildRow = $buildRow.'<div class="zlinedata">';

									$prevSize = null;
									$buildCounter = 0;
									$shorterLineCounter = 0;
									foreach ($getSizesArray AS $oneSizeKey => $oneSizeVal)
									{
										if (count($arrayUnique) == 1) {
											$shorterLineCounter++;
											if ($shorterLineCounter > 10) {
												$shorterLineCounter = 1;
												$prevSize = 'путьксвету4';
											}
										}

										if ($oneSizeVal != $prevSize && $prevSize != null)
											$buildRow = $buildRow."</ul>";

										if ($oneSizeVal != $prevSize)
										{
											if ($buildCounter % 2 == 0)
												$buildRow = $buildRow."<ul>";
											else
												$buildRow = $buildRow."<ul style='background-color: #eee;'>";

											// первую ячейку резервируем под обозначение размера
											// далее будут только плюсы
											if ($oneSizeVal != '-' && $oneSizeVal != '+') {
												$oneSizeValShort = Utility::limitString($oneSizeVal, 30);
												if ($oneSizeVal != $oneSizeValShort) {
													$oneSizeValShort .= '..';
												}
												$buildRow = $buildRow . "<li><b>{$oneSizeValShort}</b></li>";
											}

											$buildCounter++;
										}

										if (isset($getPlacedArray[$oneSizeKey.'_'.$i]) && is_array($getPlacedArray[$oneSizeKey.'_'.$i]))
										{
											// если статус закупки СТОП, надо показать кнопки наличия для текущей ячейки ряда
											$presenseBtns = '';

											if (in_array($curZHM->status, array(ZakupkaHeader::STATUS_STOP, ZakupkaHeader::STATUS_CHECKED, ZakupkaHeader::STATUS_SEND, ZakupkaHeader::STATUS_DELIVERED)))
											{
												if (isset($orderRowsArray[$oneLine->id."_".$oneSizeKey.'_'.$i]))
												{
													$orderListItem = $orderRowsArray[$oneLine->id."_".$oneSizeKey.'_'.$i];

													$presenseBtns = '<button id="zakorderreject-'.$orderListItem->id.'" class="btn btn-default btn-sm zakorderreject';
													if ($orderListItem->status == 'STATUS_NEW' || $orderListItem->status == 'STATUS_CONFIRM')
														$presenseBtns = $presenseBtns.' color-grey';

													if ($orderListItem->status == 'STATUS_REJECT')
														$presenseBtns = $presenseBtns.' color-red';

													$presenseBtns = $presenseBtns.'" type="button" title="нет в наличии"><span class="glyphicon glyphicon-minus"></span></button> <button id="zakorderconfirm-'.$orderListItem->id.'" class="btn btn-default btn-sm zakorderconfirm';

													if ($orderListItem->status == 'STATUS_NEW' || $orderListItem->status == 'STATUS_REJECT')
														$presenseBtns = $presenseBtns.' color-grey';

													if ($orderListItem->status == 'STATUS_CONFIRM')
														$presenseBtns = $presenseBtns.' color-green';

													$presenseBtns = $presenseBtns.'" type="button" title="в наличии"><span class="glyphicon glyphicon-ok"></span></button> ';

												}
											}

											$userData = $getPlacedArray[$oneSizeKey.'_'.$i];
											$ordBtn = $presenseBtns."<a target='_blank' href='/index.php?show=viewuser&id=".$userData['id']."'>".$userData['nick']."</a>";
										}
										else
										{
											if (in_array($curZHM->status, array(ZakupkaHeader::STATUS_ACTIVE, ZakupkaHeader::STATUS_ADDMORE)))
												$ordBtn = "<a href='/index.php?do=userplaceorder&lid={$oneLine->id}&rp={$oneSizeKey}&num={$i}'><span style='color: green;'><span style='color: #333;'>[</span><b>+</b><span style='color: #333;'>]</span></span></a>";
											else
												$ordBtn = "<span style='color: green;'><span style='color: #333;'>[</span><b>+</b><span style='color: #333;'>]</span></span>";
										}

										$buildRow = $buildRow."<li>{$ordBtn}</li>";

										$prevSize = $oneSizeVal;
									}

									$buildRow = $buildRow."</ul>";
									$buildRow = $buildRow.'</div>';
								}

							}

						}

						// в выбором по кол-ву
						if ($oneLine->minValue > 0)
						{
							$buildRow = $buildRow.'<select id="zlchkolvoid-'.$oneLine->id.'-sel" class="zlselselit">';

							for ($i = 1; $i <= min(5, $oneLine->minValue); $i++)
							{
								$buildRow = $buildRow.'<option value='.$i.'>'.$i.'</option>';
							}

							$buildRow = $buildRow.'</select> '.$oneLine->minName.' <button id="zlchkolvoid-'.$oneLine->id.'" class="zlselpush">В корзину</button>';

							$buildRow = $buildRow.'<br/><span style="font-size: 12px; font-style: italic;">(в коробке по '.$oneLine->minValue.' '.$oneLine->minName.')</span>';
						}

						$oneLine->buildRow = $buildRow;
						$lineListConverted[$sortKey] = $oneLine;
					}

					ksort($lineListConverted);
					$this->addData("lineList", $lineListConverted);

					if (count($prodIds))
					{
						// поднимаем товары выбранных рядов
						$prodList = $pm->getByIds($prodIds);
						if (count($prodList))
						{
							$prodListConverted = array();
							foreach ($prodList AS $oneProduct)
								$prodListConverted[$oneProduct->id] = $oneProduct;

							$this->addData("prodList", $prodListConverted);
						}

					}

				}

				// не выбран режим, т.е. будет показана основная вкладка
				if (!$mode || $mode == '')
				{
					// каменты
					$cocomm = new CoCommentManager();
					// заголовки
					$commlist = $cocomm->getModByHeadId($headid, CoComment::COMMENT_ZAKUPKA, true);
					// головы каментов
					$this->addData("commlist", $commlist);
					// пройдем циклом заголовки и получим цепочки ответов к каждому
					$subcomments = array();
					// все сабкомменты
					$allSubcomments = $cocomm->getAllSubComments($headid, CoComment::COMMENT_ZAKUPKA, true);
					if (count($allSubcomments) > 0)
					{
						foreach ($allSubcomments as $comheaditem)
							$subcomments[$comheaditem['rootId']][] = $comheaditem;

						// субкаменты
						$this->addData("subcomments", $subcomments);
					}
				}

				// режим просмотра "Заказы"
				if ($mode == 'order')
				{
					if (count($orders))
					{
						$userList = $um->getByIds($userIds);
						if (count($userList))
						{
							$showUser = array();
							foreach ($userList AS $oneUser) {
								if ($oneUser->isBot)
									$showUser[$oneUser->id] = $oneUser->nickName." (бот)";
								else
									$showUser[$oneUser->id] = $oneUser->nickName;
							}

							$this->addData("showUser", $showUser);

						}

					}

				}

				// доставка
				if ($mode == 'delivery')
				{
					$dlvrprall = 0;
					// поднять надо orderHead и orderList
					$ohm = new OrderHeadManager();
					$orderHeads = $ohm->getByHeadId($headid);
					$orderheadsIds = array();
					$userIds = array();
					if (count($orderHeads))
					{
						foreach ($orderHeads AS $oneOrderHead)
						{
							// нанизываем стоимость заказа, которую надо брать с участника
							// сумма с орг сбором
							$oneOrderHead->orderSumAmount = round($oneOrderHead->optAmount + round($oneOrderHead->optAmount*$curZHM->orgRate/100, 2), 2);

							// оплачено
							$oneOrderHead->paid = round($oneOrderHead->payAmount - $oneOrderHead->payBackAmount, 2);

							// сколько человек должен денег
							$oneOrderHead->needAmount = round($oneOrderHead->orderSumAmount - $oneOrderHead->payAmount + $oneOrderHead->payBackAmount - $oneOrderHead->payHold + $oneOrderHead->opttoorgDlvrAmount, 2);

							$oneOrderHead->overAmount = 0;
							if ($oneOrderHead->needAmount < 0)
							{
								$oneOrderHead->overAmount = round((-1)*$oneOrderHead->needAmount, 2);
								$oneOrderHead->needAmount = 0;
							}

							$userIds[] = $oneOrderHead->userId;
							$orderheadsIds[] = $oneOrderHead->id;
						}

						$userIds = array_unique($userIds);
						$olm = new OrderListManager();
						$um = new UserManager();
						$users = $um->getByIds($userIds);

						$orderLines = $olm->getLinesByHeadIds($orderheadsIds);
						// всё же нужен повором массива
						$outOrderLines = array();
						if (count($orderLines))
						{
							foreach ($orderLines AS $oneOrderLine)
							{
								$dlvrprall = $dlvrprall + $oneOrderLine->opttoorgDlvrAmount;
								$outOrderLines[$oneOrderLine->orderId][] = $oneOrderLine;
							}
						}

						$this->addData("orderHeads", $orderHeads);
						$this->addData("orderLines", $outOrderLines);
						$this->addData("users", $users);

					}

				}

				// режим просмотра "Оплаты" участников
				if ($mode == 'pay')
				{
					$payMan = new PayManager();
					$payments = $payMan->getByHeadId($headid);
					$showPay = array();
					if (count($payments))
					{
						foreach ($payments AS $onePay)
							$showPay[$onePay->userId][] = $onePay;

						$this->addData("showPay", $showPay);
					}

					// orderHead содержит баланс каждого участника
					// к нему надо насаживать список оплат и список участников
					$ohm = new OrderHeadManager();
					$orderHeads = $ohm->getByHeadId($headid);
					// список участников
					if (count($orderHeads))
					{
						$userIds = array();

						// общие суммы по оплатам
						$orderSumAmountSum = 0;
						$paidSum = 0;
						$needAmountSum = 0;
						$overAmountSum = 0;

						foreach ($orderHeads AS $oneOrderHead)
						{
							// сумма с орг сбором
							$oneOrderHead->orderSumAmount = round($oneOrderHead->optAmount + round($oneOrderHead->optAmount*$curZHM->orgRate/100, 2), 2);

							// оплачено
							$oneOrderHead->paid = round($oneOrderHead->payAmount - $oneOrderHead->payBackAmount, 2);

							// сколько человек должен денег
							$oneOrderHead->needAmount = round($oneOrderHead->orderSumAmount - $oneOrderHead->payAmount + $oneOrderHead->payBackAmount - $oneOrderHead->payHold + $oneOrderHead->opttoorgDlvrAmount, 2);

							$oneOrderHead->overAmount = 0;
							if ($oneOrderHead->needAmount < 0)
							{
								$oneOrderHead->overAmount = round((-1)*$oneOrderHead->needAmount, 2);
								$oneOrderHead->needAmount = 0;
							}

							// payHold - платежи в обработке

							// суммы
							$orderSumAmountSum = $orderSumAmountSum + $oneOrderHead->orderSumAmount;
							$paidSum = $paidSum + $oneOrderHead->paid;
							$needAmountSum = $needAmountSum + $oneOrderHead->needAmount;
							$overAmountSum = $overAmountSum + $oneOrderHead->overAmount;

							$userIds[] = $oneOrderHead->userId;
						}

						$this->addData("orderHeads", $orderHeads);

						$this->addData("orderSumAmountSum", $orderSumAmountSum);
						$this->addData("paidSum", $paidSum);
						$this->addData("needAmountSum", $needAmountSum);
						$this->addData("overAmountSum", $overAmountSum);

						$userList = $um->getByIds($userIds);
						if (count($userList))
						{
							$showUser = array();
							foreach ($userList AS $oneUser) {
								if ($oneUser->isBot)
									$showUser[$oneUser->id] = $oneUser->nickName." (бот)";
								else
									$showUser[$oneUser->id] = $oneUser->nickName;
							}

							$this->addData("showUser", $showUser);

						}

						// так же отправим во вьюшку расшифровку констант сущности pay
						$this->addData("payStatuses", Pay::getStatusDesc());
						$this->addData("payTypes", Pay::getTypeDesc());
						$this->addData("payWays", Pay::getWayDesc());

					}

				}

				// встречи
				if ($mode == 'meeting')
				{
					// сначала поднимаем список уже назначенных встреч
					$mem = new MeetingManager();
					$meetingList = $mem->getByHeadId($headid);
					$this->addData("meetingList", $meetingList);
				}

                // офисы
                if ($mode == 'offices')
                {
                    $oom = new OfficeOrderManager();
                    $officeOrders = $oom->getByHeadId($headid);
                    if (count($officeOrders))
                    {
                        $this->addData("officeOrders", $officeOrders);
                        $this->addData("officeOrderStatuses", OfficeOrder::getStatusDesc());
                        $this->addData("officeOrderPayStatuses", OfficeOrder::getPayStatusDesc());

                        // получить список пользователей и список офисов
                        $officeIds = array();
                        $userIds = array();
                        foreach ($officeOrders AS $oneOffice)
                        {
                            $officeIds[$oneOffice->officeId] = $oneOffice->officeId;
                            $userIds[$oneOffice->userId] = $oneOffice->userId;
                        }

                        $um = new UserManager();
                        $officeUserList = $um->getByIds($userIds);
                        $this->addData("officeUserList", $officeUserList);

                        $om = new OfficeManager();
                        $officeList = $om->getByIds($officeIds);
                        $this->addData("officeList", $officeList);

                    }

                }

			}

		}
		else
			Enviropment::redirectBack("Не найдена закупка");

	}

}
