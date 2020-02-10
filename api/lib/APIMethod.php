<?php
/**
 * Абстрактный класс-предок всех методов
 * @version $Id: ProfileManager.php 29 2008-01-24 13:04:43Z afi $
 * @author Andrey Filippov
 */
abstract class APIMethod implements IAPIMethod 
{
	/** Список обязательных параметров запроса */
	private $requiredParams = array();
	
	/** Список необязательных параметров запроса */
	private $optionalParams = array();
	
	/** Требует ли метод залогиненного пользователя */
	public $requiredAuth = true;
	
	/**
	 * Регистрирует обязательный параметр
	 * @param string $name Имя параметра в HTTP запросе
	 * @return void
	 */
	public function registerRequiredParameter($name)
	{
		$this->requiredParams[$name] = 1;
	}
	
	/**
	 * Регистрирует необязательный параметр
	 * @param string $name Имя параметра в HTTP запросе
	 * @return void
	 */
	public function registerOptionalParameter($name)
	{
		$this->optionalParams[$name] = 1;
	}
	
	/**
	 * Подготовка метода к выполнению
	 * Проверка необходимых параметров в HTTP запросе
	 */
	public function prepare()
	{
		$required = null;

		// проверяем переданы ли необходимые параметры
		// не их формат, а есть ли они вообще в запросе
		foreach ($this->requiredParams as $name => $k)
		{	
			if(!isset($_REQUEST[$name]))
				$required[] = $name;
		}

		if (count($required) > 0)
			throw new APIException(APIHandler::API_MISSED_REQUIRED_PARAMETERS_ERROR, "Missed required parameters: " . join(", ", $required));
	}
	
	/**
	 * Удаляет из набора данных указанные атрибуты
	 * 
	 * @param $object Объект или массив 'ключ -> значение'
	 * @param $attributes Массив с именами полей, которые надо исключить из объекта
	 */
	public function stripAttributes($object, $attributes)
	{
		if ($object == null)
			return null;
	
		if(is_object($object))
		{
			// видимо какой то глюк с передачей по ссылке, поэтом клонируем объект
			$object = clone $object;
			foreach ($attributes as $attr) 
			{
				unset($object->$attr);
			}
		}
		if (is_array($object))
		{
			foreach ($attributes as $attr) 
			{
				unset($object[$attr]);
			}			
		}
		
		return $object;
	}
	
	/**
	 * Оставляет в наборе данных указанные атрибуты
	 * 
	 * @param $object Объект или массив 'ключ -> значение'
	 * @param $attributes Массив с именами полей, которые нужно оставить в объекте
	 */
	public function preserveAttributes($object, $attributes)
	{
		if ($object == null)
			return null;
		if(count($attributes) == 0)
			return $object;

		$builtInAttributes = array();
		
		if(is_object($object))
		{
			// видимо какой то глюк с передачей по ссылке, поэтом клонируем объект
			$tmp = clone $object;
			$builtInAttributes = array_keys(get_object_vars($tmp));
		}
		if (is_array($object))
			$builtInAttributes = array_keys($object);

		$diff = array_diff( $builtInAttributes, $attributes);

		if(is_object($object))
		{
			foreach ($diff as $attr)
			{
				unset($tmp->$attr);
			}			
			return $tmp;
		}
		
		if (is_array($object))
		{
			foreach ($diff as $attr) 
			{
				unset($object[$attr]);
			}
			return $object;			
		}
	}
}
?>