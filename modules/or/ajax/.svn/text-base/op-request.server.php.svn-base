<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
//require($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');

require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
//include radio-request common
require_once($root_path.'modules/or/ajax/op-request.common.php');

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 

function PaginateORRequest(&$objResponse,$searchkey,$dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='create_time'){
	global $date_format;
	$ops_obj=new SegOps();

#$objResponse->addAlert("PaginateORRequest \n searchkey=".$searchkey. "\n pgx=".$pgx."\n thisfile=".$thisfile."\n path=".$rpath);

	#Initialize variables for search..
/*
	$totalcount = 0;
	$odir='ASC';
	$oitem='create_dt';
*/	
	#Instantiate paginator  //$HTTP_SESSION_VARS['sess_searchkey']
	$pagen=new Paginator($pgx,$thisfile,$searchkey,$rpath, $oitem, $odir);
	
#	$objResponse->addAlert("pagen=".$pagen);
	
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	#Get the max nr of rows from global config
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	# Last resort, use the default defined at the start of this page
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); 
    else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
	
    
	if(($mode == 'search' || $mode == 'paginate')&&!empty($searchkey)){
		# Convert other wildcards
		$searchkey=strtr($searchkey,'*?','%_');
	}
	
	#$objResponse->addAlert('searchkey = '.$searchkey);							  
	$ergebnis = &$ops_obj->searchLimitBasicEncounterOpInfo($searchkey,$dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir);

#$objResponse->addAlert("PaginateORRequest : ops_obj->sql='".$ops_obj->sql."'");	
#$objResponse->addAlert("pagen=".$pagen."\nergebnis=".$ergebnis."\ntbodyId=".$tbodyId);
	//count all records
	$linecount=$ops_obj->LastRecordCount();
	$pagen->setTotalBlockCount($linecount);
	
	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);		
	}else{					   
		@$ops_obj->_searchBasicEncounterOpInfo($searchkey,$dept_nr);
		$totalcount=$ops_obj->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);

	$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
	if ($linecount) 
		$textResult = '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
#		echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else 
		$textResult = '<hr width=80% align=left>'.str_replace('~nr~','0',$LDSearchFound);
#		echo str_replace('~nr~','0',$LDSearchFound); 
	$objResponse->addAssign('textResult',"innerHTML", $textResult);

	$my_count=$pagen->BlockStartNr();
