<?php
	require('./roots.php');
	include_once($root_path.'modules/laboratory/seg_lab_cron_destination.php');
	include_once($root_path.'modules/laboratory/seg_lab_cron_source.php');
	#require($root_path.'include/inc_environment_global.php');
    
	# Establish db connection
	# Added by VAN 11-18-09
	require_once($root_path.'include/inc_hclab_connection.php');

	global $db, $db_hclab, $dblink_hclab_ok;

	$srcObj = new SourceDB();		# Open source connection.

	if (!$srcObj)
		ShowErrorMsg('No connection to source db opened!');
	else {

				# Open destination connection.
				$destObj = new DestinationDB();
				if (!$destObj)
						ShowErrorMsg('No connection to destination db opened!');
				else {
						$bSuccess = true;      // Initialize status tracker.

						# Replicate the Patients' lab results to SEGWORKS hisDB.
						if ($dblink_hclab_ok) {
								#echo "Locking bot...";
								echo "Fetching lab results...";

								#validate if there are results for past 1 hour  $max_hour
								if ($withrestofetch = $srcObj->hasLabResultsToFetch()) {
										$rowres = $srcObj->rowres;
										$counter_row = $rowres['COUNTER'];

										$destObj->beginTransaction();

										#get the results for past 1 hour $max_hour
										$hdr_rs = $srcObj->getLabResults();
										$res_count = $srcObj->count;
										$cnt = 0;
										if ($res_count){
												while ($row = $hdr_rs->FetchNextObject()) {
													 #delete the existing lab result with this order_no
													 $destObj->deleteResultByOrderNo($row->PRH_ORDER_NO);

													 #save the new result in SEGHIS seg_lab_results and seg_lab_results_details
													 $destObj->saveResultHeader($row->PRH_TRX_NUM, $row->PRH_TRX_DT_2, $row->PRH_TRX_STATUS,
																												$row->PRH_ORDER_NO, $row->PRH_ORDER_DT_2, $row->PRH_LOC_CODE,
																												$row->PRH_DR_CODE, $row->PRH_PAT_ID, $row->PRH_PAT_CASENO,
																												$row->PRH_CLI_INFO, $row->PRH_PRIORITY, $row->PRH_LAB_NO,
																												$row->PRH_TEST_CODE, $row->PRH_TEST_TYPE, $row->PRH_TG_CODE,
																												$row->PRH_CTL_SEQNO);

														if ($destObj->bIsOk) {
																$det_rs = $srcObj->getLabResultDetails($row->PRH_TRX_NUM);
																$det_count = $srcObj->count;
																if ($det_count){
																		while ($row_detail = $det_rs->FetchNextObject()) {
																				$destObj->saveResultDetails($row_detail->PRD_TRX_NUM, $row_detail->PRD_LINE_NO,
																																		$row_detail->PRD_TEST_CODE, $row_detail->PRD_TEST_NAME,
																																		$row_detail->PRD_DATA_TYPE, $row_detail->PRD_RESULT_VALUE,
																																		$row_detail->PRD_UNIT, $row_detail->PRD_RESULT_FLAG,
																																		$row_detail->PRD_RANGE, $row_detail->PRD_RESULT_STATUS,
																																		$row_detail->PRD_TEST_COMMENT, $row_detail->PRD_MLT_CODE,
																																		$row_detail->PRD_MLT_NAME, $row_detail->PRD_REPORTED_DT,
																																		$row_detail->PRD_PERFORMED_LAB_CODE, $row_detail->PRD_PERFORMED_LAB_NAME,
																																		$row_detail->PRD_PARENT_ITEM);
																		}#end while ($row_detail = $det_rs->FetchNextObject())
																}#end	if ($det_count){

																// Update the tracker of records to fetch from HCLAB ...
																$date_served = date("Y-m-d h:i:s",strtotime($row->PRH_TRX_DT_2));

																#added by VAN 08-10-09
																#get the respective reference no of a given order no from LIS
																$rsOrder = $destObj->getLabRefno($row->PRH_ORDER_NO);
																$row_ref=$rsOrder->FetchRow();

																$rsCode = $destObj->getTestCode($row->PRH_TEST_CODE);
																$code_count = $destObj->count;
																#echo "<br>getTestCode = ".$destObj->qry;
																if ($code_count)
																		$rowCode = $rsCode->FetchRow();

																#transferred by VAN 03-02-2011
																#update the request if served and if there is a result
																$destObj->DoneRequest($row_ref['refno'],$rowCode['service_code'],$date_served);

																$cnt++;
														}#end if ($destObj->bIsOk)

												}#end while ($row = $hdr_rs->FetchNextObject())
										}

								}#end ($srcObj->hasLabResultsToFetch($order_no_array))

								if (!($destObj->bIsOk)) $destObj->failTransaction();

								$destObj->endTransaction();

								// Release lock on fetch tracker ...
								#echo "<br>Lock released...";
								echo "<br>End of fetching lab results. There are ".$cnt." records fetched...";
								#commented by VAN 09-02-2010
								#$srcObj->releaseFetchLock($db);
								if ($destObj->bIsOk){
										#if ($order_no_array)
										if ($counter_row)
											echo '<br>laboratory results were successfully fetched..';
										else{
											echo '<br>no laboratory results were fetched..';
										}
								}else
										echo '<br>fetching of laboratory results FAILED..';
						 #}#end if ($srcObj->isAllowedToFetchLabResults($db))
					 }#end  if ($dblink_hclab_ok)
				}#end if (!$destObj)  -false
		} #end if (!$srcObj)  - false
?>
