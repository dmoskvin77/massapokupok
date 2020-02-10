<?php
/**
 * Контрол для редактирования/созданияв страницы CMS
 */
class EditcontentControl extends BaseAdminkaControl
{
	public function render()
	{
		$id = Request::getInt("id");
		$cm = new ContentManager();
		$content = null;
		if ($id)
			$content = $cm->getById($id);
				
		if ($content == null)
		{
			$content = new Content();
			$content->ownerSiteId = $this->ownerSiteId;
			$content->ownerOrgId = $this->ownerOrgId;
		}
		else
		{
			if ($this->ownerSiteId != $content->ownerSiteId || $this->ownerOrgId != $content->ownerOrgId)
				Adminka::redirect("managecontent", "Нет прав на выполнение данного действия");
		}
			
		$this->addData("content", $content);
		$this->addData("statusList", $cm->getStatusText());
		$this->addData("menuList", $cm->getMenuText());
	}	
}
