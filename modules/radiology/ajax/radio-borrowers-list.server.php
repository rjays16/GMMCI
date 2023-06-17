<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
# Create person object
#include_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path.'modules/radiology/ajax/radio-borrowers-list.common.php');

#define('MAX_BLOCK_ROWS',30);
define('MAX_BLOCK_ROWS',10);

function PopulateRadioBorrowerList($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir ){
	global $root_path;
	$objResponse = new xajaxResponse();

	#$objResponse->addAlert("PopulateRadioBorrowerList :: ajax : tbid =".$tbId. "\n tbody = ".$tbody."\n searchkey = ".$searchkey."\n sub_dept_nr=".$sub_dept_nr."\n pgx=".$pgx."\n thisfile=".$thisfile." \n rpath= ".$rpath."\n mode=".$mode."\n oitem=".$oitem."\n odir=". $odir);
	//Display table header
	RadioBorrowerListHeader($objResponse,$tbId,$sub_dept_nr,$oitem, $odir);

	//Paginate & display list of radiology request
	PaginateRadioBorrowerList($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem);

	return $objResponse;
}//end of PopulateRadioBorrowerList


function RadioBorrowerListHeader(&$objResponse,$tbId, $sub_dept_nr, $oitem, $odir){

/*
borrower_name (borrower_id)
batch_nr
service_code
dept_name (dept_nr)
patient_name
releaser_name
date_borrowed
*/
	$tr  = "<thead>";
	$tr .= "<tr><th colspan=\"9\" id=\"mainHead".$sub_dept_nr."\"></th></tr>";
	$tr .= "<tr>";
	$tr .= "<th width=\"2%\"></th>";
	$tr .= makeSortLink('Batch No.','batch_nr', $oitem, $odir, $sub_dept_nr,'10%', 'center');
	$tr .= makeSortLink('Borrower`s Name','borrower_name', $oitem, $odir, $sub_dept_nr,'17%');
	$tr .= makeSortLink('Service Code','service_code',$oitem,$odir,$sub_dept_nr,'10%');
	$tr .= makeSortLink('Department','sub_dept_name', $oitem, $odir, $sub_dept_nr,'15%');
	$tr .= makeSortLink('Patient`s Name','patient_name', $oitem, $odir, $sub_dept_nr,'15%');
	$tr .= makeSortLink('Releaser Name','releaser_name', $oitem, $odir, $sub_dept_nr,'15%');
	$tr .= makeSortLink('Date Borrowed','date_borrowed', $oitem, $odir, $sub_dept_nr,'12%');
/*
	$tr .= makeSortLink('Batch No.','batch_nr', $oitem, $odir, $sub_dept_nr,'10%', 'center');
	$tr .= makeSortLink('PID','borrower_name', $oitem, $odir, $sub_dept_nr,'10%');
	$tr .= makeSortLink('Sex','sex',$oitem,$odir,$sub_dept_nr,'2%');
	$tr .= makeSortLink('Lastname','name_last', $oitem, $odir, $sub_dept_nr,'18%');
	$tr .= makeSortLink('Firstname','name_first', $oitem, $odir, $sub_dept_nr,'18%');
	$tr .= makeSortLink('Birthdate','date_birth', $oitem, $odir, $sub_dept_nr,'7%');
	$tr .= makeSortLink('Barangay','brgy_name', $oitem, $odir, $sub_dept_nr,'12%');
	$tr .= makeSortLink('Municipality/City','mun_name', $oitem, $odir, $sub_dept_nr,'12%');
*/
	$tr .= "<th width=\"2%\">Details</th>";
	$tr .= "</tr>";
	$tr .= "</thead> \n";

	$tbody="<tbody id=\"TBodytab".$sub_dept_nr."\"></tbody>";
#	$prevNextTR = "<tr><td id=\"prevRow\" colspan=\"6\"></td>";
#	$prevNextTR .=    "<td id=\"nextRow\" align=right></td></tr>";

#	$HTML = $tr.$tbody.$prevNextTR;
	$HTML = $tr.$tbody;

	#$objResponse->addAlert("item=".$item."\n oitem=".$oitem."\n odir=".$odir."\n sub_dept_nr=".$sub_dept_nr);
	#$objResponse->addAlert("tbId=".$tbId);
	$objResponse->addAssign($tbId,"innerHTML",$HTML);

} // end of RadioBorrowerListHeader

