<?php
# Start Smarty templating here
 /**
 * LOAD Smarty
 */
    
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
     error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    require($root_path.'include/inc_environment_global.php');
    
    require($root_path."modules/bloodBank/ajax/blood-request-list.common.php"); 
    $xajax->printJavascript($root_path.'classes/xajax');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
#$ = "labor.php";
$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 
 if ($_GET['done'])
     $done_caption = "(DONE)";
 else
     $done_caption = "(UNDONE)";  
     
 $LDBloodBank = "Blood Bank";       

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDBloodBank :: Requests Status List $done_caption");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);
 
 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBloodBank :: Requests Status List $done_caption");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 #$smarty->assign('sOnLoadJs','');
 #$smarty->assign('sOnLoadJs','onLoad="preSet(); ShortcutKeys(); startAJAXSearch(\'search\', 0, 1);"');
 $smarty->assign('sOnLoadJs','onLoad="preSet(); ShortcutKeys();DisabledSearch();"');

#added by VAN 07-02-08
#echo "done = ".$_GET['done'];
#$done = $_GET['done'];    

 # Collect javascript code
 ob_start()

?>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
    document.getElementById('search').focus();    
    /*
    if (document.getElementById('searchkey').value){
        //startAJAXSearch(document.getElementById('searchkey').id, 0, 0);
        
        var searchEL = $('searchkey');
        var keyword = document.getElementById('searchkey').value;
       // alert('keyword = '+keyword);
        if (searchEL) {
          var searchLastname = 0;    
         searchEL.style.color = "#0000ff";
         if (AJAXTimerID) clearTimeout(AJAXTimerID);
         $("ajax-loading").style.display = "";
         $("RequestList-body").style.display = "none";
         //AJAXTimerID = setTimeout("xajax_populateRequestList("+done+", 'searchkey','"+keyword+"',0,"+searchLastname+",0)",50); 
         
         lastSearch = searchEL.value;  
         
        }
    } */   
    
}

/*
function deleteRequest(refno){
    var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. "+(refno)+"?");
        if (answer){
            xajax_deleteRequest(refno);
        }
}

function removeRequest(id) {
    var table = document.getElementById("RequestList");
    var rowno;
    var rmvRow=document.getElementById("row"+id);
    if (table && rmvRow) {
        rowno = 'row'+id;
        var rndx = rmvRow.rowIndex;
        table.deleteRow(rmvRow.rowIndex);
        window.location.reload(); 
    }
}
*/

//---------------adde by VAN 02-06-08
function ShortcutKeys(){
        shortcut.add('Ctrl+Shift+N', NewRequest,
                                {
                                    'type':'keydown',
                                    'propagate':false,
                                }
                        );

         shortcut.add('Ctrl+Shift+L', RequestList,
                            {
                                'type':'keydown',
                                'propagate':false,
                            }
                         )

         shortcut.add('Ctrl+Shift+S', SearchItem,
                            {
                                'type':'keydown',
                                'propagate':false,
                            }
                         )
                         
        shortcut.add('Ctrl+Shift+M', BackMainMenu,
                            {
                                'type':'keydown',
                                'propagate':false,
                            }
                         )                     
    }

    function BackMainMenu(){
        urlholder="labor.php<?=URL_APPEND?>";
        window.location.href=urlholder;
    }

    function NewRequest(){
        //urlholder="seg-lab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
        urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood&user_origin=lab";
        window.location.href=urlholder;
    }
    
    function RequestList(){
        //urlholder="seg-lab-request-order-list.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
        urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_list&user_origin=lab";
        window.location.href=urlholder;
    }
    
    function SearchItem(){
        startAJAXSearch('search',0,0);
    }

