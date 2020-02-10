<?php
/**
* Запрос на подключение w1
*
*/
class Orgorderw1Action extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		$pum = new PurseManager();
		$purseObj = $pum->getByOrgId($actor->id);
		if ($purseObj)
		{
			Enviropment::redirectBack("У Вас уже подключен Единый Кошелёк");
		}

		// выставим счет на подключение организатору
		$scm = new SiteCommisionManager();
		// поиск уже существующего счета
		$stComObj = $scm->getByType(SiteCommision::TYPE_CONNECTW1);
		// счет добавляется только один раз
		if (!$stComObj)
		{
			$stComObj = new SiteCommision();
			$stComObj->orgId = $orgId;
			$stComObj->dateCreate = time();
			$stComObj->type = SiteCommision::TYPE_CONNECTW1;
			$stComObj->status = SiteCommision::STATUS_NEW;
			$stComObj->ownerSiteId = $this->ownerSiteId;
			$stComObj->ownerOrgId = $this->ownerOrgId;
			$stComObj->needAmount = intval(Configurator::get("application:connectw1Price"));
			$scm->save($stComObj);
		}

		Enviropment::redirectBack("Заказ на подключеие к Единому Кошельку создан, выставлен счет");

	}

}