#$objResponse->addAlert("before if-stmt \n pagen=".$pagen."\nergebnis=".$ergebnis);
	if ($ergebnis){
#$objResponse->addAlert("PaginateORRequest 2 : \npgx='".$pgx."'; \ntotalcount='".$totalcount."'; \nlinecount='".$linecount."'; \ntextResult='".$textResult."'; \nmy_count='".$my_count."'; \ndate_format='".$date_format."'");

		while($rowRequest = $ergebnis->FetchRow()){
#$objResponse->addAlert("inside while=loop \nrowRequest=".$rowRequest."\nrowRequest : \n".print_r($rowRequest,true));
			switch($rowRequest['sex']){
				case 'f': $gender = '<img src="../../../gui/img/common/default/spf.gif" >'; break;
				case 'm': $gender = '<img src="../../../gui/img/common/default/spm.gif">'; break;
				default: $gender = '&nbsp;'; break;
			}	
			if (trim($rowRequest['date_birth'])!=''){
				$date_birth = @formatDate2Local($rowRequest['date_birth'],$date_format);
				$bdateMonth = substr($date_birth,0,2);
				$bdateDay = substr($date_birth,3,2);
				$bdateYear = substr($date_birth,6,4);
				if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
					# invalid birthdate
					$date_birth = '';
				}
			}else {
				$date_birth = '';
			}
			if (trim($rowRequest['op_date'])!=''){
				$date_request = @formatDate2Local($rowRequest['op_date'],$date_format);
			}else {
				$date_request = '';
			}

			$lname = htmlentities($rowRequest['name_last']);
			$fname = htmlentities($rowRequest['name_first']);
			$dept_name = htmlentities($rowRequest['dept_id']);
#$objResponse->addAlert("PaginateORRequest : \npriority='".$priority."'");	
			$objResponse->addScriptCall("jsOPRequest",$dept_nr,$my_count, $rowRequest['op_request_nr'],
						$date_request,$dept_name,$rowRequest['pid'],$gender,
						$lname,$fname,$date_birth);
			$my_count++;
#			jsRadioRequest(tbodyId,No,batchNo,dateRq,sub_dept_name,pid,lName,fName,bDate,rStatus)
		}# end of while loop
	}else{ # else of if-stmt 'if ($ergebnis)'
			$objResponse->addScriptCall("jsOPNoFoundRequest",$dept_nr);		
	}# end of else-stmt 'if ($ergebnis)'

		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("PaginateORRequest : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);	
	$pageFirstOffset = 0;
	$pagePrevOffset = $prevIndex;
	$pageNextOffset = $nextIndex;		
	$pageLastOffset = $totalcount-($totalcount%$pagen->MaxCount());
	if ($pagen->csx){
		$pageFirstClass = "segSimulatedLink";
		$pageFirstOnClick = " setPgx($pageFirstOffset); jsSortHandler('$oitem','$oitem','$odir','$dept_nr'); ";
		$pagePrevClass = "segSimulatedLink";
		$pagePrevOnClick = " setPgx($pagePrevOffset); jsSortHandler('$oitem','$oitem','$odir','$dept_nr'); ";
	}else{
		$pageFirstClass = "segDisabledLink";
		$pagePrevClass = "segDisabledLink";
	}
	if ($nextIndex){
		$pageNextClass = "segSimulatedLink";
		$pageNextOnClick = " setPgx($pageNextOffset); jsSortHandler('$oitem','$oitem','$odir','$dept_nr'); ";
		$pageLastClass = "segSimulatedLink";
		$pageLastOnClick = " setPgx($pageLastOffset); jsSortHandler('$oitem','$oitem','$odir','$dept_nr'); ";
	}else{
		$pageNextClass = "segDisabledLink";
		$pageNextOffset = $pageLastOffset;		
		$pageLastClass = "segDisabledLink";
	}

	$img ='										<div id="pageFirst" class="'.$pageFirstClass.'" style="float:left" onclick="'.$pageFirstOnClick.'"> '.
			'											<img title="First" src="../../../images/start.gif" border="0" align="absmiddle"/> '.
			'											<span title="First">First</span> '.
			'										</div> '.
			'										<div id="pagePrev" class="'.$pagePrevClass.'" style="float:left" onclick="'.$pagePrevOnClick.'"> '.
			'											<img title="Previous" src="../../../images/previous.gif" border="0" align="absmiddle"/> '.
			'											<span title="Previous">Previous</span> '.
			'										</div> '.
			'										<div id="pageShow" style="float:left; margin-left:10px"> '.
			'											<span>List of Pending Requests</span> '.
			'										</div> '.
			'										<div id="pageLast" class="'.$pageLastClass.'" style="float:right" onclick="'.$pageLastOnClick.'"> '.
			'											<span title="Last">Last</span> '.
			'											<img title="Last" src="../../../images/end.gif" border="0" align="absmiddle"/> '.
			'										</div> '.
			'										<div id="pageNext" class="'.$pageNextClass.'" style="float:right" onclick="'.$pageNextOnClick.'"> '.
			'											<span title="Next">Next</span> '.
			'											<img title="Next" src="../../../images/next.gif" border="0" align="absmiddle"/> '.
			'										</div> ';
	$objResponse->addAssign("mainHead".$dept_nr,"innerHTML", $img);
#$objResponse->addAlert("PaginateORRequest : \n$img");		
//	return $objResponse;
}