//------------------------------------------

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page, mod) {
    var searchEL = $(searchID);
    var done = $F('done');
        
    if (mod)
        searchEL.value = "";
        
    keyword = searchEL.value;
    keyword = keyword.replace("'","^");    
        //alert('key = '+keyword);
    //var searchLastname = $('firstname-too').checked ? '1' : '0';
    //commented out/edited by pet (aug.6,2008) above line in accordance with VAS' search code changes
    var searchLastname = 0;
    if (searchEL) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ajax-loading").style.display = "";
        $("RequestList-body").style.display = "none";
        //AJAXTimerID = setTimeout("xajax_populateRequestList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+",0)",100);
        AJAXTimerID = setTimeout("xajax_populateRequestList("+done+", '"+searchID+"','"+keyword+"',"+page+","+searchLastname+",0)",50);
        lastSearch = searchEL.value;
    }
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        $("RequestList-body").style.display = "";
        searchEL.style.color = "";
    }
}

function clearList(listID) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            dBody.innerHTML = "";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function setPagination(pageno, lastpage, pagen, total) {
    currentPage=parseInt(pageno);
    lastPage=parseInt(lastpage);    
    firstRec = (parseInt(pageno)*pagen)+1;
    if (currentPage==lastPage)
        lastRec = total;
    else
        lastRec = (parseInt(pageno)+1)*pagen;
        
    //$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),0)+'-'+formatNumber((lastRec),0)+' out of '+formatNumber((parseInt(total)),0)+' record(s).</span>';
    if (parseInt(total)==0)
        $("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
    else
        $("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';    
    
    $("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
    if (el.className=="segDisabledLink") return false;
    if (lastPage==0) return false;
    switch(jumpType) {
        case FIRST_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',0,0);
        break;
        case PREV_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',currentPage-1,0);
        break;
        case NEXT_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(currentPage)+1,0);
        break;
        case LAST_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',lastPage,0);
        break;
    }
}

function refreshWindow(){
    //window.location.href=window.location.href;
}

//added by VAN 08-21-08
function ToBeServed(obj, refno, service_code){
    //alert('served = '+obj.id+" ref = "+refno+" code = "+service_code);
    var is_served;
    is_served = 1;
    var answer = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");        
    if (answer)
        xajax_savedServedPatient(refno, service_code,is_served);
}
//---------------------
                            
function addPerson(listID, refno, name, req_date, urgency, or_no, service, code, repeat, pid,age,sex,location,enctype) {
    var list=$(listID), dRows, dBody, rowSrc;
    var i, mode, editlink, ornum;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if (refno) {
            alt = (dRows.length%2)+1;
            if (urgency=='Urgent')
                priority = '<font color="#FF0000">'+urgency+'</font>';
            else
                priority = urgency;    

            if (status==1)
                reqstatus = '<font color="#000066">Done</font>';
            else if (status==0)
                reqstatus = '<font color="#FF0000">Pending</font>';
                
            if (or_no=="")
                ornum = '<font color="#FF0000">Charge</font>';
            else
                ornum = '<font color="#000066">'+or_no+'</font>';    
            
            editlink ='onclick="return overlib(OLiframeContent(\'../../modules/laboratory/seg-lab-request-result.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&paid=0&service_code='+code+'&ref='+refno+'\', 800, 440, \'flab-list\', 1, \'auto\'), ' +
                    'WIDTH, 800, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow();>\', '+
                    'CAPTIONPADDING, 4, CAPTION, \'Laboratory Request\', MIDX, 0, MIDY, 0, STATUS, \'Laboratory Request\');">';
            
            if (sex=='m')
                sex_img = '<img src="../../gui/img/common/default/spm.gif" align="absmiddle" border="0"/>';
            else if (sex=='f')
                sex_img = '<img src="../../gui/img/common/default/spf.gif" align="absmiddle" border="0"/>';    
            
            var rowid = refno+""+code;
            var done = $('done').value;
            
            if (done==1){
                
                
                serve ='onclick="return overlib(OLiframeContent(\'../../modules/laboratory/lab_results.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&status=edit&refno='+refno+'&pid='+pid+'&service_code='+code+'&done=0\', 860, 500, \'flab-list\', 1, \'auto\'), ' +
                    'WIDTH, 860, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
                    'CAPTIONPADDING, 4, CAPTION, \'Process Request\', MIDX, 0, MIDY, 0, STATUS, \'Process Request\');">';    
                        
                 donerow = '<td align="center"><a href="javascript:void(0);" '+serve+'<img src="../../images/edit.gif" border="0"></a></td>';                  
                 
                 labresult_row = "";
            }
            else{
                
                 serve ='onclick="return overlib(OLiframeContent(\'../../modules/laboratory/seg-lab-processform.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&pid='+pid+'&service_code='+code+'&refno='+refno+'\', 500, 450, \'flab-list\', 1, \'auto\'), ' +
                    'WIDTH, 500, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
                    'CAPTIONPADDING, 4, CAPTION, \'Process Request\', MIDX, 0, MIDY, 0, STATUS, \'Process Request\');">';    
                        
                 donerow = '<td align="center"><a href="javascript:void(0);" '+serve+'<img src="../../images/cashier_edit.gif" border="0"></a></td>';                  
                 
                 labresult ='onclick="return overlib(OLiframeContent(\'../../modules/laboratory/lab_results.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&status=add&refno='+refno+'&pid='+pid+'&service_code='+code+'&done=0\', 860, 500, \'flab-list\', 1, \'auto\'), ' +
                    'WIDTH, 860, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
                    'CAPTIONPADDING, 4, CAPTION, \'Process Request\', MIDX, 0, MIDY, 0, STATUS, \'Process Request\');">';    
                        
                 labresult_row = '<td align="center"><a href="javascript:void(0);" '+labresult+'<img src="../../images/cashier_edit.gif" border="0"></a></td>';                  
                
            }
                
           rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+refno+'">'+
                            '<td align="center" style="font-size:11px">'+refno+'</td>'+
                         '<td align="left" style="font-size:11px">'+name+'</td>'+
                             '<td align="left" style="font-size:11px">'+pid+'</td>'+
                            '<td align="right" style="font-size:11px">'+age+'</td>'+
                            '<td align="left">'+sex_img+'</td>'+
                             '<td align="left" style="font-size:11px">'+enctype+'</td>'+
                             '<td align="left" style="font-size:11px">'+location+'</td>'+
                             '<td align="left" style="font-size:11px">'+service+'</td>'+
                         '<td align="center" style="font-size:11px">'+req_date+'</td>'+
                        '<td align="center" style="font-size:11px;color:#007">'+priority+'</td>'+
                         '<td align="center" style="font-size:11px">'+ornum+'</td>'+
                         <!--     ''+donerow+''+-->
                            ''+labresult_row+''+
                         '</tr>';                
        }
        else {
            
            rowSrc = '<tr><td colspan="15">No requests available at this time...</td></tr>';
          
        }
        dBody.innerHTML += rowSrc;
    }
}

function ReloadWindow(){
    window.location.href=window.location.href;
}

//added by VAN 01-29-10
function isValidSearch(key) {          

		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();
		//edited by VAN 05-13-2012
        //for bloodbank only as per Mrs Angie Balayon's request
        /*return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
		);*/
        return (
                        /^\d+$/.test(s) 
        );
}
		 
function DisabledSearch(){
		var b=isValidSearch(document.getElementById('search').value);
		document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
		document.getElementById("search-btn").disabled = !b;
}
//------------------------------------------
// -->
</script> 

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    background-color:#0000ff;
    border:1px solid #4d4d4d;
}
.olcg {
    background-color:#aa00aa; 
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
    background-color:#ffffcc; 
    text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
    font-family:Arial; font-size:13px; 
    font-weight:bold; 
    color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}
