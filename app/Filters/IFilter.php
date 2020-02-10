<?php
/**
 * Интерфейс для классов, реализующих проверку входных данных
 */
interface IFIlter
{	
	public function isValid();
	public function getMessage();
	public function getValue();	
}
?>