function makeSortLink($txt='SORT',$class='',$item,$oitem,$odir='ASC',$dept_nr='',$width=''){

	if($item==$oitem){
		if($odir=='ASC'){
			$img = '<img src="../../../gui/img/common/default/arrow_red_up_sm.gif">';
		}else{
			$img = '<img src="../../../gui/img/common/default/arrow_red_dwn_sm.gif">';
		}
	}else{
		$img='&nbsp;';
	}

	if($odir=='ASC') $dir='DESC';
	else $dir='ASC';
	$td = "<td class=\"".$class."\" width=\"".$width."\" align=\"center\" onClick=\"jsSortHandler('$item','$oitem','$dir','$dept_nr');\">".$img."<b>".$txt."</b></td> \n";	
	return $td;
}

function PopulateORRequest($tbId, $searchkey,$dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir ){
	global $root_path;
	$objResponse = new xajaxResponse();	
	
#$objResponse->addAlert("PopulateORRequest : tbid =".$tbId. "\n dept_nr='".$dept_nr."'");
	//Display table header 
	ColHeaderORRequest($objResponse,$tbId,$dept_nr,$oitem, $odir);
		
	//Paginate & display list of OR request
	PaginateORRequest($objResponse,$searchkey,$dept_nr,$pgx, $thisfile, $rpath,$odir,$oitem);
	return $objResponse;
}//end of PopulateRadioRequest


//$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;
#function ColHeaderORRequest($tbId, $tbody, $searchkey,$dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir){
function ColHeaderORRequest(&$objResponse, $tbId, $dept_nr, $oitem,$odir){
#	$objResponse = new xajaxResponse();
	global $root_path;
	#$append = '&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&dept_nr'.$dept_nr;
	$class= 'adm_list_titlebar';

	$th  = "<thead><tr><th colspan=\"10\" id=\"mainHead".$dept_nr."\">";
	$th .= "</th></tr></thead>";
	
	$thead  = "<thead><tr>";
	$thead .= "<td class=\"".$class."\" width='2%'><b>No.</b></td> \n";
	$thead .= makeSortLink('Request No.',$class,'op_request_nr',$oitem,$odir,$dept_nr,'12%');
	$thead .= makeSortLink('Operation Date',$class,'op_date',$oitem,$odir,$dept_nr,'15%');
	$thead .= makeSortLink('Department',$class,'dept_name',$oitem,$odir,$dept_nr,'15%');
	$thead .= makeSortLink('Patient No.',$class,'pid',$oitem,$odir,$dept_nr,'11%');
	$thead .= makeSortLink('Sex',$class,'sex',$oitem,$odir,$dept_nr,'6%');
	$thead .= makeSortLink('Family Name',$class,'name_last',$oitem,$odir,$dept_nr,'13%');
	$thead .= makeSortLink('Given Name',$class,'name_first',$oitem,$odir,$dept_nr,'13%');
	$thead .= makeSortLink('Birthdate',$class,'date_birth',$oitem,$odir,$dept_nr,'10%');
	$thead .= "<td class=\"".$class."\" width='2%' align=\"center\"><b>Details</b></td>";
	$thead .= "</tr></thead> \n";
	
	
	$thead1 = $th.$thead;
	$tbodyHTML = "<tbody id='TBodytab".$dept_nr."'></tbody>";
#	$objResponse->addAlert("thead1 : \n".$thead1." tbodyHTML : \n".$tbodyHTML." prevNextTR : \n".$prevNextTR);
	$objResponse->addAssign($tbId,"innerHTML", $thead1.$tbodyHTML);
	//function PaginateORRequest(&$objResponse,$searchkey, $pgx, $thisfile, $rpath){
#	PaginateORRequest($objResponse,$searchkey,$dept_nr,$pgx, $thisfile, $rpath,$odir,$oitem);
#	PaginateORRequest($objResponse, $searchkey, $pgx, $thisfile, $rpath);
	
#	return $objResponse;
}

$xajax->processRequest();
?>