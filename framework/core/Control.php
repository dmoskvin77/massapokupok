<?php 
/**
 * Класс для подготовки данных для отображения
 * View - в триаде MVC
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

abstract class Control implements IControl
{
	/**
	 * Список дочерних контролов
	 * 
	 * @var array
	 */
	public $controls = array();
	
	/**
	 * Файл общего шаблона страницы
	 * 
	 * @var string
	 */
	public $layout = null;	
	
	/**
	 * Файл шаблона текущего контрола
	 * 
	 * @var string
	 */
	public $template = null;
	
	/**
	 * каталог, в котором находится файл шаблона текущего контрола 
	 * (относительно каталога для шаблонов по умолчанию)
	 * 
	 * @var string
	 */
	public $folder = null;

	/**
	 * Данные шаблона
	 * 
	 * @var mixed
	 */
	protected $data = null;
	
	/**
	 * В этом методе выполняется
	 * получение данных для шаблона и их установка в шаблон
	 * 
	 * @return void
	 */
	public abstract function render();
	
	/**
	 * Выполняется перед заполнением шаблона, т.е. перед вызовом render()
	 * 
	 * @return void
	 */
	public function preRender()
	{
	}
	
	/**
	 * Выполняется после заполнения шаблона, т.е. после вызова render()
	 * 
	 * @return void
	 */
	public function postRender()
	{	
	}
	
	/**
	 * Добавляет дочерний контрол и производит его разбор
	 * 
	 * @param Control $control Экземпляр класса Control, добавляемый в текущий
	 * контрол 
	 * 
	 * @return void
	 * */
	public function addControl(Control $control)
	{
		$control->preRender();
		$control->render();
		$control->postRender();
		
		$name = get_class($control);
		
		// в коллекцию контролов
		$this->controls[$name] = $control;
		$data = $control->getData();
		
		// данные в общий коллектор. Доступны в массиве по имени класса в качестве ключа
		$this->data[$name] = $data[$name];		
	}
	
	/**
	* Возвращает данные контрола
	* 
	* @return mixed
	*/
	public function getData()
	{
		return $this->data;
	}

	/**
	* Добавляет данные в контроллер
	* 
	* @param string $name Имя этого объекта, по которому он будет доступен в шаблоне
	* @param mixed $object Объект или другие данные
	* 
	* @return void
	*/
	public function addData($name, $object)
	{
		$this->data[get_class($this)][$name] = $object;
	}
}
?>