<?php
error_reporting(E_COMPILE_ERROR | E_ERROR|E_CORE_ERROR);
require('./roots.php');
require('social_repgen.ini.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

class socialReport extends RepGen{
	var $fromDate; 
	var $toDate;
	
	function socialReport($fromDate, $toDate){
		$this->ColumnWidth = array(); // fill this with data
		$this->RowHeight = 4;
		$this->Alignment = array() // 
		$this->PageOrientation = "L";
		$this->from_date = $fromDate;
		$this->to_date = $toDate;
	}//end of function socialReport
	
	function Header(){
		//
	}// end of function Header

	function FetchData(){
		global $db;	
					
		/*select scg.encounter_nr , substring(max(scg.grant_dte),1, 10 ) as grant_dte, 
	substring(max(concat(scg.grant_dte, scg.discountid)), 20) as discountid, 
	sd.discountdesc
from seg_charity_grants as scg
	left join seg_discount as sd on scg.discountid = sd.discountid
where DATE(grant_dte) < '2007-11-14'
group by encounter_nr
*/			
					
	}// end of fucntion FetchData 
}
	
?>