function makeSortLink($txt='SORT',$item, $oitem,$odir='ASC', $subDeptNr='', $width='', $align='left'){
	if($item == $oitem){
		if($odir == 'ASC'){
			$img = "<img src=\"../../gui/img/common/default/arrow_red_up_sm.gif\">";
		}else{
			$img = "<img src=\"../../gui/img/common/default/arrow_red_dwn_sm.gif\">";
		}
	}else{
		$img='&nbsp;';
	}

	if($odir=='ASC') $dir ='DESC';
	else $dir = 'ASC';
											 #jsSortHandler(items, oitem, dir, sub_dept_nr)
	$td = "<th width=\"".$width."\" align=\"".$align."\" onClick=\"jsSortHandler('$item', '$oitem','$dir', '$subDeptNr');\">".$img."<b>".$txt."</b></th> ";

	return $td;
} // end of function makeSortLink

function PaginateRadioBorrowerList(&$objResponse, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $odir='ASC', $oitem='create_dt'){
	global $date_format;
	$objRadio = new SegRadio();

	#Instantiate paginator
	$pagen = new Paginator($pgx, $thisfile, $searchkey, $rpath, $oitem, $odir);

	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	# Last resort, use the default defined at the start of this page
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS);
		else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

#$pagen->setMaxCount(MAX_BLOCK_ROWS);
#$objResponse->addAlert("PaginateRadioBorrowerList:: searchkey 1 = '".$searchkey."' \n mode = '".$mode."'");
#	if(($mode == 'search' || $mode == 'paginate') && !empty($searchkey)){
		$searchkey = strtr($searchkey, '*?', '%_');
#	}
#$objResponse->addAlert("PaginateRadioBorrowerList:: searchkey 2 = '".$searchkey."'");
#	$objResponse->addAlert("PaginateRadioBorrowerList:: searchkey = ".$searchkey."\n sub_dept_nr =".$sub_dept_nr);
	#$ergebnis = &$objRadio->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir);
#$objResponse->addAlert("PaginateRadioBorrowerList:: oitem = ".$oitem);
#	$ergebnis = &$objRadio->searchLimitBasicInfoRadioPatientList($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir);

#$objResponse->addAlert($searchkey." , ".$sub_dept_nr." , ".$pagen->MaxCount()." , ".$pgx." , ".$oitem." , ".$odir);

		#added code by angelo m. 08.11.2010
		#--------------------------------------
		#commented by VAN 08-27-2010
		/*$is_owner=0;
		if((string)$searchkey !=""){
			$is_owner=$objRadio->isBorrowerOwner($searchkey);
//			$objResponse->addAlert("before isowner ".$is_owner);
		}  */

		#--------------------------------------

	#$objRadio->is_owner=$is_owner;
	#$ergebnis = &$objRadio->searchLimitRadioBorrowers($searchkey,$is_owner,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir);
	$ergebnis = &$objRadio->searchLimitRadioBorrowers($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir);

#$objResponse->addAlert("PaginateRadioBorrowerList:: SQL objRadio->sql = '".$objRadio->sql."'");
#$objResponse->addAlert("PaginateRadioBorrowerList:: ergebnis = '".$ergebnis."'");
	#$objResponse->addAlert("PaginateRadioBorrowerList:: ergebnis = ".print_r($ergebnis));
