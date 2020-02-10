<?php

require_once "field.php";

class Table
{
	var $Name = "";
	var $Fields = array();
	var $_oConn = null;
	
	function Table($conn, $name)
	{
		$this->_oConn = $conn;
		$this->Name = $name;
	}
	
	function Process()
	{
		$sql = "DESCRIBE $this->Name";
		$result = $this->_oConn->getRows($sql); 
		foreach ($result as $row) 
		{
			$this->Fields[] = $this->_getFieldFromRow($row);
		}
	}
	
	function _getFieldFromRow($row)
	{
		$field = new Field();
		$field->Field = $row["Field"];
		$field->Type = preg_replace("/\(.*\)/", "", $row["Type"]);
		$field->Null = $row["Null"];
		$field->Key = $row["Key"];
		$field->Default = $row["Default"];
		$field->Extra = $row["Extra"];
		return $field;
	}
}

?>