<?php
/**
* Фильтр для проверки значения на соответствие регулярному выражению
*/
class RegexFilter extends BaseFilter 
{
	/**
	* Проверка по регулярному выражению
	*
	* @param string $name Имя поля
	* @param string $pattern Шаблон регулярного выражения
	* @param string $description Описание поля
	* @return void
	*/
	public function __construct($name, $pattern, $description)
	{
		$value = Request::getVar($name);
		
		// обязательно вызывать
		$this->setValue($value);
		
		if(!preg_match($pattern, $value))
		{
			$this->message = "Неправильный формат {$description}";
			return false;
		}
	}
}
?>