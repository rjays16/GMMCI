<?php
	 /*--- created by cha on 04-22-09 ---*/
	 /*function populateHospitalService($service_type,$page)
	 {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$srvObj= new Price_Adjustments();
				$offset = $page * $maxRows;
				$total_srv = $srvObj->countPriceListRecord($service_type,0,$maxRows,$offset);
				$total = $srvObj->count;

				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$dataRow=$srvObj->viewPriceList($service_type,0,$maxRows,$offset);
				//$objResponse->addAlert("sql = ".$srvObj->sql);
				$rows=0;

				$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->call("clearList","PriceList");
				$serviceCodeArray=array();
				if ($dataRow) {
						$rows=$dataRow->RecordCount();
						$cnt=0;
						while($result=$dataRow->FetchRow())
						{
								if($service_type==1 or $service_type==2)
								{
										$objResponse->call("viewPriceList","PriceList",trim($result["service_code"]),trim($result["name"]),trim($result["price_cash"]),trim($result["price_charge"]));
										$serviceCodeArray[$cnt]=$result["service_code"];
										$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
								}
								if($service_type==3 or $service_type==4)
								{
										$objResponse->call("viewPriceList","PriceList",trim($result["bestellnum"]),trim($result["artikelname"]),trim($result["price_cash"]),trim($result["price_charge"]));
										$serviceCodeArray[$cnt]=$result["bestellnum"];
										$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
								}
								if($service_type==5)
								{
										$objResponse->call("viewPriceList","PriceList",trim($result["service_code"]),trim($result["name"]),trim($result["price"]),trim($result["price"]));
										$serviceCodeArray[$cnt]=$result["service_code"];
										$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
								}
								$cnt++;

						}#end of while

				} #end of if
				if (!$rows) $objResponse->call("viewPriceList","PriceList",NULL);
				$objResponse->call("endAJAXSearch",$sElem);

				return $objResponse;
		} */

		function populateLabServiceList($searchId, $keyword, $page)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$labObj = new SegLab();

			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$offset = $page * $maxRows;
			$total_srv = $labObj->SearchService('',$keyword,0,$maxRows,$offset,'',0,1);
			$total = $labObj->count;
			$lastPage = floor($total/$maxRows);

			if ((floor($total%10))==0)
					$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;
			$dataRow = $labObj->SearchService('',$keyword,0,$maxRows,$offset,'',0, 0);

			$rows=0;
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total,"edit_price");
			$objResponse->call("clearList","PriceList");
			$serviceCodeArray = array();
			if($dataRow)
			{
				$rows=$dataRow->RecordCount();
				$cnt=0;
				while($result=$dataRow->FetchRow()) {
					$objResponse->call("viewPriceList","PriceList",trim($result["service_code"]),trim($result["name"]),trim($result["price_cash"]),trim($result["price_charge"]));
					$serviceCodeArray[$cnt]=$result["service_code"];
					$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
					$cnt++;
				}#end of while
			}
			if (!$rows) $objResponse->call("viewPriceList","PriceList",NULL);
			$objResponse->call("endAJAXSearch",$sElem);
			return $objResponse;
		}

		function populateRadioServiceList($searchId, $keyword, $page)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$radObj = new SegRadio();

			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$offset = $page * $maxRows;
			$total_srv = $radObj->countSearchService('',$keyword,$maxRows,$offset);
			$total = $radObj->count;
			$lastPage = floor($total/$maxRows);

			if ((floor($total%10))==0)
					$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;
			$dataRow = $radObj->SearchService('',$keyword,$maxRows,$offset);
			//$objResponse->alert($radObj->sql);
			$rows=0;
            //$objResponse->alert("P:" . $page . " LP:" . $lastPage . " MR:" . $maxRows . " OffSet " . $offset . " Tot:" . $total);
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total,"edit_price");
			$objResponse->call("clearList","PriceList");
			$serviceCodeArray = array();
			if($dataRow)
			{
				$rows=$dataRow->RecordCount();
				$cnt=0;
				while($result=$dataRow->FetchRow()) {
					$objResponse->call("viewPriceList","PriceList",trim($result["service_code"]),trim($result["name"]),trim($result["price_cash"]),trim($result["price_charge"]));
					$serviceCodeArray[$cnt]=$result["service_code"];
					$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
					$cnt++;
				}#end of while
			}
			if (!$rows) $objResponse->call("viewPriceList","PriceList",NULL);
			$objResponse->call("endAJAXSearch",$sElem);
			return $objResponse;
		}

		function populatePharmaServiceList($searchId, $keyword, $page)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$pharmaObj = new SegPharmaProduct();

			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$offset = $page * $maxRows;

			$dataRow = $pharmaObj->search_products_for_tray($keyword, '', '', $offset, $maxRows);
			if($dataRow)
			{
				$total = $pharmaObj->FoundRows();
				$lastPage = floor($total/$maxRows);
				if ($page > $lastPage) $page=$lastPage;

				$rows=$dataRow->RecordCount();
				$serviceCodeArray = array();
				$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total,"edit_price");
				$objResponse->call("clearList","PriceList");
				$cnt = 0;
				while($result=$dataRow->FetchRow()) {
					$objResponse->call("viewPriceList","PriceList",trim($result["bestellnum"]),trim($result["artikelname"]),trim($result["price_cash"]),trim($result["price_charge"]));
					$serviceCodeArray[$cnt]=$result["bestellnum"];
					$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
					$cnt++;
				}#end of while
			}
			if (!$rows) $objResponse->call("viewPriceList","PriceList",NULL);
			$objResponse->call("endAJAXSearch",$sElem);
			return $objResponse;
		}

		function populateMiscServiceList($searchId, $keyword, $page)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$srvObj = new Price_Adjustments();

			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$offset = $page * $maxRows;
			$total_srv = $srvObj->countMiscService($keyword,$maxRows,$offset);
			$total = $srvObj->count;
			$lastPage = floor($total/$maxRows);

			if ((floor($total%10))==0)
					$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;
			$dataRow = $srvObj->getMiscServiceList($keyword,$maxRows,$offset);
			$rows=0;
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total,"edit_price");
			$objResponse->call("clearList","PriceList");
			if($dataRow)
			{
				$rows=$dataRow->RecordCount();
				$serviceCodeArray = array();
				$cnt = 0;
				while($result=$dataRow->FetchRow()) {
					$objResponse->call("viewPriceList","PriceList",trim($result["bestellnum"]),trim($result["artikelname"]),trim($result["price_cash"]),trim($result["price_charge"]));
					$serviceCodeArray[$cnt]=$result["bestellnum"];
					$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
					$cnt++;
				}#end of while
			}
			if (!$rows) $objResponse->call("viewPriceList","PriceList",NULL);
			$objResponse->call("endAJAXSearch",$sElem);
			return $objResponse;
		}

		function populateOtherServiceList($searchId, $keyword, $page)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$srvObj = new Price_Adjustments();

			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$offset = $page * $maxRows;
			$total_srv = $srvObj->countOtherService($keyword,$maxRows,$offset);
			$total = $srvObj->count;
			$lastPage = floor($total/$maxRows);

			if ((floor($total%10))==0)
					$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;
			$dataRow = $srvObj->getOtherService($keyword,$maxRows,$offset);
			$rows=0;
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total,"edit_price");
			$objResponse->call("clearList","PriceList");
			if($dataRow)
			{
				$rows=$dataRow->RecordCount();
				$serviceCodeArray = array();
				$cnt = 0;
				while($result=$dataRow->FetchRow()) {
					$objResponse->call("viewPriceList","PriceList",trim($result["code"]),trim($result["name"]),trim($result["price"]),trim($result["price"]));
					$serviceCodeArray[$cnt]=$result["code"];
					$objResponse->call("saveServiceCode", $serviceCodeArray[$cnt]);
					$cnt++;
				}#end of while
			}
			if (!$rows) $objResponse->call("viewPriceList","PriceList",NULL);
			$objResponse->call("endAJAXSearch",$sElem);
			return $objResponse;
		}

		function savePriceAdjustments($date,$service_type,$modifiedPriceList,$max)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$output=$srvObj->saveChangesToPriceList($service_type,$date,$modifiedPriceList,$max);
			if($output)
			{
			 /*$objResponse->alert("Save successful!");
			 $objResponse->call("clearHeader","PriceList");
			 $objResponse->call("clearList","PriceList");
			 $objResponse->call("viewPriceList","PriceList",NULL); */
			 $objResponse->call("showOutputResponse","Save successful!");
			}
			//else $objResponse->alert("Save not successful!");
			else $objResponse->call("showOutputResponse","Save not successful!");
			return $objResponse;
		}

		function populatePriceHistory($date, $page)
		{
			global $db;
			if(!$date) $date = date("Y-m-d");
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
			$offset = $page * $maxRows;

			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$total_srv = $srvObj->countPriceHistory(date("Y-m-d",strtotime($date)));
			#$objResponse->alert($srvObj->sql);
			$total = $srvObj->count;
			$lastPage = floor($total/$maxRows);

			if ((floor($total%10))==0)
					$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;
			$dataRow=$srvObj->getPriceHistoryDetails(date("Y-m-d",strtotime($date)),0,$maxRows,$offset);
			$rows=0;

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total,"view_history");
            $objResponse->call("clearList","PriceHistory");
			if ($dataRow) {
					$rows=$dataRow->RecordCount();
					while($result=$dataRow->FetchRow())
					{
						if (($result['create_dt'])||($result['create_dt']!='0000-00-00 00:00:00'))
							$create_dt = DATE("m/d/Y h:i A",strtotime($result['create_dt']));
						else
							$create_dt = "unspecified";

						$objResponse->call("printAjax","PriceHistory",$srvObj->getServiceName($result['service_code'],$result['ref_source']),$result['service_code'],$result['price_cash'],$result['price_charge'],$create_dt,$result['refno'],$result['status'],$result['ref_source']);

					}#end of while
			} #end of if
			if (!$rows) $objResponse->call("printAjax","PriceHistory",NULL);
			$objResponse->call("endAJAXSearch",$sElem);

			return $objResponse;
		}

		function updatePriceAdjustment($refno,$pcash,$pcharge,$effect_date,$history)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$date = date("Y-m-d",strtotime($effect_date));
			$output=$srvObj->updatePriceHistory($refno,$pcash,$pcharge,$date,$history);
			//$objResponse->alert("output=".$output);
			if($output)
			{
			 $objResponse->call("showUpdateOutputResponse","Save successful!");
			}
			else $objResponse->call("showUpdateOutputResponse","Save not successful!");
			return $objResponse;
		}

		function deletePriceAdjustment($refno)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$output = $srvObj->deletePriceHistory($refno);
			if($output)
			{
				/*$objResponse->alert("Delete successful");
				$objResponse->call("clearHeader","PriceHistory");
				$objResponse->call("clearList","PriceHistory");
				$objResponse->call("printAjax","PriceHistory",NULL);*/
				$objResponse->call("showOutputResponse","Delete successful!");
			}
			//else $objResponse->alert("Delete not successful!");
			else $objResponse->call("showOutputResponse","Delete not successful!");
			return $objResponse;
		}

	#added by VAN 07-14-2010
	function savePriceList($service_type,$area,$modifiedPriceList,$max)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$output=$srvObj->savePriceList($service_type,$area,$modifiedPriceList,$max);
			#$objResponse->alert($srvObj->sql);
			if($output)
			{
			 $objResponse->call("showOutputResponse","Save successful!");
			}

			else $objResponse->call("showOutputResponse","Save not successful!");
			return $objResponse;
		}

	function populatePriceListHistory($area,$source, $page, $keyword)
		{
			global $db;

			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
			$offset = $page * $maxRows;

			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
            //added iscount variable to function getPriceListHistoryDetails
            $srvObj->getPriceListHistoryDetails($area, $source, $keyword, 0, $maxRows, $offset, 1);
			$total = $srvObj->count;
			$lastPage = floor($total/$maxRows);
            if ((floor($total%10))==0)
					$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;

			$dataRow=$srvObj->getPriceListHistoryDetails($area, $source, $keyword, 0, $maxRows, $offset, 0);
            //$objResponse->alert($srvObj->rec_count);
			//$objResponse->alert($srvObj->sql);
            //$objResponse->alert($dataRow->RecordCount());
            //$objResponse->alert(print_r($dataRow,1));
            //$objResponse->alert("P:" . $page . " LP:" . $lastPage . " MR:" . $maxRows . " OffSet " . $offset . " Tot:" . $total);
			$rows=0;
			$objResponse->call("setPagination", $page, $lastPage, $maxRows, $total, "view_history");
			$objResponse->call("clearList","PriceHistory");
			if ($dataRow) {
					$rows=$dataRow->RecordCount();
					while($result=$dataRow->FetchRow())
					{
						if (($result['create_dt'])||($result['create_dt']!='0000-00-00 00:00:00'))
							$create_dt = DATE("m/d/Y h:i A",strtotime($result['create_dt']));
						else
							$create_dt = "unspecified";

						$objResponse->call("printAjax","PriceHistory",$srvObj->getServiceName($result['service_code'],$result['ref_source']),$area,$result['service_code'],$result['price_cash'],$result['price_charge'],$create_dt,$result['status'],$result['ref_source']);
					}#end of while
			} #end of if
			if (!$rows) $objResponse->call("printAjax","PriceHistory",NULL);

            $objResponse->call("endAJAXSearch",$sElem);
			return $objResponse;
		}

		function updatePriceList($code,$refsource,$area,$pcash,$pcharge)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$date = date("Y-m-d",strtotime($effect_date));
			$output=$srvObj->updatePriceListHistory($code,$refsource,$area,$pcash,$pcharge);
			//$objResponse->alert("output=".$output);
			if($output)
			{
			 $objResponse->call("showUpdateOutputResponse","Save successful!");
			}
			else $objResponse->call("showUpdateOutputResponse","Save not successful!");
			return $objResponse;
		}

		function deletePriceList($code,$refsource,$area)
		{
			global $db;
			$objResponse = new xajaxResponse();
			$srvObj = new Price_Adjustments();
			$output = $srvObj->deletePriceListHistory($code,$refsource,$area);
			#$objResponse->alert($srvObj->sql);
			if($output)
			{
				$objResponse->call("showOutputResponse","Delete successful!");
			}

			else $objResponse->call("showOutputResponse","Delete not successful!");
			return $objResponse;
		}
	#---------------

	 require('./roots.php');
	 require($root_path.'include/inc_environment_global.php');
	 require($root_path.'include/care_api_classes/class_price_adjustments.php');
	 require_once($root_path.'include/care_api_classes/class_pharma_product.php');
     require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	 require_once($root_path.'include/care_api_classes/class_radiology.php');
	 require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	 require($root_path.'modules/price_adjustments/ajax/price_adjustments.common.php');
	 $xajax->processRequest();
?>