.olfgleft {background-color:#cceecc; text-align: left;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 


<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
$hclabObj = new HCLAB;

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">
   
    <div id="tabFpanel">
        <div align="center" style="display:">
            <table width="100%" cellpadding="4">
                <tr>
                    <td width="30%" align="center">
                        <!-- "Given Name" in this text as search key removed by pet (aug.5,2008) in accordance with VAS' search code changes -->
                        <!--<span>Enter the search key (HRN, Case No., Batch No., Family Name or Request Date).<br> Enter dates in <font color="#0000FF"><b>MM.DD.YYYY</b></font> format.
                                Enter asterisk (<b>*</b>) to show all data.</span>-->    
                        <span>Enter the search key (limited to HRN only).....</span>    
                        <br><br>
                        
                        <input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('search').value))) startAJAXSearch(this.id,0,0); " onBlur="DisabledSearch();" />
                        <input type="image"  class="jedInput"  id="search-btn" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0,0);return false;" align="absmiddle" disabled="disabled" /><br />
                        <!--<span><a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                  <br>-->
                        <!-- <input type="checkbox" id="firstname-too" checked> Search for first names too.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <br> --> <!-- "First Name" in this text as search key removed by pet (aug.5,2008) in accordance with VAS' search code changes -->
                    </td>
                </tr>
            </table>
        </div>
				<!--
        <div align="center">
            <table cellpadding="4" style="display:none">
                <tr>
                    <td align="center">Enter the person's Health Record No. (HRN)</td>
                </tr>
                <tr>
                    <td align="center">
                        <input class="segInput" id="search-pid" name="spid" type="text" size="50" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
                        <input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" />
                    </td>
                </tr>
            </table>
        </div>
        <div align="center">
            <table cellpadding="4" style="display:none">
                <tr>
                    <td align="center">Enter the search keyword (family name)</td>
                </tr>
                <tr>
                    <td align="center">
                        <input class="segInput" id="search-name" name="sname" type="text" size="50" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
                        <input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" />
                    </td>
                </tr>
            </table>
        </div>
				-->
    </div>
