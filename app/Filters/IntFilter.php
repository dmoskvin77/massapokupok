<?php
/**
* фильтр int
*/
class IntFilter extends BaseFilter 
{
	/**
	 * Проверка int
	 *
	 * @param string $name Имя поля
	 * @param bool $isRequired Флаг обязательности
	 * @param string $description Описание поля
	 * @param int $maxLen Максимальная длина
	 * @return void
	 */
	public function __construct($name, $isRequired, $description, $maxLen = 11)
	{
		$value = Request::getVar($name, 0);
		
		// обязательно вызывать
		$this->setValue( intval($value) );
		
		if($isRequired && !$value)
		{
			$this->message = "Обязательное поле {$description}";
			return false;
		}
		
		if(mb_strlen($value) > $maxLen)
		{
			$this->message = "{$description} превышает допустимую длину";
			return false;			
		}
		
		if(filter_var($value, FILTER_VALIDATE_INT) === null)
		{
			$this->message = "Неправильный формат {$description}";
			return false;
		}
	}
}
?>