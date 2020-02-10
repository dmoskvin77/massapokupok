<?php
/**
 * Менеджер 
 */
class AllvkNoticeManager extends BaseEntityManager
{
	function __construct()
	{
		$this->setCommonConnection(Application::getConnection("master"));
	}

	/** сообщения отправлены */
	const STATUS_SENT = 'STATUS_SENT';
	/** нет писем для отправки */
	const STATUS_NOTHING = 'STATUS_NOTHING';

	/**
	 * Функция добавляет сообщение в очередь отправки
	 *
	 */
	public static function addVkMessage($body, $toVkId, $status = null, $ccId = null, $zcId = null)
	{
		$vkmm = new self();
		$vkmail = new allvkNotice();

		if ($status)
			$vkmail->status = $status;
		else
			$vkmail->status = allMail::STATUS_NEW;

		if ($ccId > 0)
			$vkmail->ccId = $ccId;

		if ($zcId > 0)
			$vkmail->zcId = $zcId;

		$vkmail->body = $body;
		$vkmail->vkId = $toVkId;
		$vkmm->save($vkmail);

		return $vkmail->id;
	}

}
