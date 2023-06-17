<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
//require($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
//include radio-request common
require_once($root_path.'modules/radiology/ajax/radio-done-request.common.php');

#added by VAN 06-18-08
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_ward.php');

require_once($root_path.'include/care_api_classes/class_person.php');
#--------------------

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30);


function PopulateRadioDoneRequest($tbId, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir, $encounter_nr='', $is_doctor=0, $pid='', $is_perpatient=0){
	$objResponse = new xajaxResponse();

	#$objResponse->addAlert("PopulateRadioDoneRequest :: ajax : tbid =".$tbId. "\n tbody = ".$tbody."\n searchkey = ".$searchkey."\n sub_dept_nr=".$sub_dept_nr."\n pgx=".$pgx."\n thisfile=".$thisfile." \n rpath= ".$rpath."\n mode=".$mode."\n oitem=".$oitem."\n odir=". $odir);
	//Display table header
	ColHeaderRadioRequest($objResponse,$tbId, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir, $is_perpatient);

	//Paginate & display list of done radiology request
	PaginateRadioDoneRequestList($objResponse,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir,$oitem, $encounter_nr, $is_doctor, $pid, $is_perpatient);

	return $objResponse;
}#end of function PopulateRadioDoneRequest

function PaginateRadioDoneRequestList(&$objResponse,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='create_dt', $encounter_nr='', $is_doctor=0, $pid='', $is_perpatient=0){
	global $date_format;
	$radio_obj=new SegRadio();
	$dept_obj=new Department;
	$ward_obj = new Ward;
	$person_obj=new Person();

#$objResponse->addAlert("PaginateRadioDoneRequestList \n searchkey=".$searchkey. "\n pgx=".$pgx."\n thisfile=".$thisfile."\n path=".$rpath);

	#Initialize variables for search..
/*
	$totalcount = 0;
	$odir='ASC';
	$oitem='create_dt';
*/
	#Instantiate paginator  //$HTTP_SESSION_VARS['sess_searchkey']
	$pagen=new Paginator($pgx,$thisfile,$searchkey,$rpath, $oitem, $odir);

	#$objResponse->addAlert("pagen=".$pagen);

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	#Get the max nr of rows from global config
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	# Last resort, use the default defined at the start of this page
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS);
		else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);


#	if(($mode == 'search' || $mode == 'paginate')&&!empty($searchkey)){
		# Convert other wildcards
		$searchkey=strtr($searchkey,'*?','%_');
#	}

		$cond= '';
		if ($is_perpatient){
			if ($encounter_nr)
				$cond= "AND r_serv.pid='$pid' AND r_serv.encounter_nr='$encounter_nr'";
			else
				$cond= "AND r_serv.pid='$pid'";
		}


//	$encounter=& $radio_obj->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(),$pgx,$oitem,$odir);
    #added by daryl
	$ergebnis = &$radio_obj->searchLimitBasicInfoRadioDone($searchkey,$sub_dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir, $is_doctor, $encounter_nr, $cond);
     $total = $radio_obj->FoundRows();
    
    $ergebnis_walk = &$radio_obj->searchBasicInfoRadioDone_walk($searchkey,$sub_dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir, $is_doctor, $encounter_nr, $cond);
    $total_walk = $radio_obj->FoundRows();


	//count all records
/*
if ($is_doctor){
	$totalcount = $radio_obj->rec_count;
	$linecount = $totalcount;
	$pagen->setTotalBlockCount($linecount);
	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);
	}else{
		@$radio_obj->_searchBasicInfoRadioDone($searchkey,$sub_dept_nr);
		$totalcount=$radio_obj->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);
	}

	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
}else{
*/$totalcount = $total + $total_walk;
	if ($is_doctor){
        $totalcount = $radio_obj->record_count;
	}
	$linecount=$radio_obj->LastRecordCount();
   
	$pagen->setTotalBlockCount($linecount);

	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);
	}else{
		#@$radio_obj->_searchBasicInfoRadioDone($searchkey,$sub_dept_nr);
		#$totalcount=$radio_obj->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);
	}

	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
#}
   
	$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
	if ($linecount)
		$textResult = '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';

	else
		$textResult = '<hr width=80% align=left>'.str_replace('~nr~','0',$LDSearchFound);

	$objResponse->addAssign('textResult',"innerHTML", $textResult);

	$my_count=$pagen->BlockStartNr();

