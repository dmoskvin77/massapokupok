<?php 
header("Content-Type: text/html; charset=utf-8");
set_time_limit(0);
ini_set("memory_limit", "512M");
require_once "../app/Config/framework.php";

echo "<h1>Installation</h1>";
echo "<a href='install.php'>Install Page</a><br>";

$task = @$_GET["task"];

//$config = Configurator::getCurrentMode();

switch ($task)
{
	case "build":
		build();
		break;
	case "sql":
		createDB();
		break;
	case "cs":
		checkCodingStandard();
	default:
		using();
}

function build()
{
	// проверим валидность version.xml
	$ver = @simplexml_load_file(DOCUMENT_ROOT . "/version.xml");

	echo "<h1>Check version.xml</h1>";
	if(!$ver)
	{
		die("incorrect version.xml");
	}
	else
	{
		if (!isset($ver->entry["revision"]))
			die("Undefined 'revision' attribute");
		else
			echo "version.xml ok";
	}
	
	// Сборка приложения
	echo "<h1>Build repository</h1>";	
		
	require_once FRAMEWORK_ROOT_DIR . '/tools/Installer.php';

	// Указываются каталоги относительно APPLICATION_DIR
	$include = array
	(
		// включим файлы фреймворка
		FRAMEWORK_ROOT_DIR . "/core",
		
		// фильтры входных данных
		APPLICATION_DIR . "/Filters",
		
		// Файлы UI и бизнес-логики
		Configurator::get("framework:directory.controls"),
		Configurator::get("framework:directory.actions"),
		Configurator::get("framework:directory.managers"),
		Configurator::get("framework:directory.entity"),
		
		// каталог с библиотеками
		APPLICATION_DIR . "/Lib"
	);

	$exclude = array
	(
		// классы библиотек не подключаем
		APPLICATION_DIR . "/Lib/Swift",
		APPLICATION_DIR . "/Lib/kcaptcha",
		APPLICATION_DIR . "/Lib/Exceptionizer",
		APPLICATION_DIR . "/Lib/IntrusionDetector",
		APPLICATION_DIR . "/Lib/num2str",
		APPLICATION_DIR . "/Lib/PhpQuery"
	);
	
	$installer = new Installer();
	
	// в какой файл пишем репозитарий
	$repFile = Configurator::get("framework:file.repository");
	$repository = $installer->createRepository($include, $exclude, $repFile);
	
	return $repository;
	
}

function createDB()
{
	
	$scheme = "scheme.sql";
	$data = "data.sql";
//	$triggers = "triggers.sql";
	
	$conn = Application::getConnection("master");
	
	echo "<h1>Create database scheme</h1>";
	echo "Opening $scheme<br>";	
	restoreDB($scheme, $conn);	
	echo "<h3>Completed!</h3>";
	
	echo "<h1>Create init data</h1>";
	echo "Opening $data<br>";	
	restoreDB($data, $conn);
	echo "<h3>Completed!</h3>";
	
//	echo "<h1>Create triggers</h1>";
//	echo "Opening $triggers<br>";	
//	executeTrigger($triggers, $conn);
//	echo "<h3>Completed!</h3>";	
}

function restoreDB($file, $connection)
{
	try 
	{
		$command = "";
		if (!file_exists($file))
			die("File does not exists : {$file}");
		$fp = fopen($file, "rb");
		while (!feof($fp)) 
		{
			$c = chop(fgets($fp, 1500000));
			// remove comments
			$c = preg_replace("/^[\s\t]*--.*/", "", $c);
			$c = preg_replace("/^[\s\t]*#.*/", "", $c);
	
			$command .= $c;

			if (preg_match("/;$/", $command))
			{
				$command = preg_replace("/;$/", "", $command);
	
				if (preg_match("/CREATE TABLE /i", $command) || preg_match("/DROP TABLE /i", $command))
				{
					if (preg_match("/CREATE/i", $command))
					{
						$table_name = preg_replace("/\s.*$/", "", preg_replace("/^.*CREATE TABLE /i", "", $command));
						echo "Creating table: [$table_name] ... "; flush();
					} 
					elseif (preg_match("/DROP/i", $command))
					{
						$table_name = preg_replace("/\s.*$/", "", preg_replace("/^.*DROP TABLE IF EXISTS /i", "", $command));
						echo "Dropping table: [$table_name] ... "; flush();
					}

					$connection->executeNonQuery($command);
					echo "[OK]<br>";
					
				} 
				else 
				{
					$connection->executeNonQuery($command);
					$limit = 150;
					$limitedCommand = $command = htmlspecialchars($command);
					if (mb_strlen($command) > $limit)
						$limitedCommand = mb_substr($command, 0, $limit) . '...';
					echo "<sub>Executed command: <div style=\"display: inline;\" title=\"{$command}\">{$limitedCommand}</div></sub></br>";
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

function executeTrigger($file, $connection)
{
	try 
	{
		$f = fopen($file, "r");
		$command = fread($f, filesize($file));
		fclose($f);

		$command = str_replace("\r\n", " ", $command);
		$command = preg_replace("/\/[^\/]*\//", "", $command);
		
		$command = str_replace("create", "CREATE", $command);
		$split = explode("CREATE", $command);
		
		foreach($split as $trigger)
		{
			$command = trim($trigger); 
			if(!trim($command))
				continue;
			
			$command = "CREATE " . $command;

			$split = explode(" ", $command);
			$type = $split[1];
			$name = $split[2];
			$name = explode("(", $name);
			$name = $name[0];
			
			if(strtoupper($type) == "TRIGGER")
			{
				$ifexist = "SELECT * FROM information_schema.TRIGGERS WHERE TRIGGER_NAME = '$name';";
				$res = $connection->getOneRow($ifexist);
				echo "Dropping trigger: [$name] ... "; flush();
				if($res)
					$connection->executeNonQuery("DROP TRIGGER $name;"); 
				echo "[OK]<br>"; flush();
				
				echo "Creating trigger: [$name] ... "; flush();
				$connection->executeNonQuery($command);
				echo "[OK]<br>"; flush();
			}

			if(strtoupper($type) == "PROCEDURE")
			{
				echo "Dropping procedure: [$name] ... "; flush();
				$connection->executeNonQuery("DROP PROCEDURE IF EXISTS $name;"); 
				echo "[OK]<br>"; flush();
				
				echo "Creating procedure: [$name] ... "; flush();
				$connection->executeNonQuery($command);
				echo "[OK]<br>"; flush();
			}
		}
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
		exit();
	}
}

function using()
{
	$using = <<<EOT
	<ul>
	Tasks :
		<li><a href='install.php?task=build'>Build</a></li>
		<li><a href='install.php?task=sql'>Create  database</a></li>	
		
	</ul>
EOT;

	echo $using;

	$pass = "28121987!@";
	echo 'org: '.sha1(md5($pass).sha1($pass)).'<br><br>';
	echo 'user: '.md5(md5($pass).sha1($pass));

}


?>
