<?php
        #added by Raissa 05-28-2009
        function populateServiceListByGroup($area='',$group_code,$sElem,$searchkey,$page) {
                global $db;
                $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
                #$glob_obj->getConfig('pagin_or_patient_search_max_block_rows');
                #$maxRows = $GLOBAL_CONFIG['pagin_or_patient_search_max_block_rows']; # 5 rows
                $glob_obj->getConfig('pagin_patient_search_max_block_rows');
                $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

                $objResponse = new xajaxResponse();
                $srv=new SegLab;
                $offset = $page * $maxRows;
                $searchkey = utf8_decode($searchkey);

                #$objResponse->addAlert('key = '.$searchkey);
                #--------
                if (stristr($searchkey,",")){
                        $keyword_multiple = explode(",",$searchkey);

                        for ($i=0;$i<sizeof($keyword_multiple);$i++){
                                $keyword .= "'".trim($keyword_multiple[$i])."',";
                        }
                        #$objResponse->addAlert('keyword1 = '.$keyword);
                        $word = trim($keyword);
                        #$objResponse->addAlert('word = '.$word);
                        $searchkey = substr($word,0,strlen($word)-1);
                        #$objResponse->addAlert('keyword = '.$keyword);
                        $multiple = 1;
                }else{
                        $multiple = 0;
                }
                #----------------

                $total_srv = $srv->SearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area,1);
                #$objResponse->alert($srv->sql);
                $total = $srv->count;
                #$objResponse->alert('total = '.$group_code);

                $lastPage = floor($total/$maxRows);

                if ((floor($total%10))==0)
                        $lastPage = $lastPage-1;

                if ($page > $lastPage) $page=$lastPage;
                $ergebnis=$srv->SearchService($group_code,$searchkey,$multiple, $maxRows,$offset,$area,0);
                #$objResponse->addAlert("sql = ".$srv->sql);
                $rows=0;

                $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
                $objResponse->addScriptCall("clearList","product-list");
                if ($ergebnis) {
                        $rows=$ergebnis->RecordCount();
                        while($result=$ergebnis->FetchRow()) {
                                $objResponse->addScriptCall("addProductToList","product-list",trim($result["service_code"]),trim($result["name"]));
                        }#end of while
                } #end of if

                if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
                if ($sElem) {
                        $objResponse->addScriptCall("endAJAXSearch",$sElem);
                }

                return $objResponse;
        }

        function PopulateRequests($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir, $done=0, $samplelist=0, $isERIP=0){
                global $root_path;
                $objResponse = new xajaxResponse();

                //Display table header
                ColHeaderRequest($objResponse,$tbId);

                //Paginate & display list of radiology undone request
                PaginateRequestList($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem, $done, $samplelist, 1 , $tbId, $isERIP);

                return $objResponse;
        }

        function ColHeaderRequest(&$objResponse, $tbId){
                global $root_path;
                $class= 'adm_list_titlebar';
                $tbNr = substr($tbId, 4);
                $th  = "<thead><tr><th colspan=\"14\" id=\"mainHead".$tbNr."\">";
                $th .= "</th></tr></thead>";

                $thead  = "<thead><tr style='font:bold 11.5px Arial; color:#000000'>";
                $thead .= "<td width=\"8%\" class=\"".$class."\" align=\"center\"><b>Batch No.</b></td>";
                $thead .= "<td width=\"*\" class=\"".$class."\" align=\"center\"><b>Name</b></td>";
                $thead .= "<td width=\"10%\" class=\"".$class."\" align=\"center\"><b>Hospital No.</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Age</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Sex</b></td>";
                $thead .= "<td width=\"5%\" class=\"".$class."\" align=\"center\"><b>Type</b></td>";
                $thead .= "<td width=\"13%\" class=\"".$class."\" align=\"center\"><b>Location</b></td>";
                $thead .= "<td width=\"13%\" class=\"".$class."\" align=\"center\"><b>Request Date</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Priority</b></td>";
                $thead .= "<td width=\"6%\" class=\"".$class."\" align=\"center\"><b>OR No.</b></td>";
                $thead .= "<td width=\"5%\" class=\"".$class."\" align=\"center\"><b>Repeat</b></td>";
                $thead .= "<td width=\"4%\" class=\"".$class."\" align=\"center\"><b>Details</b></td>";
                $thead .= "<td width=\"4%\" class=\"".$class."\" align=\"center\"><b>Delete</b></td>";
                $thead .= "<td width=\"4%\" class=\"".$class."\" align=\"center\"><b>Done</b></td>";
                $thead .= "</tr></thead> \n";

                $thead1 = $th.$thead;
                $tbodyHTML = "<tbody id='TBodytab".$tbNr."'></tbody>";
                $objResponse->addAssign($tbId,"innerHTML", $thead1.$tbodyHTML);
        }#end of function ColHeaderUndoneRequest

//added by daryl
        //sort by time
function compare_time($a, $b) {
    if ($a['serv_tm'] == $b['serv_tm']) return 0;
    return ($a['serv_tm'] > $b['serv_tm']) ? -1 : 1;
}
//end by daryl
        function PaginateRequestList(&$objResponse,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='name_last',$done=0, $samplelist=0, $mode=0, $tab='', $isERIP=0){
                global $db, $date_format;
                $lab_obj=new SegLab();
                $dept_obj=new Department;
                $ward_obj = new Ward;
                $person_obj=new Person();

                $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
                $glob_obj->getConfig('pagin_patient_search_max_block_rows');
                $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

                #Instantiate paginator
                $pagen=new Paginator($pgx,$thisfile,$searchkey,$rpath, $oitem, $odir);

                #$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
                #Get the max nr of rows from global config
                #$glob_obj->getConfig('pagin_patient_search_max_block_rows');
                # Last resort, use the default defined at the start of this page
                if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS);
                else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

                $searchkey = utf8_decode($searchkey);
                $searchkey = str_replace("^","'",$searchkey);
                $searchkey=addslashes($searchkey);

                if ($searchkey==NULL)
                        $searchkey = 'now';

                # Convert other wildcards
                $searchkey=strtr($searchkey,'*?','%_');

                $offset = $pgx/10 * $pagen->MaxCount();
                $tab = substr($tab,4);

                $ref_source = 'LB';
                $ergebnis = $lab_obj->SearchSelect($searchkey,$pagen->MaxCount(),$offset,$oitem,$odir,0,1,$done, $samplelist, 0, '', $ref_source, '', 0,$tab, $isERIP);
                $total = $lab_obj->FoundRows();
               
                 $ergebnis_walk = $lab_obj->SearchSelect_walk($searchkey,$pagen->MaxCount(),$offset,$oitem,$odir,0,1,$done, $samplelist, 0, '', $ref_source, '', 0,$tab, $isERIP);
                $total_walk = $lab_obj->FoundRows();
                // $objResponse->alert($total+$total_walk);
                //count all records
                $linecount=$total_walk+$total;
                #$objResponse->alert($pgx);
                if ($linecount > $maxRows)
                    $pagen->setTotalBlockCount($linecount%10);
                else
                    $pagen->setTotalBlockCount($linecount);

                $lastPage = floor($total/$pagen->MaxCount());
                if ((floor($total%10))==0)
                    $lastPage = $lastPage-1;

                if ($pgx > $lastPage) $pgx=$lastPage;
                $last = $pagen->BlockStartNr()+$pagen->MaxCount()-1;
                #$objResponse->alert($last."=".$total);
                if ($pgx==$lastPage){
                    if ($last >= $total+$total_walk)
                        $lastRec = $total+$total_walk;
                    else
                        $lastRec = $last;
                }else{
                    $lastRec = ($pgx+1)*$pagen->MaxCount();
                }

                $blkcount = $lastRec;
                /*if(isset($totalcount)&&$totalcount){
                        $pagen->setTotalDataCount($totalcount);
                }else{*/
                    #    $ergebnis = $lab_obj->SearchSelect($searchkey,$pagen->MaxCount(),$offset,$oitem,$odir,0,1,$done, $samplelist, 0, '', 0, 0,$tab);
                        //@$radio_obj->_searchBasicInfoRadioPending($search_type,$searchkey,$sub_dept_nr);
                        #$objResponse->alert($lab_obj->sql);
                        $totalcount=$total;
                        $pagen->setTotalDataCount($totalcount);
                #}
                $pagen->setSortItem($oitem);
                $pagen->setSortDirection($odir);
                #$objResponse->addAlert("blkcount '".$pagen->blkcount."' nextIndex '".$pagen->nextIndex()."' csx '".$pagen->csx."' max_nr '".$pagen->max_nr."' ");
                $LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
                #$objResponse->alert($linecount);
                if ($linecount){
                        #if ($linecount==$maxRows)
                        #    $BlockEndNr = $maxRows;
                        #else
                        #    $BlockEndNr = $pagen->BlockEndNr();
 //added by daryl
 
        if(($ergebnis) || ($ergebnis_walk)){          
            $sumtotalAll = $total_walk + $total;
        } else {
             $sumtotalAll =  $totalcount;
        }

 //ended by daryl
              $textResult = '<hr width=80% align=left>'.str_replace("~nr~",$sumtotalAll,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$blkcount.'.';
                }else
                        $textResult = '<hr width=80% align=left>'.str_replace('~nr~','0',$LDSearchFound);
                #$objResponse->addAlert(" '".$pagen->BlockEndNr()."'");
                $objResponse->addAssign('textResult',"innerHTML", $textResult);

                $withOR = 0;
                $my_count=$pagen->BlockStartNr();

//edited and added by daryl
$all_results = array();

                if (($ergebnis) || ($ergebnis_walk)){
                if ($ergebnis){
                        while($result = $ergebnis->FetchRow()){
                                $all_results[] = $result;
                             }
                         }
                            if ($ergebnis_walk){
                                while($results = $ergebnis_walk->FetchRow())   {
                                      $all_results[] = $results;
                                }
                           }   

                usort($all_results, 'compare_time');                                 

                        foreach($all_results as $result)
                       {
                                $with_res = 0;
                                $sql = "SELECT refno FROM seg_lab_resultdata WHERE service_code='".$result["service_code"]."' AND refno='".$result["refno"]."'";
                                $rs = $db->Execute($sql);
                                if($rs){
                                        if($res = $rs->FetchRow())
                                                $with_res = 1;
                                }
                                $urgency = $result["is_urgent"]?"Urgent":"Normal";
                                if ($result["pid"]!=" ")
                                        $name = ucwords(strtolower(trim($result["name_last"]))).", ".ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])));
                                else
                                        $name = trim($result["ordername"]);

                                if (!$name) $name='<i style="font-weight:normal">No name</i>';

                                if ($result["serv_dt"]) {
                                        $date = strtotime($result["serv_dt"]);
                                        $time = strtotime($result["serv_tm"]);
                                        $requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
                                }

                                 $sql = "SELECT c.charge_name, d.*
                                                    FROM seg_lab_servdetails AS d
                                                    LEFT JOIN seg_type_charge AS c ON c.id=d.request_flag
                                                    WHERE refno='".trim($result["refno"])."'
                                                    AND status NOT IN ('deleted','hidden','inactive','void')
                                                    AND request_flag IS NOT NULL ORDER BY ordering LIMIT 1";

                                 $res=$db->Execute($sql);
                                 $row=$res->RecordCount();
                                 $result_paid = $res->FetchRow();
                                 $or_no = '';

                                 if ($row==0){
                                        $paid = 0;
                                 }else{
                                         if ($result["is_cash"]==1)
                                             $paid = 1;
                                         else
                                             $paid = 0;

                                             if ($result_paid["request_flag"]=='paid'){
                                                    $sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
                                                                                        FROM seg_pay_request AS pr
                                                                                        INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$result["pid"]."'
                                                                                        WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
                                                                                        AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
                                                            $rs_paid = $db->Execute($sql_paid);
                                                            if ($rs_paid){
                                                                    $result2 = $rs_paid->FetchRow();
                                                                    $or_no = $result2['or_no'];
                                                            }

                                                             #added by VAN 06-03-2011
                                                             #for temp workaround
                                                             if (!$or_no){
                                                                 $sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='LB' AND refno='".trim($result["refno"])."' AND is_deleted=0";
                                                                 $res_manual=$db->Execute($sql_manual);
                                                                 $row_manual_count=$res_manual->RecordCount();
                                                                 $row_manual = $res_manual->FetchRow();

                                                                 $or_no = $row_manual['control_no'];
                                                             }

                                             }elseif ($result_paid["request_flag"]=='charity'){
                                                    $sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
                                                                                        FROM seg_granted_request AS pr
                                                                                        WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
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

                                if ($result["date_birth"]!='0000-00-00')
                                        $age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
                                else
                                        $age = $result["age"];

                                if ($result['encounter_type']==1){
                                        $enctype = "ERPx";
                                        $location = "EMERGENCY ROOM";
                                }elseif ($result['encounter_type']==2){
                                        if ($result['encounter_status']=='phs')
                                            $enctype = "PHSx";
                                        else
                                            $enctype = "OPDx";

                                        $dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                                        $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                                }elseif (($result['encounter_type']==3)||($result['encounter_type']==4)||($result['encounter_type']==6)){
                                        if ($result['encounter_type']==3)
                                                $enctype = "INPx (ER)";
                                        elseif ($result['encounter_type']==4)
                                                $enctype = "INPx (OPD)";

                                        $ward = $ward_obj->getWardInfo($result['current_ward_nr']);
                                        $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
                                }else{
                                        $enctype = "WPx";
                                        $location = 'WALK-IN';
                                }
                                if (empty($result["parent_refno"]))
                                        $repeat = 0;
                                else
                                        $repeat = 1;

                                // Added janjan 09/07/2015
                                $is_done = $lab_obj->checkIfAllDone(trim($result["refno"]));

                                if ($mod){
                                        $labresult = $lab_obj->hasResult(trim($result["refno"]));

                                        if ($labresult)
                                                $labstatus = 1;
                                        else
                                                $labstatus = 0;

                                        #if ($result["type_charge"]){
                                        #        $result2['or_no'] = $result['charge_name'];
                                        #}

                                        $objResponse->addScriptCall("jsRequest",$sub_dept_nr,$my_count,trim($result["refno"]),$name,$requestDate,$urgency,$or_no, $labstatus, $paid, $repeat,trim($result['encounter_nr']),trim($result["pid"]),floor($age),mb_strtolower($result["sex"]),$location, $enctype,$or_no,$result["is_cash"], $with_res,$result["is_repeat"], $is_done);
                                }else{
                                        $labresult = $lab_obj->hasResult(trim($result["refno"]), $result["service_code"]);

                                        if ($labresult)
                                                $labstatus = 1;
                                        else
                                                $labstatus = 0;

                                        if ($result["type_charge"]){
                                                $result2['or_no'] = $result['charge_name'];
                                        }

                                        #added by VAN 11-14-09
                                        $rsRef = $lab_obj->getLabOrderNo(trim($result["refno"]));

                                        $rowRef = $rsRef->FetchRow();

                                        $rsCode = $lab_obj->getTestCode($result["service_code"]);
                                        #echo "w= ".$srvObj->sql;

                                        if (($result['encounter_type'] == 1) || ($result['encounter_type'] == 3) || ($result['encounter_type'] == 4)){
                                            #$code = $rsCode['service_code'];
                                            $code = $rsCode['ipdservice_code'];
                                        }else{
                                            $code = $rsCode['oservice_code'];
                                        }

                                        $labresult = $lab_obj->hasResult2($rowRef['lis_order_no'], $code);
                                        #$objResponse->alert($srv->sql);
                                        $service_date = 'unknown';
                                        if ($srv->count){
                                            if (($labresult["trx_dt"])&&($labresult["trx_dt"]!='0000-00-00 00:00:00')) {
                                                #$date_serve = strtotime($labresult["trx_dt"]);
                                                #$time_serve = strtotime($labresult["trx_dt"]);
                                                #$service_date = date("M d, Y",$date_serve)." ".date("h:i A",$time_serve);
                                                $service_date = date("M d, Y h:i A",strtotime($labresult["trx_dt"]));
                                            }
                                        }

                                        #added by daryl
                                         $with_res = $withresult;
                                         $labwalk_ = $lab_obj->selectifwalkin($result['pid']);
                                         $labwalk = $labwalk_->FetchRow();
                                        $iflabwalk = $labwalk['ifwalk'];
                                        
                                        if ($iflabwalk == 1){
                                            $sex_="";
                                        }else{
                                              $sex_=  mb_strtolower($result["sex"]);
                                        }

                                        $objResponse->addScriptCall("jsRequest",$sub_dept_nr,$my_count,trim($result["refno"]),$name,$requestDate,$urgency,$or_no, $labstatus, $paid, $repeat,trim($result['encounter_nr']), trim($result["pid"]),floor($age),$sex_,$location, $enctype, $result["is_cash"],$with_res,$result["is_repeat"],$is_done);
                                }
                          $my_count++;
                        }# end of while loop
                }
                else{
                        $objResponse->addScriptCall("jsNoFoundRequest",$sub_dept_nr);

                }# end of else-stmt 'if ($ergebnis)'
