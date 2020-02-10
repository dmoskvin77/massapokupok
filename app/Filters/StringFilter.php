<?php
/**
 * Для валидации строковых данных
 */
class StringFilter extends BaseFilter 
{
	public function __construct($name, $isRequired, $description, $maxLen = 255, $minLen = 0)
	{
		$value = Request::getVar($name);
		
		// обязательно вызывать
		$this->setValue($value);
		
		if($isRequired && $value == null)
		{
			$this->message = "Обязательное поле $description";
			return false;
		}
		
		if (mb_strlen($value) > $maxLen)
		{
			$this->message = "$description превышает допустимую длину";
			return false;
		}		

		if (mb_strlen($value) < $minLen)
		{
			$this->message = "$description меньше необходимой длины";
			return false;
		}		
	}
}
?>