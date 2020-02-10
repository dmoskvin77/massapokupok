<?php
/**
 * Вспомогательный Класс для инсталляции приложения
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Installer
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

class Installer
{
	/**
	 * Список игнорируемых каталогов. 
	 * Содержащиеся в них файлы не попадут в сборку
	 * 
	 * @var array
	 */
	public $ignoreDirs = array();
	
	/**
	 * Символ конца строки для отображения 
	 * в браузере или консоли
	 * 
	 * @var string
	 */
	private $eol = "<br/>";
	
	/**
	 * Признак выполнения скрипта в консоли
	 * 
	 * @var boolean
	 */
	private $isConsole = false;
	
	/**
	 * Конструктор
	 * 
	 * @return void
	 */
	public function __construct()
	{
		// удалим кэш состояния файлов
		clearstatcache();
		
		if (@$_SERVER['SESSIONNAME'] == "Console")
		{
			$this->eol = "\n";
			$this->isConsole = true;
		}
		else 
		{
			$this->eol = "<br/>";
		}
	}
	
	/**
	 * Создание БД
	 * 
	 * @param string $file Путь к файлу с SQL скриптом
	 * @param IDBAdapter &$connection Объект соединения к БД
	 * 
	 * @return void
	 */
	public function createDB($file, IDBAdapter &$connection)
	{
		try 
		{
		
			$command = "";
			$fp = fopen($file, "rb");
			while (!feof($fp)) 
			{
				$c = chop(fgets($fp, 1500000));
				// remove comments
				$c = preg_replace("/^[ \t]*--.*/", "", $c);
				$c = preg_replace("/^[ \t]*#.*/", "", $c);
		
				$command .= $c;
	
				if (preg_match("/;$/", $command)) 
				{
					$command = preg_replace("/;$/", "", $command);
		
					if (preg_match("/CREATE TABLE /i", $command) || preg_match("/DROP TABLE /i", $command)) 
					{
						if (preg_match("/CREATE/i", $command)) 
						{
							$table_name = preg_replace("/ .*$/", "", preg_replace("/^.*CREATE TABLE /i", "", $command));
							echo "Creating table: [$table_name] ... "; flush();
						} 
						else if (preg_match("/DROP/i", $command)) 
						{
							$table_name = preg_replace("/ .*$/", "", preg_replace("/^.*DROP TABLE IF EXISTS /i", "", $command));
							echo "Dropping table: [$table_name] ... "; flush();
						}
	
						$connection->executeNonQuery($command);
						echo "[OK]" . $this->eol;
						
					} 
					else 
					{
						$connection->executeNonQuery($command);
						if ( $this->isConsole)
							echo "<sub>Executed command : {$command}</sub>" . $this->eol;
						else 
							echo "Executed command : {$command}" . $this->eol;
					}
		
					$command = "";
					flush();
				}
					
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}	

	/**
	 * Рекурсивный обход директорий
	 * 
	 * @param string $directory Каталог с файлами
	 * @param array $ignore Список игнорируемых файлов и каталогов
	 * @param array $rawExclude Список игнорируемых файлов и каталогов без APPLICATION_DIR в начале пути
	 * @param array &$repository Список файлов, попавших в репозиторий
	 * 
	 * @return void
	 */
	private function recursive($directory, $ignore, $rawExclude, &$repository)
	{
		if (in_array($directory, $ignore))
		{
			//echo "ignore {$directory}\n\n";	
			return false;		
		}
		//return false;
			
		$dir = dir($directory);
		while ($entry = $dir->read())
		{		
			if ($entry == "." || $entry == ".." || $entry == ".svn"|| $entry[0] == ".")
				continue;
			$test = $dir->path .DIRECTORY_SEPARATOR. $entry;
			
			if (is_dir($test))
				$this->recursive($test, $ignore, $rawExclude, $repository);
			if (is_file($test))
			{
				// игнорируем файл, указанный в списке игноров				
				$ignoreItem = false;		
				foreach ($rawExclude as $item)
				{
					if (strstr($test, $item) !== false)
						$ignoreItem = true;
				}				
				if ($ignoreItem)
					continue;
				
				if (pathinfo($test, PATHINFO_EXTENSION) != "php")
					continue;
					
				$name = strtolower(substr($entry, 0, strlen($entry) - 4));
				if (array_key_exists($name, $repository))
					throw new Exception ("Class '$name' -> '$test' already exists in repository. Please, rename it.");
				$repository[$name] = $test;			
			}
		}
	}
	
	
	/**
	 * Пример использования
	 * 
	 * @return string
	 */
	public function using()
	{
		$using = null;
		
		if ($this->isConsole)
		{
			$using = "
		
			USING:
			> php -f install.php sql - for install database
			> php -f install.php build - for build repository
			";

		}
		else 
		{
			$using = "For using on server define links.";
		
		}
		return $using;
	}
	
	/**
	 * Печатает строку
	 * 
	 * @param string $string Строка для отображения
	 * 
	 * @return void
	 */
	private function showLine($string)
	{
		if ($this->isConsole)
			echo $string . "\n\n";
		else
			echo "<p>{$string}</p>";
	}
	
	/**
	 * Упорядочивает слеши в пути к файлу или каталогу
	 * убирает последний слеш
	 * убирает повторения слешей
	 * 
	 * @param string $path Путь к файлу
	 * 
	 * @return string
	 */
	private function normalizePath($path)
	{
		$search = array("\\", "/");		
		// замена всех слешей на разделитель каталогов, принятый в OS
		$path = str_replace($search, DIRECTORY_SEPARATOR, $path);
		
		// убираем последний слеш
		if (substr($path, strlen($path) - 1, 1) === DIRECTORY_SEPARATOR)
			$path = substr($path, 0, strlen($path) - 1);

		// убираем повторения слешей
		$path = preg_replace("/([\\\|\/]{1,})/", DIRECTORY_SEPARATOR, $path);
		return $path;
	}
	
	/**
	 * Creates the repository file
	 * Возвращает список файлов репозитария
	 * 
	 * @param array $include Additional folders to include
	 * @param array $exclude Excludes selected folders
	 * @param string $repoFile Path to repository file
	 * 
	 * @return array
	 */
	public function createRepository($include, $exclude, $repoFile)
	{
		$rawExclude = $exclude;
		
		// нормализовать пути включаемых каталогов
		for ($i = 0; $i < count($include); $i++)
		{
			$include[$i] = $this->normalizePath($include[$i]);
		}
				
		// добавляем путь к каталогу приложения
		for ($i = 0; $i < count($exclude); $i++)
		{
			$exclude[$i] = $this->normalizePath( $exclude[$i] );
			$rawExclude[$i] = $this->normalizePath($rawExclude[$i]);
		}
		
		$repository = array();
		try 
		{
			for ($i = 0; $i < count($include); $i++)
			{
				$this->recursive($include[$i], $exclude, $rawExclude, $repository);
			}
		}
		catch (Exception  $exc)
		{
			echo $exc->getMessage();
		}		
		
		// добавленные
		$this->showLine("Included dirs:");
		
		foreach ($include as $k => $v)
		{
			echo $v . $this->eol;
		}
		
		// игнорированы
		$this->showLine("Excluded dirs and files:");
		
		foreach ($exclude as $k => $v)
		{
			echo $v . $this->eol;
		}		
		
		$this->showLine("Files in repository: ");
		
		foreach ($repository as $k => $v)
		{
			echo "[{$k}] -> {$v}" . $this->eol;
		}
		

		$filename = $repoFile;
		$handle = fopen($filename, "wb");// || die("Can't open $filename");
		if (!$handle)
			die("Can't open $filename");
		if (fwrite($handle, serialize($repository)) === false)
			die("Can't write into $filename");
		fclose($handle);
		
		$this->showLine("Build repository into : " . $repoFile . $this->eol);
		$this->showLine("Build completed. Total files count : " . count($repository) . $this->eol);		
	
		return $repository;
	}
}
?>