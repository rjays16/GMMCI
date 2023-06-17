<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');

# Create person object
#include_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path.'modules/radiology/ajax/radio-request-list.common.php');

#define('MAX_BLOCK_ROWS',30);
define('MAX_BLOCK_ROWS',10);

	function deleteRadioServiceRequest($ref_nr){
		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;

#$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : ref_nr='$ref_nr'");
		if ($radio_obj->deleteRefNo($ref_nr)){
#			$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : Successfully deleted! ");
			$objResponse->addScriptCall("jsOnClick");
			$objResponse->addScriptCall("msgPopUp","Successfully deleted!");
		}else{
#			$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : Failed to deleted! ");
			$objResponse->addScriptCall("msgPopUp","Failed to delete!");
		}
#		$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : radio_obj->sql = '".$radio_obj->sql."' ");
		return $objResponse;
	}#end of function deleteRadioServiceRequest

#function PopulateRadioRequest($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir ){
function PopulateRadioRequest($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir, $mod=0){
	global $root_path;
	$objResponse = new xajaxResponse();	
	
	#$objResponse->addAlert("ajax : tbid =".$tbId. "\n tbody = ".$tbody."\n searchkey = ".$searchkey."\n sub_dept_nr=".$sub_dept_nr."\n pgx=".$pgx."\n thisfile=".$thisfile." \n rpath= ".$rpath."\n mode=".$mode."\n oitem=".$oitem."\n odir=". $odir);
	
	//Display table header 
	RadioRequestHeader($objResponse,$tbId,$sub_dept_nr,$oitem, $odir);
		
	//Paginate & display list of radiology request
	#PaginateRadioRequestlist($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem);
	PaginateRadioRequestlist($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem,$mod);
	
	return $objResponse;
}//end of PopulateRadioRequest


function RadioRequestHeader(&$objResponse,$tbId, $sub_dept_nr, $oitem, $odir){

	$tr  = "<thead>";
	$tr .= "<tr><th colspan=\"15\" id=\"mainHead".$sub_dept_nr."\"></th></tr>";
	$tr .= "<tr>";
	$tr .= "<th width=\"5%\"></th>";
	$tr .= makeSortLink('Batch No','refno', $oitem, $odir, $sub_dept_nr,'7%');
	$tr .= makeSortLink('RID','rid', $oitem, $odir, $sub_dept_nr,'7%', 'center');
	$tr .= makeSortLink('Name','ordername', $oitem, $odir, $sub_dept_nr,'45%', 'left');
	$tr .= "<th width=\"10%\">Hosp. No.</th>";
	$tr .= "<th width=\"5%\">Age</th>";
	$tr .= "<th width=\"7%\">Bdate</th>";
	$tr .= "<th width=\"5%\">Type</th>";
	$tr .= "<th width=\"10%\">Location</th>";
	$tr .= makeSortLink('Date Requested','request_date', $oitem, $odir, $sub_dept_nr,'10%');
	$tr .= "<th width=\"10%\">OR No.</th>";
	$tr .= "<th width=\"10%\">Priority</th>";  		
	#$tr .= "<th width=\"10%\">Status</th>";
	$tr .= "<th width=\"5%\">Details</th>";
	$tr .= "<th width=\"5%\">Delete</th>";	
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
	
} // end of RadioRequestHeader