//ended by daryl

                # Previous and Next button generation
                $nextIndex = $pagen->nextIndex();
                $prevIndex = $pagen->prevIndex();
                $pageFirstOffset = 0;
                $pagePrevOffset = $prevIndex;
                $pageNextOffset = $nextIndex;

                if ($totalcount%$pagen->MaxCount()==0)
                    $pageLastOffset = $total-$pagen->MaxCount();
                else
                    $pageLastOffset = $totalcount-($totalcount%$pagen->MaxCount());

                #$objResponse->alert($pageLastOffset);

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

                $title ="List of Service Requests";

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
        }

        function PopulateUndoneRequests($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir, $done=0, $isERIP=0){
                global $root_path;
                $objResponse = new xajaxResponse();

                //Display table header
                ColHeaderUndoneRequest($objResponse,$tbId, $sub_dept_nr,$done);

                //Paginate & display list of radiology undone request
                PaginateUndoneRequestList($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem, $done, $mode, $tbId, $isERIP);

                return $objResponse;
        }

        function ColHeaderUndoneRequest(&$objResponse, $tbId, $sub_dept_nr,$done=0){
                global $root_path;
                $class= 'adm_list_titlebar';

                $th  = "<thead><tr><th colspan=\"14\" id=\"mainHead".$sub_dept_nr."\">";
                $th .= "</th></tr></thead>";

                $thead  = "<thead><tr style='font:bold 11.5px Arial; color:#000000'>";
                $thead .= "<td width=\"8%\" class=\"".$class."\" align=\"center\"><b>Batch No.</b></td>";
                $thead .= "<td width=\"*\" class=\"".$class."\" align=\"center\"><b>Name</b></td>";
                $thead .= "<td width=\"10%\" class=\"".$class."\" align=\"center\"><b>Hospital No.</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Age</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Sex</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Type</b></td>";
                $thead .= "<td width=\"5%\" class=\"".$class."\" align=\"center\"><b>Location</b></td>";
                $thead .= "<td width=\"15%\" class=\"".$class."\" align=\"center\"><b>Service Requested</b></td>";
                $thead .= "<td width=\"10%\" class=\"".$class."\" align=\"center\"><b>Request Date</b></td>";
                $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Priority</b></td>";
                $thead .= "<td width=\"6%\" class=\"".$class."\" align=\"center\"><b>OR No.</b></td>";
                $thead .= "<td width=\"5%\" class=\"".$class."\" align=\"center\"><b>Repeat</b></td>";
                if($done==0)
                        $thead .= "<td width=\"3%\" class=\"".$class."\" align=\"center\"><b>Sent Out</b></td>";
                $thead .= "<td width=\"4%\" class=\"".$class."\" align=\"center\"><b>Lab Result</b></td>";
                $thead .= "</tr></thead> \n";

                $thead1 = $th.$thead;
                $tbodyHTML = "<tbody id='TBodytab".$sub_dept_nr."'></tbody>";
                $objResponse->addAssign($tbId,"innerHTML", $thead1.$tbodyHTML);
        }#end of function ColHeaderUndoneRequest

        #edited by VAN 01-09-10, add the with_result parameter for lab test
        //edited by VAN 02-11-2013
        //included the serial lab result info and exclude the grouping or the test item not in LIS
        function PaginateUndoneRequestList(&$objResponse,$searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$odir='ASC',$oitem='name_last',$done=0, $mode=0, $tab='', $isERIP=0){
                global $date_format;
                $lab_obj=new SegLab();
                global $db;
                $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
                $glob_obj->getConfig('pagin_patient_search_max_block_rows');
                $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

                $srv=new SegLab;
                $dept_obj=new Department;
                $ward_obj = new Ward;
                $person_obj=new Person();

                #Instantiate paginator
                $pagen=new Paginator($pgx,$thisfile,$searchkey,$rpath, $oitem, $odir);

                $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
                #Get the max nr of rows from global config
                $glob_obj->getConfig('pagin_patient_search_max_block_rows');
                # Last resort, use the default defined at the start of this page
                if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS);
                else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

                $searchkey = utf8_decode($searchkey);
                $searchkey = str_replace("^","'",$searchkey);
                $searchkey=addslashes($searchkey);

                if ($searchkey==NULL)
                        $searchkey = 'now';

                # Convert other wildcards
                $searchkey=strtr($searchkey,'*?','%_');

                $offset = $pgx/10 * $pagen->MaxCount();
                $tab = substr($tab,4);
                #$objResponse->addAlert(" '".$searchkey."' '".$pagen->MaxCount()."' '".$offset."' '".$oitem."' '".$odir."' '0' '0' '".$done."' ' '0' '0'");
                $ref_source = 'LB';
                #$objResponse->alert($tab);
                $ergebnis = $lab_obj->SearchLabRequests($searchkey,$pagen->MaxCount(),$offset,$oitem,$odir,0,0,$done, 0, '',$source='LB');
               //added by daryl
                $total = $lab_obj->FoundRows();
                $ergebnis_walk = $lab_obj->SearchLabRequests_walk($searchkey,$pagen->MaxCount(),$offset,$oitem,$odir,0,0,$done, 0, '',$source='LB');
                $total_walk = $lab_obj->FoundRows();
