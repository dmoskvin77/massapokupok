<?php
/**
 * Действие БО для сохранения страницы CMS
 * 
 */
class SavecontentAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		$alias = FilterInput::add(new StringFilter("alias", true, "Псевдоним"));
		$title = FilterInput::add(new StringFilter("title", true, "Заголовок"));
		$pageTitle = Request::getVar("pageTitle");
		$pageDesc = Request::getVar("pageDesc");
		$pageKeys = Request::getVar("pageKeys");
		$text = Request::getVar("text");
		$status = Request::getVar("status", Content::STATUS_DISABLED);
		$menu = Request::getVar("menu", Content::MENU_NONE);
				
		if (mb_strlen($text) > 10000000 || mb_strlen($pageTitle) > 10000000 || mb_strlen($pageDesc) > 10000000 || mb_strlen($pageKeys) > 10000000)
			FilterInput::addMessage("Слишком большой текст");
			
		$cm = new ContentManager();	
		$tmp = $cm->isExists($this->ownerSiteId, $this->ownerOrgId, $alias);
		if ($tmp != null && $tmp->id != $id)
			FilterInput::addMessage("Страница с таким псевдонимом уже существует");	
		
		if (!FilterInput::isValid())
		{
			FormRestore::add("edit-content");
			Adminka::redirect("managecontent", FilterInput::getMessages());
		}
		
		$content = null;
		if ($id)
			$content = $cm->getById($id);
		
		if (!$content)
			 $content = new Content();
		else
		{
			if ($this->ownerSiteId != $content->ownerSiteId || $this->ownerOrgId != $content->ownerOrgId)
				Adminka::redirect("managecontent", "Нет прав на выполнение данного действия");
		}
		
				 
		$content->alias = $alias;
		$content->title = $title;
		$content->pageTitle = $pageTitle;
		$content->pageDesc = $pageDesc;
		$content->pageKeys = $pageKeys;
		$content->text = $text;
		$content->status = $status;
		$content->menu = $menu;
		$content->ownerSiteId = $this->ownerSiteId;
		$content->ownerOrgId = $this->ownerOrgId;
		$cm->save($content);
	
		Adminka::redirect("managecontent", "Страница успешно сохранена");

	}

}