function makeSortLink($txt='SORT',$item, $oitem,$odir='ASC', $subDeptNr='', $width='', $align='center'){
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

//added by daryl
        //sort by time
function compare_time($a, $b) {
    if ($a['request_time'] == $b['request_time']) return 0;
    return ($a['request_time'] > $b['request_time']) ? -1 : 1;
}
//end by daryl


function PaginateRadioRequestlist(&$objResponse, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $odir='ASC', $oitem='create_dt', $mod=0){
	global $date_format;
	global $db;
	$objRadio = new SegRadio();
	$dept_obj=new Department;
	$ward_obj = new Ward;
	
	#Instantiate paginator 
	$pagen = new Paginator($pgx, $thisfile, $searchkey, $rpath, $oitem, $odir);
	
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	# Last resort, use the default defined at the start of this page
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); 
    else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
		
#$pagen->setMaxCount(MAX_BLOCK_ROWS); 
#$objResponse->addAlert('searchkey = '.$searchkey);
#	if(($mode == 'search' || $mode == 'paginate') && !empty($searchkey)){
		$searchkey = strtr($searchkey, '*?', '%_');
#	}
// $objResponse->alert($oitem);
	$ergebnis = &$objRadio->searchLimitBasicInfoRadioRefNo($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir, $mod);
                $total = $objRadio->FoundRows();

    $ergebnis_walk = &$objRadio->_searchBasicInfoRadioRefNo_walkin($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir, $mod);
                $total_walk = $objRadio->FoundRows();

	#$objResponse->addAlert("PaginateRadioRequestlist:: SQL objRadio->sql = ".$objRadio->sql);
    // $objResponse->addAlert(&$objRadio->sql);
                $linecount=$total_walk+$total;
    
	#$linecount = $total;
	$pagen->setTotalBlockCount($linecount);
	
	#$totalcount = $total;
	if(isset($totalcount)&& $totalcount){
		$pagen->setTotalDataCount($totalcount);	
	}else{
		@$objRadio->_searchBasicInfoRadioRefNo($searchkey, $sub_dept_nr);	
		$totalcount = $objRadio->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);	
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
	
	#$objResponse->addAlert("PaginateRadioRequestlist:: ergebnis = ".$ergebnis);
#$objResponse->addAlert(" 2 : linecount=".$linecount." \n totalcount=".$totalcount);
	
	$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
	if ($linecount) 
        $textResult = '<hr width="80%" align="center">'.str_replace("~nr~",$linecount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
#		echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else 
		$textResult = '<hr width="80%" align="center">'.str_replace('~nr~','0',$LDSearchFound);
#		echo str_replace('~nr~','0',$LDSearchFound); 
	$objResponse->addAssign('textResult',"innerHTML", $textResult);
	
	$my_count=$pagen->BlockStartNr();


//edited by daryl
$all_results = array();

    if(($ergebnis) || ($ergebnis_walk)) {
		$temp=0;

                       if ($ergebnis)
                     {
                            while($result = $ergebnis->FetchRow()){
                                $all_results[] = $result;
                            }
                    }
                        if ($ergebnis_walk)  {
                                while($results = $ergebnis_walk->FetchRow())  {
                                      $all_results[] = $results;
                                }
                      }   

        usort($all_results, 'compare_time');                                 


    foreach($all_results as $row){
     
			$gender = $row['sex'];	
			$date_request = @formatDate2Local($row['request_date'], $date_format);
			
			#added by VAN 06-17-08
			$date_request = $date_request." ".date("h:i A",strtotime($row['request_time']));
			#------------------
			
			$lname = htmlentities($row['name_last']);
			$fname = htmlentities($row['name_first']);
			$mname = htmlentities($row['name_middle']);
			
			if ( (!empty($row['pid'])) || ($row['pid']!='')){
#				$comma = (!empty($lname))? $comma = ", ":$comma = ""; 
#				$name = ucwords($lname).$comma.ucwords($fname);
				$name = $lname.", ".$fname." ".$mname;
			}else{
				$name = $row['ordername'];
			}
			
			#added by VAN 01-15-08
			if ((!empty($row['parent_batch_nr'])) && (!empty($row['parent_refno'])))
				$repeat = 1;
			else	
				$repeat = 0;
			
			if ($row['encounter_type']==1){
				$enctype = "ERPx";
				$location = "EMERGENCY ROOM";
			}elseif ($row['encounter_type']==2){
				$enctype = "OPDx";
				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
				if ($row['encounter_type']==3)
					$enctype = "INPx (ER)";
				elseif ($row['encounter_type']==4)
					$enctype = "INPx (OPD)";
						
				$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
				$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room_nr'];
			}else{
				$enctype = "WPx";
				#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				#$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				$location = 'WALK-IN';
			}

					 if (($row['is_cash']==0)&&(!$row['charge_name'])){
							$or_no="Charge";
							$paid = 0;
							#$objResponse->alert($row["refno"]);
					 }else{
								$sql = "SELECT 
													  c.firm_id AS charge_name,
													  d.* 
													FROM
													  care_test_request_radio AS d 
													  LEFT JOIN care_insurance_firm AS c 
													    ON c.hcare_id = d.request_flag 
													WHERE refno='".trim($row["refno"])."'
													 AND d.status NOT IN (
													    'deleted',
													    'hidden',
													    'inactive',
													    'void'
													  ) 
													  AND request_flag IS NOT NULL 
													LIMIT 1 ";
													
								 $res=$db->Execute($sql);
								 $rows=$res->RecordCount();
								 $result_paid = $res->FetchRow();
								 $or_no = '';

								 if ($rows==0){
										$paid = 0;
								 }else{
										 if ($row["is_cash"]==1)
										 $paid = 1;
										 else
											 $paid = 0;

										 if ($result_paid["request_flag"]=='paid'){
												$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																					FROM seg_pay_request AS pr
																					INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$row['pid']."'
																					WHERE pr.ref_source = 'RD' AND pr.ref_no = '".trim($row["refno"])."'
																					AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
														$rs_paid = $db->Execute($sql_paid);
														if ($rs_paid){
																$result2 = $rs_paid->FetchRow();
																$or_no = $result2['or_no'];
														}
														#added by VAN 06-03-2011
											 			#for temp workaround
											 			if (!$or_no){
												 			$sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='RD' AND refno='".trim($row["refno"])."' AND is_deleted=0";
												 			$res_manual=$db->Execute($sql_manual);
												 			$row_manual_count=$res_manual->RecordCount();
												 			$row_manual = $res_manual->FetchRow();
			
												 			$or_no = $row_manual['control_no'];
			
											 			}
			
										 }elseif ($result_paid["request_flag"]=='charity'){
												$sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																					FROM seg_granted_request AS pr
																					WHERE pr.ref_source = 'RD' AND pr.ref_no = '".trim($row["refno"])."'
																					LIMIT 1";
												$rs_paid = $db->Execute($sql_paid);
												if ($rs_paid){
														$result2 = $rs_paid->FetchRow();
														$or_no = 'CLASS D';
												}
										 }elseif (($result_paid["request_flag"]!=NULL)||($result_paid["request_flag"]!="")){
											 if ($withOR)
													$or_no = $off_rec;
			else	
													$or_no = $result_paid["charge_name"];
										 }
								}
					 }
			$bdate = date("m/d/Y",strtotime($row['date_birth']));

            if (empty($row['age'])){
                $age_ = '<img src="../../gui/img/common/default/frage.gif">';
            }else{
                $age_=$row['age'];
            }

			#$objResponse->addAlert('onqueue = '.$row['charge_name']);
			#$objResponse->addScriptCall("jsListRows",$sub_dept_nr, $my_count,$row['refno'],$row['rid'],$name,$gender,$date_request,$row['is_urgent'],$repeat,$paid,$row['charge_name'], $enctype,$location,$row['pid'],$row['age'],$bdate);
																							#sub_dept_nr,No,refNo,rid,name,sex,dateRequest,priority, repeat, charge_type, enctype,location,pid,age,bdate
            $objResponse->addScriptCall("jsListRows",$sub_dept_nr, $my_count,$row['refno'],$row['rid'],$name,$gender,$date_request,$row['is_urgent'],$repeat,$paid,$or_no,$enctype,$location,$row['pid'],$row['encounter_nr'],$age_,$bdate);
			$my_count++;
		}//end while loop
	//end if (ergebnis)
	}else{
		//$tr = "<tr><td colspan=\"8\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:\"Arial\",Courier, mono; font-style:Bold; font-weight:Bold; font-size:12px;\">NO MATCHING REQUEST FOUND</td></tr>";
		$tr = "<tr><td colspan=\"15\"  style=\"\">No requests available at this time...</td></tr>";
		$objResponse->addAssign("TBodytab".$sub_dept_nr, "innerHTML", $tr);
	}

		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("PaginateRadioRequestlist : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);	
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
}// end of function PaginateRadioRequestlist 


$xajax->processRequest();
?>