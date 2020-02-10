<?php
/**
 * Реализация Mutex (одновременный доступ только одного потока к ресурсу)
 *
 * @example 
 * $m = new Mutex("test", "/tempdir");
 *	
 *	if ($m->isAcquired())
 *		die("no");
 *	else 
 *		echo "ok";
 *		
 *	$m->lock();
 *	
 *	sleep(5);
 *	$m->release();
 * 
 */

class Mutex
{
	private $name = null;
	private $dir = null;
	private $lockFile = null;
	private $isWindows = false;
	private $processControl = true;
	
	/**
	 * Конструктор 
	 *
	 * @param string $name Имя lock-файла
	 * @param string $directory Путь к каталогу для Lock-файлов, 
	 * 				если не указан - будет установлен временный каталог ОС
	 * @param bool $processControl Влючение\отключение режима контроля идентификаторов процесса.
	 * 				если он включен, то проверяется, существует ли процесс, который залочил ресурс.
	 * 				Если не существует, то лок-файл удаляется и ресурс разблокируется 
	 */
	public function __construct($name, $directory = null, $processControl = true)
	{
		$this->processControl = $processControl;
		
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			$this->isWindows = true;
	
		// установка каталога для lock-файлов
		$this->setDirectory($directory);
		
		$unique = "";
		// если скрипт выполняется по HTTP
		// добавляем уникальную строку
		if(isset($_SERVER['HTTP_HOST']))
			$unique = $_SERVER['HTTP_HOST'] ."_";
		
		// иначе считаем, что скрипт выполняется из консоли (напр. по крону)	
			
		$this->name = $unique . $name . ".lock";	
		$this->lockFile = $this->dir . DIRECTORY_SEPARATOR . $this->name;
	}
	
	/**
	 * Установка каталога, в котором будут создаваться lock-файлы
	 *
	 * @param string $directory
	 */
	private function setDirectory($directory = null)
	{
		// каталог не указан
		if($directory == null)
		{
			if($this->isWindows)
			{
				$this->dir = getenv("TEMP");
				if(!$this->dir)
					throw new Exception("Temp directory does not exists, please define environment variable TEMP");				
			}
			else 
			{
				$this->dir = "/tmp";
			}
		}
		else // каталог указан
		{
			$this->dir = $directory;
		}

		// проконтролируем правый слеш
		$this->dir = rtrim($this->dir, '\/');
		$this->dir = $this->dir . DIRECTORY_SEPARATOR;
		
		// можем ли записывать в каталог?
		if(!is_writable($this->dir))
			throw new Exception("Can't write to {$this->dir}");
	}
	
	/**
	 * Проверяет, занят ли ресурс другим процессом
	 *
	 * @return bool True - Ресурс занят другим процессом, False - ресурс не занят
	 */
	public function isAcquired()
	{
		if (file_exists($this->lockFile))
		{
			// если включен контроль PID
			if($this->processControl)
			{
				$pid = file_get_contents($this->lockFile);				
				$check = $this->checkProcessId($pid);
				if($check)
				{
					// файл есть, процесс существует. Значит ресурс занят
					return true;
				}
				else 
				{
					// процесса, занявшего ресурс не существует
					// освобождаем ресурс
					$this->release();
					return false;
				}
			}
			else 
			{
				// достаточно проверки существования лок-файла
				return true;
			}

		}
		else
		{
			// лок-файла не существует - ресурс не занят
			return false;
		}
	}
	
	/**
	 * проверяем, существует ли процесс с таким PID
	 *
	 * @param int $processId
	 * @return bool
	 */
	private function checkProcessId($processId)
	{
		$pid = null;
		if ($this->isWindows)
			$pid = shell_exec("wmic process get processId | findstr \"\<{$processId}\>\"");
		else
			$pid = shell_exec("ps -p {$processId} | grep '{$processId}' | sed 's/[^0-9]/ /g' | awk '{print $1}'");
		
		// процесс не найден
		if ($pid === null)
			return false;
			
		$pid = trim($pid);
		// если что то найдено:
		// PID должен иметь тип INT. Иначе считаем, что произошла ошибка
		$pid = filter_var($pid, FILTER_VALIDATE_INT);
		if($pid === false)
			throw new Exception("Method checkProcessId: {$pid}");
		
		return true;		
	}
	
	/**
	 * Закрывает доступ к ресурсу
	 *
	 */
	public function lock()
	{
		// cоздание файл для записи, если он не существует
		$res = @fopen($this->lockFile, "x");
		if ($res === false)
			throw new Exception("Can't create lock file {$this->lockFile}");

		fwrite($res, getmypid());
		fclose($res);
		
		chmod($this->lockFile, 0666);
	}
	
	/**
	 * Освобождает ресурс
	 *
	 */
	public function release()
	{
		$res = @unlink($this->lockFile);
		if($res === false)
			throw new Exception("Can't delete lock file {$this->lockFile}");
	}
}

?>