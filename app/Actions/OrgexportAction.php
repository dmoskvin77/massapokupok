<?php
/**
 * формироване файлов экспорта
 *
 * headid - orderHead id
 * mode - режим (supplierorder)
 *
*/

class OrgexportAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", true, "ID закупки"));
		$mode = FilterInput::add(new StringFilter("mode", true, "Режим"));
		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		// выходные данные Excel
		$excelData = "";

		// поиск закупки
		$zhm = new ZakupkaHeaderManager();
		$zhObj = $zhm->getById($headid);
		if (!$zhObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zhObj->orgId != $this->actor->id || $this->ownerSiteId != $zhObj->ownerSiteId || $this->ownerOrgId != $zhObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// заявка поставщику по данным закупки
		if ($mode == "supplierorder")
		{
			$olm = new OrderListManager();
			$orderLines = $olm->getByHeadId($headid, "zlId, orderId");
			if (!count($orderLines))
				Enviropment::redirectBack("Нет данных для выгрузки");

			$excelData = $excelData.'<html><body><table><tr><td></td><td>Артикул</td><td>Товар</td><td>Размер</td><td>Цвет</td><td>Цена</td><td>Кол-во</td><td>Сообщение пользователя</td></tr>';

			// готовим строки таблицы Excel
			foreach ($orderLines AS $oneOrderLine)
			{
				if ($oneOrderLine->status == OrderList::STATUS_REJECT)
					continue;

				$excelData = $excelData."<tr>";

				$showIsRow = '';
				if ($oneOrderLine->zlId)
					$showIsRow = '[ряд]';

				$showPrice = round($oneOrderLine->optPrice, 2);
				$showPrice = str_replace('.', '.', $showPrice);

				$excelData = $excelData."<td>{$showIsRow}</td>";
				$excelData = $excelData."<td>{$oneOrderLine->prodArt}</td>";
				$excelData = $excelData."<td>{$oneOrderLine->prodName}</td>";
				$excelData = $excelData."<td>{$oneOrderLine->size}</td>";
				$excelData = $excelData."<td>{$oneOrderLine->color}</td>";
				$excelData = $excelData."<td>{$showPrice}</td>";
				$excelData = $excelData."<td>{$oneOrderLine->count}</td>";
				$excelData = $excelData."<td>{$oneOrderLine->comment}</td>";

				$excelData = $excelData."</tr>";
			}

			$excelData = $excelData."</table></body></html>";

		}

		// дата-время текущее
		$getDate = date("Y_m_d_H_i");

		// имя файла
		$fileName = $mode."_".$getDate.".xls";

		// вывод заголовков
		header('Content-Type: text/html; charset=utf-8');
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
		header('Content-transfer-encoding: binary');
		header('Content-Disposition: attachment; filename='.$fileName);
		header('Content-Type: application/x-unknown');

		echo $excelData;

		/*
		 * данные передавать так:
		 *
		echo<<<HTML
		<table border="1">
		<tr><td>
		htmlentities(iconv("utf-8", "windows-1251", $val),ENT_QUOTES, "cp1251"));
		</td></tr>
		</table>
		HTML;
		 */

		exit;

	}

}