#added by dayl
$all_results = array();
    if (($ergebnis) || ($ergebnis_walk)){
	if ($ergebnis){
                while($result = $ergebnis->FetchRow()){
                    $all_results[] = $result;
                }
            }

            if($ergebnis_walk){
                while($results = $ergebnis_walk->FetchRow()){
                    $all_results[] = $results;
                }
   }
		$borrow_details = '';
		$is_borrowed =0;

    
        foreach($all_results as $rowRequest ){
			switch($rowRequest['sex']){
				case 'f': $gender = '<img src="../../gui/img/common/default/spf.gif" >'; break;
				case 'm': $gender = '<img src="../../gui/img/common/default/spm.gif">'; break;
				default: $gender = '&nbsp;'; break;
			}
			switch($rowRequest['is_urgent']){
#				case '0': $priority = '<img src="../../gui/img/common/default/spf.gif" >'; break;
#				case '1': $priority = '<img src="../../gui/img/common/default/spm.gif">'; break;
				case '0': $priority = '<span style="font:bold 11px Arial; color:#003366">Normal</span>'; break;
				case '1': $priority = '<span style="font:bold 11px Arial; color:red">Urgent</span>'; break;
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

			#added by VAN 06-18-08
			#$objResponse->addAlert("type = ".$result["encounter_type"]);
			if ($rowRequest['encounter_type']==1){
					$enctype = "ERPx";
					$location = "EMERGENCY ROOM";
			}elseif ($rowRequest['encounter_type']==2){
					#$enctype = "OUTPATIENT (OPD)";
					$enctype = "OPDx";
					$dept = $dept_obj->getDeptAllInfo($rowRequest['current_dept_nr']);
					$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}elseif (($rowRequest['encounter_type']==3)||($rowRequest['encounter_type']==4)){
					if ($rowRequest['encounter_type']==3)
						$enctype = "INPx (ER)";
					elseif ($rowRequest['encounter_type']==4)
						$enctype = "INPx (OPD)";

					$ward = $ward_obj->getWardInfo($rowRequest['current_ward_nr']);
					$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))."&nbsp;&nbsp;&nbsp;Rm # : ".$rowRequest['current_room_nr'];
			}else{
					$enctype = "WIPx";
					#$dept = $dept_obj->getDeptAllInfo($rowRequest['current_dept_nr']);
					#$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
					$location = "WALK-IN";
				}
			#------------------------

			$rs = $radio_obj->getBorrowedInfo($rowRequest['batch_nr']);

			if ($rs){
					$row2= $rs->FetchRow();
					if((string)$row2["remarks"]=="")
							$strRemarks="None";
							else
								$strRemarks=$row2["remarks"];

					if ($row2["is_borrowed"]==1) {
						$borrower_name=$row2['borrower'];
						if($borrower_name=="")
							$borrower_name=$row2['borrower_name'];

							$borrow_details = 'Borrower : '.mb_strtoupper($borrower_name).' <br>
																 Date Borrowed : '.date('m/d/Y',strtotime($row2['date_borrowed'])).'<br>
																 Time Borrowed : '.date('h:i A',strtotime($row2['time_borrowed'])).
																 "</br>\nRemarks: ".$strRemarks;                           #added by angelo m. 08.11.2010
							$is_borrowed = 1;
					}else{


							$borrow_details = 'Still Available';
							$is_borrowed = 0;
					}
			}else{
					 $borrow_details = 'Still Available';
					 $is_borrowed = 0;
			}


			$objResponse->addScriptCall("jsRadioRequest",$sub_dept_nr,$my_count,$rowRequest['batch_nr'],$rowRequest['refno'],
						$date_request,$rowRequest['dept_short_name'],$rowRequest['pid'],$rowRequest['rid'],$gender,
						$lname,$fname,$date_birth, ucwords(strtolower($rowRequest['status'])),$priority,$enctype,$location,$rowRequest['service_code'], $is_perpatient, $is_borrowed, $borrow_details);
			$my_count++;
#			jsRadioRequest(tbodyId,No,batchNo,dateRq,sub_dept_name,pid,lName,fName,bDate,rStatus)
		}# end of while loop
	}else{ # else of if-stmt 'if ($ergebnis)'
			$objResponse->addScriptCall("jsRadioNoFoundRequest",$sub_dept_nr);
	}# end of else-stmt 'if ($ergebnis)'

		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();

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
			'										<div id="pageShow" style="float:left; margin-left:10px"> '.
			'											<span>List of Pending Requests</span> '.
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

//	return $objResponse;
}

function makeSortLink($txt='SORT',$class='',$item,$oitem,$odir='ASC',$sub_dept_nr='',$width=''){

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
	$td = "<td class=\"".$class."\" width=\"".$width."\" align=\"center\" onClick=\"jsSortHandler('$item','$oitem','$dir','$sub_dept_nr');\">".$img."<b>".$txt."</b></td> \n";
	return $td;
}

