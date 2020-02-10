<?php
/**
 * Менеджер работы с ролями
 */
class RoleManager extends EntityManager 
{
	function __construct()
	{
		$this->setCommonConnection(Application::getConnection("master"));
	}	
	
	/**
	 * Функция возвращает массив ролей [id => name]
	 *
	 * @return array
	 */
	public function getRoleList($ownerSiteId, $ownerOrgId)
	{
		$sql = "SELECT id, name FROM role";
		$res = $this->getByAnySQL($sql);
		$out = null;
		
		foreach ($res as $item)
			$out[$item['id']] = $item['name'];
		
		return $out;
	}

}
