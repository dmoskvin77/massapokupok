<?php
/**
* Фильтр для проверки значения на тип float
*/
class FloatFilter extends BaseFilter 
{
	/**
	 * Проверка float
	 *
	 * @param string $name Имя поля
	 * @param bool $isRequired Флаг обязательности
	 * @param string $description Описание поля
	 * @param int $maxLen Максимальная длина
	 * @return void
	 */
	public function __construct($name, $isRequired, $description, $maxLen = 9)
	{		
		$value = Request::getVar($name, 0.00);
		
		// обязательно вызывать
		$this->setValue($value);
		
		if($isRequired && !$value)
		{
			$this->message = "Обязательное поле {$description}";
			return false;
		}
		
		if(!preg_match("/^[\-\+]?\d*(\.\d+)?$/", $value))
		{
			$this->message = "Неправильный формат {$description}";
			return false;
		}
		
		if(mb_strlen($value) > $maxLen)
		{
			$this->message = "Слишком большое значение {$description} ";
			return false;		
		}
	}
}
?>