//$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;
function ColHeaderRadioRequest(&$objResponse,$tbId, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir, $is_perpatient=0){
#	$objResponse = new xajaxResponse();
	global $root_path;
	#$append = '&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&dept_nr'.$dept_nr;
	$class= 'adm_list_titlebar';

	if ($is_perpatient){
		$th  = "<thead><tr><th colspan=\"16\" id=\"mainHead".$sub_dept_nr."\">";
	$th .= "</th></tr></thead>";

	$thead  = "<thead><tr style='font:bold 11.5px Arial; color:#000000'>";
	$thead .= "<td width=\"2%\" class=\"".$class."\"><b>No.</b></td> \n";
	$thead .= makeSortLink('Ref. No.',$class,'batch_nr',$oitem,$odir,$sub_dept_nr,'8%');
	$thead .= makeSortLink('Batch No.',$class,'batch_nr',$oitem,$odir,$sub_dept_nr,'8%');
	$thead .= makeSortLink('Date Requested',$class,'request_date',$oitem,$odir,$sub_dept_nr,'11%');
	$thead .= makeSortLink('Deparment',$class,'sub_dept_name',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Exam',$class,'service_code',$oitem,$odir,$sub_dept_nr);
	$thead .= makeSortLink('Status',$class,'status',$oitem,$odir,$sub_dept_nr,'8%');
	$thead .= makeSortLink('Priority',$class,'is_urgent',$oitem,$odir,$sub_dept_nr,'5%');
	$thead .= "<td class=\"".$class."\" align=\"center\"><b>Findings</b></td>";
	$thead .= "</tr></thead> \n";

	$thead1 = $th.$thead;
	}else{
		$th  = "<thead><tr><th colspan=\"16\" id=\"mainHead".$sub_dept_nr."\">";
		$th .= "</th></tr></thead>";

		$thead  = "<thead><tr style='font:bold 11.5px Arial; color:#000000'>";
		$thead .= "<td width=\"2%\" class=\"".$class."\"><b>No.</b></td> \n";
		$thead .= makeSortLink('Ref. No.',$class,'batch_nr',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Batch No.',$class,'batch_nr',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Date Requested',$class,'request_date',$oitem,$odir,$sub_dept_nr,'11%');
		$thead .= makeSortLink('Deparment',$class,'sub_dept_name',$oitem,$odir,$sub_dept_nr);
		$thead .= makeSortLink('Exam',$class,'service_code',$oitem,$odir,$sub_dept_nr);
		$thead .= makeSortLink('RID',$class,'rid',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Sex',$class,'sex',$oitem,$odir,$sub_dept_nr,'1%');
		$thead .= makeSortLink('Family Name',$class,'name_last',$oitem,$odir,$sub_dept_nr,'*');
		$thead .= makeSortLink('Name',$class,'name_first',$oitem,$odir,$sub_dept_nr);
		$thead .= makeSortLink('Birthdate',$class,'date_birth',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Status',$class,'status',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Priority',$class,'is_urgent',$oitem,$odir,$sub_dept_nr,'5%');
		$thead .= makeSortLink('Patient Type',$class,'encounter_type',$oitem,$odir,$sub_dept_nr,'5%');
		$thead .= makeSortLink('Location ',$class,'',$oitem,$odir,$sub_dept_nr,'10%');
		$thead .= "<td class=\"".$class."\" align=\"center\"><b>Findings</b></td>";
		$thead .= "</tr></thead> \n";

		$thead1 = $th.$thead;
	}
	$tbodyHTML = "<tbody id='TBodytab".$sub_dept_nr."'></tbody>";
#	$objResponse->addAlert("thead1 : \n".$thead1." tbodyHTML : \n".$tbodyHTML." prevNextTR : \n".$prevNextTR);
	$objResponse->addAssign($tbId,"innerHTML", $thead1.$tbodyHTML);
#$objResponse->addAlert(" ColHeaderRadioRequest");
	//function PaginateRadioDoneRequestList(&$objResponse,$searchkey, $pgx, $thisfile, $rpath){
#	PaginateRadioDoneRequestList($objResponse,$tbody,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir,$oitem);
#	PaginateRadioDoneRequestList($objResponse, $searchkey, $pgx, $thisfile, $rpath);

#	return $objResponse;
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
	//function PaginateRadioDoneRequestList(&$objResponse,$searchkey, $pgx, $thisfile, $rpath){
	PaginateRadioDoneRequestList($objResponse, $searchkey, $subDept, $pgx, $thisfile, $rpath);

	return $objResponse;
*/
}

$xajax->processRequest();
?>