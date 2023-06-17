<?php

Loader::import('request.pharmacy.PharmacyRequestItem');

class PharmacyRequestItemTest extends HisUnitTestCase
{
    
	/**
	 * Test for checking if the related item for a pharmacy request item
     * is a PharmacyRequestItem
	 */
	public function testRequestOfItemIsAPharmacyRequestObject()
	{
        $item = new PharmacyRequestItem(array(
            'refno' => '2010000001',
            'bestellnum' => 1
        ));
        $item->setMapper($this->getMapper());
        $this->assertIsA($item->request, 'PharmacyRequest');
	}
    
    /**
     * Tests that the reference no of a request item and the related
     * request is the same
     */
    public function testRequestOfItemRefnoIsSame() {
        $item = new PharmacyRequestItem(array(
            'refno' => '2010000001',
            'bestellnum' => 1
        ));
        $item->setMapper($this->getMapper());
        $this->assertEqual($item->request->refno, $item->refno);
    }
    
    /**
     * Test if isServed method returns the correct value
     */
    public function testIsServed() 
    {
        $request = new PharmacyRequestItem(array('serve_status' => 'S'));
        $this->assertTrue($request->isServed());
    }

}
