<?php
/**
 * Контрол для визуального представления ссылок
 * на страницы при выводе пейджинкга
 */
class PagerControl extends BaseControl implements IComponent
{
	protected $total = null;
	protected $per = null;
	protected $param = null;
	protected $url = null;
	protected $currentPage = 0;
	
	/**
	 * Конструктор
	 * 
	 * @param int $currentPage Номер текущей страницы
	 * @param int $total Общее количество записей
	 * @param int $per Количество записей на странице
	 * @param string $url  для формирования ссылки типа $url&page=1
	 * @param string $param Имя параметра, передающего номер страницы
	 */
	public function __construct($currentPage, $total, $per = 10, $url = null, $param = "page")
	{
		$this->currentPage = $currentPage;
		$this->total = $total;
		$this->per = $per;
		$this->param = $param;	
		$this->url = $url;
	}
	
	public function render()
	{
		// формируем URL
		// из текущего надо убрать значение param
		$pattern = "/[&]{0,1}{$this->param}=[\d]+/i";
		$this->url = preg_replace($pattern, "", $this->url);
		
		if($this->total == 0)
			return false;
		
		// всего страниц
		$totalPages = floor( $this->total / $this->per );
		if ($this->total % $this->per)
			$totalPages++;		
		
		// значения для навигации
		$prevPage = $this->currentPage - 1;
		$nextPage = null;
		if ($this->currentPage != $totalPages)
			$nextPage = $this->currentPage + 1;

		$this->addData("currentPage", $this->currentPage);
		$this->addData("totalPages", $totalPages);
		$this->addData("prevPage", $prevPage);
		$this->addData("nextPage", $nextPage);
		$this->addData("url", $this->url);	
		$this->addData("param", $this->param);
	}
	
	// номер страницы пэйджера $pageParam
	// если страница не задана, возвращает 1
	public static function getPage($pageParam = "page")
	{
		$page = Request::getInt($pageParam);
		$page = $page > 1 ? $page : 1;
		return $page;
	}

	/**
	* Возвращает часть списка в соотве-ии с номером страницы
	* 
	* @param array Список для пейждинга
	* @param int $perPage Сколько записей на странице
	*/
	public static function limit($list, $perPage, $pageParam = "page")
	{
		if(!$list)
			return array();
			
		$page = PagerControl::getPage($pageParam);
			
		$ids = array_slice($list, ($page - 1) * $perPage, $perPage, true);
		
		// если страница не первая и на этой странице ничего нет,
		// но есть сушности на предыдущих страницах, то перенаправляю на последнюю из тех страниц
		if(!$ids && $page > 1)
		{
			$total = count($list);
			$lastPage = 0;
			while($total > 0)
			{
				$lastPage ++;
				$total -= $perPage;
			}
			$url = preg_replace("/[&\/]{$pageParam}[=\/][-\d]+/i", "", Request::requestUri());
			$url .= strpos($url, "?") === false ? "?" : "&";
			$url .= "$pageParam=$lastPage";
			Request::redirect($url);
		}
		
		return $ids;
	}
}
