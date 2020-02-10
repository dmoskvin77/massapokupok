<?php
/**
 * Тег для использования восстановления данных в форме после
 * постбека (если форма оказалась невалидной)
 * @example {formrestore id="my_form"}
 */
function smarty_function_formrestore($params, &$smarty)
{
	$list = @$params["id"];
	$ids = explode(",", $list);
	
	FormRestore::process($ids);
}
?>