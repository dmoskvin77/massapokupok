<?php
/**
* Голова закупки (особенно когда в ней нескольдко товаров)
*
*/
class ZakupkaHeader extends BaseEntity
{
	public $entityTable = 'zakupkaHeader';
	public $vikupId = null;
	public $name = null;
	public $categoryId1 = null;
	public $categoryId2 = null;
	public $categoryId3 = null;
	public $entityStatus = null;
	public $status = null;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $orgId = null;
	public $optId = null;
	public $orgRate = null;
	public $minAmount = null;
	public $minValue = null;
	public $curAmount = 0;
	public $opttoorgDlvrAmount = 0;
	public $curValue = 0;
	public $dateAmount = null;
	public $dateValue = null;
	public $narate = null;
	public $voteCount = 0;
	public $orderCount = 0;
	public $startDate = null;
	public $validDate = null;
	public $description = null;
	public $specialNotes = null;
	public $useForm = null;
	public $usePay = null;
	public $pageUrl = null;
	public $picFile1 = null;
	public $picSrv1 = null;
	public $picVer1 = 1;
	public $docFile1 = null;
	public $docFile2 = null;
	public $docFile3 = null;
	public $docSrv1 = null;
	public $docSrv2 = null;
	public $docSrv3 = null;
	public $currency = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	// статусы
	const STATUS_NEW = "STATUS_NEW";				// новая закупка
	const STATUS_VOTING = "STATUS_VOTING";			// голосование / модерация
	const STATUS_ACTIVE = "STATUS_ACTIVE";			// закупка активна
	const STATUS_STOP = "STATUS_STOP";				// закупка закончилась (всё купили - объявлен стоп)
	const STATUS_CHECKED = "STATUS_CHECKED";		// проверена (наличие отмечено)
	const STATUS_ADDMORE = "STATUS_ADDMORE";		// дозаказ после стопа
	const STATUS_SEND = "STATUS_SEND";				// закупка отправлена поставщиком
	const STATUS_DELIVERED = "STATUS_DELIVERED";	// доставлена
	const STATUS_CLOSED = "STATUS_CLOSED";			// закрыта


