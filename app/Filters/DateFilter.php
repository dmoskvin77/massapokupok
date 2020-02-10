<?php
/**
* фильтр date
*/
class DateFilter extends BaseFilter 
{
	/**
	 * Проверка date
	 *
	 * @param string $name Имя поля
	 * @param bool $isRequired Флаг обязательности
	 * @param string $description Описание поля
	 * @param int $maxLen Максимальная длина
	 * @return void
	 */
	public function __construct($name, $isRequired, $description)
	{
		$value = Request::getVar($name);
		
		// обязательно вызывать
		$this->setValue($value);
		
		if($isRequired && !$value)
		{
			$this->message = "Обязательное поле {$description}";
			return false;
		}
		
		if(!preg_match("/^\d{2}\.\d{2}\.\d{4}$/", $value))
		{
			$this->message = "Неправильный формат {$description}";
			return false;
		}		
	}
}
?>