</div>

    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="mode" value="search">  
    <input type="hidden" name="searchkey" id="searchkey" value="<?=$_GET["searhkey"]?>">      
</form>

<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:99%; background-color:#e5e5e5">
<b>Refresh</b>&nbsp;&nbsp;<img src="../../images/cashier_refresh.gif" style="cursor:pointer" onclick="ReloadWindow();" />
    <table class="segList" width="100%" border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr class="nav">
            <th colspan="15">
                <div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
                    <img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
                    <span title="First">First</span>
                </div>
                <div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
                    <img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
                    <span title="Previous">Previous</span>
                </div>
                <div id="pageShow" style="float:left; margin-left:10px">
                    <span></span>
                </div>
                <div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
                    <span title="Last">Last</span>
                    <img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
                </div>
                <div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
                    <span title="Next">Next</span>
                    <img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
                </div>
            </th>
        </tr>
        
        </thead>
    </table>
</div>

<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; overflow-x:hidden; height:305px; width:99%; background-color:#e5e5e5">
<table id="RequestList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
    
    <thead>
       
        <tr>
            
            <th width="8%" align="center">Batch No.</th>
            <th width="*" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name</th>
            <th width="10%" align="left">Hospital No.</th>
            <th width="2%" align="left">Age</th>
            <th width="2%" align="left">Sex</th>
            <th width="2%" align="left">Type</th>
            <th width="5%" align="left">Location</th>
            <th width="15%" align="center">Service Requested</th>
            <th width="10%" align="center">Request Date</th>
            <th width="2%" align="center">Priority</th>
            <!--<th width="3%" align="center">Status</th>-->
            <th width="3%" align="center">OR No.</th>
            <?php
                    if ($_GET['done']){
            ?>
                <th width="3%" align="center">Results</th>
            <?php }else{?>    
                <!--<th width="3%" align="center">Reagents</th><th width="4%" align="center">Lab Result</th>-->
								 <th width="3%" align="center">Results</th>
            <?php } ?>    
        </tr>
    </thead>
    <?php
			if (empty($rows))
				$rows = '<tr><td colspan="14">No requests available at this time...</td></tr>';
	?>
    <tbody id="RequestList-body">
        <?= $rows ?>
    </tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<br />
<hr>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
    /**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
    include_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common',FALSE,FALSE,FALSE);
    
    # Set a flag to display this page as standalone
    $bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="done" id="done" value="<?=$_GET['done']?>" />
 
</form>



<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