//ended by daryl
                //count all records
                $linecount=$total+$total_walk;

                if ($linecount > $maxRows)
                    $pagen->setTotalBlockCount($linecount%10);
                else
                    $pagen->setTotalBlockCount($linecount);

                $totalcount=$total+$total_walk;

                $pagen->setTotalDataCount($totalcount);

                $pagen->setSortItem($oitem);
                $pagen->setSortDirection($odir);
                #$objResponse->alert($linecount);
                //$objResponse->addAlert("blkcount '".$pagen->blkcount."' nextIndex '".$pagen->nextIndex()."' csx '".$pagen->csx."' max_nr '".$pagen->max_nr."' ");
                $LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";

                if ($linecount){
                $sumtotalAll = $total + $total_walk;

                    $textResult = '<hr width=80% align=left>'.str_replace("~nr~",$sumtotalAll,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
               }
                else
                    $textResult = '<hr width=80% align=left>'.str_replace('~nr~','0',$LDSearchFound);
                #$objResponse->addAlert(" '".$pagen->BlockEndNr()."'");
                $objResponse->addAssign('textResult',"innerHTML", $textResult);

                $my_count=$pagen->BlockStartNr();

//added by daryl
$all_undone_results = array();


                if (($ergebnis) || ($ergebnis_walk)){
                if ($ergebnis){
                    while($result = $ergebnis->FetchRow()){
                                $all_undone_results[] = $result;
                             }
                        }
                         if ($ergebnis_walk) {
                             while($results = $ergebnis_walk->FetchRow()) {
                                      $all_undone_results[] = $results;
                              }
                         }   

                     usort($all_undone_results, 'compare_time');                                 

                        foreach($all_undone_results as $result){
                        $urgency = $result["is_urgent"]?"Urgent":"Normal";
                        if ($result["pid"]!=" ")
                            $name = ucwords(strtolower(trim($result["name_last"]))).", ".ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])));
                        else
                            $name = trim($result["ordername"]);

                        if (!$name) $name='<i style="font-weight:normal">No name</i>';

                        if ($result["serv_dt"]) {
                            $date = strtotime($result["serv_dt"]);
                            $time = strtotime($result["serv_tm"]);
                            $requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
                        }

                        if ($result["date_birth"]!='0000-00-00')
                            $age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
                        else
                            $age = $result["age"];

                        if ($result['encounter_type']==1){
                            $enctype = "ERPx";
                            $location = "EMERGENCY ROOM";
                        }elseif (($result['encounter_type']==2)||($result['encounter_type']==5)){
                            if ($result['encounter_type']==2)
                                $enctype = "OPDx";
                            else
                                $enctype = "PHSx";

                            $dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                            $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                        }elseif (($result['encounter_type']==3)||($result['encounter_type']==4)||($result['encounter_type']==6)){
                            if ($result['encounter_type']==3)
                                    $enctype = "INPx (ER)";
                            elseif ($result['encounter_type']==4)
                                    $enctype = "INPx (OPD)";
                            elseif ($result['encounter_type']==6)
                                    $enctype = "INPx (PHS)";

                            $ward = $ward_obj->getWardInfo($result['current_ward_nr']);
                            $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
                        }else{
                            $enctype = "WPx";
                            $location = 'WALK-IN';
                        }
                        if (empty($result["parent_refno"]))
                            $repeat = 0;
                        else
                            $repeat = 1;

                        $service = $result['service_name'];

                        if ($result['in_lis'])
                            $service_code = "";
                        else
                            $service_code = $result["service_name"];

                        $labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);

                        if ($labresult)
                                $labstatus = 1;
                        else
                                $labstatus = 0;

                        if ($result['is_cash']){
                            if ($result["charge_name"]){
                                $result2['or_no'] = mb_strtoupper($result['charge_name']);
                            }#else
                                #$result2['or_no'] = 'PAID';
                        }else{
                            $result2['or_no']  = 'CHARGE';
                        }

                        #commented by VAN 02-11-2013
                        #added by VAN 09-03-2010

                        if ($result["group_id"]){
                            $service="";
                                $sql = "SELECT s.name, s.`service_code`
                                                FROM seg_lab_servdetails AS d
                                                LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code
                                                LEFT JOIN seg_lab_services AS s ON s.service_code = gp.service_code
                                                WHERE d.refno='".$result["refno"]."' AND gp.group_id=".$result["group_id"];
                                #$objResponse->addAlert($sql);
                                $rs = $db->Execute($sql);
                                while($rs!=NULL && $rst = $rs->FetchRow()){
                                        if($service==""){
                                                $service .= $rst["name"];
                                                $service_code .= $rst['service_code'];
                                            }
                                        else{
                                                $service .= ", ".$rst["name"];
                                                $service_code .= "," .$rst['service_code'];
                                            }
                                }
                                if($service==""){
                                        $sql = "SELECT DISTINCT(s.name)
                                                        FROM seg_lab_servdetails AS d
                                                        LEFT JOIN seg_lab_result_group AS g ON g.service_code_child = d.service_code
                                                        LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = g.service_code
                                                        LEFT JOIN seg_lab_services AS s ON s.service_code = g.service_code_child
                                                        WHERE d.refno='".$result["refno"]."' AND gp.group_id=".$result["group_id"];
                                        #$objResponse->addAlert($sql);
                                        $rs = $db->Execute($sql);
                                        while($rs!=NULL && $rst = $rs->FetchRow()){
                                                if($service=="")
                                                        $service .= $rst["name"];
                                                else
                                                        $service .= ", ".$rst["name"];
                                        }
                                }

                        }
                        #------
                        if (!$result["group_id"]){
                            $result["group_id"] = 0;
                            $service = $result['services'];
                            $service_code = $result['service_code'];
                        }
                            
                        //to get the list of services included in the request
                        //separate the serial test (except 1st take)
                        $lis_order_no = $result["lis_order_no"];     
                        
                        if ($result['nth_take']){
                        /*   $service = $result['services'].'<font color="RED"> (First Take)</font>'; 
                        }elseif (!$result['nth_take'] > 1){
                           $service_code = $db->qstr($result['service_code']); 
                           $sql_l = "SELECT name FROM seg_lab_services WHERE service_code=$service_code"; 
                           $service = $db->GetOne($sql_l);*/
                           
                           switch($result['nth_take']){
                                case '1' :  
                                            $nth_take = 'First'; 
                                            break;
                                case '2' :  
                                            $nth_take = 'Second'; 
                                            break;
                                case '3' :  
                                            $nth_take = 'Third'; 
                                            break;
                                case '4' :  
                                            $nth_take = 'Fourth'; 
                                            break;
                                case '5' :  
                                            $nth_take = 'Fifth'; 
                                            break;
                                case '6' :  
                                            $nth_take = 'Sixth'; 
                                            break;
                                case '7' :  
                                            $nth_take = 'Seventh'; 
                                            break;
                                case '8' :  
                                            $nth_take = 'Eighth'; 
                                            break;
                                case '9' :  
                                            $nth_take = 'Ninth'; 
                                            break;
                                case '10' : 
                                            $nth_take = 'Tenth'; 
                                            break;
                            }
    
                           $service = $result['services'].'<font color="RED"> ('.$nth_take.' Take)</font>'; 
                        }else{
                           $service = $result['services'];
                        }
                        
                        $result_date = '';
                        if (($result["date_received"])&&($result["date_received"]!='0000-00-00 00:00:00')) {
                            $result_date = date("M d, Y h:i A",strtotime($result["date_received"]));
                        } 
                        
                        $withresult = 1;
                        if ($result['filename'])
                            $withresult = 1;
                            
                            #added by daryl
                        $with_res = $withresult;
                         $labwalk_ = $lab_obj->selectifwalkin($result['pid']);
                         $labwalk = $labwalk_->FetchRow();
                        $iflabwalk = $labwalk['ifwalk'];
                        
                        if($iflabwalk == 1) {
                            $sex_ = "";
                        }
                        else
                        {
                            $sex_ = mb_strtolower($result["sex"]);
                        }
            
                        $objResponse->addScriptCall("jsRequest",$sub_dept_nr,$my_count,trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $service, $service_code, $repeat, trim($result["pid"]),floor($age),$sex_,$location, $enctype, $with_res, $result["group_id"], $result["with_result"],$result["is_repeat"], $result['nth_take'], $result["in_lis"], $result["lis_order_no"]);

                        #}
                        $my_count++;
                    }# end of while loop
                    //commented by VAN 02-11-2013
                    #if($grp!="")
                    #        $objResponse->addScriptCall("jsRequest",$sub_dept_nr,$my_count,trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $service, $result["service_code"], $repeat, trim($result["pid"]),floor($age),mb_strtolower($result["sex"]),$location, $enctype, $with_res, $result["group_id"], $result["with_result"],$result["in_lis"]);
                }
                else{
                        $objResponse->addScriptCall("jsNoFoundRequest",$sub_dept_nr);

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

                if ($done==1)
                        $title ="List of Done Requests";
                else
                        $title ="List of Undone Requests";

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
        }

    function deleteRequest($refno){
        global $db, $HTTP_SESSION_VARS;
        $srv=new SegLab;
        $enc_obj=new Encounter;
        $objResponse = new xajaxResponse();
        $details = (object) 'details';
        
        #$objResponse->alert("ajax deleteRequest refno = $refno");
        $sql = "SELECT ref_no FROM seg_pay_request
                    WHERE ref_source = 'LD' AND ref_no = '$refno'
                    UNION
                    SELECT refno FROM seg_lab_result
                    WHERE refno = '$refno'";

         $res=$db->Execute($sql);
         $row=$res->RecordCount();
         
         #get encounter and charge type info
        $ref = $db->GetRow("SELECT encounter_nr,IF(is_cash,NULL,grant_type) AS charge_type FROM seg_lab_serv\n".
                            "WHERE refno=".$db->qstr($refno));
         
        #check if the encounter of the request has a final bill                    
        $hasfinal_bill = $enc_obj->hasFinalBilling($ref['encounter_nr']);
        
        if (($row==0)&&(!$hasfinal_bill)){
            $status=$srv->deleteRequestor($refno); 
            #$status = 1;
            if ($status) {
                
                $objInfo = new Hospital_Admin();
                $row_hosp = $objInfo->getAllHospitalInfo();
                
                if ($row_hosp['connection_type']=='hl7'){
                    #validate if there a LIS posted request
                    $hl7_row = $srv->isExistHL7Msg($refno);
                    if ($hl7_row['msg_control_id']){
                        #update HL7 message tracker
                        $row_comp = $objInfo->getSystemCreatorInfo();
        
                        $details->protocol_type = $row_hosp['LIS_protocol_type'];
                        $details->protocol = $row_hosp['LIS_protocol'];
                        $details->address_lis = $row_hosp['LIS_address'];
                        $details->address_local = $row_hosp['LIS_address_local'];
                        $details->port = $row_hosp['LIS_port'];
                        $details->username = $row_hosp['LIS_username'];
                        $details->password = $row_hosp['LIS_password'];
                        
                        $details->folder_LIS = $row_hosp['LIS_folder_path'];
                        #LIS SERVER IP
                        $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path'];
                        #HIS SERVER IP
                        $details->directory = "\\\\".$details->address_local.$row_hosp['LIS_folder_path'];
                        #HIS SERVER IP
                        $details->directory_local = "\\\\".$details->address_local.$row_hosp['LIS_folder_path_local'];
                        $details->extension = $row_hosp['LIS_HL7_extension'];
                        $details->service_timeout = $row_hosp['service_timeout'];    #timeout in seconds
                        $details->directory_LIS = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path_inbox'];
                        $details->hl7extension = ".".$row_hosp['LIS_HL7_extension'];
                        
                        #if ($details->protocol_type=='tcp')
                        #    $transfer_method = 'SOCKET';
                        #else    
                        #    $transfer_method = 'NFS';
                            
                        $transfer_method = $details->protocol_type;    
            
                        #msh
                        $details->system_name = trim($row_comp['system_id']);
                        $details->hosp_id = trim($row_hosp['hosp_id']);
                        $details->lis_name = trim($row_comp['lis_name']);
                        $details->currenttime = strftime("%Y%m%d%H%M%S");
                        
                        $fileObj = new seg_create_HL7_file($details);
                            
                        $order_control = "CA";
                        $hl7msg_row = $srv->isforReplaceHL7Msg($refno,$order_control); 
                        
                        if ($hl7msg_row['msg_control_id']){
                            $msg_control_id = $hl7msg_row['msg_control_id'];
                            $forreplace = 1;   
                        }else
                            $msg_control_id = $srv->getLastMsgControlID();
                        
                        $prefix = "HIS";
                        
                        #replace NW or RP to CA
                        $filecontent = $hl7_row['hl7_msg'];
                        #search for the string NW or RP in the message
                        if (!stristr($filecontent, 'ORC|NW|') === FALSE){
                            #replace NW to CA
                            $filecontent = str_replace("ORC|NW|", "ORC|CA|", $filecontent);
                        }elseif (!stristr($filecontent, 'ORC|RP|') === FALSE){
                            #replace RP to CA
                            $filecontent = str_replace("ORC|RP|", "ORC|CA|", $filecontent);
                        }    
                        
                        $details->msg_control_id_db = $msg_control_id;
                        $details->msg_control_id = $prefix.$msg_control_id;
                        
                        $details->order_control = $order_control;
                        
                        $file = $details->msg_control_id;
                        
                        #create a file
                        $filename_local = $fileObj->create_file_to_local($file);
                                                        
                        #Thru file sharing
                        #write a file to a local directory
                        $fileObj->write_file($filename_local, $filecontent);
                        
                        switch ($transfer_method){
                            #FTP (File Transfer Protocol) approach
                            case "ftp" :
                                        $transportObj = new seg_transport_HL7_file($details);
                                        $transportObj->ftp_transfer($file, $filecontent);
                                        break;
                                        
                            #window NFS approach or network file sharing
                            case "nfs" :
                                        #create a file
                                        $filename_local = $fileObj->create_file_to_local($file);
                                        #Thru file sharing
                                        #write a file to a local directory
                                        $fileObj->write_file($filename_local, $filecontent); 
                        
                                        $filename_hclab = $fileObj->create_file_to_hclab($file);
                                        #write a file to a hclab directory   
                                        $fileObj->write_file($filename_hclab, $filecontent); 
                                        unlink($filename_local);
                                        break;
                            #TCP/IP (communication approach)                    
                            case "tcp" :
                                        $transportObj = new seg_transport_HL7_file($details);
                                        
                                        #if ($transportObj->isConnected()){
                                             #send the message
                                             $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
                                             
                                             #return/print result
                                             $text = "LIS Server said:: ".$obj;
                                             #$text = "connected...";
                                        #}else{
                                        #     $text = "Unable to connect to LIS Server. Error: ".$transportObj->error."...";   
                                        #}
                                        
                                        echo $text;
                                        break;                    
                        }
                                                        
                        #update msg control id
                        $details->msg_control_id = $details->msg_control_id_db;
                        
                        #if new message control id, update the tracker
                        if (!$forreplace)
                            $hl7_ok = $srv->updateHL7_msg_control_id($details->msg_control_id);
                            
                        #HL7 tracker
                        $details->lis_order_no = $hl7_row['lis_order_no'];
                        $details->msg_type = $hl7_row['msg_type'];
                        $details->event_id = $hl7_row['event_id'];
                        $details->refno = $refno;
                        $details->pid = $hl7_row['pid'];
                        $details->encounter_nr = $hl7_row['encounter_nr'];
                        $details->hl7_msg =  $filecontent;
                                                    
                        if ($forreplace){
                            $hl7_ok = $srv->updateInfo_HL7_tracker($details);
                        }else{
                            $hl7_ok = $srv->addInfo_HL7_tracker($details);
                        }
                    }    
                    #--------------------------        
                }    
                
                $srv->deleteLabServ_details($refno);
                $objResponse->addScriptCall("handleOnclick");
                #added by VAS 03-23-2012
                #update the applied coverage. minus the total of the cancelled request
                if ($ref['charge_type'] == 'phic') {
                    #get all items and store in an array
                    $sql_item = "SELECT service_code, price_cash*quantity AS total, is_served
                                    FROM seg_lab_servdetails d
                                    INNER JOIN seg_lab_serv s ON s.refno=d.refno
                                    WHERE s.refno=".$db->qstr($refno)."
                                    AND s.grant_type=".$db->qstr($ref['charge_type'])." AND d.is_served=1";
                    
                    $rs = $db->Execute($sql_item);
                    
                    if ($rs){ 
                        
                        while($item_details=$rs->FetchRow()) {
                            # Handle applied coverage for PHIC and other benefits
                            # Hardcode hcare ID (temporary workaround)
                            define('__PHIC_ID__', 18);
                            
                            $item = $item_details['service_code'];
                            
                            $sql_app = "SELECT coverage FROM seg_applied_coverage\n".
                                            "WHERE ref_no='T{$ref['encounter_nr']}'\n".
                                            "AND source='L'\n".
                                            "AND item_code=".$db->qstr($item)."\n".
                                            "AND hcare_id=".__PHIC_ID__;
                            
                            #less the cancelled or deleted item                                                    
                            $coverage = parseFloatEx($db->GetOne($sql_app)) - parseFloatEx($item_details['total']);
                            
                            $result = $db->Replace('seg_applied_coverage',
                                                    array(
                                                         'ref_no'=>"T{$ref['encounter_nr']}",
                                                         'source'=>'L',
                                                         'item_code'=>$item,
                                                         'hcare_id'=>__PHIC_ID__,
                                                         'coverage'=>$coverage
                                                    ),
                                                    array('ref_no', 'source', 'item_code', 'hcare_id'),
                                                    $autoquote=TRUE
                                               );
                        } 
                        $withcoverage=1;                 
                    }    
                }                    
                
                if ($withcoverage)
                    $objResponse->addAlert("The request is successfully deleted and Update the applied coverage.");
                else
                    $objResponse->addAlert("The request is successfully deleted.");
                    
            }else
                $objResponse->addScriptCall("showme", $srv->sql);
         }else{
                if ($hasfinal_bill)
                    $objResponse->addAlert("Unable to delete the request. It has a saved bill or a final bill.");
                elseif ($row)    
                    $objResponse->addAlert("Unable to delete the request. It is already or partially paid.");
                else
                    $objResponse->addAlert("Unable to delete the request.");    
         }
        /*if ($row==0){

            $status=$srv->deleteRequestor($refno);

            if ($status) {
                $srv->deleteLabServ_details($refno);
                $objResponse->call("handleOnclick");
                $objResponse->alert("The request is successfully deleted.");
            }else
                $objResponse->call("showme", $srv->sql);
         }else{
                $objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
         }*/
        return $objResponse;
    }

    #added by VAN 03-08-08
    function populateLabServiceList($area='',$group_code,$sElem,$searchkey,$page) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        #$glob_obj->getConfig('pagin_or_patient_search_max_block_rows');
        #$maxRows = $GLOBAL_CONFIG['pagin_or_patient_search_max_block_rows']; # 5 rows
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        $offset = $page * $maxRows;
        $searchkey = utf8_decode($searchkey);

        #$objResponse->addAlert('key = '.$searchkey);
        #--------
        if (stristr($searchkey,",")){
            $keyword_multiple = explode(",",$searchkey);
            #$objResponse->alert($keyword_multiple[0]);
            $codenum = 0;
            if (is_numeric($keyword_multiple[0]))
                    $codenum = 1;

            for ($i=0;$i<sizeof($keyword_multiple);$i++){
                $keyword .= "'".trim($keyword_multiple[$i])."',";
            }
            #$objResponse->addAlert('keyword1 = '.$keyword);
            $word = trim($keyword);
            #$objResponse->addAlert('word = '.$word);
            $searchkey = substr($word,0,strlen($word)-1);
            #$objResponse->addAlert('keyword = '.$keyword);
            $multiple = 1;
        }else{
            $multiple = 0;
        }
        #----------------

        $total_srv = $srv->SearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area, $codenum,1);
        #$objResponse->alert('total = '.$group_code);
        #$objResponse->alert($srv->sql);
        $total = $srv->count;
        #$objResponse->addAlert('total = '.$total);

        $lastPage = floor($total/$maxRows);

        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;

        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$srv->SearchService($group_code,$searchkey,$multiple, $maxRows,$offset,$area, $codenum,0);
        #$objResponse->addAlert("sql = ".$srv->sql);
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","product-list");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                     #$objResponse->addAlert("sql = ".$result["service_code"]."  - ".$result["price_cash"]);
                    #check if the service is socialized

                    if ($result["is_socialized"]){
                        $sql2 = "SELECT DISTINCT * FROM seg_service_discounts
                                     WHERE service_code='".$result["service_code"]."'";
                                                    # $objResponse->addAlert("sql = ".$sql2);
                        $res=$db->Execute($sql2);
                        $row=$res->RecordCount();

                        if ($row!=0){
                            while($rsObj=$res->FetchRow()) {
                                if ($rsObj["discountid"] == C1){
                                    $price_C1 = $rsObj["price"];
                                }
                                if ($rsObj["discountid"] == C2){
                                    $price_C2 = $rsObj["price"];
                                }
                                if ($rsObj["discountid"] == C3){
                                    $price_C3 = $rsObj["price"];
                                }
                            }
                        }else{
                                                        $price_C1 = '';
                                                        $price_C2 = '';
                                                        $price_C3 = '';

                                                }
                    }else{

                        $price_C1 = number_format(trim($result["price_cash"]),2,'.', '');
                        $price_C2 = number_format(trim($result["price_cash"]),2,'.', '');
                        $price_C3 = number_format(trim($result["price_cash"]),2,'.', '');
                    }

                    if ($result['status']=='unavailable')
                        $available = 0;
                    else
                        $available = 1;

                $objResponse->addScriptCall("addProductToList","product-list",trim($result["service_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''), $result["is_socialized"],$result["group_code"],$price_C1,$price_C2,$price_C3, $available);
            }#end of while
        } #end of if

        if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }

    function populateServiceGroups($group_code) {
        global $db;
        $dbtable='seg_lab_services';
        $prctable = 'seg_pharma_prices';
        $objResponse = new xajaxResponse();

        if ($group_code) {
            # clean input data
             $sql="SELECT * FROM seg_lab_service_groups WHERE group_code='$group_code'";
             $ergebnis=$db->Execute($sql);
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                $objResponse->addScriptCall("appendGroup",$result["group_code"],$result["name"]);
            }
        }
        return $objResponse;
    }

    function srvGui($grpCode, $grpName){
        $objResponse = new xajaxResponse();

        #$objResponse->addAlert("srvGui");

        $thead  =    "<thead class=\"\"><td colspan=\"4\">";
        $thead .=    "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\"><tr>";
        $thead .=    "<td width=\"*\" class=\"reg_header\">".$grpName."</td>";
        $thead .=    "<td width=\"1%\" align=\"right\" style=\"padding:2px;2px;font-weight:normal\" class=\"reg_header\">";
        $thead .=    "<span class=\"reglink\" onclick=\"toggleDisplay('grpBody".$grpCode."');\">Show/Hide</span>";
        $thead .=    "</td>";
        $thead .=    "</tr></table>";
        $thead .=    "</td></thead>";

        $thead1  =   "<thead id=\"grphead".$grpCode."\" class=\"reg_list_titlebar\" style=\"height:0;overflow:visible;font-weight:bold;padding:4px;\" id=\"srcRowsHeader\">";
        $thead1 .=   "<td width=\"1\"><input type=\"checkbox\" id=\"chk_all_".$grpCode."\" name=\"chk_all_".$grpCode."\" onChange=\"checkAll(this.checked);countItem('".$grpCode."', 1);\"></td>";

        $thead1    .=     "<td width=\"15%\" nowrap>Code</td>";
        $thead1 .=   "<td width=\"60%\" nowrap>Description</td>";
        $thead1 .=     "<td width=\"15%\" nowrap>Price</td>";
        $thead1    .=     "</thead>";

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


    function populateServices($group_code, $iscash, $refno, $serv_code) {
        global $db;
        $objResponse = new xajaxResponse();

        if (($serv_code=="none")||($serv_code=="*")){
            $sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
                         WHERE s.group_code=g.group_code
                        AND s.group_code='$group_code'
                            ORDER BY s.name";
        }else{
            /*
            $sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
                         WHERE s.group_code=g.group_code
                        AND s.group_code='$group_code'
                        AND s.service_code LIKE '$serv_code%'
                            ORDER BY s.service_code";
            */
            $sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
                         WHERE s.group_code=g.group_code
                        AND s.group_code='$group_code'
                        AND ((s.service_code LIKE '%$serv_code%') OR (s.name LIKE '%$serv_code%'))
                            ORDER BY s.name";
        }

         #$objResponse->addAlert("populateServices sql : $sql");
        $objResponse->addScriptCall("ajxGetPrevReq",0);
        $ergebnis=$db->Execute($sql);
        $rows=$ergebnis->RecordCount();
        #$objResponse->addAlert("populateServices rows : $rows");

        if ($rows > 0 ){
            $objResponse->addScriptCall("ajxClearTable",$group_code);

            while($result=$ergebnis->FetchRow()) {

                $price=$iscash?$result["price_cash"]:$result["price_charge"];
                if (!$price) $price="N/A";
                else $price=number_format($price,2,'.','');

                if ($refno!=NULL){
                    #$objResponse->addAlert("populateServices refno : NOT NULL");
                    $sql2 = "SELECT * FROM seg_lab_servdetails WHERE service_code = '".$result["service_code"]."' AND refno='".$refno."'";
                    #$objResponse->addAlert("populateServices sql2 : $sql2");
                    $res=$db->Execute($sql2);
                    $row=$res->RecordCount();

                    if ($row!=0){
                        $rsObj=$res->FetchRow();
                        #$objResponse->addAlert("populateServices rsObj : ".$rsObj["service_code"]);
                        $servlist = $servlist.$rsObj["service_code"].",";
                        #$objResponse->addAlert("populateServices servlist : ".$servlist);
                        $objResponse->addScriptCall("ajxGetPrevReq",1,$servlist);
                        #$objResponse->addScriptCall("ajxGetPrevReq",$rsObj["service_code"]);
                        $chk = 1;

                    }else{
                        $chk = 0;
                    }
                }else
                    $chk = 0;

                #$objResponse->addScriptCall("appendServiceItemToGroup",$result["group_id"],$result["service_code"],$result["name"],$price,$chk);

                $objResponse->addScriptCall("appendServiceItemToGroup",$result["group_code"],$result["service_code"],$result["name"],$price,$chk);
            }
        }else{
             #$objResponse->addAlert("populateServices FALSE");
             #$objResponse->addScriptCall("ajxGetPrevReq",0);
             $objResponse->addScriptCall("ajxClearTable",$group_code);
             $objResponse->addScriptCall("appendServiceItemToGroup2",$group_code);
        }

        #$objResponse->addAlert(print_r($sql,TRUE));
        return $objResponse;
    }


    function addTransactionDetail($refno, $pid, $name, $price, $qty) {
        $pharma_obj=new SegPharma;
        $entryno=$pharma_obj->AddTransactionDetails($refno, $pid, $qty, $price);
        $objResponse = new xajaxResponse();
        if ($entryno) {
            $objResponse->addScriptCall("pharma_retail_gui_addDestProductRow", $pid, $name, $entryno, round($price,2), round($qty), TRUE);
            #$objResponse->addAlert($pharma_obj->sql);
        }

        return $objResponse;
    }

    function delTransactionDetail($refno, $entryno, $rowno) {
        $pharma_obj=new SegPharma;
        $result=$pharma_obj->RemoveTransactionDetails($refno, $entryno);
        $objResponse = new xajaxResponse();
        if ($result) {
            $objResponse->addScriptCall("pharma_retail_gui_rmvDestProductRow",$rowno);
        }
        //$objResponse->addAlert($pharma_obj->sql);
        return $objResponse;
    }

    function populateDetails($refno) {
        $pharma_obj=new SegPharma;
        $ergebnis=$pharma_obj->GetTransactionDetails($refno);
        $objResponse = new xajaxResponse();
        $objResponse->addScriptCall("pharma_retail_gui_clearDestRows");
        $recCount = $pharma_obj->result->RecordCount();
        $counter=0;
        if ($recCount>0) {
            while($result=$ergebnis->FetchRow()) {
                $counter++;
                $objResponse->addScriptCall("pharma_retail_gui_addDestProductRow",$result["bestellnum"],$result["artikelname"],$result["entrynum"],round($result["rpriceppk"],2),$result["qty"]-0, $counter==$recCount);
            }
        }
        //$objResponse->addAlert(print_r($pharama_obj->sql,TRUE));
        return $objResponse;
    }

    function populateServiceList($keyword, $iscash) {
        global $db;
        $dbtable='care_pharma_products_main';
        $prctable = 'seg_pharma_prices';
        # clean input data

        $sql="SELECT a.*, b.ppriceppk, b.chrgrpriceppk, b.cshrpriceppk FROM $dbtable AS a LEFT JOIN $prctable AS b ON a.bestellnum=b.bestellnum WHERE artikelname REGEXP '[[:<:]]$keyword' ORDER BY artikelname";
        $ergebnis=$db->Execute($sql);
        $rows=$ergebnis->RecordCount();
        $objResponse = new xajaxResponse();
        $objResponse->addScriptCall("pharma_retail_gui_clearSrcRows");


        while($result=$ergebnis->FetchRow()) {
            $price=$iscash?$result["cshrpriceppk"]:$result["chrgrpriceppk"];
            if (!$price) $price="N/A";
            else $price=number_format($price,2,'.','');
            $objResponse->addScriptCall("pharma_retail_gui_addSrcProductRow",$result["bestellnum"],$result["artikelname"],  $price);
        }
        if (!$rows) $objResponse->addScriptCall("pharma_retail_gui_addSrcProductRow",NULL);

        //$objResponse->addAlert(print_r($sql,TRUE));
        return $objResponse;
    }

    function populateDiscountSelection() {
        global $db;
        $dbtable='seg_discount';
        $sql="SELECT * FROM $dbtable ORDER BY discountdesc";
        $ergebnis=$db->Execute($sql);
        $rows=$ergebnis->RecordCount();
        $objResponse = new xajaxResponse();
        $objResponse->addScriptCall("clearDiscountOptions");

        $cntr=0;
        while($result=$ergebnis->FetchRow()) {
            $objResponse->addScriptCall("addDiscountOption",$result["discountid"],$result["discountdesc"], $result["discount"], !$cntr);
            $cntr++;
        }
        if (!$rows) $objResponse->addScriptCall("addDiscountOption",NULL);

        //$objResponse->addAlert(print_r($sql,TRUE));
        return $objResponse;
    }

    function addRetailDiscount($refno, $id, $desc, $discount) {
        $dscObj=new SegDiscount;
        $result=$dscObj->AddRetailDiscount($refno, $id, "pharma");
        $objResponse = new xajaxResponse();
        if ($result) {
            $objResponse->addScriptCall("gui_addRDiscountRow", $id, $desc, $discount, TRUE);
            $objResponse->addAlert("Discount added");
        }
        else {
            $objResponse->addAlert(print_r($dscObj->sql,TRUE));
        }

        //$objResponse->addAlert("refno:$refno, id=$id, desc=$desc, discount=$discount");
        return $objResponse;
    }

    function populateRetailDiscounts($refno) {
        global $db;
        $objResponse = new xajaxResponse();
        $objResponse->addScriptCall("gui_clearRDiscountRows");

        $dbtable='seg_discount';
        $rdtable='seg_pharma_rdiscount';
        $sql="SELECT a.* FROM $dbtable AS a, $rdtable AS b WHERE a.discountid=b.discountid AND b.refno='$refno'";
        $ergebnis=$db->Execute($sql);
        $rows=$ergebnis->RecordCount();
        $cntr=0;
        while($result=$ergebnis->FetchRow()) {
            //$objResponse->addAlert(print_r($result,TRUE));
            $objResponse->addScriptCall("gui_addRDiscountRow", $result['discountid'], $result['discountdesc'], $result['discount']);
            $cntr++;
        }
        return $objResponse;
    }

    function rmvRetailDiscount($refno, $discountid, $rowno) {
        $dscObj=new SegDiscount;
        $result=$dscObj->RemoveRetailDiscount($refno, $discountid, "pharma");
        $objResponse = new xajaxResponse();
        if ($result) {
            $objResponse->addScriptCall("gui_rmvRDiscountRow",$rowno);
        }
        else {
            $objResponse->addAlert($dscObj->sql);
        }

        return $objResponse;
    }

        #----------added by Raissa 04-03-09
        function populateResultList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0 ) {
                global $db;
                $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
                $glob_obj->getConfig('pagin_patient_search_max_block_rows');
                $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

                $objResponse = new xajaxResponse();
                $srv=new SegLab;
                $dept_obj=new Department;
                $ward_obj = new Ward;
                $person_obj=new Person();

                $offset = $page * $maxRows;
                #$searchkey = strtr($searchkey, '*?', '%_');
                #$objResponse->addAlert('is done = '.$done);

                #added by VAN 03-24-08
                $searchkey = utf8_decode($searchkey);

                if ($searchkey==NULL)
                        $searchkey = 'now';

                        #$objResponse->addAlert("mode = ".$mod);
                //$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
                $total_srv = $srv->countSearchReqResults($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
                #$objResponse->addAlert($srv->sql);
                $total = $srv->count;
                #$objResponse->addAlert('total = '.$total);
                $lastPage = floor($total/$maxRows);
                #$objResponse->addAlert('total = '.floor($total%10));
                if ((floor($total%10))==0)
                        $lastPage = $lastPage-1;

                if ($page > $lastPage) $page=$lastPage;
                //$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
                $ergebnis=$srv->SearchReqResults($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
                #$objResponse->addAlert("sql = ".$srv->sql);
                $rows=0;

                #$objResponse->addAlert("pageno, lastpage, pagen, total = ".$page.", ".$lastPage.", ".$maxRows.", ".$total);
                $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
                $objResponse->addScriptCall("clearList","RequestList");
                if ($ergebnis) {
                        $rows=$ergebnis->RecordCount();
                        while($result=$ergebnis->FetchRow()) {
                                #$objResponse->addAlert(print_r($result));
                                $with_res = 0;
                                if($result["group_id"]!=""){
                                        $sql = "SELECT refno FROM seg_lab_resultdata WHERE group_id='".$result["group_id"]."' AND refno='".$result["refno"]."'";
                                        $rs = $db->Execute($sql);
                                        if($rs){
                                                if($res = $rs->FetchRow())
                                                        $with_res = 1;
                                        }
                                }
                                else{
                                        $sql = "SELECT refno FROM seg_lab_resultdata WHERE service_code='".$result["service_code"]."' AND refno='".$result["refno"]."'";
                                        $rs = $db->Execute($sql);
                                        if($rs){
                                                if($res = $rs->FetchRow())
                                                        $with_res = 1;
                                        }
                                }
                                #$objResponse->addAlert($result["service_name"]);
                                $urgency = $result["is_urgent"]?"Urgent":"Normal";
                                if ($result["pid"]!=" ")
                                        #$name = ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])))." ".ucwords(strtolower(trim($result["name_last"])));
                                        $name = ucwords(strtolower(trim($result["name_last"]))).", ".ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])));
                                else
                                        $name = trim($result["ordername"]);

                                if (!$name) $name='<i style="font-weight:normal">No name</i>';

                                if ($result["serv_dt"]) {
                                        $date = strtotime($result["serv_dt"]);
                                        $time = strtotime($result["serv_tm"]);
                                        $requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
                                }

                                        $sql = "SELECT pr.ref_no,pr.service_code, pr.or_no AS or_no
                                                            FROM seg_pay_request AS pr
                                                            INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
                                                                WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
                                                            AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
                                                    UNION
                                                        SELECT gr.ref_no,gr.service_code,IF(gr.grant_no,'CLASS D','') AS or_no
                                                             FROM seg_granted_request AS gr
                                             WHERE gr.ref_source = 'LD'
                                             AND gr.ref_no = '".trim($result["refno"])."'";
                                        //echo $sql;
                                         $res=$db->Execute($sql);
                                     $row=$res->RecordCount();
                                     $result2 = $res->FetchRow();
                                        if ($row==0){
                                            $paid = 0;
                                        }else{
                                            $paid = 1;
                                        }
                                if ($result["date_birth"]!='0000-00-00')
                                        $age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
                                else
                                        #if ($result["age"]==0)
                                        $age = $result["age"];

                                #$objResponse->addAlert("type = ".$result["encounter_type"]);
                                if ($result['encounter_type']==1){
                                        $enctype = "ERPx";
                                        $location = "EMERGENCY ROOM";
                                }elseif ($result['encounter_type']==2){
                                        #$enctype = "OUTPATIENT (OPD)";
                                        $enctype = "OPDx";
                                        $dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                                        $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                                }elseif (($result['encounter_type']==3)||($result['encounter_type']==4)){
                                        if ($result['encounter_type']==3)
                                                $enctype = "INPx (ER)";
                                        elseif ($result['encounter_type']==4)
                                                $enctype = "INPx (OPD)";

                                        $ward = $ward_obj->getWardInfo($result['current_ward_nr']);
                                        $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
                                }else{
                                        $enctype = "WPx";
                                        #$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                                        #$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                                        $location = 'WALK-IN';
                                }

                                #---------------------

                                #added by VAN 01-14-08
                                if (empty($result["parent_refno"]))
                                        $repeat = 0;
                                else
                                        $repeat = 1;

                                $service=$result["service_code"];
                                $name = $result["service_name"];
                                /*$sql = "SELECT s.name
                                                FROM seg_lab_servdetails AS d
                                                LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code
                                                LEFT JOIN seg_lab_services AS s ON s.service_code = gp.service_code
                                                WHERE d.refno='".$result["refno"]."' AND gp.group_id=".$result["group_id"];
                                #$objResponse->addAlert($sql);
                                $rs = $db->Execute($sql);
                                while($rs!=NULL && $rst = $rs->FetchRow()){
                                        if($service=="")
                                                $service .= $rst["name"];
                                        else
                                                $service .= ", ".$rst["name"];
                                }
                                if($service==""){
                                        $sql = "SELECT DISTINCT(s.name)
                                                        FROM seg_lab_servdetails AS d
                                                        LEFT JOIN seg_lab_group AS g ON g.service_code_child = d.service_code
                                                        LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = g.service_code
                                                        LEFT JOIN seg_lab_services AS s ON s.service_code = g.service_code
                                                        WHERE d.refno='".$result["refno"]."' AND gp.group_id=".$result["group_id"];
                                        #$objResponse->addAlert($sql);
                                        $rs = $db->Execute($sql);
                                        while($rs!=NULL && $rst = $rs->FetchRow()){
                                                if($service=="")
                                                        $service .= $rst["name"];
                                                else
                                                        $service .= ", ".$rst["name"];
                                        }
                                }*/
                                if ($mod){
                                        $labresult = $srv->hasResult(trim($result["refno"]));

                                        if ($labresult)
                                                $labstatus = 1;
                                        else
                                                $labstatus = 0;

                                        if ($result["type_charge"]){
                                                $result2['or_no'] = $result['charge_name'];
                                        }

                                        #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $paid);
                                        #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
                                        $objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),mb_strtolower($result["sex"]),$location, $enctype,$result2['or_no'],$result["is_cash"], $with_res, $result["is_served"]);
                                }else{
                                        #$objResponse->addAlert("ref = ".trim($result["refno"])." - ".$result["service_code"]);
                                        $labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);

                                        if ($labresult)
                                                $labstatus = 1;
                                        else
                                                $labstatus = 0;

                                        if ($result["type_charge"]){
                                                $result2['or_no'] = $result['charge_name'];
                                        }
                                        $objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $service, $result["group_id"], $repeat, trim($result["pid"]),floor($age),mb_strtolower($result["sex"]),$location, $enctype,'','', $with_res, $result["is_served"]);
                                }
                                #$count++;
                        }
                }
                if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
                if ($sElem) {
                        $objResponse->addScriptCall("endAJAXSearch",$sElem);
                }

                return $objResponse;
        }

    #----------added by VAN 09-12-07
    function populateRequestList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0 ) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        $dept_obj=new Department;
        $ward_obj = new Ward;
        $person_obj=new Person();

        $offset = $page * $maxRows;
        #$searchkey = strtr($searchkey, '*?', '%_');

                #$objResponse->addAlert(' "'.$done.'" "'.$sElem.'" "'.$searchkey.'" "'.$page.'" "'.$include_firstname.'" "'.$mod.'" "'.$encounter_nr.'" "'.$is_doctor.'"');

        #added by VAN 03-24-08
        $searchkey = utf8_decode($searchkey);

        if ($searchkey==NULL)
            $searchkey = 'now';

            #$objResponse->addAlert("mode = ".$mod);
        //$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);

        $ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, 0,$is_doctor, $encounter_nr,0,1);
        #$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, 0,$is_doctor, $encounter_nr);
        #$objResponse->addAlert($srv->sql);
        $total = $srv->count;
        #$objResponse->addAlert('total = '.$total);
        $lastPage = floor($total/$maxRows);
        #$objResponse->addAlert('total = '.floor($total%10));
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;

        if ($page > $lastPage) $page=$lastPage;
        //$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
        $ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, 0,$is_doctor, $encounter_nr,0,0);
        #$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, 0,$is_doctor, $encounter_nr);
                #$objResponse->addAlert("sql = ".$srv->sql);
        $rows=0;

        #$objResponse->addAlert("pageno, lastpage, pagen, total = ".$page.", ".$lastPage.", ".$maxRows.", ".$total);
        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","RequestList");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {

                                $with_res = 0;
                                $sql = "SELECT * FROM seg_lab_resultdata WHERE service_code='".$result["service_code"]."' AND refno='".$result["refno"]."'";
                                $rs = $db->Execute($sql);
                                if($rs){
                                        if($rs = $rs->FetchRow())
                                                $with_res = 1;
                                }

                $urgency = $result["is_urgent"]?"Urgent":"Normal";
                if ($result["pid"]!=" ")
                    #$name = ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])))." ".ucwords(strtolower(trim($result["name_last"])));
                    $name = ucwords(strtolower(trim($result["name_last"]))).", ".ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])));
                else
                    $name = trim($result["ordername"]);

                if (!$name) $name='<i style="font-weight:normal">No name</i>';

                if ($result["serv_dt"]) {
                    $date = strtotime($result["serv_dt"]);
                    $time = strtotime($result["serv_tm"]);
                    $requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
                }

                #$objResponse->addAlert("type = ".$result["charge_name"]);

                #$objResponse->addAlert("type = ".$result2['or_no']);
                # check if this request is already paid or not
                #if ($mod){
                /*
                    $sql = "SELECT pr.ref_no,pr.service_code, pr.or_no AS or_no
                                FROM seg_pay_request AS pr
                                    LEFT JOIN care_test_request_radio AS d ON d.refno=pr.ref_no AND ref_source='RD' AND d.service_code=pr.service_code
                                LEFT JOIN seg_radio_serv AS r ON r.refno=pr.ref_no    AND ref_source='RD'
                                WHERE r.status NOT IN ('deleted','hidden','inactive','void')  AND d.status NOT IN ('deleted','hidden','inactive','void')
                                    AND pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
                                        UNION
                                            SELECT gr.ref_no,gr.service_code,IF(gr.grant_no,'CLASS D','') AS or_no
                                FROM seg_granted_request AS gr
                                LEFT JOIN care_test_request_radio AS d ON d.refno=gr.ref_no AND ref_source='RD' AND d.service_code=gr.service_code
                                LEFT JOIN seg_radio_serv AS r ON r.refno=gr.ref_no    AND ref_source='RD'
                                WHERE r.status NOT IN ('deleted','hidden','inactive','void')  AND d.status NOT IN ('deleted','hidden','inactive','void')
                                             AND gr.ref_source = 'LD' AND gr.ref_no = '".trim($result["refno"])."'";
                */
                    $sql = "SELECT pr.ref_no,pr.service_code, pr.or_no AS or_no
                                                            FROM seg_pay_request AS pr
                                                            INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
                                                                WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
                                                            AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
                                                    UNION
                                                        SELECT gr.ref_no,gr.service_code,IF(gr.grant_no,'CLASS D','') AS or_no
                                                             FROM seg_granted_request AS gr
                                             WHERE gr.ref_source = 'LD'
                                             AND gr.ref_no = '".trim($result["refno"])."'";
                    //echo $sql;
                         $res=$db->Execute($sql);
                         $row=$res->RecordCount();
                     $result2 = $res->FetchRow();
                    /*
                    #added by VAN 03-07-08
                    $sql2 = "SELECT pr.ref_no,pr.service_code FROM seg_pay_request AS pr
                                    WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'";

                         $res2=$db->Execute($sql2);
                        $row2=$res->RecordCount();
                    $result = $res2->FetchRow();
                    $objResponse->addAlert('or no = '.$result['or_no']);
                    */
                    if ($row==0){
                        $paid = 0;
                    }else{
                        $paid = 1;
                    }
                    #$objResponse->addAlert('or no = '.$result2['or_no']);
                #}

                #$objResponse->addAlert("labresult = ".$result["refno"]." - ".$labresult);
                #$objResponse->addAlert($result["refno"]." - row = ".$row." paid = ".$paid);
                #$objResponse->addScriptCall("addPerson","RequestList",$result["refno"],$name,$requestDate,$urgency,$count);
                #$objResponse->addAlert("repeat = ".$result["parent_refno"]);

                #added by VAN 06-03-08
                if ($result["date_birth"]!='0000-00-00')
                    $age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
                else
                    #if ($result["age"]==0)
                    $age = $result["age"];

                #$objResponse->addAlert("type = ".$result["encounter_type"]);
                if ($result['encounter_type']==1){
                    $enctype = "ERPx";
                    $location = "EMERGENCY ROOM";
                }elseif ($result['encounter_type']==2){
                    #$enctype = "OUTPATIENT (OPD)";
                    $enctype = "OPDx";
                    $dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                    $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                }elseif (($result['encounter_type']==3)||($result['encounter_type']==4)){
                    if ($result['encounter_type']==3)
                        $enctype = "INPx (ER)";
                    elseif ($result['encounter_type']==4)
                        $enctype = "INPx (OPD)";

                    $ward = $ward_obj->getWardInfo($result['current_ward_nr']);
                    $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
                }else{
                    $enctype = "WPx";
                    #$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                    #$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                    $location = 'WALK-IN';
                }

                #---------------------

                #added by VAN 01-14-08
                if (empty($result["parent_refno"]))
                    $repeat = 0;
                else
                    $repeat = 1;


                if ($mod){
                    $labresult = $srv->hasResult(trim($result["refno"]));

                    if ($labresult)
                        $labstatus = 1;
                    else
                        $labstatus = 0;

                    if ($result["type_charge"]){
                        $result2['or_no'] = $result['charge_name'];
                    }

                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $paid);
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
                    $objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),mb_strtolower($result["sex"]),$location, $enctype,$result2['or_no'],$result["is_cash"], $with_res);
                }else{
                    #$objResponse->addAlert("ref = ".trim($result["refno"])." - ".$result["service_code"]);
                    $labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);

                    if ($labresult)
                        $labstatus = 1;
                    else
                        $labstatus = 0;

                    if ($result["type_charge"]){
                        $result2['or_no'] = $result['charge_name'];
                    }
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$labstatus, $result["service_name"], $result["service_code"]);
                    #$objResponse->addAlert($result['charge_name']);
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$labstatus, $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
                    #edited by VAN 07-03-08
                    $objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),mb_strtolower($result["sex"]),$location, $enctype, $with_res);
                }
                #$count++;
            }
        }
        if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }


    function setALLDepartment($dept_nr=0){
            #    global $dept_obj;

        $dept_obj=new Department;

        $objResponse = new xajaxResponse();
        #$objResponse->addAlert("setALLDepartment");
        $rs=$dept_obj->getAllMedicalObject();
#$objResponse->addAlert("setALLDepartment : rs = '".$rs."'");
        $objResponse->addScriptCall("ajxClearDocDeptOptions",1);
        if ($rs) {
            $objResponse->addScriptCall("ajxAddDocDeptOption",1,"-Select a Department-",0);
            while ($result=$rs->FetchRow()) {
                 $objResponse->addScriptCall("ajxAddDocDeptOption",1,trim($result["name_formal"]),trim($result["nr"]));
            }
        if($dept_nr)
                $list='';
                $objResponse->addScriptCall("ajxSetDepartment", $dept_nr, $list); # set the department
        }
        else {
            $objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
        }
        return $objResponse;
    }

    function setDepartmentOfDoc($personell_nr=0) {
#        global $dept_obj;

        $dept_obj=new Department;

        $objResponse = new xajaxResponse();
        #$objResponse->addAlert("setDepartmentOfDoc : personell_nr ='$personell_nr'");
            if ($personell_nr!=0){
            $result=$dept_obj->getDeptofDoctor($personell_nr);
            #$objResponse->addAlert("setDepartmentOfDoc : dept_obj->sql = '$dept_obj->sql'");
            #$objResponse->addAlert("setDepartmentOfDoc : name_formal = ".$result["name_formal"]." - ".$result["nr"]);
            if ($result){
                $list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
    #$objResponse->addAlert("setDepartmentOfDoc : list = '$list'; result['nr'] = '".$result['nr']."'");
                if (trim($list)!="")
                    $list .= ",".trim($result["nr"]);
                else
                    $list .= trim($result["nr"]);
                $objResponse->addScriptCall("ajxSetDepartment",trim($result["nr"]),$list); # set the department
            }
            if($personell_nr)
                $objResponse->addScriptCall("ajxSetDoctor",$personell_nr); # set the doctor

        }else{
            $objResponse->addAlert("setDepartmentOfDoc : Error retrieving Department information of a doctor...");
        }
        return $objResponse;
    }

    function setDoctors($dept_nr=0, $personell_nr=0) {
#        global $pers_obj;

        $objResponse = new xajaxResponse();

        $pers_obj=new Personell;
        #$objResponse->addAlert("dept : $dept_nr");
        if ($dept_nr)
            $rs=$pers_obj->getDoctorsOfDept($dept_nr);
        else
            $rs=$pers_obj->getDoctors(2);    # argument, $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

#        $objResponse->addAlert("setDoctors : dept_nr = '".$dept_nr."'");
#        $objResponse->addAlert("setDoctors : pers_obj->sql = '".$pers_obj->sql."'");
        #$objResponse->addAlert("setDoctors".$admit_inpatient."=".$dept_nr);

        $objResponse->addScriptCall("ajxClearDocDeptOptions",0);
        if ($rs) {
            $objResponse->addScriptCall("ajxAddDocDeptOption",0,"-Select a Doctor-",0);

            while ($result=$rs->FetchRow()) {
                    #$doctor_name = trim($result["name_first"])." ".trim($result["name_2"])." ".trim($result["name_last"]);
                #$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));

                if (trim($result["name_middle"]))
                    $dot  = ".";

                $doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
                $doctor_name = ucwords(strtolower($doctor_name)).", MD";

                $doctor_name = htmlspecialchars($doctor_name);
                $objResponse->addScriptCall("ajxAddDocDeptOption",0,$doctor_name,trim($result["personell_nr"]));
            }
            if($personell_nr)
                $objResponse->addScriptCall("ajxSetDoctor", $personell_nr); # set the doctor
            if($dept_nr)
                $objResponse->addScriptCall("ajxSetDepartment", $dept_nr); # set the department
            $objResponse->addScriptCall("request_doc_handler"); # set the 'request_doctor_out' textbox
        }
        else {
            #$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
            $objResponse->addScriptCall("ajxAddDocDeptOption",0,"-No Doctor Available-",0);
        }
        return $objResponse;
    }
    #--------------------------------------

    #added by VAN 08-21-08
    /*function savedServedPatient($refno, $service_code,$is_served){
        global $db, $HTTP_SESSION_VARS;

        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        #$objResponse->addAlert("ajax : refno, code = ".$refno." , ".$service_code);

        if ($is_served)
            $date_served = date("Y-m-d H:i:s");
        else
            $date_served = '';

        $save = $srv->ServedLabRequest($refno, $service_code, $is_served, $date_served);
        #$objResponse->addAlert("sql = ".$srv->sql);
        if ($save){
            $objResponse->addScriptCall("ReloadWindow");
        }

        return $objResponse;

    }*/
        function savedServedPatient($refno, $group_id,$is_served, $service_code=''){
                global $db, $HTTP_SESSION_VARS;

                $objResponse = new xajaxResponse();
                $srv=new SegLab;
                #$objResponse->alert("ajax : refno, code = ".$refno." , ".$group_id);

                if ($is_served)
                        $date_served = date("Y-m-d H:i:s");
                else
                        $date_served = '';

                $save = $srv->ServedLabRequest($refno, $group_id, $is_served, $date_served, $service_code);
                #$objResponse->addAlert("sql = ".$srv->sql);
                if ($save){
                        $objResponse->addScriptCall("ReloadWindow");
                }

                return $objResponse;

        }

        function saveOfficialResult($refno, $group_id,$is_served, $service_code=''){
                global $db, $HTTP_SESSION_VARS;

                $objResponse = new xajaxResponse();
                $srv=new SegLab;
                #$objResponse->addAlert("ajax : refno, code = ".$refno." , ".$group_id);
                if ($is_served)
                        $date_served = date("Y-m-d H:i:s");
                else
                        $date_served = '';

                $save = $srv->OfficialLabResult($refno, $group_id, $is_served, $date_served, $service_code);
                #$objResponse->addAlert("sql = ".$srv->sql);
                if ($save){
                    #$objResponse->alert("Finalizing ". $refno ."...");
                    $objResponse->addAlert("Successfully Saved and Done Results!");
                    $objResponse->addScriptCall("ReloadWindow",$pid);
                }

                return $objResponse;

        }


    function getDeptDocValues($encounter_nr){
        global $db;
                $objResponse = new xajaxResponse();

                $enc_obj=new Encounter;

        $patient = $enc_obj->getPatientEncounter($encounter_nr);

        #$objResponse->alert($patient['current_dept_nr']);
        if (($patient['encounter_type']==1)|| ($patient['encounter_type']==2) || ($patient['encounter_type']==5)){
            $dept_nr = $patient['current_dept_nr'];
            $doc_nr = $patient['current_att_dr_nr'];
        }elseif (($patient['encounter_type']==3)|| ($patient['encounter_type']==4) || ($patient['encounter_type']==6)){
            $dept_nr = $patient['consulting_dept_nr'];
            $doc_nr = $patient['consulting_dr_nr'];
        }else{
            $dept_nr = 0;
            $doc_nr = 0;
        }

        $objResponse->addScriptCall("setDeptDocValues",$dept_nr, $doc_nr);

        return $objResponse;
    }

    function saveProcessRequest($mode, $refno='', $service_code, $reagents){
                global $root_path, $HTTP_SESSION_VARS;

                $objResponse = new xajaxResponse();

                $lab_obj=new SegLab();
                $inventory_obj=new Inventory();

                $list_reagents = array();
                foreach ($reagents as $i=>$v) {
                        if ($v) $list_reagents[] = $v;
                }

                $unit = 2;
                if ($list_reagents!=NULL){
                        #$objResponse->addAlert(print_r($list_reagents,true));
                        $lab_obj->clearLabReagentProcess($refno, $service_code);
                #$objResponse->addAlert("clear = ".$lab_obj->sql);
                        $ok = $lab_obj->createLabReagentProcess($refno, $service_code, $list_reagents);
                        #$objResponse->addAlert("add = ".$ok);
                        #$objResponse->addAlert(print_r($list_reagents,true));

                        if($ok){
                                $request = $lab_obj->getRequestExamInfo($refno, $service_code);
                                #$objResponse->addAlert("select = ".$lab_obj->sql);
                                for ($i=0; $i<sizeof($list_reagents);$i++){
                                        #$objResponse->addAlert("code,qty = ".$list_reagents[$i][0]." - ".$list_reagents[$i][1]);
                                        #$inventory_obj->setParams($list_reagents[$i][0],$request["area_code"]);
                                        $inventory_obj->setInventoryParams($list_reagents[$i][0],$request["area_code"]);
                                        $inventory_obj->remInventory($list_reagents[$i][1], $unit);
                                        #$objResponse->addAlert("sql = ".$inventory_obj->sql);
                                }

                        }

                }

                if ($ok){
                        $objResponse->addScriptCall("msgPopUp","Successfully processed the request.");
                }else{
                        $objResponse->addScriptCall("msgPopUp","Failed to processed the request.");
                }

                return $objResponse;
        }#end of function saveScheduledRequest

    function savedSentOutRequest($refno,$group_id,$service_code,$reason,$key,$page,$mod){
        global $db, $HTTP_SESSION_VARS;

        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        #$objResponse->addAlert("ajax : refno, code, reason, id = ".$refno." , ".$service_code." , ".$reason." , ".$group_id);

        #$sent_out_date = date("Y-m-d H:i:s");
        #$objResponse->addAlert("sql = ".$key.','.$page.','.$mod);
        #$save = $srv->SentOutLabRequest($refno,$service_code,$reason, $sent_out_date);
        if (!$group_id){
            $save = $srv->SentOutLabRequest($refno,$group_id,$service_code,$reason);
            #$objResponse->alert($srv->sql);
        }else{
                #will add a function for group test, enhance it
                #$save = $srv->SentOutLabRequest($refno,$group_id,$service_code,$reason);

                $sql_grp = "SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled
                                                                        FROM seg_lab_result_groupparams as gp
                                                                        LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
                                                                        LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='".$refno."' AND (ISNULL(r.status) OR r.status!='deleted')
                                                                        LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
                                                                        INNER JOIN seg_lab_servdetails AS d ON d.service_code=p.service_code AND d.refno='".$refno."'
                                                                        WHERE gp.group_id=".$group_id." AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
                                                                        UNION SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled
                                                                        FROM seg_lab_result_groupparams as gp
                                                                        LEFT JOIN seg_lab_result_group as g ON g.service_code = gp.service_code
                                                                        LEFT JOIN seg_lab_result_params as p ON p.service_code = g.service_code_child
                                                                        LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='".$refno."' AND (ISNULL(r.status) OR r.status!='deleted')
                                                                        LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
                                                                        INNER JOIN seg_lab_servdetails AS d ON (d.service_code=g.service_code OR d.service_code=p.service_code) AND d.refno='2010000010'
                                                                        WHERE gp.group_id=".$group_id." AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
                                                                        ORDER BY order2, order_nr ASC";
                $rs = $db->Execute($sql_grp);
                #$objResponse->alert($sql_grp);
                if ($rs){
                    $count = $rs->RecordCount();

                    if ($count==1){
                        $row = $rs->FetchRow();
                        $service_code = "'".$row['service_code']."'";
                    }else{
                        while($row=$rs->FetchRow()){
                                $keyword .=  "'".trim($row['service_code'])."',";
                                $word = trim($keyword);
                                $service_code = substr($word,0,strlen($word)-1);
                            }
                    }
                    $save = $srv->SentOutLabRequest($refno,$group_id,$service_code,$reason);
                }
        }
        #$objResponse->addAlert("sql = ".$srv->sql);
        if ($save){#(searchID, page, mod)
            $objResponse->addScriptCall("startAJAXSearch2",$key, $page,$mod);
        }

        return $objResponse;
    }
    #----------------------

    #added by VAN 10-02-09
        function getAllServiceOfPackage($service_code){
                global $db;
                $objResponse = new xajaxResponse();
                $srv=new SegLab;

                #$objResponse->alert("ajax = ".$service_code);
                $is_package = $srv->isTestAPackage($service_code);
                #$rs_count = $srv->count;

                #$objResponse->alert("ajax count = ".$rs_count);
                if ($is_package){
                    #$objResponse->alert("it is a package");
                    $rs_group_inc = $srv->getAllServiceOfPackage($service_code);
                    #lab exam request that is a package
                    while ($row=$rs_group_inc->FetchRow()){
                            #$objResponse->alert('ajax = '.$row['service_code']);
                            $objResponse->addScriptCall("prepareAdd_Package",$row['service_code'],$row['name'],$row['cash'],$row['charge'],$row['sservice'],$row['group_code'],$row['priceC1'],$row['priceC2'],$row['priceC3']);
                    }

                } else{
                     #lab exam request that is not a package
                     $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code);
                }

                return $objResponse;
        }

        #added by VAN 01-09-10
        function servedRequest($refno,$group_id,$service_code,$key,$page,$mod,$is_served=0){
            global $db, $HTTP_SESSION_VARS;

            $objResponse = new xajaxResponse();
            $srv=new SegLab;

            if (!$group_id){
                if ($is_served)
                        $date_served = date("Y-m-d H:i:s");
                else
                        $date_served = '';

                $save = $srv->ServedLabRequest($refno, $group_id, $is_served, $date_served, $service_code,'done');
                #$objResponse->addAlert("sql = ".$srv->sql);

            }else{
                    #will add a function for group test
            }
            #$objResponse->addAlert("sql = ".$srv->sql);
            if ($save){#(searchID, page, mod)
                $objResponse->addScriptCall("startAJAXSearch2",$key, $page,$mod);
            }

            return $objResponse;
        }

    #added by CHA, March 20, 2010
    function populate_lab_checklist($section, $searchkey="")
    {
        global $db;
        $objResponse = new xajaxResponse();
        $objResponse->addAssign("checklist-div", "innerHTML", "");
        $query = "SELECT gm.* FROM seg_gui_mgr AS gm WHERE gm.ref_source='LD' AND gm.section=".$db->qstr($section);
        #$objResponse->alert($query);
        $result = $db->Execute($query);
        if($result->RecordCount()>0) {
            while($row=$result->FetchRow())
            {
                //$query2 = "SELECT gmd.*, l.name, l.status, l.price_cash as`cash`, l.price_charge as `charge`, \n".
//                                    "l.group_code,l.is_socialized FROM seg_gui_mgr_details AS gmd \n".
//                                    "LEFT JOIN seg_lab_services AS l ON gmd.service_code=l.service_code \n".
//                                    "WHERE gmd.nr=".$db->qstr($row["nr"]);

                #edited by VAN 07-29-2010
                $query2 = "SELECT gmd.*, l.name, l.status,
                                    IF(l.is_socialized=0,
                                         IF((l.in_phs=1 AND '$discountid'='PHS' AND $is_cash),(l.price_cash*(1-$discount)),IF($is_cash,IF($is_senior,l.price_cash*(1-$sc_walkin_discount),l.price_cash),l.price_charge)),
                                         IF($is_cash,
                                                     IF($is_senior,IF($is_cash,IF($is_walkin,(l.price_cash*(1-$sc_walkin_discount)),
                                                     IF(sd.price,sd.price,(l.price_cash*(1-$discount)))),l.price_charge),
                                                     IF($is_cash,
                                                             IF(sd.price,sd.price,
                                                                 IF($is_cash,
                                                                            (l.price_cash*(1-$discount)),
                                                                            (l.price_charge*(1-$discount))
                                                                 )
                                                             ),
                                                             l.price_charge
                                                        )
                                            ),
                                            l.price_charge)
                                    ) AS net_price,
                                    l.price_cash as`cash`, l.price_charge as `charge`, \n".
                                    "l.group_code,l.is_socialized FROM seg_gui_mgr_details AS gmd \n".
                                    "LEFT JOIN seg_lab_services AS l ON gmd.service_code=l.service_code \n".
                                    "LEFT JOIN seg_service_discounts AS sd ON sd.service_code=l.service_code
                                        AND sd.service_area='LB' AND sd.discountid='D'  \n".
                                    "WHERE gmd.nr=".$db->qstr($row["nr"]);
                #$objResponse->alert($query2);
                $if_exists = true;
                if($searchkey!="") {
                    $search_sql = "SELECT IF(EXISTS(SELECT l.service_code FROM seg_lab_services AS l WHERE l.service_code=".
                        "gmd.service_code),1,0) AS `is_existing` \n".
                        "FROM seg_gui_mgr_details AS gmd LEFT JOIN seg_lab_services AS l ON l.service_code=gmd.service_code \n".
                        "WHERE (gmd.service_code LIKE '%".$searchkey."%' OR l.name LIKE '%".$searchkey."%') AND gmd.nr='".$row["nr"]."'";
                    $if_exists = $db->GetOne($search_sql);
                    if(!empty($if_exists))
                    {
                         $query2.= "AND ((l.service_code like '%".$searchkey."%' OR l.name like '%".$searchkey."%')".
                                            " OR gmd.name_type='H')";
                    }
                }

                if($if_exists)
                {
                    $query2.=" ORDER BY gmd.row_order_no, gmd.col_order_no ASC";
                    $guiRes = $db->Execute($query2);
                    while($guiDetails=$guiRes->FetchRow())
                    {
                         if($guiDetails["name_type"]=="D")
                         {
                             #added by VAN 06-26-2010
                             if ($guiDetails['status']=='unavailable')
                                $available = 0;
                             else
                                $available = 1;
                            #edited by VAN 07-30-2010
                             $service_details[] = array(
                                    "type"=>$guiDetails["name_type"],
                                    "col_nr"=>$guiDetails["col_order_no"],
                                    "row_nr"=>$guiDetails["row_order_no"],
                                    "service_code"=>$guiDetails["service_code"],
                                    "service_name"=>$guiDetails["name"],
                                    "service_cash"=>$guiDetails["cash"],
                                    "service_charge"=>$guiDetails["charge"],
                                    "service_net_price"=>$guiDetails["net_price"],
                                    "group_code"=>$guiDetails["group_code"],
                                    "sservice"=>$guiDetails["is_socialized"],
                                    "available"=>$available
                                );
                         }
                         else    if($guiDetails["name_type"]=="H") {
                             $service_details[] = array(
                                "type"=>$guiDetails["name_type"],
                                "col_nr"=>$guiDetails["col_order_no"],
                                "row_nr"=>$guiDetails["row_order_no"],
                                "header"=>$guiDetails["header_data"]);
                         }
                    }
                    $objResponse->addScriptCall("print_checklist", $service_details, $row["nr"]);
                }
                $service_details = array();
            }
            if(!$if_exists){
                    $objResponse->addScriptCall("print_checklist_message", "SERVICE NOT FOUND..");
            }
        }
        else {
                $objResponse->addScriptCall("print_checklist_message", "NO CHECKLIST AVAILABLE FOR THIS SECTION..");
        }

        return $objResponse;
    }
    #end CHA---------------------

    function populateLabRequestList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0,$pid='',$ref_source='LB') {
            global $db;
            $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
            $glob_obj->getConfig('pagin_patient_search_max_block_rows');
            $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

            $objResponse = new xajaxResponse();
            $srv=new SegLab;
            $dept_obj=new Department;
            $ward_obj = new Ward;
            $person_obj=new Person();

            $offset = $page * $maxRows;
            #$objResponse->alert($pid);

            $searchkey = utf8_decode($searchkey);

            if ($searchkey==NULL)
                $searchkey = '*';

            $ergebnis=$srv->getLabRequestByEnc($searchkey,$maxRows,$offset,$encounter_nr,$pid,$ref_source,0);
            $total = $srv->FoundRows();
            $lastPage = floor($total/$maxRows);
            #$objResponse->alert($srv->sql);
            if ((floor($total%10))==0)
                $lastPage = $lastPage-1;

            if ($page > $lastPage) $page=$lastPage;
            #$ergebnis=$srv->getLabRequestByEnc($searchkey,$maxRows,$offset,$encounter_nr,$pid,1);
            $rows=0;
            #$objResponse->alert($srv->sql);
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","RequestList");
            if ($ergebnis) {
                $rows=$ergebnis->RecordCount();
                while($result=$ergebnis->FetchRow()) {
                        $urgency = $result["is_urgent"]?"Urgent":"Normal";

                        if ($result["serv_dt"]) {
                            $date = strtotime($result["serv_dt"]);
                            $time = strtotime($result["serv_tm"]);
                            $requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
                        }

                        $servDate = '';
                        if (($result["date_served"])&&($result["date_served"]!='0000-00-00 00:00:00')) {
                            $servDate = date("M d, Y h:i A",strtotime($result["date_served"]));
                        }

                        if ($result['is_cash']==0)
                            $or_no = "CHARGE";
                        elseif ($result['request_flag'])
                            $or_no = mb_strtoupper($result["request_flag"]);
                            
                        $lis_order_no = $result["lis_order_no"];     
                        
                        if ($result['nth_take']==1){
                           $services = $result['services'].'<font color="RED"> (First Take)</font>'; 
                        }elseif ($result['nth_take'] > 1){
                           $service_code = $db->qstr($result['service_code']); 
                           $sql_l = "SELECT name FROM seg_lab_services WHERE service_code=$service_code"; 
                           $services = $db->GetOne($sql_l);
                           
                           switch($result['nth_take']){
                                case '1' :  
                                            $nth_take = 'First'; 
                                            break;
                                case '2' :  
                                            $nth_take = 'Second'; 
                                            break;
                                case '3' :  
                                            $nth_take = 'Third'; 
                                            break;
                                case '4' :  
                                            $nth_take = 'Fourth'; 
                                            break;
                                case '5' :  
                                            $nth_take = 'Fift'; 
                                            break;
                                case '6' :  
                                            $nth_take = 'Sixth'; 
                                            break;
                                case '7' :  
                                            $nth_take = 'Seventh'; 
                                            break;
                                case '8' :  
                                            $nth_take = 'Eighth'; 
                                            break;
                                case '9' :  
                                            $nth_take = 'Ninth'; 
                                            break;
                                case '10' : 
                                            $nth_take = 'Tenth'; 
                                            break;
                            }
    
                           $services = $services.'<font color="RED"> ('.$nth_take.' Take)</font>'; 
                        }else{
                           $services = $result['services'];
                        }
                        
                        $result_date = '';
                        if (($result["date_received"])&&($result["date_received"]!='0000-00-00 00:00:00')) {
                            $result_date = date("M d, Y h:i A",strtotime($result["date_received"]));
                        } 
                        
                        $withresult = 0;
                        if ($result['filename'])
                            $withresult = 1;
                        
                        $objResponse->addScriptCall("addResultList","RequestList",$result["refno"],$services,$requestDate,$urgency,$or_no,$pid,$encounter_nr,$servDate, $lis_order_no, $result_date, $withresult);

                }
        }
        if (!$rows) $objResponse->addScriptCall("addResultList","RequestList",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }
    
    function populateLabResultList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0,$pid='',$ref_source='LB') {
            global $db;
            $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
            $glob_obj->getConfig('pagin_patient_search_max_block_rows');
            $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

            $objResponse = new xajaxResponse();
            $srv=new SegLab;
            $dept_obj=new Department;
            $ward_obj = new Ward;
            $person_obj=new Person();

            $offset = $page * $maxRows;
            
            $searchkey = utf8_decode($searchkey);

            if ($searchkey==NULL)
                $searchkey = '*';

            $ergebnis=$srv->getLabResultPid($searchkey,$maxRows,$offset,$encounter_nr,$pid,$ref_source,0);
            $total = $srv->FoundRows();
            $lastPage = floor($total/$maxRows);
            #$objResponse->alert($srv->sql);
            if ((floor($total%10))==0)
                $lastPage = $lastPage-1;

            if ($page > $lastPage) $page=$lastPage;
            
            $rows=0;
            #$objResponse->alert($srv->sql);
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","RequestList");
            if ($ergebnis) {
                $rows=$ergebnis->RecordCount();
                while($result=$ergebnis->FetchRow()) {
                        // $resultDate = date("M d, Y h:i A",strtotime($result["request_date"]));
                            
                        // $lis_order_no = $result["lis_order_no"];     
                        
                        if ($result['nth_take']){
                           switch($result['nth_take']){
                                case '1' :  
                                            $nth_take = 'First'; 
                                            break;
                                case '2' :  
                                            $nth_take = 'Second'; 
                                            break;
                                case '3' :  
                                            $nth_take = 'Third'; 
                                            break;
                                case '4' :  
                                            $nth_take = 'Fourth'; 
                                            break;
                                case '5' :  
                                            $nth_take = 'Fifth'; 
                                            break;
                                case '6' :  
                                            $nth_take = 'Sixth'; 
                                            break;
                                case '7' :  
                                            $nth_take = 'Seventh'; 
                                            break;
                                case '8' :  
                                            $nth_take = 'Eighth'; 
                                            break;
                                case '9' :  
                                            $nth_take = 'Ninth'; 
                                            break;
                                case '10' : 
                                            $nth_take = 'Tenth'; 
                                            break;
                            }
    
                           $services = $result['name'].'<font color="RED"> ('.$nth_take.' Take)</font>'; 
                        }else{
                           $services = $result['name'];
                        }
                                                                          
                        $objResponse->addScriptCall("addResultList","RequestList",$services,$result['service_code'],date('Y-m-d',strtotime($result['service_date'])), $result['group_id'], $result['refno'], $result['form_id'], $result['nth_take']);

                }
        }
        if (!$rows) $objResponse->addScriptCall("addResultList","RequestList",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }

        //added by daryl
    function serviceByGroup($group_id){
     global $root_path;
     $objResponse = new xajaxResponse();
     $srv=new SegLab;

     $result=$srv->getserviceByGroup($group_id);
     if($result){
        for ($i=0; $i <= count($result); $i++) { 
            if($result[$i]['service_code'] != ""){
             $objResponse->addScriptCall("js_AddOptions","service_list",$result[$i]['name'],$result[$i]['service_code']);
            }
        }
     }

         return $objResponse;
    }

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    #include_once($root_path.'include/inc_date_format_functions.php');
    require($root_path.'include/care_api_classes/class_pharma_transaction.php');
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    require($root_path.'include/care_api_classes/class_discount.php');
    require($root_path.'modules/laboratory/ajax/lab-new.common.php');
    #-----------added by VAN 09-26-07-----
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/class_personell.php');
    require_once($root_path.'include/care_api_classes/class_ward.php');

    require_once($root_path.'include/care_api_classes/class_person.php');

    require_once($root_path.'include/care_api_classes/class_encounter.php');

    require_once($root_path.'include/care_api_classes/class_paginator.php');
    #-------------------------------------
    
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');

    $xajax->processRequests();
?>