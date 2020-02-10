<?php
/**
 * Контрол для редактирования поставщика
 *
 */
class OrgEditOptControl extends AuthorizedOrgControl
{
	public $pageTitle = "Поставщик";

	public function render()
	{
		$optId = Request::getInt("id");
		$actor = $this->actor;
		// вытащим редактируемого оптовика и проверим принадлежит ли он этому оргу
		if ($optId)
		{
			$showOpt = null;
			$op = new OptovikManager();
			$curOpt = $op->getById($optId);
			if ($curOpt)
			{
				if ($curOpt->userId == $actor->id || $this->ownerSiteId != $curOpt->ownerSiteId || $this->ownerOrgId != $curOpt->ownerOrgId)
				{
					// всё нормально
					$showOpt = $curOpt;
					$this->addData("optovik", $curOpt);

					// присоединим теперь ссылки
					$ulm = new UrlListManager();
					$this->addData("urls", $ulm->getByOptovik($curOpt->id));

				}

			}

			if ($showOpt == null)
				Enviropment::redirectBack("Редактирование недопустимо, либо объект не найден");

		}
		else
		{
			// добавление нового поставщика
			// ничего не передаём во вьюшку

		}

	}

}
