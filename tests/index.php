<?php
require_once dirname(dirname(__FILE__)).'/include/api/bootstrap.php';
Loader::import('db.mappers.AdoMapper');
Loader::registerAlias('test', APP_PATH . 'tests' . DIRECTORY_SEPARATOR . 'api');

require_once APP_PATH.'classes/simpletest/autorun.php';
require_once APP_PATH.'classes/adodb/adodb.inc.php';
require_once APP_PATH.'include/inc_environment_global.php';

// Generate MockAdoConnection
Mock::generate('ADOConnection');

class HISTestSuite extends TestSuite 
{

	public function __construct()
	{
		parent::__construct();

		// test core classes
        $this->collect(dirname(__FILE__) . '/api/core',
            new SimplePatternCollector('/Test.php/'));
        
        // test db package
        $this->collect(dirname(__FILE__) . '/api/db',
            new SimplePatternCollector('/Test.php/'));
        
        
        // test request
        // $this->collect(dirname(__FILE__) . '/api/request/pharmacy',
        //     new SimplePatternCollector('/Test.php/'));
	}

}

class HisUnitTestCase extends UnitTestCase
{
    protected $mockMapper;
    protected $actualMapper;
    
    public function getMockMapper()
    {
        if (empty($this->mockMapper)) {
            $this->mockMapper = new AdoMapper(new MockADOConnection());
        }
        return $this->mockMapper;
    }
    
    public function getMapper() 
    {
        if (empty($this->actualMapper)) {
            global $db;
            $this->actualMapper = new AdoMapper($db);
        }
        return $this->actualMapper;
    }
    
}