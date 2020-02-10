<?php 
/**
 * Класс предоставляет методы для установки данных в шаблон
 * и отрисовки шаблона
 * 
 * Использует в себе Configurator и Smarty
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Core
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */


require_once FRAMEWORK_ROOT_DIR . "/lib/smarty/Smarty.class.php";

class View 
{
	/**
	 * Экземпляр объекта Smarty
	 * 
	 * @var Smarty
	 */
	protected $smarty = null;
	
	/**
	 * Имя файла, содержащего шаблон
	 * 
	 * @var string
	 */
	protected $template = null;

	
	/**
	 * Конструктор
	 * 
	 * @param string $template Путь к файлу с шаблоном
	 * 
	 * @return void
	 */
	public function View($template) 
	{
		$this->template = $template;		
		$this->smarty = new Smarty();

		//
		// Конфигурация Smarty
		//
		
		// template_dir указываем , чтобы система безопасности Smarty
		// позволяла читать файлы
		$this->smarty->template_dir = Configurator::get("framework:directory.templates");
		$this->smarty->compile_dir = Configurator::get("smarty:compile.dir");
		$this->smarty->config_dir = Configurator::get("smarty:config.dir");
		$this->smarty->cache_dir = Configurator::get("smarty:cache.dir");
		$this->smarty->compile_check = Configurator::get("smarty:compile.check");
		$this->smarty->debugging = Configurator::get("smarty:debugging");
		$this->smarty->error_reporting = Configurator::get("smarty:error.reporting");

		
		//Загрузка пользовательских плагинов и функций
		$this->smarty->plugins_dir[] = Configurator::get("smarty:user.plugins");
		
		// Настройки безопасного режима 
		if (Configurator::get("smarty:security"))
		{
			// Включение безопасного режима 
			$this->smarty->security = true;
	
			// объединение массивов разрешенных PHP функций, разрешенных к использованию в условиях IF.
			$this->smarty->security_settings["IF_FUNCS"] = array_merge(
				$this->smarty->security_settings["IF_FUNCS"], 
				Configurator::getArray("smarty:IF_FUNCS")
			);
			
			// объединение массивов разрешенных PHP функций, разрешенных к использованию в качестве модификаторов переменных.
			$this->smarty->security_settings["MODIFIER_FUNCS"] = array_merge(
				$this->smarty->security_settings["MODIFIER_FUNCS"], 
				Configurator::getArray("smarty:MODIFIER_FUNCS")
			);
		}
	}
	
	/**
	* Выводит обработанный шаблон в браузер
	* 
	* @return void
	*/
	public function display()
	{
		if ($this->template)
			$this->smarty->display($this->template);
	}
	
	/**
	 * Добавляет в шаблон данные
	 * 
	 * @param string $name Имя переменной
	 * @param mixed $value Значение переменной
	 * 
	 * @return void
	 */
	public function assign($name, $value)
	{
		return $this->smarty->assign($name, $value);
	}

	/**
	 * Возвращает текст обработанного шаблона
	 * 
	 * @return string
	 */
	public function fetch()
	{
		return $this->smarty->fetch($this->template);
	}
}
?>