<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
//require($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
//include radio-request common
require_once($root_path.'modules/radiology/ajax/radio-request.common.php');

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 

function PopulateRadioRequest(&$objResponse,$tbodyId,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='create_dt'){
	global $date_format;
	$radio_obj=new SegRadio();

#$objResponse->addAlert("PopulateRadioRequest \n searchkey=".$searchkey. "\n pgx=".$pgx."\n thisfile=".$thisfile."\n path=".$rpath);

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
	
	
//	$encounter=& $radio_obj->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(),$pgx,$oitem,$odir);
	$ergebnis = &$radio_obj->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir);
	
#$objResponse->addAlert("pagen=".$pagen."\nergebnis=".$ergebnis."\ntbodyId=".$tbodyId);
	//count all records
	$linecount=$radio_obj->LastRecordCount();
	$pagen->setTotalBlockCount($linecount);
	
	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);		
	}else{
		@$radio_obj->_searchBasicInfoRadioPending($searchkey,$sub_dept_nr);
		$totalcount=$radio_obj->LastRecordCount();
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
	if ($ergebnis){
#$objResponse->addAlert("PopulateRadioRequest 2 : \npgx='".$pgx."'; \ntotalcount='".$totalcount."'; \nlinecount='".$linecount."'; \ntextResult='".$textResult."'; \nmy_count='".$my_count."'; \ndate_format='".$date_format."'");
		while($rowRequest = $ergebnis->FetchRow()){
			switch($rowRequest['sex']){
				case 'f': $gender = '<img src="../../gui/img/common/default/spf.gif" >'; break;
				case 'm': $gender = '<img src="../../gui/img/common/default/spm.gif">'; break;
				default: $gender = '&nbsp;'; break;
			}	
			switch($rowRequest['is_urgent']){
				case '0': $priority = '<img src="../../gui/img/common/default/spf.gif" >'; break;
				case '1': $priority = '<img src="../../gui/img/common/default/spm.gif">'; break;
				default: $priority = '&nbsp;'; break;
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
			if (trim($rowRequest['request_date'])!=''){
				$date_request = @formatDate2Local($rowRequest['request_date'],$date_format);
			}else {
				$date_request = '';
			}

			$lname = htmlentities($rowRequest['name_last']);
			$fname = htmlentities($rowRequest['name_first']);
			$sub_dept_name = htmlentities($rowRequest['sub_dept_id']);
#$objResponse->addAlert("PopulateRadioRequest : \npriority='".$priority."'");	
			$objResponse->addScriptCall("jsRadioRequest",$sub_dept_nr,$my_count,$rowRequest['batch_nr'],
						$date_request,$sub_dept_name,$rowRequest['pid'],$gender,
						$lname,$fname,$date_birth, $rowRequest['status'],$priority);
			$my_count++;
#			jsRadioRequest(tbodyId,No,batchNo,dateRq,sub_dept_name,pid,lName,fName,bDate,rStatus)
		}# end of while loop
	}else{ # else of if-stmt 'if ($ergebnis)'
			$objResponse->addScriptCall("jsRadioNoFoundRequest",$sub_dept_nr);		
	}# end of else-stmt 'if ($ergebnis)'

		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("PopulateRadioRequest : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);	

/*
	if($odir=='ASC') $dir='DESC';
	else $dir='ASC';

<img src="../../gui/img/common/default/next2.gif" height="20" width="60" id="nextButton" name="nextButton" onClick="setPgx($nextIndex); jsSortHandler('$oitem','$oitem','$dir','$sub_dept_nr');">
<img src="../../gui/img/common/default/prev2.gif" height="20" width="94" id="prevButton" name="prevButton" onClick="setPgx($prevIndex); jsSortHandler('$oitem','$oitem','$dir','$sub_dept_nr');">
*/

	if ($pagen->csx){
#	if ($prevIndex){
#$objResponse->addAlert("PopulateRadioRequest : PREVIOUS button will be generated : prevIndex =".prevIndex);
		$temp = "jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr');";
	   $img = '<img src="../../gui/img/common/default/prev1.png" height="20" width="94" id="prevButton" name="prevButton" onClick="setPgx('.$prevIndex.'); '.$temp.'">&nbsp;&nbsp;';
		$objResponse->addAssign("prevRow","innerHTML", $img);
	}
	if ($nextIndex){
#$objResponse->addAlert("PopulateRadioRequest : NEXT button will be generated : nextIndex =".$nextIndex);	
		$temp = "jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr');";
	   $img = '<img src="../../gui/img/common/default/next1.png" height="20" width="60" id="nextButton" name="nextButton" onClick="setPgx('.$nextIndex.'); '.$temp.'">&nbsp;&nbsp;';
		$objResponse->addAssign("nextRow","innerHTML", $img);
	}
	
//	return $objResponse;
}

function makeSortLink($txt='SORT',$class='',$item,$oitem,$odir='ASC',$sub_dept_nr=''){

	if($item==$oitem){
		if($odir=='ASC'){
#			$img = '<img src="'.$root_path.'gui/img/common/default/pixel.gif">';
			$img = '<img src="../../gui/img/common/default/arrow_red_up_sm.gif">';
#			$img = '<img src="'.$root_path.'gui/img/common/default/arrow_red_up_sm.gif">';
#			$img ='<img '.createComIcon($root_path,'arrow_red_up_sm.gif','0').'>';
		}else{
#			$img = '<img src="'.$root_path.'gui/img/common/default/pixel.gif">';
			$img = '<img src="../../gui/img/common/default/arrow_red_dwn_sm.gif">';
#			$img = '<img src="'.$root_path.'gui/img/common/default/arrow_red_dwn_sm.gif">';
#			$img = '<img '.createComIcon($root_path,'arrow_red_dwn_sm.gif','0').'>';
		}
	}else{
		$img='&nbsp;';
	}

	if($odir=='ASC') $dir='DESC';
	else $dir='ASC';
	$td = "<td class=\"".$class."\" align=\"center\" onClick=\"jsSortHandler('$item','$oitem','$dir','$sub_dept_nr');\">".$img."<b>".$txt."</b></td> \n";	
	return $td;
}

//$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;
function ColHeaderRadioRequest($tbId, $tbody, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir){
	$objResponse = new xajaxResponse();
	global $root_path;
	#$append = '&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&dept_nr'.$dept_nr;
	$class= 'adm_list_titlebar';

	$th  = "<thead><tr><th colspan=\"12\">";
	$th .= "</th></tr></thead>";
	
	$thead  = "<thead><tr>";
	$thead .= "<td class=\"".$class."\"><b>No.</b></td> \n";
	$thead .= makeSortLink('Batch No.',$class,'batch_nr',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Date Requested',$class,'request_date',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Deparment',$class,'sub_dept_name',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Patient No.',$class,'pid',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Sex',$class,'sex',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Family Name',$class,'name_last',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Name',$class,'name_first',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Birthdate',$class,'date_birth',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Request Status',$class,'status',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Priority',$class,'is_urgent',$oitem,$odir,$sub_dept_nr);
	$thead .= "<td class=\"".$class."\" align=\"center\"><b>Details</b></td>";
	$thead .= "</tr></thead> \n";
	
	$thead1 = $th.$thead;
	$tbodyHTML = "<tbody id='TBodytab".$sub_dept_nr."'></tbody>";
	$prevNextTR = "						<tr>				
							<td id='prevRow' colspan='11'>
							</td>
							<td id='nextRow' align=right></td>
						</tr>";

#	$objResponse->addAlert("thead1 : \n".$thead1." tbodyHTML : \n".$tbodyHTML." prevNextTR : \n".$prevNextTR);
	$objResponse->addAssign($tbId,"innerHTML", $thead1.$tbodyHTML.$prevNextTR);
	//function PopulateRadioRequest(&$objResponse,$searchkey, $pgx, $thisfile, $rpath){
	PopulateRadioRequest($objResponse,$tbody,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir,$oitem);
#	PopulateRadioRequest($objResponse, $searchkey, $pgx, $thisfile, $rpath);
	
	return $objResponse;
/*
	$objResponse = new xajaxResponse();
	#$append = '&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&dept_nr'.$dept_nr;
	$class= 'adm_list_titlebar';

	$th  = "<thead><tr>";
	$th	.= "<th colspan=\"11\">List of Pending Requests</th>";
 	$th .= "</tr></thead>";
	
	$thead  = "<thead><tr>";
	$thead .= "<td class=\"".$class."\"><b>No.</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Batch No.</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Date Request</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Department</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Patient No.</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Sex</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Family Name</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Name</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Birthdate</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Request Status</b></td>";
	$thead .= "<td class=\"".$class."\"><b>Details</b></td>";
	$thead .= "</tr></thead>";
	
	$thead1 = $th.$thead;
	
	$objResponse->addAssign($tbId,"innerHTML", $thead1);
	//function PopulateRadioRequest(&$objResponse,$searchkey, $pgx, $thisfile, $rpath){
	PopulateRadioRequest($objResponse, $searchkey, $subDept, $pgx, $thisfile, $rpath);
	
	return $objResponse;
*/
}

$xajax->processRequest();
?>