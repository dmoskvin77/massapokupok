<?php
/**
 * фильтр e-mail
 */
class EmailFilter extends BaseFilter 
{
	/**
	 * Проверка e-mail
	 *
	 * @param string $name Имя поля
	 * @param bool $isRequired Флаг обязательности
	 * @param string $description Описание поля
	 * @param int $maxLen Максимальная длина
	 * @return void
	 */
	public function __construct($name, $isRequired, $description, $maxLen = 255)
	{
		$value = Request::getVar($name);
		
		// обязательно вызывать
		$this->setValue($value);
		
		if (mb_strlen($value) > $maxLen)
		{
			$this->message = "{$description} превышает допустимую длину";
			return false;
		}
		
		if($isRequired && $value == null)
		{
			$this->message = "Обязательное поле $description";
			return false;
		}
			
		$pattern = "/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9+\-]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/";
		if(!preg_match($pattern, $value))
		{
			$this->message = "Неправильный формат {$description}";
			return false;
		}		
	}
}
?>