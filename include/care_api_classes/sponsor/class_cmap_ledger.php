<?php
/**
* class_cmap_referral
* 
* provides helper functions for posting entries in the CMAP account ledger
* 
* @package care_api_classes/sponsor
* @author Alvin Quinones
* @version v1.0.0 (2010-06-09)
*/

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCmapLedger extends Core {
	private $ledgerTable = 'seg_cmap_main_ledger';
	
	/**
	* Constructor
	* 
	*/
	function SegCmapLedger() {
		$this->setTable($this->$ledgerTable, $fetch_metadata=true);
	}
	
	/**
	* Records a new entry to a CMAP account's ledger 
	* 
	* 
	*/	
	function post( $ledger_data ) {
		
	}
}
