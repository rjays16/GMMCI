<?php
	/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
	if (eregi('inc_retail_db_rdetails_mod.php',$PHP_SELF)) 
		die('<meta http-equiv="refresh" content="0; url=../">');
	/*------end------*/
	
	if ($mode=="savedetails") {
		$produnit=trim($produnit);
		$data=array(
			'refno'=>$refno,
			'bestellnum'=>$prodid,
			'qty'=>$prodqty,
			'rpriceppk'=>$prodppk,
			'pack'=>$produnit
		);
				
		# Set core to main products
		$pharma_obj->usePharmaRdetails();
		$pharma_obj->setDataArray($data);
		$saveok=$pharma_obj->insertDataFromInternalArray();
	}
	elseif ($mode=="deldetails") {
		$saveok=$pharma_obj->RemoveTransactionDetails($refno, $editpentrynum);
		$deleteok=$saveok;
	}
?>