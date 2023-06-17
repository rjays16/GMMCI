<?php
/**
 * 
 */

Loader::import('request.pharmacy.return.PharmacyReturn');

/**
 * Description of PharmacyReturnTest
 *
 * @author Alvin Quinones
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

class PharmacyReturnTest extends HisUnitTestCase
{
    protected $return = null;
    
    
    public function setUp() 
    {
        $this->return = new PharmacyReturn;
        $this->return->setMapper($this->getMapper());
        $this->getMapper()->getConnection()->StartTrans();
        $this->return->clean();
    }
    
    /**
     * 
     */
    public function tearDown()
    {
        $this->getMapper()->getConnection()->RollbackTrans();
    }
    
    public function testReturnIdGeneration()
    {
        $id = $this->return->generateReturnId();
        $result = $this->getMapper()->query("SELECT MAX(return_nr) `nr` FROM seg_pharma_returns");
        $_id = @$result[0]['nr'];
        $this->assertEqual($id, $_id+1);
        
    }
    
    /**
     * 
     */
    public function testReturnNrIncrement() 
    {
        // get latest nr
        $result = $this->getMapper()->query("SELECT MAX(return_nr) `nr` FROM seg_pharma_returns WHERE return_nr LIKE CONCAT(YEAR(NOW()),'%')");
        $expectedNr = @$result[0]['nr'];
        if (empty($expectedNr)) {
            $expectedNr = date('Y') . '000000';
        } else {
            $expectedNr++;
        }
        
        // create new return
        $r = $this->return;
        $r->return_date = '2012-01-01';
        $r->save();
        
        $this->assertEqual($expectedNr, $r->return_nr);
    }
    
    /**
     * 
     */
    public function testSaving() 
    {
        $nr = '2000000001';
        $this->return->set(array(
            'return_nr' => $nr
        ));

        $this->assertTrue($this->return->save());

        
    }
    
    /**
     * 
     */
    public function testSaveWithReturnNrGiven()
    {   
        $nr = '2000000001';
        $r = new PharmacyReturn(array(
            'return_nr' => $nr,
            'return_date' => '2000-01-01'
        ));
        $r->setMapper($this->getMapper());
        $r->save();
        
        $this->assertEqual($r->return_nr, $nr);
    }
    
    /**
     * 
     */
    public function testReturnOneItem() {
        
        // check if coverage was updated upon return
        $coverage = new PhicCoverage(array(
            'ref_no' => 'T2013000020',
            'source' => 'M',
            'item_code' => '3256',
            'hcare_id' => 18
        ));
        $coverage->setMapper($this->getMapper());
        $originalCoverage = $coverage->coverage;
        
        $r = $this->return;
        $r->set(array(
            'return_nr' => '3000000001',
            'return_date' => date('Y-m-d H:i:s')
        ));
        $returnItem = new PharmacyReturnItem(array(
            'ref_no' => '2013000016',
            'bestellnum' => '3256',
            'quantity' => 1
        ));
        $r->addItem($returnItem);
        $r->save();
        
        $r->clean();
        $r->set(array(
            'return_nr' => '3000000001'
        ));
        // check if return header was saved
        $this->assertTrue($r->exists());
        
        // check if return item was saved
        $item = current($r->returnItems);
        $this->assertEqual($item->bestellnum, 3256);
        
        $this->assertTrue($item->requestItem->request->isPhic());
        
        // check if coverage was updated upon return
        $coverage->clean();
        $coverage->set(array(
            'ref_no' => 'T2013000020',
            'source' => 'M',
            'item_code' => '3256',
            'hcare_id' => 18
        ));

        $this->assertEqual((float)$coverage->coverage, (float)$originalCoverage - $returnItem->requestItem->getPrice());
    }

}
