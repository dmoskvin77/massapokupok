<?php
/**
 * Действие БО для сохранения новости
 */
class SaveNewsAction extends AdminkaAction
{
	public function execute()
	{
		$newsId = FilterInput::add(new IntFilter("newsId", false, "id"));
		$subject = FilterInput::add(new StringFilter("subject", true, "Заголовок"));
        $content = Request::getVar("body");
		if (mb_strlen($content) > 10000000)
			FilterInput::addMessage("Слишком большой текст");

		$dateFrom = Request::getVar("dateFrom");

        if ($dateFrom == null)
            $dateFrom = strtotime(XDateTime::formatDateTime("now"));
        else
            $dateFrom = strtotime($dateFrom);

		if (!FilterInput::isValid())
		{
			FormRestore::add("form");
			Adminka::redirect("managenews", FilterInput::getMessages());
		}

		$nm = new NewsManager();
		$doAct = "Изменена ";
		if ($newsId)
		{
			$news = $nm->getById($newsId);
			if (!$news)
				Adminka::redirect("managenews", "Новость не найдена");

			if ($this->ownerSiteId != $news->ownerSiteId || $this->ownerOrgId != $news->ownerOrgId)
				Adminka::redirect("managenews", "Нет прав на выполнение данной операции");
		}
		else 
		{
			$news = new News();
			$doAct = "Добавлена ";
		}
		
		$news->subject = $subject;
		$news->showDate = $dateFrom;
		$news->creationDate = $dateFrom;
		$news->body = $content;
		$news->ownerSiteId = $this->ownerSiteId;
		$news->ownerOrgId = $this->ownerOrgId;
		$nm->save($news);

		Adminka::redirect("managenews", $doAct."новость");

	}

}