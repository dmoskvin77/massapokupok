<?php
/**
 * Действие БО для сохранения подсказки
 * 
 */
class SavehintAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		$alias = FilterInput::add(new StringFilter("alias", true, "Псевдоним"));
		$title = FilterInput::add(new StringFilter("title", true, "Заголовок"));
		$contAlias = FilterInput::add(new StringFilter("contAlias", false, "Альяс страницы с подробностями"));
		$hint = Request::getVar("hint");

		if (mb_strlen($hint) > 10000000)
			FilterInput::addMessage("Слишком большой текст");
			
		$hm = new HintManager();
		$tmp = $hm->isExists($this->ownerSiteId, $this->ownerOrgId, $alias);
		if ($tmp != null && $tmp->id != $id)
			FilterInput::addMessage("Страница с таким псевдонимом уже существует");	
		
		if (!FilterInput::isValid())
		{
			FormRestore::add("edit-hint");
			Adminka::redirect("managehits", FilterInput::getMessages());
		}
		
		$hintObj = null;
		
		if ($id != 0)
			$hintObj = $hm->getById($id);
		
		if (!$hintObj)
			 $hintObj = new Hint();
		else
		{
			if ($this->ownerSiteId != $hintObj->ownerSiteId || $this->ownerOrgId != $hintObj->ownerOrgId)
				Adminka::redirect("managehits", "Нет прав на выполнение данного действия");
		}
				 
		$hintObj->alias = $alias;
		$hintObj->title = $title;
		$hintObj->hint = $hint;
		$hintObj->ownerSiteId = $this->ownerSiteId;
		$hintObj->ownerOrgId = $this->ownerOrgId;

		if ($contAlias)
			$hintObj->contAlias = $contAlias;
		
		$hm->save($hintObj);

		$fileName = Configurator::get("application:hintsFolder").$this->tplFolder."_".$alias.".html";

		@$fp = fopen($fileName, "w");
		if ($fp)
		{
			fwrite($fp, str_replace("&quot;", '"', htmlspecialchars_decode($hint, ENT_NOQUOTES)));
			fclose($fp);
		}
		else
		{
			Adminka::redirect("managehints", "Подсказка сохранена, но не удалось сохранить текст во внешний файл");
		}
	
		Adminka::redirect("managehints", "Подсказка успешно сохранена");

	}

}

