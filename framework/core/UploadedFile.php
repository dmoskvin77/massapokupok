<?php 
/**
 * Реализует функционал загрузки файла на сервер
 */

class UploadedFile 
{
	/**
	 * Наименование поля в форме
	 * 
	 * @var string
	 */
	private $fieldName = null;
	
	/**
	 * Массив данных, полученных из браузера
	 * 
	 * @var array
	 */
	private $fileData = null;
	
	/**
	 * Флаг ошибки
	 * 
	 * @var boolean
	 */
	public $isError = false;
	
	/**
	 * Размер файла
	 * 
	 * @var integer
	 */
	public $size = 0;
	
	/**
	 * MIME тип файла
	 * 
	 * @var string
	 */
	public $type = "";
	
	/**
	 * Имя файла
	 * 
	 * @var string
	 */
	public $name = "";
	
	/**
	 * Код ошибки
	 * 
	 * @var integer
	 */
	public $errorCode = UPLOAD_ERR_OK;
	
	/**
	 * Расширение файла
	 * 
	 * @var string
	 */
	public  $extension = "";
	
	/**
	 * Временное имя загруженного файла
	 * 
	 * @var string
	 */
	private $tmpName = null;
	
	/**
	 * Содержимое файла
	 * 
	 * @var string
	 */
	private $content = null;
	
	/**
	 * Признак того, что файл пустой
	 * 
	 * @var int
	 */
	const UPLOAD_NO_FILE = UPLOAD_ERR_NO_FILE;
	
	/**
	 * Файл загружен частично
	 * 
	 * @var int
	 */
	const UPLOAD_PARTIAL = UPLOAD_ERR_PARTIAL;
	
	/**
	 * Конструктор
	 * 
	 * @param string $fieldName Имя поля в форме (атрибут name) 
	 * @param bool $loadFromArray 
	 * 				Значение FALSE : Параметр $fieldName - имя поля в форме
	 * 				Значение TRUE : Параметр $fieldName - массив с данными о загруженном файле (структура как в $_FILES)
	 * 
	 * @return void
	 * */
	public function UploadedFile($fieldName, $loadFromArray = false, $type = "image")
	{
		if ($loadFromArray === false)
		{
			$this->fieldName = $fieldName;
			if (isset($_FILES[$this->fieldName]))
				$this->fileData = $_FILES[$this->fieldName];
			else 
				throw new Exception("Undefined field name '{$fieldName}'");
		}
		else 
		{
			if (!is_array($fieldName))
				throw new Exception("parameter fieldName must be an array like \$_FILES");
			
			$this->fileData = $fieldName;
		}
		
		$this->errorCode = $this->fileData['error'];
		$this->name = $this->fileData['name'];
		$this->type = $this->fileData['type'];
		$this->size = $this->fileData['size'];
		$this->tmpName = $this->fileData['tmp_name'];
		$this->isError = (bool)$this->errorCode;

		if ($this->size == 0)
		{
			$this->isError = true;
			$this->errorCode = UPLOAD_ERR_NO_FILE;
		}

		// $this->name не должно содержать расширений php, sh и прочих исполняемых файлов и скриптов
		if (strpos(strtolower($this->type), 'shell') !== false || strpos(strtolower($this->type), 'cgi') !== false || strpos(strtolower($this->type), 'php') !== false || strpos(strtolower($this->name), '.php') !== false || strpos(strtolower($this->name), '.sh') !== false || strpos(strtolower($this->name), '.html') !== false || strpos(strtolower($this->name), '.phtml') !== false || strpos(strtolower($this->name), '.php') !== false || strpos(strtolower($this->name), '.pl') !== false)
		{
			$this->isError = true;
			$this->errorCode = UPLOAD_ERR_EXTENSION;
		}

		if ($type == "image")
		{
			// должно содержать одно из расширений png, bmp, gif, jpg, jpeg
			if (strpos(strtolower($this->name), '.png') === false && strpos(strtolower($this->name), '.bmp') === false && strpos(strtolower($this->name), '.gif') === false && strpos(strtolower($this->name), '.jpg') === false && strpos(strtolower($this->name), '.jpeg') === false)
			{
				$this->isError = true;
				$this->errorCode = UPLOAD_ERR_EXTENSION;
			}

			if ($this->type != 'image/png' && $this->type != 'image/bmp' && $this->type != 'image/gif' && $this->type != 'image/jpg' && $this->type != 'image/jpeg')
			{
				$this->isError = true;
				$this->errorCode = UPLOAD_ERR_EXTENSION;
			}
		}

		if ($this->isError)
		{
			switch ($this->errorCode)
			{
				case UPLOAD_ERR_OK: //no error; possible file attack!
					break;
				case UPLOAD_ERR_INI_SIZE : //uploaded file exceeds the upload_max_filesize directive in php.ini
						throw new Exception ( "The file you are trying to upload is too big", $this->errorCode);
					break;
				case UPLOAD_ERR_PARTIAL: //uploaded file was only partially uploaded
						throw new Exception( "The file you are trying upload was only partially uploaded.", $this->errorCode);
					break;
				case UPLOAD_ERR_NO_FILE: //no file was uploaded						
						throw new Exception( "No file was uploaded.", $this->errorCode);
					break;
				default: //a default error, just in case!  :)
						throw new Exception( "There was a problem during upload.", $this->errorCode);
					break;
			}			
		}
		
		if (is_uploaded_file($this->tmpName))
		{
			$fp = fopen($this->tmpName, "rb");
			$this->content = fread($fp, filesize($this->tmpName));
			fclose($fp);	
		}
		
		// определим расширение
		$match = null;
		$pattern = "/([a-zA-Z0-9_]+)$/i";
		preg_match_all($pattern, $this->name, $match);
		$this->extension = $match[0][0];

	}
	
	/**
	* Возвращает содержимое файла
	* 
	* @return string
	*/
	public function getContent()
	{
		return $this->content;
	}
	
	/**
	* Флаг ошибки при загрузке файла
	* 
	* @return bool
	*/
	public function isError()
	{
		return $this->isError;
	}
	
	/**
	* Переименование файла
	* 
	* @param string $newName Новое имя
	* 
	* @return void
	*/
	public function rename($newName)
	{
		$this->name = $newName;
	}
	
	/**
	* Перемещение загруженного файла
	* 
	* @param string $path Путь к каталогу
	* 
	* @return bool
	*/
	public function saveTo($path)
	{
		return move_uploaded_file($this->tmpName, $path . $this->name);
	}
	
	/**
	* Конвертирует файл в base64 строку
	* 
	* @return string
	*/
	public function encode()
	{
		return base64_encode($this->content);
	}
	
	/**
	* Раскодирует base64 строку
	* 
	* @param string $string base64 строка 
	* 
	* @return string
	*/
	public static function decode($string)
	{
		return base64_decode($string);
	}
	
	/**
	 * Возвращает имя временного файла
	 * 
	 * @return string
	 */
	public function getTempName()
	{
		return $this->tmpName;
	}
}
?>