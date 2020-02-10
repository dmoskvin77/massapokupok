//<?php
///**
// * Тег для отрисовки контрола с возможностью передачи
// * данных в его конструктор
// * 
// * !!!делаем допущение, что конструктор контрола не будет иметь больше 15-ти параметров!!!
// * 
// * Пример:
// * {rendercontrol name="Pager" curr=1 total=10 per=2 }
// * {rendercontrol name="ViewPage" num=10 blabla=10 abc=2 test='dddd' }
// * 
// * Параметр name - Обязательный
// * Имена остальных параметров произвольные
// * Число параметров ограничено 15-ю
// */
//function smarty_function_rendercontrol($params, &$smarty)
//{
//	if (!isset($params['name']))
//		return "{rendercontrol} : не задано имя контрола";
//	
//	$name = $params['name'] . "Control";
//	unset($params['name']);
//		
//	// здесь делаем допущение, что конструктор контрола не будет иметь больше 15-ти параметров
//	if(count($params) > 15)
//		return "{rendercontrol} : Число параметров контруктора больше 15, наверное придется исправить код в теге";
//		
//	list(
//			$par0, $par1, $par2, 
//			$par3, $par4, $par5, 
//			$par6, $par7, $par8, 
//			$par9, $par10, $par11,
//			$par12, $par13, $par14,
//			$par15 
//		) 
//		= array_values($params);
//		
//	$obj = new $name($par0, $par1, $par2, 
//					 $par3, $par4, $par5, 
//					 $par6, $par7, $par8, 
//					 $par9, $par10, $par11,
//					 $par12, $par13, $par14,
//					 $par15 );
//
//	$ui = UIGenerator::getInstance();
//	$ui->forceViewComponent = true;		
//	$ui->renderControl($obj);	
//	return $ui->display();
//}
//?>