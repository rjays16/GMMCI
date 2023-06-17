<?php
/**
 * 
 */

Loader::import('request.pharmacy.PharmacyRequest');

/**
 * Description of PharmacyRequestTest
 *
 * @author Alvin Quinones
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

class PharmacyRequestTest extends HisUnitTestCase
{
    
    public function testIsPhicNotCashNotPhicReturnsFalse()
    {
        $request = new PharmacyRequest(array(
            'is_cash' => 0,
            'charge_type' => 'not phic'
        ));
        $this->assertFalse($request->isPhic());
    }
    
    public function testIsPhicCashNotPhicReturnsFalse()
    {
        $request = new PharmacyRequest(array(
            'is_cash' => 1,
            'charge_type' => 'not phic'
        ));
        $this->assertFalse($request->isPhic());
    }
    
    public function testIsPhicIsCashPhicReturnsFalse()
    {
        $request = new PharmacyRequest(array(
            'is_cash' => 1,
            'charge_type' => 'phic'
        ));
        $this->assertFalse($request->isPhic());
    }
    
    public function testIsPhicReturnTrue()
    {
        $request = new PharmacyRequest(array(
            'is_cash' => 0,
            'charge_type' => 'phic'
        ));
        $this->assertTrue($request->isPhic());        
    }
}
