<?php
/**
* отрисовывает компонент, вызванный из другого компонента
* 
* можно передавать параметры, следующие за именем компонента
* параматры должны идти точно в том порядке, в котором они указаны в классе компонента
* пример: {component name=randomclassmates userId=$SchoolsControl.userId schoolId=$SchoolsControl.schoolId}
* в этом случае в классе компонента нужно добавить конструктор, принимающий параметры
* 
*/
function smarty_function_component($params, &$smarty)
{
	if(!array_key_exists('name', $params))
		return "{component}: Не задано имя компонента";

	$controlName = ucfirst($params['name']) . 'Control';
	unset($params['name']);

	// если нужно больше 8-ми параметров, добавьте
	list($par1, $par2, $par3, $par4, $par5, $par6, $par7, $par8) = array_values($params);
	$control = new $controlName($par1, $par2, $par3, $par4, $par5, $par6, $par7, $par8);

	$ui = UIGenerator::getInstance();
	$ui->forceViewComponent = true;
	$ui->renderControl($control);
	return $ui->display();
}
?>