<?php
/**
 * Класс для восстановления данных формы.
 * Требует для работы jQuery
 *
 * @example 
 * CLIENT html code:
 * 
 * <html>
 * <script language="javascript" type="text/javascript" src="jquery.js" />
 *
 * <?php
 * 	FormRestore::process(array("my_form"));
 * ?> 
 * 
 * <form id='my_form'>
 * 		<input type='text' name='age' />
 * 		<input type="checkbox" name="value" value="42"/>
 * 		<input type="submit" value="check!"/>
 * </form>
 * </html>
 * 
 * SERVER php code:
 * <?php
 * 	$age = $_POST['age'];
 * 	if (!is_int($age))
 * 	{
 * 		FormRestore::add('my_form');
 * 		// redirect to previous page here!
 * 	} 	
 * ?>
 */
class FormRestore
{

	private static $isScriptInserted = false;
	
	/**
	 * Нельзя создавать экземпляры
	 */
	private function __construct()
	{}
	
	/**
	 * Добавляем данные формы
	 * 
	 * @param string $formId Значение атрибута id формы
	 * @return void
	 */
	public static function add($formId)
	{		
		if (Request::isPost())
		{
			// удаляем все записи
			Session::clear("__formrestore__");
			
			$data[$formId] = json_encode($_POST);
			Session::set("__formrestore__", $data);
		}	
		
		if (Request::isGet())
		{
			// удаляем все записи
			Session::clear("__formrestore__");
			
			$data[$formId] = json_encode($_GET);
			Session::set("__formrestore__", $data);
		}		
	}
	
	/**
	 * Возвращает данные для формы
	 * 
	 * @param string $formId Значение атрибута id формы, для 
	 * 		которой происходит восстановление
	 */
	public static function get($formId)
	{
		$res = Session::push("__formrestore__");
		if (isset($res[$formId]))
			return $res[$formId];
		else
			return null;
	}
	
	/**
	 * Выводит javascript кода с данными для
	 * восстановления состояния формы
	 * 
	 * @param string $formId Значение атрибута id формы, для 
	 * 		которой происходит восстановление
	 */
	public static function restore($formId)
	{
		$data = self::get($formId);
		if ($data != null)
		{			
			// вставляем скрипт десериализации
			self::insertScript();
			
			$script = "
				<script language=\"javascript\" type=\"text/javascript\">
				$().ready(function() 
				{
					$('#{$formId}').deserialize({$data}, {isPHPnaming:true});
				});
				</script>
			";
			echo $script;
		}
	}
	
	/**
	 * Восстановление форм на странице
	 * 
	 * 1. Выводит скрипт для десериализации данных
	 * 2. Генерирует и выводит данные для восстановления указанных форм
	 * 
	 * @param array $idList Список идентификаторов форм на странице
	 */
	public static function process($idList)
	{		
		foreach ($idList as $id)
		{
			$id = trim($id);
			self::restore($id);
		}
	}
	
	/**
	 * Выводит яваскрипт десериализации
	 */
	public static function insertScript()
	{
		if (self::$isScriptInserted)
			return null;
			
$script = <<<EOT
<script language="javascript" type="text/javascript">
$.fn.deserialize=function(d,_2){
var _3=d;
me=this;
if(d===undefined){
return me;
}
_2=$.extend({isPHPnaming:false,overwrite:true},_2);
if(d.constructor==Array){
_3={};
for(var i=0;i<d.length;i++){
if(typeof _3[d[i].name]!="undefined"){
if(_3[d[i].name].constructor!=Array){
_3[d[i].name]=[_3[d[i].name],d[i].value];
}else{
_3[d[i].name].push(d[i].value);
}
}else{
_3[d[i].name]=d[i].value;
}
}
}
$("input,select,textarea",me).each(function(){
var p=this.name;
var v=[];
if(_2.isPHPnaming){
p=p.replace(/\[\]$/,"");
}
if(p&&_3[p]!=undefined){
v=_3[p].constructor==Array?_3[p]:[_3[p]];
}
if(_2.overwrite===true||_3[p]){
switch(this.type||this.tagName.toLowerCase()){
case "radio":
case "checkbox":
this.checked=false;
for(var i=0;i<v.length;i++){
this.checked|=(this.value!=""&&v[i]==this.value);
}
break;
case "select-multiple"||"select":
for(i=0;i<this.options.length;i++){
this.options[i].selected=false;
for(var j=0;j<v.length;j++){
this.options[i].selected|=(this.options[i].value!=""&&this.options[i].value==v[j]);
}
}
break;
case "button":
case "submit":
this.value=v.length>0?v.join(","):this.value;
break;
default:
this.value=v.join(",");
}
}
});
return me;
};
</script>
EOT;
		self::$isScriptInserted = true;
		echo $script;
	}
}
?>