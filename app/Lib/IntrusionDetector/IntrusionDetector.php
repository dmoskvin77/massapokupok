<?php
/**
 * Класс для сканирования входящих параметров для обнаружения
 * XSS, SQL-инъекций, инъекций заголовков, обхода каталогов, RFE/LFI, DoS и LDAP атак
 */
class IntrusionDetector
{
	/**
	 * Список настроек
	 *
	 * @var array
	 */
	private $options = null;
	
	/**
	 * Разрешено ли действие детектора
	 *
	 * @var bool
	 */
	private $isEnabled = false;
	
	/**
	 * Список правил детектора
	 *
	 * @var array
	 */
	private $rules = null;
	
	/**
	 * прекращать проверку при первом же совпадении
	 *
	 * @var bool
	 */
	private $stopOnFirst = false;
	
	/**
	 * Результаты сканирования
	 * Если null - то данные не опасны
	 *
	 * @var array
	 */
	private $result = null;
	
	/**
	 * Список стоп-слов
	 * Это строки, которые вызывают ложное срабатывание фильтра
	 * Фильтр можно обучать, добавляя стоп-слова в XML файл с правилами
	 * в раздел <stopList>
	 *
	 * @var array
	 */
	private $stopList = null;
	
	/**
	 * Конструктор
	 *
	 * @param array $options
	 * enabled - true\false - Включено\Выключено
	 * rules - Путь у XML файлу с правилами
	 * callback - Имя метода для обработки события срабатывания правила
	 * stopOnFirstOccurence - true\false прекращать проверку при первом же совпадении
	 */
	public function __construct(array $options)
	{
		$this->options = $options;
		$this->isEnabled = $this->getOption("enabled");
		
		if(!$this->isEnabled)
			return false;
		
		$this->stopOnFirst = $this->getOption("stopOnFirstOccurence");
		$this->parseRulesFile($this->getOption("rules"));
		
		// запускаем  сканирование
		$this->run();
		
		if(count($this->result) > 0)
		{
			call_user_func($this->getOption("callback"), $this->result);
		}
	}
	
	/**
	 * Запуск детектора
	 *
	 * @param mixed $request
	 */
	private function run($request = null)
	{		
		if($request == null)
			$request = $_REQUEST;
		
		// берем данные из $_REQUEST
		foreach ($request as $k => $v)
		{
			if(is_array($v))
			{
				$this->run($v);
			}
			else 
			{
				$isDetect = $this->detect($v);
				
				if($isDetect && $this->stopOnFirst)
					break;
			}
		}
	}
	
	/**
	 * Прогон значения по всем указанным правилам
	 *
	 * @param mixed $value
	 * @return bool true - есть совпадения с правилами, иначе - false
	 */
	private function detect($value)
	{
		// если значение числовое - пропускаем для ускорения
		if(filter_var($value, FILTER_VALIDATE_INT))
			return false;
			
		$originalValue = $value;
		
		// проверяем стоп-слова
		foreach ($this->stopList as $stop)
		{
			// нашли стоп-слово в значении
			// то вырезаем его из тестируемой строки
			if(strpos($value, $stop) >= 0)
				$value = str_replace($stop, "", $value);
		}
			
		foreach ($this->rules as $item)
		{
			$res = @preg_match("/{$item["rule"]}/", $value);
			if($res === false)
				throw new Exception($item["rule"]);
			
			if($res !== 0)
			{
				// сработало правило
				$out["value"] = $originalValue;
				$out["info"] = $item["info"];
				$this->result[] = $out;
				
				if($this->stopOnFirst)
					break;
			}
		}
		
		// есть совпадения в шаблонах
		if( count($this->result) > 0 )
			return true;
	}
	

	/**
	 * Формирует список правил в виде массива
	 * 
	 * Формат файла:
	 * 	<filters>
	 *		<filter>
	 *			<rule><![CDATA[(?:"+.*[^-]?>)|(?:[^\w\s]\s*\/>)|(?:>")]]></rule>
	 *			<info>finds html breaking injections including whitespace attacks</info>
	 *		</filter>
	 *	<filters/>
	 *
	 * @param string $path Путь до XML файла
	 */
	private function parseRulesFile($path)
	{
		if(!file_exists($path))
			throw new Exception("can't open file {$path}");
			
		$content = simplexml_load_file($path, "SimpleXMLElement", LIBXML_NOCDATA);
		foreach ($content->filters->filter as $item)
		{
			$rule = array();
			$rule['rule'] = trim($item->rule);
			$rule['info'] = trim($item->info);
			$this->rules[] = $rule;
		}
		$this->parseStopList($content);
	}
	
	/**
	 * Формирует список стоп-слов
	 *
	 * @param object $xml
	 */
	private function parseStopList($xml)
	{
		foreach ($xml->stopList->item as $item)
		{
			$this->stopList[] = $item;
		}		
	}
	
	/**
	 * Возвращает значение настройки
	 *
	 * @param string $name
	 * @return mixed
	 */
	private function getOption($name)
	{
		if(!isset($this->options[$name]))
			throw new Exception("Option {$name} must be defined");
		return $this->options[$name];
	}

}
?>