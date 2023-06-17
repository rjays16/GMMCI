<?php
	
	function srvGui($grpCode, $grpName){
		$objResponse = new xajaxResponse();
		
		#$objResponse->addAlert("srvGui");
	
		$thead  =	"<thead class=\"\"><td colspan=\"4\">";
		$thead .=	"<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\"><tr>";
		$thead .=    "<td width=\"*\" class=\"reg_header\">".$grpName."</td>";
		$thead .=	"<td width=\"1%\" align=\"right\" style=\"padding:2px;2px;font-weight:normal\" class=\"reg_header\">";
		$thead .=	"<span class=\"reglink\" onclick=\"toggleDisplay('grpBody".$grpCode."');\">Show/Hide</span>";
		$thead .=	"</td>";
		$thead .=    "</tr></table>";
		$thead .=	"</td></thead>";
				
		$thead1  =   "<thead id=\"grphead".$grpCode."\" class=\"reg_list_titlebar\" style=\"height:0;overflow:visible;font-weight:bold;padding:4px;\" id=\"srcRowsHeader\">";
		$thead1 .=   "<td width=\"1\"><input type=\"checkbox\" id=\"chk_all_".$grpCode."\" name=\"chk_all_".$grpCode."\" onChange=\"checkAll(this.checked);countItem('".$grpCode."', 1);\"></td>";
	
		$thead1	.=	 "<td width=\"15%\" nowrap>Code</td>";
		$thead1 .=   "<td width=\"60%\" nowrap>Description</td>";
		$thead1 .=	 "<td width=\"15%\" nowrap>Price</td>";
		$thead1	.=	 "</thead>";

		#$objResponse->addAlert("thead1->".$thead1);
	
		$tbody = "<tbody id=\"grpBody".$grpCode."\" style=\"height:0; overflow:visible\"></tbody>";
	
		#$objResponse->addAlert("grpCode->".$grpCode);
	
		$html = $thead.$thead1.$tbody;
		#$objResponse->addAlert($html);
		
		$objResponse->addAssign("srcRowsTable", "innerHTML", $html);
		
		return $objResponse;
	}

	
	function getAjxGui($group_code, $iscash, $refno, $serv_code){
		$objResponse = new xajaxResponse();
		
		$objResponse->addScriptCall("xajax_populateServices", $group_code, $iscash, $refno, $serv_code);
	
		return $objResponse;
	}
	
	
	#----------added by VAN 09-12-07
	function populateRequestList($sElem,$searchkey,$page,$include_firstname) {
		global $db, $date_format;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		
		//$srv=new SegLab; //change this object.. 
		$srv = new SocialService;
		
		
		$offset = $page * $maxRows;
		if ($searchkey==NULL)
			$searchkey = 'now';
		
		$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
		#$objResponse->addAlert($srv->sql);
		#$objResponse->addAlert($total_srv);
		
		$total = $srv->count;
		#$objResponse->addAlert($total);
		$lastPage = floor($total/$maxRows);
		#if ($page > $lastPage) $page=$lastPage;
		#added by VAN 05-14-08
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		
		$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
		#$objResponse->addAlert("sql = ".$srv->sql);
		#$objResponse->addAlert("count = ".$srv->count);
		#$objResponse->addAlert(print_r($ergebnis, true));
		
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","RequestList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			$i=1;
			while($result=$ergebnis->FetchRow()) {
				
				#if($result["encounter_nr"] != ""){
					$name = trim($result["name_first"])." ".trim($result["name_middle"])." ".trim($result["name_last"]);
				#}else{
				#	$name = trim($result["ordername"]);
				#}
				
				#if (!$name) $name='<i style="font-weight:normal">No name</i>';
				
				if($result["grant_dte"]){
					$dte = substr($result["grant_dte"], 0, 10);
					$time = @formatDate2Local($dte,$date_format);
				}#else{
					#$name = trim($result["ordername"]);
				#}
				#$objResponse->alert(trim($result["encounter_nr"]));
				
				$objResponse->addScriptCall("addPerson","RequestList",trim($result["pid"]), trim($result["encounter_nr"]),$name,$time,$result["discountid"], $result["mss_no"]);
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}

	#--------------------------------------
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	#-----------added by VAN 09-26-07-----
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	require_once($root_path.'modules/social_service/ajax/social_list_common.php');
	require_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');

	#-------------------------------------
	$xajax->processRequests();
?>