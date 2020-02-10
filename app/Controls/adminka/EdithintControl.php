<?php
/**
 * Контрол для редактирования/создания подсказки
 */
class EdithintControl extends BaseAdminkaControl
{
	public function render()
	{
		$id = Request::getInt("id");
		
		$hm = new HintManager();
		$hint = $hm->getById($id);
				
		if ($hint == null)
		{
			$hint = new Hint();
			$hint->ownerSiteId = $this->ownerSiteId;
			$hint->ownerOrgId = $this->ownerOrgId;
		}
			
		$this->addData("hint", $hint);
	}	
}
?>