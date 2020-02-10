<?php
/**
 * //TODO: Описание класса
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Core
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

abstract class CachedEntityManager extends EntityManager 
{
	/**
	 * Экземпляр объекта, предоставляющий
	 * методы кеширования
	 * 
	 * @var object
	 */
	protected $cache = null;
	
	/**
	* Сохраняет объект
	* 
	* @param Entity &$object Сущность
	* 
	* @return Object
	**/
	public function save(Entity &$object)
	{
		$object = parent::save($object);
		// в кэш
		if ($this->cache != null)
			$this->cache->set("{$this->defineClass()}-{$object->getId()}", $object);
		return $object;
	}
	
	/**
	* Удаляет сущность по ее идентификатору
	* 
	* @param int $id Идентификатор сущности
	* 
	* @return void
	**/
	public function remove($id)
	{
		$res = parent::remove($id);
		
		// удаление из кэша
		if ($this->cache != null)
			$this->cache->remove("{$this->defineClass()}-{$id}");
		
		return $res;
	}
	
	/**
	* Возвращает сущность по идентификатору
	* 
	* @param int $id идентификатор сущности
	* 
	* @return object
	*/
	public function getById($id)
	{
		// выбираем из кэша
		if ($this->cache != null)
		{
			$res = $this->cache->get("{$this->defineClass()}-{$id}");
			if ($res != null)
				return $res;
		}		
		
		$res = parent::getById($id);
		if ($res == null)
			return null;
		
		// получили? в кэш!
		if ($this->cache != null)
		{
			$this->cache->set("{$this->defineClass()}-{$res->getId()}", $res);
		}
		return $res;		
	}
	
	/**
	 * Удаляет все записи данной сущности в БД
	 * Существует нюанс при включенном кэше:
	 * при удалении всех записей, помечаются все данные к кэше как устаревшие,
	 * т.к. таблицы могут быть связаны, и данные для другой таблицы из кэша не удалятся
	 * 
	 * @return void
	 * */
	public function removeAll()
	{
		$res = parent::removeAll();
		// при удалении всех записей, помечаем все данные к кэше как устаревшие
		if ($this->cache != null)
			$this->cache->flush();
		
		return $res;
	}
	
	/**
	 * Установка кэша для кэширования сущностей
	 * 
	 * @param IDataCache &$cache Объект кэша
	 * 
	 * @return void
	 */
	public function setCache(IDataCache &$cache)
	{
		$this->cache = &$cache;
	}
}
?>