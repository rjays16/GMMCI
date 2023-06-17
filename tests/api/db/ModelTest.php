<?php
/**
 * ModelTest.php
 * 
 * @package tests.api.db
 */

Loader::import('db.Model');

/**
 * 
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */
class ModelTest extends HisUnitTestCase
{
    protected $dummyRecord = array(
        'id' => 10000,
        'column1' => 'Dummy',
        'column2' => 'Dummer'
    );
    
    public function setUp()
    {
        $this->getMapper()->startTransaction();
    }
    
    public function tearDown()
    {
        $this->getMapper()->rollbackTransaction();
    }
    
    /**
     * 
     */
    public function testIsNewRecord() 
    {
        $model = new ConcreteModel();
        $this->assertTrue($model->isNewRecord());
    }
    
    /**
     * 
     */
    public function testSet() 
    {
        $model = new ConcreteModel();
        $model->set(array(
            'id' => 1
        ));
        $this->assertEqual($model->id, 1);
    }
    
    public function testAccessProperty()
    {
        $m = new ConcreteModel($this->dummyRecord);
        $m->setMapper($this->getMapper());
        $m->save();
        
        $m->clean();
        $m->id = $this->dummyRecord['id'];        
        $this->assertEqual($m->column1, $this->dummyRecord['column1']);

    }
    
    /**
     * 
     */
    public function testExists() 
    {
        $record = array(
            'id' => 1,
            'column1' => 'One',
            'column2' => 'Two'
        );
        $modelA = new ConcreteModel($record);
        $modelA->setMapper($this->getMapper());
        
        $this->assertFalse($modelA->exists());
        
        $modelA->save();
        
        $modelB = new ConcreteModel($record);
        $modelB->setMapper($this->getMapper());
        
        $this->assertTrue($modelB->exists());
    }
    
    /**
     * 
     */
    public function testImmutabilityOfIdentity()
    {
        $record = array(
            'id' => 1,
            'column1' => 'One',
            'column2' => 'Two'
        );
        $modelA = new ConcreteModel($record);
        $modelA->setMapper($this->getMapper());
        $modelA->save();
        
        $modelB = new ConcreteModel($record);
        $modelB->setMapper($this->getMapper());
        $this->expectException('DbException');
        $modelB->id = 5;
    }
    
    /**
     * 
     */
    public function testCreateNew()
    {
        $model= new ConcreteModel($this->dummyRecord);
        $model->setMapper($this->getMapper());
        $this->assertTrue($model->save());
    }
    
    /**
     * 
     */
    public function testSaveWithNoMapper()
    {
        $model= new ConcreteModel(array(
            'id' => 1000,
            'column1' => 'Dummy',
            'column2' => 'Dummer'
        ));
        $this->assertFalse($model->save());
    }

    
    /**
     * 
     */
    public function testIsNOTNewRecord()
    {
        $a = new ConcreteModel($this->dummyRecord);
        $a->setMapper($this->getMapper());
        $a->save();
        
        $b = new ConcreteModel($this->dummyRecord);
        $b->setMapper($this->getMapper());
        $this->assertFalse($b->isNewRecord());
    }
}

/**
 * Mock realization of the Model abstract class
 * 
 */
class ConcreteModel extends Model
{
    public static function getFieldNames() 
    {
        return array(
            'id',
            'column1',
            'column2'
        );
    }

    public static function getPrimaryKeys() 
    {
        return array('id');
    }

    public static function getRelations() 
    {
        return array();
    }

    public static function getTableName() 
    {
        return 'test';
    }
}