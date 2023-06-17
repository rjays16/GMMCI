<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
//require($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
//include radio-undone-request common
require_once($root_path.'modules/radiology/ajax/radio-unified-results.common.php');

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 


function saveScheduledRequest($mode, $batch_nr='', $scheduled_date='', $scheduled_time='', $instructions='', $remarks='',
																						$sub_dept_nr='', $pmonth='', $pday='', $pyear=''){
		global $root_path, $date_format, $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();

		$radio_obj=new SegRadio();
		$ok = TRUE;
		if ($radio_obj->getScheduledRadioRequestInfo($batch_nr,TRUE)){
				$objResponse->addScriptCall("msgPopUp","This request has been scheduled already.");
				$ok = FALSE;
		}

		if ($ok){
				$scheduled_dt = @formatDate2STD($scheduled_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
				#commented by VAN 03-26-08
				#$scheduled_dt .= " ".$scheduled_time;
				$serialized_instructions = serialize($instructions);
				$msg = "batch_nr = '".$batch_nr."' \n".
								"scheduled_date = '".$scheduled_date."' \n".
								"scheduled_time = '".$scheduled_time."' \n".
								"scheduled_dt = '".$scheduled_dt."' \n".
								"serialized_instructions = '".$serialized_instructions."' \n".
								"instructions = '".$instructions."' \n".
								"instructions : '".print_r($instructions,true)."' \n".
								"remarks = '".$remarks."' \n".
								" HTTP_SESSION_VARS['sess_temp_fullname'] = '".$HTTP_SESSION_VARS['sess_temp_fullname']."'";
#$objResponse->addAlert("radio-undone-request.server.php : saveScheduledRequest :: ".$msg);
				#added by VAN 03-26-08
				if (empty($HTTP_SESSION_VARS['sess_temp_fullname']))
						$encoder = $HTTP_SESSION_VARS['sess_user_name'];
				else    
						$encoder = $HTTP_SESSION_VARS['sess_temp_fullname'];
				
				$ok = $radio_obj->createRadioSchedule($batch_nr, $scheduled_dt, $scheduled_time, $serialized_instructions, $remarks, $encoder);
				if ($ok){
						$objResponse->addScriptCall("resetForm"); # reset the form
						_PopulateRadioScheduledRequests($objResponse, $mode, $sub_dept_nr, $pmonth, $pday, $pyear);
						$objResponse->addScriptCall("msgPopUp","Successfully saved the schedule form.");
				}else{
						$objResponse->addScriptCall("msgPopUp","Failed to save the schedule form.");
				}
		}# end of 'if ($ok)'
		return $objResponse;
}#end of function saveScheduledRequest

function updateScheduledRequest($mode, $batch_nr='', $scheduled_date='', $service_date='', $scheduled_time='', $instructions='', $remarks='',
																						$sub_dept_nr='', $pmonth='', $pday='', $pyear=''){
		global $root_path, $date_format, $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();
		$radio_obj=new SegRadio();
		
		if ($radio_obj->getScheduledRadioRequestInfo($batch_nr,TRUE)){
				$ok = TRUE;
		}else{
				$objResponse->addScriptCall("msgPopUp","Failed to update the schedule form. \n Close this window and try again.");
				$ok = FALSE;        
		}

		if ($ok){
				$msg = "";
				if($service_date){
						$service_dt = @formatDate2STD($service_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
						if ($radio_obj->updateRadioRequestServiceDate($batch_nr, $service_dt)){
								$msg .= "Successfully updated the Date of Service. \n";
						}else{
								$msg .= "Failed to update the Date of Service. \n";
						}
				}
				$scheduled_dt = @formatDate2STD($scheduled_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
				#commented by VAN 03-26-08
				#$scheduled_dt .= " ".$scheduled_time;
				$serialized_instructions = serialize($instructions);

			 #added by VAN 03-26-08
				if (empty($HTTP_SESSION_VARS['sess_temp_fullname']))
						$encoder = $HTTP_SESSION_VARS['sess_user_name'];
				else    
						$encoder = $HTTP_SESSION_VARS['sess_temp_fullname'];
				
				$ok = $radio_obj->updateRadioSchedule($batch_nr, $scheduled_dt, $scheduled_time, $service_dt, $serialized_instructions, $remarks, $encoder);

				if ($ok){
						$objResponse->addScriptCall("resetForm"); # reset the form
						_PopulateRadioScheduledRequests($objResponse, $mode, $sub_dept_nr, $pmonth, $pday, $pyear);
						$msg .= "Successfully updated the schedule form. \n";
				}else{
						$msg .= "Failed to update the schedule form. \n";
				}
				$objResponse->addScriptCall("msgPopUp",$msg);
		}# end of 'if ($ok)'
		return $objResponse;
}#end of function updateScheduledRequest


function deleteScheduledRadioRequest($mode, $batch_nr='',$sub_dept_nr='', $pmonth='', $pday='', $pyear=''){
		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;    

		if($radio_obj->deleteRadioSchedule($batch_nr)){
				_PopulateRadioScheduledRequests($objResponse, $mode, $sub_dept_nr, $pmonth, $pday, $pyear);
				$objResponse->addScriptCall("msgPopUp","Successfully deleted!");
		}else{
				$objResponse->addScriptCall("msgPopUp","Failed to delete!");
		}
		return $objResponse;
}# end of function deleteScheduledRadioRequest


function PopulateRadioScheduledRequests($mode, $sub_dept_nr, $pmonth, $pday, $pyear){
		$objResponse = new xajaxResponse();
#$objResponse->addAlert("radio-undone-request.server.php : PopulateRadioScheduledRequests :: mode = '".$mode."'");
		_PopulateRadioScheduledRequests($objResponse, $mode, $sub_dept_nr, $pmonth, $pday, $pyear);
		return $objResponse;
}#end of function PopulateRadioScheduledRequests

		/*
		 * access private
		*/
function _PopulateRadioScheduledRequests(&$objResponse, $mode, $sub_dept_nr, $pmonth, $pday, $pyear){
//    $objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;    

		$recordScheduledObj = $radio_obj->getScheduledRadioRequestInfo('','',$sub_dept_nr,date("m/d/Y",mktime(0, 0, 0, $pmonth, $pday, $pyear)));
#$objResponse->addAlert("he = ".$radio_obj->sql);
#$objResponse->addAlert("radio-undone-request.server.php : _PopulateRadioScheduledRequests :: recordScheduledObj = '".$recordScheduledObj."'");
#$objResponse->addAlert("radio-undone-request.server.php : _PopulateRadioScheduledRequests :: radio_obj->sql = '".$radio_obj->sql."'");
		$objResponse->addScriptCall("clearScheduledList",$mode);

		if (is_object($recordScheduledObj)){
				$myCount=1;
				while($scheduledHistory=$recordScheduledObj->FetchRow()){
#$objResponse->addAlert("radio-undone-request.server.php : _PopulateRadioScheduledRequests :: \n scheduledHistory : \n".print_r($scheduledHistory,true)."");
						$batch_nr = $scheduledHistory['batch_nr'];
						$service_code = $scheduledHistory['service_code'];
						$rid = $scheduledHistory['rid'];
						$status = $scheduledHistory['status'];

								# FORMATTING of Scheduled Date
						$scheduled_dt = $scheduledHistory['scheduled_dt'];
						#if (($scheduled_dt!='0000-00-00 00:00:00')  && ($scheduled_dt!=""))
						if (($scheduled_dt!='0000-00-00')  && ($scheduled_dt!=""))
								#$scheduled_time = @formatDate2Local($scheduled_dt,$date_format,'',TRUE); # return time ONLY
								$scheduled_time =  date("h:i A",strtotime($scheduledHistory['scheduled_time']));
						else
								$scheduled_time='';
						
						$patient_name=$scheduledHistory['name_last'].', '.$scheduledHistory['name_first'];
						if (!empty($scheduledHistory['name_middle'])){
								$patient_name .= ' <font style="font-style:italic; color:#FF0000">'.$scheduledHistory['name_middle'].'</font>';
						}
		
						if (!empty($scheduledHistory['modify_id'])){
								$scheduled_by = trim($scheduledHistory['modify_id']);
						}else{
								$scheduled_by = trim($scheduledHistory['create_id']);
						}            
						
						$temp_instructions = unserialize($scheduledHistory['instructions']);
						$instructions='';
						if (is_array($temp_instructions) && !empty($temp_instructions)){
								foreach($temp_instructions as $value){
										if (substr($value,0,1)=='0'){
												$instructions .= substr($value,2)."<br>";
										}else{
												$instruction_info = $radio_obj->getRadioInstructionsInfo($sub_dept_nr,$value);
												if($instruction_info){
														$instructions .= $instruction_info['instruction']."<br>";
												}                
										}
								}
						}
		#        $instructions="'".$instructions."'";
						$toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'.$myCount.'\').value, CAPTION,\'Instructions\',  '.
																'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '.
																'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
		
		
		
#            $editImg = 'src="'.$root_path.'gui/img/control/default/en/en_edit_icon_06.gif" border=0 width="20" height="21"';
#            $deleteImg = 'src="'.$root_path.'gui/img/control/default/en/en_trash_06.gif" border=0 width="20" height="21"';
						$option_edit = '<img name="edit'.$myCount.'" id="edit'.$myCount.'" '.$editImg.'>';
						$option_delete ='<img name="delete'.$myCount.'" id="delete'.$myCount.'" '.$deleteImg.' onClick="deleteScheduledRequest('.$batch_nr.','.$myCount.');"> ';

						$objResponse->addScriptCall("jsAddScheduledRequest",$mode,$myCount,$instructions,$batch_nr,
																$scheduled_time,$service_code,$rid, $patient_name,$scheduled_by,$status);

						$myCount++;
				}#end of while loop
		}else{
				#EMPTY
				$objResponse->addScriptCall("jsRadioNoFoundScheduledRequest",$mode);
		}
//    return $objResponse;
}#end of function _PopulateRadioScheduledRequests


function PopulateRadioUnscheduledRequest($sElem, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $oitem, $odir ){
		global $root_path;
		$objResponse = new xajaxResponse();    

#$objResponse->addAlert("radio-undone-request.server.php : PopulateRadioUndoneRequest :: sElem = '".$sElem."'");

		//Display table header 
		ColHeaderRadioUnscheduledRequest($objResponse, $sub_dept_nr, $oitem, $odir);

		//Paginate & display list of radiology undone request
		PaginateRadioUndoneRequestList($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem, 'unscheduled');
		if ($sElem) {
#$objResponse->addAlert("radio-undone-request.server.php : PopulateRadioUndoneRequest :: 1 sElem = '".$sElem."'");
				$objResponse->addScriptCall("endAJAXSearch",$sElem);
#        $objResponse->addScriptCall("endAJAXSearch",'search');
#$objResponse->addAlert("radio-undone-request.server.php : PopulateRadioUndoneRequest :: 2 sElem = '".$sElem."'");
		}
		return $objResponse;
}//end of PopulateRadioUnscheduledRequest

function ColHeaderRadioUnscheduledRequest(&$objResponse, $sub_dept_nr, $oitem, $odir){
#    $objResponse = new xajaxResponse();
		global $root_path;
		#$append = '&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&dept_nr'.$dept_nr;
		$class= 'adm_list_titlebar';

		$th  = "<thead><tr><th colspan=\"7\" id=\"mainHead".$sub_dept_nr."\">";
		$th .= "</th></tr></thead>";
		
		$thead  = "<thead><tr style='font:bold 11.5px Arial; color:#000000'>";
		$thead .= makeSortLink('RID',$class,'rid',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Lastname',$class,'name_last',$oitem,$odir,$sub_dept_nr);
		$thead .= makeSortLink('Firstname',$class,'name_first',$oitem,$odir,$sub_dept_nr);
		$thead .= makeSortLink('Patient Type',$class,'encounter_type',$oitem,$odir,$sub_dept_nr,'8%');
		$thead .= makeSortLink('Priority',$class,'is_urgent',$oitem,$odir,$sub_dept_nr,'5%');
		$thead .= "<td class=\"".$class."\" width=\"1%\" align=\"center\"></td>";
		$thead .= "<td class=\"".$class."\" width=\"1%\" align=\"center\"></td>";
		$thead .= "</tr></thead> \n";
		
		$thead1 = $th.$thead;
		$tbodyHTML = "<tbody id='person-list-body'></tbody>";
#$objResponse->addAlert("thead1 : \n".$thead1." tbodyHTML : \n".$tbodyHTML." prevNextTR : \n".$prevNextTR);
		$objResponse->addAssign('person-list',"innerHTML", $thead1.$tbodyHTML);
#$objResponse->addAlert("ColHeaderRadioUnscheduledRequest ::");
#    return $objResponse;
}#end of function ColHeaderRadioUnscheduledRequest


function PopulateRadioUndoneRequest($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir ){
		global $root_path;
		$objResponse = new xajaxResponse();    

#    $objResponse->addAlert("radio-undone-request.server.php : PopulateRadioUndoneRequest :: ");

		//Display table header 
		ColHeaderRadioUndoneRequest($objResponse,$tbId, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir);

		//Paginate & display list of radiology undone request
		PaginateRadioUndoneRequestList($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem);    
		
		return $objResponse;
}//end of PopulateRadioUndoneRequest

#function PopulateRadioRequest(&$objResponse,$tbodyId,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='create_dt'){
function PaginateRadioUndoneRequestList(&$objResponse,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='create_dt',$search_type=''){
		global $date_format;
		$radio_obj=new SegRadio();

#$objResponse->addAlert("PaginateRadioUndoneRequestList \n searchkey=".$searchkey. "\n pgx=".$pgx."\n thisfile=".$thisfile."\n path=".$rpath);

		#Initialize variables for search..
/*
		$totalcount = 0;
		$odir='ASC';
		$oitem='create_dt';
*/    
		#Instantiate paginator  //$HTTP_SESSION_VARS['sess_searchkey']
		$pagen=new Paginator($pgx,$thisfile,$searchkey,$rpath, $oitem, $odir);
		
#    $objResponse->addAlert("pagen=".$pagen);
		
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		#Get the max nr of rows from global config
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		# Last resort, use the default defined at the start of this page
		if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); 
		else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
		
		#added by VAN 07-31-08
		$searchkey = utf8_decode($searchkey);
		$searchkey = str_replace("^","'",$searchkey);
		$searchkey=addslashes($searchkey);
		#-------------
		
				# Convert other wildcards
				$searchkey=strtr($searchkey,'*?','%_');
		$ergebnis = &$radio_obj->searchRadioPatients($search_type,$searchkey,$sub_dept_nr,TRUE,$pagen->MaxCount(), $pgx);
#$objResponse->addAlert("type = ".$radio_obj->sql);

		//count all records
		$linecount=$radio_obj->LastRecordCount();
		$pagen->setTotalBlockCount($linecount);
		
		if(isset($totalcount)&&$totalcount){
				$pagen->setTotalDataCount($totalcount);        
		}else{
				$radio_obj->searchRadioPatients($search_type,$searchkey,$sub_dept_nr,FALSE,$pagen->MaxCount(), $pgx);//_searchBasicInfoRadioPending($search_type,$searchkey,$sub_dept_nr);
				$totalcount=$radio_obj->LastRecordCount();
				$pagen->setTotalDataCount($totalcount);
		}
		$pagen->setSortItem($oitem);
		$pagen->setSortDirection($odir);

		$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
		if ($linecount) 
				$textResult = '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
#        echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
		else 
				$textResult = '<hr width=80% align=left>'.str_replace('~nr~','0',$LDSearchFound);
#        echo str_replace('~nr~','0',$LDSearchFound); 
		$objResponse->addAssign('textResult',"innerHTML", $textResult);

		$my_count=$pagen->BlockStartNr();
		if ($ergebnis){
#$objResponse->addAlert("PaginateRadioUndoneRequestList 2 : \npgx='".$pgx."'; \ntotalcount='".$totalcount."'; \nlinecount='".$linecount."'; \ntextResult='".$textResult."'; \nmy_count='".$my_count."'; \ndate_format='".$date_format."'");
				while($rowRequest = $ergebnis->FetchRow()){
						$lname = htmlentities($rowRequest['name_last']);
						$fname = htmlentities($rowRequest['name_first']);
						#$sub_dept_name = htmlentities($rowRequest['sub_dept_id']);
						#edited by VAN 06-28-08
						$sub_dept_name = htmlentities($rowRequest['name_short']);
						
						$objResponse->addScriptCall("jsRadioRequest",$sub_dept_nr,$my_count,$rowRequest['batch_nr'],$rowRequest['refno'],
												$date_request,$sub_dept_name,$rowRequest['pid'],$rowRequest['rid'],$gender,
												$lname,$fname,$date_birth, $rowRequest['status'],$priority, $rowRequest['encounter_type'], $rowRequest['service_code']);
						$my_count++;
#            jsRadioRequest(tbodyId,No,batchNo,dateRq,sub_dept_name,pid,lName,fName,bDate,rStatus)
				}# end of while loop
		}else{ # else of if-stmt 'if ($ergebnis)'
						$objResponse->addScriptCall("jsRadioNoFoundRequest",$sub_dept_nr);        
		}# end of else-stmt 'if ($ergebnis)'

				# Previous and Next button generation
		$nextIndex = $pagen->nextIndex();
		$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("PaginateRadioUndoneRequestList : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);    
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
		
		if ($search_type=='unscheduled'){
				$title ="List of Unscheduled Requests";    
		}else{
				$title ="List of Pending Requests";
		}

		$img ='                                        <div id="pageFirst" class="'.$pageFirstClass.'" style="float:left" onclick="'.$pageFirstOnClick.'"> '.
						'                                            <img title="First" src="../../images/start.gif" border="0" align="absmiddle"/> '.
						'                                            <span title="First">First</span> '.
						'                                        </div> '.
						'                                        <div id="pagePrev" class="'.$pagePrevClass.'" style="float:left" onclick="'.$pagePrevOnClick.'"> '.
						'                                            <img title="Previous" src="../../images/previous.gif" border="0" align="absmiddle"/> '.
						'                                            <span title="Previous">Previous</span> '.
						'                                        </div> '.
						'                                        <div id="pageShow" style="float:left; margin-left:10px; text-align:center"> '.
						'                                            <span>'.$title.'</span> '.
						'                                        </div> '.
						'                                        <div id="pageLast" class="'.$pageLastClass.'" style="float:right" onclick="'.$pageLastOnClick.'"> '.
						'                                            <span title="Last">Last</span> '.
						'                                            <img title="Last" src="../../images/end.gif" border="0" align="absmiddle"/> '.
						'                                        </div> '.
						'                                        <div id="pageNext" class="'.$pageNextClass.'" style="float:right" onclick="'.$pageNextOnClick.'"> '.
						'                                            <span title="Next">Next</span> '.
						'                                            <img title="Next" src="../../images/next.gif" border="0" align="absmiddle"/> '.
						'                                        </div> ';
		$objResponse->addAssign("mainHead".$sub_dept_nr,"innerHTML", $img);
#$objResponse->addAlert("PaginateRadioUndoneRequestList : \n$img");        
//    return $objResponse;
}

function makeSortLink($txt='SORT',$class='',$item,$oitem,$odir='ASC',$sub_dept_nr='',$width='',$optional=''){
		$td = "<td class=\"".$class."\" width=\"".$width."\" align=\"center\" onClick=\"jsSortHandler('$item','$oitem','$dir','$sub_dept_nr');\" $optional>".$img."<b>".$txt."</b></td> \n";
		return $td;
}

function ColHeaderRadioUndoneRequest(&$objResponse, $tbId, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode,$oitem,$odir){
		global $root_path;
		#$append = '&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&dept_nr'.$dept_nr;
		$class= 'adm_list_titlebar';

		$th  = "<thead><tr><th colspan=\"7\" id=\"mainHead".$sub_dept_nr."\">";
		$th .= "</th></tr></thead>";
		
		$thead  = "<thead><tr style='font:bold 11.5px Arial; color:#000000'>";
		//$thead .= "<td width=\"2%\" class=\"".$class."\"><b>No.</b></td> \n";
		$thead .= makeSortLink('RID',$class,'rid',$oitem,$odir,$sub_dept_nr,'13%');
		$thead .= makeSortLink('Family Name',$class,'name_last',$oitem,$odir,$sub_dept_nr,'20%');
		$thead .= makeSortLink('Name',$class,'name_first',$oitem,$odir,$sub_dept_nr);
		$thead .= makeSortLink('Patient Type',$class,'encounter_type',$oitem,$odir,$sub_dept_nr,'12%');
		$thead .= makeSortLink('Deparment',$class,'sub_dept_name',$oitem,$odir,$sub_dept_nr,'22%');
		$thead .= "<td class=\"".$class."\" align=\"center\" width='7%'><b>Details</b></td>";
		$thead .= "</tr></thead> \n";
		
		$thead1 = $th.$thead;
		$tbodyHTML = "<tbody id='TBodytab".$sub_dept_nr."'></tbody>";
#    $objResponse->addAlert("thead1 : \n".$thead1." tbodyHTML : \n".$tbodyHTML." prevNextTR : \n".$prevNextTR);
		$objResponse->addAssign($tbId,"innerHTML", $thead1.$tbodyHTML);
#$objResponse->addAlert(" ColHeaderRadioUndoneRequest");
}#end of function ColHeaderRadioUndoneRequest

#added by VAN 07-09-08
function saveProcessRequest($mode, $batch_nr='', $sizes){
		global $root_path, $date_format, $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();

		$radio_obj=new SegRadio();

		$list_sizes = array();
		foreach ($sizes as $i=>$v) {
				if ($v) $list_sizes[] = $v;
		}
		
		#$objResponse->addAlert(print_r($list_sizes,true));
						
		if ($list_sizes!=NULL){
				#$srvObj->clearDiscounts($data['refno']);
				$radio_obj->clearRadioProcess($batch_nr);
				#$srvObj->addDiscounts($data['refno'],$bulk);
		#    $ok = $radio_obj->createRadioProcess($batch_nr, $list_sizes);
						$ok = $radio_obj->createRadioProcess($batch_nr, $list_sizes);
				#    $objResponse->addAlert($radio_obj->sql);        
		}
		
		
		if ($ok){
				$objResponse->addScriptCall("msgPopUp","Successfully processed the request.");
		}else{
				$objResponse->addScriptCall("msgPopUp","Failed to processed the request.");
		}

		return $objResponse;
}#end of function saveScheduledRequest

function updateProcessRequest($mode, $batch_nr='', $sizes){
		global $root_path, $date_format, $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();
		$radio_obj=new SegRadio();
		
		if ($radio_obj->getScheduledRadioRequestInfo($batch_nr,TRUE)){
				$ok = TRUE;
		}else{
				$objResponse->addScriptCall("msgPopUp","Failed to update the schedule form. \n Close this window and try again.");
				$ok = FALSE;        
		}

		if ($ok){
				$msg = "";
				if($service_date){
						$service_dt = @formatDate2STD($service_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
						if ($radio_obj->updateRadioRequestServiceDate($batch_nr, $service_dt)){
								$msg .= "Successfully updated the Date of Service. \n";
						}else{
								$msg .= "Failed to update the Date of Service. \n";
						}
				}
				$scheduled_dt = @formatDate2STD($scheduled_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
				#commented by VAN 03-26-08
				#$scheduled_dt .= " ".$scheduled_time;
				$serialized_instructions = serialize($instructions);
				#added by VAN 03-26-08
				if (empty($HTTP_SESSION_VARS['sess_temp_fullname']))
						$encoder = $HTTP_SESSION_VARS['sess_user_name'];
				else    
						$encoder = $HTTP_SESSION_VARS['sess_temp_fullname'];
				
				$ok = $radio_obj->updateRadioSchedule($batch_nr, $scheduled_dt, $scheduled_time, $service_dt, $serialized_instructions, $remarks, $encoder);

				if ($ok){
						$objResponse->addScriptCall("resetForm"); # reset the form
						_PopulateRadioScheduledRequests($objResponse, $mode, $sub_dept_nr, $pmonth, $pday, $pyear);
						$msg .= "Successfully updated the schedule form. \n";
				}else{
						$msg .= "Failed to update the schedule form. \n";
				}
				$objResponse->addScriptCall("msgPopUp",$msg);
		}# end of 'if ($ok)'
		return $objResponse;
}#end of function updateScheduledRequest

#-------------------------

$xajax->processRequest();
?>