	function getFields()
	{
		return array
		(
			"id" => self::ENTITY_FIELD_INT,
			"vikupId" => self::ENTITY_FIELD_INT,
			"name" => self::ENTITY_FIELD_STRING,
			"categoryId1" => self::ENTITY_FIELD_INT,
			"categoryId2" => self::ENTITY_FIELD_INT,
			"categoryId3" => self::ENTITY_FIELD_INT,
			"entityStatus" => self::ENTITY_FIELD_INT,
			"status" => self::ENTITY_FIELD_STRING,
			"dateCreate" => self::ENTITY_FIELD_INT,
			"dateUpdate" => self::ENTITY_FIELD_INT,
			"orgId" => self::ENTITY_FIELD_INT,
			'optId' => self::ENTITY_FIELD_INT,
			'orgRate' => self::ENTITY_FIELD_INT,
			'minAmount' => self::ENTITY_FIELD_INT,
			'minValue' => self::ENTITY_FIELD_INT,
			'curAmount' => self::ENTITY_FIELD_STRING,
			'opttoorgDlvrAmount' => self::ENTITY_FIELD_STRING,
			'curValue' => self::ENTITY_FIELD_INT,
			'dateAmount' => self::ENTITY_FIELD_INT,
			'dateValue' => self::ENTITY_FIELD_INT,
			"narate" => self::ENTITY_FIELD_STRING,
			"voteCount" => self::ENTITY_FIELD_INT,
			'orderCount' => self::ENTITY_FIELD_INT,
			"startDate" => self::ENTITY_FIELD_INT,
			"validDate" => self::ENTITY_FIELD_INT,
			"description" => self::ENTITY_FIELD_STRING,
			"specialNotes" => self::ENTITY_FIELD_STRING,
			"useForm" => self::ENTITY_FIELD_STRING,
			"usePay" => self::ENTITY_FIELD_STRING,
			"pageUrl" => self::ENTITY_FIELD_STRING,
			"picFile1" => self::ENTITY_FIELD_STRING,
			"picSrv1" => self::ENTITY_FIELD_STRING,
			"picVer1" => self::ENTITY_FIELD_INT,
			"docFile1" => self::ENTITY_FIELD_STRING,
			"docFile2" => self::ENTITY_FIELD_STRING,
			"docFile3" => self::ENTITY_FIELD_STRING,
			"docSrv1" => self::ENTITY_FIELD_STRING,
			"docSrv2" => self::ENTITY_FIELD_STRING,
			"docSrv3" => self::ENTITY_FIELD_STRING,
			'currency' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}


	// описание статусов
    public static function getStatusDesc($stat = null)
    {
    	$statList = array(
				self::STATUS_NEW => "Новая / на редактировании",
				self::STATUS_VOTING => "На голосовании / на модерации",
				self::STATUS_ACTIVE => "Активна (идёт набор заказов)",
				self::STATUS_STOP => "Стоп (набор заказов завершён)",
				self::STATUS_ADDMORE => "Дозаказ (идёт набор заказов)",
				self::STATUS_CHECKED => "Проверено наличие",
				self::STATUS_SEND => "В пути к организатору",
				self::STATUS_DELIVERED => "Доставлена организатору",
				self::STATUS_CLOSED => "Завершена (закрыта)"
			);
		return $stat ? $statList[$stat] : $statList;
    }

	// в зависимости от текущего
    public static function getStatusDescCurrent($stat = null)
    {
		$statList = array();

		if (!$stat || $stat == self::STATUS_NEW)
			$statList = array(
							self::STATUS_NEW => "На редактировании (новая)",
							self::STATUS_VOTING => "На голосовании / на модерации",
							self::STATUS_ACTIVE => "Активна (идёт набор заказов)"
						);

		if ($stat == self::STATUS_VOTING)
			$statList = array(
				self::STATUS_VOTING => "На голосовании / на модерации",
				self::STATUS_NEW => "На редактировании (новая)",
			);

		if ($stat == self::STATUS_ACTIVE)
			$statList = array(
							self::STATUS_ACTIVE => "Активна (идёт набор заказов)",
							self::STATUS_STOP => "Стоп (набор заказов завершён)"
						);

		if ($stat == self::STATUS_STOP)
			$statList = array(
							self::STATUS_STOP => "Стоп (набор заказов завершён)",
							self::STATUS_ADDMORE => "Дозаказ (идёт набор заказов)",
							self::STATUS_CHECKED => "Проверено наличие",
							self::STATUS_DELIVERED => "Доставлена организатору"
						);

		if ($stat == self::STATUS_ADDMORE)
			$statList = array(
							self::STATUS_STOP => "Стоп (набор заказов завершён)",
							self::STATUS_ADDMORE => "Дозаказ (идёт набор заказов)",
							self::STATUS_CHECKED => "Проверено наличие",
							self::STATUS_DELIVERED => "Доставлена организатору"
						);

		if ($stat == self::STATUS_CHECKED)
			$statList = array(
							self::STATUS_CHECKED => "Проверено наличие",
							self::STATUS_ADDMORE => "Дозаказ (идёт набор заказов)",
							self::STATUS_SEND => "В пути к организатору",
						);

		if ($stat == self::STATUS_SEND)
			$statList = array(
							self::STATUS_SEND => "В пути к организатору",
							self::STATUS_DELIVERED => "Доставлена организатору",
							self::STATUS_CLOSED => "Завершена (закрыта)"
						);

		if ($stat == self::STATUS_DELIVERED)
			$statList = array(
							self::STATUS_DELIVERED => "Доставлена организатору",
							self::STATUS_CLOSED => "Завершена (закрыта)"
						);

		return $statList;
    }
}