//	$objResponse->addAlert("after isowner ".$objRadio->$is_owner);

	$linecount = $objRadio->LastRecordCount();
	$pagen->setTotalBlockCount($linecount);


	if(isset($totalcount)&& $totalcount){
		$pagen->setTotalDataCount($totalcount);
	}else{
//		$objRadio->is_owner=$is_owner;
//		$objResponse->addAlert("hahayz ".$objRadio->is_owner);
		@$objRadio->_searchRadioBorrowers($searchkey,$is_owner,$sub_dept_nr);
		#@$objRadio->_searchBasicInfoRadioPending($searchkey, $sub_dept_nr);
//		$objResponse->addAlert("after isowner ".$objRadio->ownerthis);
//	$objResponse->addAlert("".$objRadio->sql);
//	$objResponse->addAlert("hahayz2 ".$objRadio->is_owner);

		$totalcount = $objRadio->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);

#$objResponse->addAlert("PaginateRadioBorrowerList:: ergebnis = ".$ergebnis);
#$objResponse->addAlert("linecount=".$linecount." \n totalcount=".$totalcount);

	$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
	if ($linecount)
		$textResult = '<hr width="80%" align="center">'.str_replace("~nr~",$totalcount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
#		echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else
		$textResult = '<hr width="80%" align="center">'.str_replace('~nr~','0',$LDSearchFound);
#		echo str_replace('~nr~','0',$LDSearchFound);
	$objResponse->addAssign('textResult',"innerHTML", $textResult);

	$my_count=$pagen->BlockStartNr();
	if($ergebnis){
		while($row = $ergebnis->FetchRow() ){
#$objResponse->addAlert("PaginateRadioBorrowerList: row : \n".print_r($row,true));
/*
			$gender = $row['sex'];
			$birthdate = @formatDate2Local($row['date_birth'], $date_format);
			$lname = htmlentities($row['name_last']);
			$fname = htmlentities($row['name_first']);
$msg="\n row['rid'] = ".$row['rid']."\n row['pid'] = ".$row['pid'].
		"\n gender = ".$gender."\n birthdate = ".$birthdate.
		"\n lname = ".$lname."\n fname = ".$fname.
		"\n row['brgy_name'] = ".$row['brgy_name']."\n row['mun_name'] = ".$row['mun_name'];
#$objResponse->addAlert("PaginateRadioBorrowerList: $msg");
			$objResponse->addScriptCall("jsListRows",$sub_dept_nr, $my_count,$row['rid'],$row['pid'],
																	$gender,$lname,$fname,$birthdate,
																	$row['brgy_name'],$row['mun_name']);
*/

			if (($row['date_borrowed']!='0000-00-00')&&($row['date_borrowed']!=''))
				$date_borrowed = @formatDate2Local($row['date_borrowed'], $date_format);
			else
				$date_borrowed ='';
			$borrower_name = htmlentities($row['borrower_name']);
			$patient_name = htmlentities($row['patient_name']);
			$releaser_name = htmlentities($row['releaser_name']);
$msg="\n row['borrower_name'] = '".$row['borrower_name']."'".
		"\n borrower_name ='".$borrower_name."'".
		"\n row['patient_name'] = '".$row['patient_name']."'".
		"\n patient_name ='".$patient_name."'".
		"\n row['releaser_name'] = '".$row['releaser_name']."'".
		"\n releaser_name ='".$releaser_name."'".
		"\n row['date_borrowed'] = '".$row['date_borrowed']."'".
		"\n date_borrowed ='".$date_borrowed."'".
		"\n row['borrower_id'] = '".$row['borrower_id']."'";
#$objResponse->addAlert("PaginateRadioBorrowerList: $msg");

			#added by VAN 07-24-08
			/*
			if ($borrower_name)
				$borrower = $borrower_name;
			else
				$borrower = $row['patient_name'];
			*/
			if ($row['sub_dept_name'])
				$dept = $row['sub_dept_name'];
			else
				$dept = 'Patient';

			$objResponse->addScriptCall("jsListRows",$sub_dept_nr, $my_count,$row['rid'],$row['pid'],
																	$row['batch_nr'], $row['borrower_id'], ucwords(strtolower($borrower_name)),
																	$row['service_code'], $dept,
																	ucwords(strtolower($row['patient_name'])), $row['releaser_name'],
																	$row['is_owner'], $date_borrowed);
/*
borrower_name (borrower_id)
batch_nr
service_code
dept_name (dept_nr)
patient_name
releaser_name
date_borrowed
*/
			$my_count++;
		}//end while loop
	//end if (ergebnis)
	}else{
		//$tr = "<tr><td colspan=\"8\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:\"Arial\",Courier, mono; font-style:Bold; font-weight:Bold; font-size:12px;\">NO MATCHING REQUEST FOUND</td></tr>";
		$tr = "<tr><td colspan=\"9\"  style=\"\">No available list of radiology borrowers...</td></tr>";
		$objResponse->addAssign("TBodytab".$sub_dept_nr, "innerHTML", $tr);
	}
/*
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
	if($pagen->csx){
		$temp = "jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr');";
		$img = '<img src="../../gui/img/common/default/prev1.png" height="20" width="94" id="prevButton" name="prevButton" onClick="setPgx('.$prevIndex.'); '.$temp.'">';
		$objResponse->addAssign("prevRow","innerHTML", $img);
	}
	if($nextIndex){
		$temp = "jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr');";
			$img = '<img src="../../gui/img/common/default/next1.png" height="20" width="60" id="nextButton" name="nextButton" onClick="setPgx('.$nextIndex.'); '.$temp.'">';
			$objResponse->addAssign("nextRow","innerHTML", $img);
	}
*/
		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("PaginateRadioBorrowerList : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);
	$pageFirstOffset = 0;
	$pagePrevOffset = $prevIndex;
	$pageNextOffset = $nextIndex;
	$pageLastOffset = $totalcount-($totalcount%$pagen->MaxCount());
	if ($pagen->csx){
		$pageFirstClass = "segSimulatedLink";
		$pageFirstOnClick = " setPgx($pageFirstOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
		$pagePrevClass = "segSimulatedLink";
		$pagePrevOnClick = " setPgx($pagePrevOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
	}else{
		$pageFirstClass = "segDisabledLink";
		$pagePrevClass = "segDisabledLink";
	}
	if ($nextIndex){
		$pageNextClass = "segSimulatedLink";
		$pageNextOnClick = " setPgx($pageNextOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
		$pageLastClass = "segSimulatedLink";
		$pageLastOnClick = " setPgx($pageLastOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
	}else{
		$pageNextClass = "segDisabledLink";
		$pageNextOffset = $pageLastOffset;
		$pageLastClass = "segDisabledLink";
	}

	$img ='										<div id="pageFirst" class="'.$pageFirstClass.'" style="float:left" onclick="'.$pageFirstOnClick.'"> '.
			'											<img title="First" src="../../images/start.gif" border="0" align="absmiddle"/> '.
			'											<span title="First">First</span> '.
			'										</div> '.
			'										<div id="pagePrev" class="'.$pagePrevClass.'" style="float:left" onclick="'.$pagePrevOnClick.'"> '.
			'											<img title="Previous" src="../../images/previous.gif" border="0" align="absmiddle"/> '.
			'											<span title="Previous">Previous</span> '.
			'										</div> '.
			'										<div id="pageShow" style="float:left;margin-left:10px;"> '.
			'											<span>List of Service Requests</span> '.
			'										</div> '.
			'										<div id="pageLast" class="'.$pageLastClass.'" style="float:right" onclick="'.$pageLastOnClick.'"> '.
			'											<span title="Last">Last</span> '.
			'											<img title="Last" src="../../images/end.gif" border="0" align="absmiddle"/> '.
			'										</div> '.
			'										<div id="pageNext" class="'.$pageNextClass.'" style="float:right" onclick="'.$pageNextOnClick.'"> '.
			'											<span title="Next">Next</span> '.
			'											<img title="Next" src="../../images/next.gif" border="0" align="absmiddle"/> '.
			'										</div> ';
	$objResponse->addAssign("mainHead".$sub_dept_nr,"innerHTML", $img);
}// end of function PaginateRadioBorrowerList


$xajax->processRequest();
?>