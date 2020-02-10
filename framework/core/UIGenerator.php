<?php 
/**
 * Анализ входящих параметров, построение контролов (Controls) и выполнение действий (Actions)
 * 
 * Имплементирует паттерн Singleton
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Core
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: UIGenerator.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

class UIGenerator
{
	/**
	 * Экземпляр тукущего контрола
	 * 
	 * @var Control
	 */
	public $currentControl = null;
	
	/**
	 * Имя контрола по умолчанию
	 * 
	 * @var string
	 */
	public $defaultControl = "index";
	
	/**
	 * Имя действия по умолчанию
	 * 
	 * @var string
	 */
	public $defaultAction = "";
	
	/**
	 * Расширение файлов, содержащих шаблоны
	 * 
	 * @var string
	 */
	public $templateExtension = ".html";
	
	/**
	 * Флаг принудительного показа контрола, даже если он имплементирует интерфейс IComponent
	 * 
	 * @var boolean
	 */
	public $forceViewComponent = false;
	
	/**
	 * Ссылка на текущий экземпляр UIGenerator
	 * 
	 * @var UIGenerator
	 */
	private static $instance = null;
	
	/**
	* Приватный конструктор
	* 
	* @return void
	*/
	private function UIGenerator()
	{
	}
	
	/**
	 * Нельзя создавать копии
	 * 
	 * @return void
	 */
	public function __clone()
	{
		throw new Exception("Can't clone singleton object ". __CLASS__);
	}
	
	/**
	* Возвращает экземпляр класса
	* 
	* @return UIGenerator
	*/
	public static function getInstance()
	{	
		if (!isset(self::$instance))
		{			
			self::$instance = new UIGenerator();
		}
		return self::$instance;
	}
	
	/**
	* Выполняет указанное действие или рендерит контрол
	* 
	* @param string $actionName Имя действия
	* @param string $controlName Имя Контрола
	* 
	* @return void
	*/
	public function exec($actionName = null, $controlName = null)
	{
		// Действие имеет приоритет перед контролом, т.е. выполняется первым, если существует
		// Действие, имплементирующее интерфейс IPrivateAction не выполняется напрямую при запросе из
		// браузера. Оно может выполняться только из кода.
		// В одном из методов preExecute, execute, postExecute после Действия должен быть выполнен редирект.
		
		$action = $this->recognizeAction($actionName);
		
		if ($action instanceof IPrivateAction)
		{
			if (Configurator::get("application:debug"))
				throw new Exception("Trying run Private Action : {$actionName}");

			Request::send404();
		}
			
		if (null != $action && !($action instanceof IPrivateAction))
		{
			$action->preExecute();
			$action->execute();
			$action->postExecute();
			// если действие имплементирует интерфейс IAjaxAction, то
			// редирект не нужен 
			if ( !($action instanceof IAjaxAction) )
				die("Action " . get_class($action) . " has been executed. Redirect?");
			else 
				exit();
		}
		else 
		{
			$control = $this->recognizeControl($controlName);
			$control->preRender();
			$control->render();
			$control->postRender();
		}
	}
	
	/**
	* Распознает текущее действие и, в случае успеха, 
	* возвращает соответствующий объект Action
	* 
	* @param string $action Имя действия
	* 
	* @return Action
	*/
	private function recognizeAction($action)
	{
		if ($action == null)
			return null;
			
		$class = ucfirst($action) . "Action";
		if (!class_exists($class, true))
		{
			if (Configurator::get("application:debug"))
				throw new Exception("Action {$class} undefined.");

			return null;
		}
		else
		{
			return new $class;
		}
	}
	
	/**
	* Распознает запрашиваемый контрол.
	* В случае успеха возвращает соответствующий объект Controls
	* 
	* @param string $control Имя Контрола
	* 
	* @return Control
	*/
	private function recognizeControl($control)
	{
		$givedControlName = mb_strtolower($control, 'utf8');

		if ($control == null)
			$control = $this->defaultControl;

		$class = ucfirst($control) . "Control";
		if (!class_exists($class, true))
		{
			/*
			 * какой-то свой обработчик в определенном контроллере
			*/
			$mappingArray = Request::getMappingArray();
			if (array_key_exists($givedControlName, $mappingArray))
			{
				$mappingParams = $mappingArray[$givedControlName];
				if (count($mappingParams) && isset($mappingParams['control']))
				{
					Context::setArray($mappingParams);
					if ($mappingParams['control'] == 'page')
					{
						$class = "PageControl";
						if (Configurator::autoload($class) === false)
							Request::send404();
					}
					else
						Request::send404();
				}
				else
					Request::send404();
			}
			else
				Request::send404();

		}

		return $this->currentControl = new $class;
	}
	
	/**
	 * Отрисовывает контрол
	 * Чтобы показать результат, нужно вызвать display()
	 * 
	 * @param Control $control Контрол для прямой отрисовки
	 * 
	 * @return void
	 */
	public function renderControl(IControl $control)
	{
		$this->currentControl = $control;
		$control->preRender();
		$control->render();
		$control->postRender();		
	}
	
	/**
	 * Возвращает имя контрола из строки запроса
	 * 
	 * @return string
	 * */
	public static function getControl()
	{
		return Request::getVar("show");
	}
	
	/**
	 * Возвращает имя действия из строки запроса
	 * 
	 * @return string
	 */
	public static function getAction()
	{
		return Request::getVar("do");
	}	
	

	/**
	* Возвращает путь к файлу общего шаблона страницы
	* 
	* @return string
	*/
	private function getLayout()
	{
		$layoutDir = Configurator::get("framework:directory.layouts");
		$file = $layoutDir . DIRECTORY_SEPARATOR . $this->currentControl->layout;
		return $file;
	}
	
	
	/**
	* Возвращает путь к файлу шаблона заданного контрола
	* 
	* @param Control $control Экземпляр контрола
	* 
	* @return string
	*/
	private function getControlTemplate(Control $control)
	{
		// если указан каталог с шаблоном для этого контрола
		$folder = "";
		if ($control->folder !== null)
			$folder = $control->folder . DIRECTORY_SEPARATOR ;

		$file = null;
		if ($control->template)
			$file = $folder . $control->template;
		else 
			$file = $folder . get_class($control) . $this->templateExtension;

		return Configurator::get("framework:directory.templates") . DIRECTORY_SEPARATOR . $file;
	}	
	
	/**
	* выводит обработанный шаблон в браузер
	* 
	* @return string
	*/
	public function display()
	{
		// проверяем, показывать ли контрол напрямую
		if (($this->currentControl instanceof IComponent) && !$this->forceViewComponent)
			Request::send404();

		$isAjax = false;
		if ($this->currentControl instanceof IAjaxControl)
			$isAjax = true;
		
		// данные контрола
		// $data = $this->currentControl->getData();
		
		$view = null;
		if ($isAjax || $this->forceViewComponent)
		{
			// если контрол имплементирует IAjaxControl, то будем показывать
			// только шаблон этого контрола (без макета)
			$template = $this->getControlTemplate($this->currentControl);
			$view = new View($template);
		}
		else 
		{
			// если контрол обычный - создаем макет
			$layout = $this->getLayout();
			$view = new View($layout);
		}
		
		// Заполняем шаблон данными
		$view = $this->assignToView($this->currentControl, $view);
		
		// Специальные переменные
		if (!$isAjax)
		{
			$view->assign("CURRENT_CONTROL_TEMPLATE", $this->getControlTemplate($this->currentControl));
		}
		else 
		{
			$view->display();
			exit();
		}
		
		// возвращаем содержимое
		$view->display();
	}
	
	/**
	 * Возвращает содержимое выполненного контрола
	 * 
	 * @param Control $control Экземпляр контрола
	 * 
	 * @return string
	 */
	public function fetch(IControl $control)
	{
		$control->preRender();
		$control->render();
		$control->postRender();
		
		$file = $this->getControlTemplate($control);
	
		$view = new View($file);
		$view = $this->assignToView($control, $view);
		
		return $view->fetch();
	}
	
	/**
	 * Заполняем шаблон данными
	 * 
	 * @param Control $control Экземпляр контрола
	 * @param View $view Экземпляр шаблонизатора
	 * 
	 * @return void
	 */
	protected function assignToView(Control $control, View $view)
	{
		$data = $control->getData();
		// Заполняем шаблон данными
		if (is_array($data))
		{
			foreach ($data as $key => $value) 
			{
				$view->assign($key, $value);
			}
		}
		else 
		{
			$view->assign(get_class($control), $data);
		}
		
		// помещаем в шаблон публичные переменные контроллера, 
		// они имеют глобальную область видимости
		$vars = get_object_vars($control);
		foreach ($vars as $key => $value)
		{
			$view->assign($key, $value);
		}

		return $view;
	}

}
?>
