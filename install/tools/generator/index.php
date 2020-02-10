<?php

error_reporting (1+2+4+8);

require_once "config.php";
require_once "table.php";

try 
{
	$oConn = Application::getConnection(DATABASE);
	
	$table = new Table($oConn, CURRENT_TABLE);
	$table->Process();
}
catch (Exception $e)
{
	echo $e->getMessage();
	die();
}

$tplFile = "../install/tools/generator/template.php";
$f = file($tplFile);
$template = "";

for($i=0; $i<count($f); $i++)
	$template .= $f[$i];


$variables = "";
$identificator = "";
$variables .= "public $" ."entityTable = '" . CURRENT_TABLE . "';\n\n";
$getFields = "";

for ($i=0; $i< count($table->Fields); $i++)
{
	$def = $table->Fields[$i]->Default == null ? "null" : $table->Fields[$i]->Default;
	if ("CURRENT_TIMESTAMP" == $def)
		$def = "null";

	
	if ($table->Fields[$i]->Key === "PRI")
	{
		//$identificator .= "\$this->". $table->Fields[$i]->Field;
		$variables .= "\tpublic $"."primaryKey = '{$table->Fields[$i]->Field}'" .";\n\n";
	}
	
	if (!($table->Fields[$i]->Field == "id" || $table->Fields[$i]->Field == "entityStatus"))
		$variables .= "\tpublic $" . $table->Fields[$i]->Field . " = ". $def .";\n\n";

	$getFields .= "\t\t\t'{$table->Fields[$i]->Field}' => " . recognizeType( $table->Fields[$i]->Type ). ",\n";
	
//	$getters .= "function get" . ucfirst($table->Fields[$i]->Field) . '($val)';
//	$getters .= "\n{\n";
//	$getters .= "\t return \$this->" . $table->Fields[$i]->Field . ';';
//	$getters .= "\n}\n\n";
//	
//	$setters .= "function set" . ucfirst($table->Fields[$i]->Field) . '($val)';
//	$setters .= "\n{\n";
//	$setters .= "\t\$this->" . $table->Fields[$i]->Field . ' = $val;';
//	$setters .= "\n}\n\n";
	
	// метод для получения сущности из записи в бд
	//$fromRowMethod .= "\t\$entity->set" . ucfirst($table->Fields[$i]->Field) ."(\$row['". $table->Fields[$i]->Field ."']);\n";
}

// имя класса (и конструктора)
$template = str_replace("[CLASSNAME]", CLASS_NAME ,$template);
// уникальный идентификатор
$template = str_replace("[IDENTIFICATOR]", $identificator ,$template);
// переменные класса
$template = str_replace("[CLASSVARIABLES]", $variables ,$template);
// getters
//$template = str_replace("[GETTER]", $getters ,$template);
// setters
//$template = str_replace("[SETTER]", $setters ,$template);
//метод getFields
$template = str_replace("[GET_FIELDS]", $getFields ,$template);

///echo CLASS_NAME;
echo  $template;



$fp = fopen(Configurator::get("framework:directory.entity") . "/" . CLASS_NAME . ".php", "w");
fwrite($fp, $template);
fclose($fp);

function recognizeType($type)
{
    switch ($type)
    {
        case "int":
        case "tinyint":
        case "bit":
        case "float":
            return "self::ENTITY_FIELD_INT";
            break;        
        case "char":
            return "self::ENTITY_FIELD_STRING";
            break;
        case "date":
            return "self::ENTITY_FIELD_DATETIME";
            break;
        case "varchar":
        case "text":
        case "tinytext":
            return "self::ENTITY_FIELD_STRING";
            break;
        case "timestamp":
        	return "self::ENTITY_FIELD_TIMESTAMP";
        case "datetime":
        	return "self::ENTITY_FIELD_DATETIME"; 
     	
        default:
            return $type;
    }
}
?>