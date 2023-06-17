<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
		
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');    
require($root_path.'include/care_api_classes/class_icpm.php');

$query = html_entity_decode(urldecode($_GET['query']));
getSelectedICP($query);

function getSelectedICP($sfilter) {
		 $objicp = new Icpm(); 
		
		if ($result = $objicp->getSelectedICPDesc($sfilter)) {
				while ($row = $result->FetchRow()) {
						// $scode = trim($row["code"]);
						$scode = trim($row["code"]);
						#$desc  = substr(trim($row["description"]),0,70);       #--modified by CHA, Feb 22, 2010         
						$desc  = trim($row["description"]);		
						print "$desc\t$scode\n";  
						#print "$scode\t$desc\n"; 
				}
		}
		else
				print "\tNo ICP found!\n";            
}
?>