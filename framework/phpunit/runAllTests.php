<?php

require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'phpunit/EntityManagerTest.php';
require_once 'phpunit/PHPSettingsTest.php';
require_once 'phpunit/RequestTest.php';
require_once 'phpunit/XDateTimeTest.php';
require_once 'phpunit/ControlTest.php';
require_once 'phpunit/ConfiguratorTest.php';
require_once 'phpunit/SessionTest.php';

/**
 * Запуск всех юнит-тестов
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Test
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

class runAllTests extends PHPUnit_Framework_TestSuite
{

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct ()
	{
		$this->setName('Framework tests');
		$this->addTestSuite('EntityManagerTest');
		$this->addTestSuite("PHPSettingsTest");
		$this->addTestSuite("RequestTest");
		$this->addTestSuite("XDateTimeTest");
		$this->addTestSuite("ControlTest");
		$this->addTestSuite("ConfiguratorTest");
		$this->addTestSuite("SessionTest");
	}

	/**
	 * Creates the suite.
	 */
	public static function suite ()
	{
		return new self();
	}
}

?>
