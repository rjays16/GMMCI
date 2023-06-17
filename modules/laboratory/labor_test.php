<?php
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

# edited by VAN 03-12-08
    
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
     error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    require($root_path.'include/inc_environment_global.php');
    
    require($root_path."modules/laboratory/ajax/lab-admin.common.php");
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

$breakfile = "labor.php";
#$local_user='ck_prod_db_user';
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

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

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDLab :: Tests Manager");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLab :: Tests Manager");

 # Assign Body Onload javascript code
 
 $onLoadJS="onLoad='preSet();'";
 //echo $onLoadJS;
 //$onLoadJS.='"';
  $smarty->assign('sOnLoadJs',$onLoadJS);
  //$smarty->assign('sOnLoadJs',$onLoadJS);//$onLoadJS);


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
    startAJAXSearch('search',0);
    //alert("enter preset");
    /*document.getElementById('parameterselect').focus();
    
    if (document.getElementById("parameterselect").value==0){
        document.getElementById("search").readOnly = true;  
    }*/    
}

function deleteService(code){
    var answer = confirm("Are you sure you want to delete the laboratory service with a code of "+(code.toUpperCase())+"?");
        if (answer){
            xajax_deleteService(code);
        }
}

function removeService(id) {
    var table = document.getElementById("ServiceList");
    var rowno;
    var rmvRow=document.getElementById("row"+id);
    if (table && rmvRow) {
        rowno = 'row'+id;
        var rndx = rmvRow.rowIndex;
        table.deleteRow(rmvRow.rowIndex);
        //window.location.reload(); 
        refreshWindow(key,pagekey);
    }
    //self.opener.location.href=self.opener.location.href;
    //window.parent.location.href=window.parent.location.href;
}

//---added by VAN 02-06-08------------

    function ShortcutKeys(){
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
    
    function SearchItem(){
        startAJAXSearch('search',0);
    }
    
//----------------------------------

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page) {
    var searchEL = $(searchID);
    var keyword;
    //alert("startAJAXSearch");
        //alert(searchEL.value);
    //var aLabServ = $("parameterselect").value;
    //alert(aLabServ);
    
    //document.getElementById('key').value = searchEL.value;
    //document.getElementById('pagekey').value = page;
    if (searchEL.value)
        document.getElementById('key').value = searchEL.value;
    else
        document.getElementById('key').value = '*';
        
    if (page)    
        document.getElementById('pagekey').value = page;
    else
        document.getElementById('pagekey').value = '0';    
    
    keyword = searchEL.value;
    keyword = keyword.replace("'","^");
    
    if (searchEL) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ServiceList-body").style.display = "none";
        $("ajax-loading").style.display = "";
        AJAXTimerID = setTimeout("xajax_populateLabServiceList('','"+searchID+"','"+keyword+"',"+page+")",50);
        lastSearch = searchEL.value;
    }
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        $("ServiceList-body").style.display = "";
        searchEL.style.color = "";
    }
}
function jsGetServiceGroup(d){
     xajax_getServiceGroup("none");    
}

function ajxClearOptions() {
    var optionsList;
    //alert(document.forms["paramselect"].parameterselect.value);    
   /* var el=document.forms["paramselect"].parameterselect;
    if (el) {
        optionsList = el.getElementsByTagName('OPTION');
        for (var i=optionsList.length-1;i>=0;i--) {
            optionsList[i].parentNode.removeChild(optionsList[i]);
        }
    }*/
}/* end of function ajxClearOptions */
         
function ajxAddOption(text, value) {
    /*var grpEl = document.forms["paramselect"].parameterselect;
    if (grpEl) {
        var opt = new Option( text, value );
        opt.id = value;
        grpEl.appendChild(opt);
    }
    var optionsList = grpEl.getElementsByTagName('OPTION');*/
}/* end of function ajxAddOption */

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
    
//    alert('currentPage, lastPage, firstRec, total = '+currentPage+", "+lastPage+", "+firstRec+", "+total);
    
    if (currentPage==lastPage)
        lastRec = total;
    else
        lastRec = (parseInt(pageno)+1)*pagen;
    
    //alert('firstrec, lastrec, total = '+(firstRec)+" = "+(lastRec)+" = "+parseInt(total));
    
    if (parseInt(total)==0)
        $("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
    else
        $("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
    //$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),2)+'-'+formatNumber((lastRec),2)+' out of '+formatNumber((parseInt(total)),2)+' record(s).</span>';
    
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
            startAJAXSearch('search',0);
            document.getElementById('pagekey').value=0;
        break;
        case PREV_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',parseInt(currentPage)-1);
            document.getElementById('pagekey').value=currentPage-1;
        break;
        case NEXT_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(currentPage)+1);
            document.getElementById('pagekey').value=parseInt(currentPage)+1;
        break;
        case LAST_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(lastPage));
            document.getElementById('pagekey').value=parseInt(lastPage);
        break;
    }
}

function startAJAXSearch2(keyword, page) {
    var keyword;
    //var aLabServ = $("parameterselect").value;
    keyword = keyword.replace("'","^");
    
    if (document.getElementById('search').value)
        document.getElementById('key').value = document.getElementById('search').value;
    else
        document.getElementById('key').value = '*';
        
    if (page)    
        document.getElementById('pagekey').value = page;
    else
        document.getElementById('pagekey').value = '0';    
    //alert('key, page = '+document.getElementById('key').value+" - "+document.getElementById('pagekey').value);
    if (AJAXTimerID) clearTimeout(AJAXTimerID);
    $("ajax-loading").style.display = "";
    $("ServiceList-body").style.display = "none";
    AJAXTimerID = setTimeout("xajax_populateLabServiceList('','search','"+keyword+"',"+page+")",50);
    lastSearch = keyword;
}

//function refreshWindow(){
function refreshWindow(key,page){
    //window.location.href=window.location.href;
    //alert(key.value+", "+page.value);
    startAJAXSearch2(key.value, page.value)
}

function addslashes(str) {
    str=str.replace("'","\\'");
    return str;
}

function addProductToList(listID, id, name, cash, charge, sservice, codenum) {    
    var list=$(listID), dRows, dBody, rowSrc;
    var i;
    var classified, mode, editlink;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
//alert('sulod listID, id, name, cash, charge, sservice = '+listID+" , "+id, name+" , "+cash+" , "+charge+" , "+sservice);
        //alert(dBody.innerHTML);
        // get the last row id and extract the current row no.
        if (id) {
        
            alt = (dRows.length%2)+1;
            
            if (sservice==1)
                classified = 'Yes';         
            else    
                classified = 'No';        
            //alert('id = '+id);
            //mode = '<img name="delete'+addslashes(id)+'" id="delete'+addslashes(id)+'" src="../../images/delete.gif" style="cursor:pointer" border="0" onClick="deleteService(\''+addslashes(id)+'\'); refreshWindow(key,pagekey); "/>';     
            /*         
            editlink ='onclick="return overlib(OLiframeContent(\'seg-lab-services-edit.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&code='+id+'\', 500, 450, \'flab-list\', 1, \'auto\'), ' +
                         'WIDTH, 500, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
                          'CAPTIONPADDING, 4, CAPTION, \'Add Laboratory Service\', MIDX, 0, MIDY, 0, STATUS, \'Add Laboratory Service\');">';
            */
            
            //var editlink = '<a href="javascript:void(0);" onclick="AddItem(\''+addslashes(id)+'\',\'update\');"><img src="../../images/edit.gif" border="0"></a>';
            
            var reagent ='onclick="return overlib(OLiframeContent(\'labor_test_params.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&service_code='+id+'\', 750, 450, \'flab-list\', 1, \'auto\'), ' +
                    'WIDTH, 500, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
                    'CAPTIONPADDING, 4, CAPTION, \'Edit Test\', MIDX, 0, MIDY, 0, STATUS, \'Edit Test\');">';    
                        
            var editlink = '<td align="center"><a href="javascript:void(0);" '+reagent+'<img src="../../images/edit.gif" border="0"></a></td>';
            //alert(reagentlink);
            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(id)+'">'+
                            '<td align="left"><span style="font:bold 12px Arial">'+name+'</span></td>'+
                         '<td align="left"><span style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
                         '<td align="left">'+codenum+'</td>'+
                        '<td align="left">'+charge+'</td>'+
                            editlink+
                      '</tr>';        
            //'<td align="center"><a href="javascript:void(0);" '+editlink+'<img src="../../images/edit.gif" border="0"></a></td>'+                                    
        } 
        else {
            rowSrc = '<tr><td colspan="10" style="">No such laboratory test exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
        //alert(dBody.innerHTML);
    }
}

function AddItem(code,mode){
        //urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-services-edit-031208.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+escape(nr)+"&grpcode="+grpcode+"&row="+rowno;
    /*var grpcode = document.forms["paramselect"].parameterselect.value;    
    var label;
    
    if (mode=='save')
        label = 'Add';
    else
        label = 'Edit';    
        
    //alert('grpcode = '+grpcode);
    return overlib(
         OLiframeContent('seg-lab-services-edit.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&nr='+escape(code)+"&grpcode="+grpcode+"&mode="+mode, 500, 450, 'fGroupTray', 1, 'auto'),
                                  WIDTH,500, TEXTPADDING,0, BORDER,0, 
                                    STICKY, SCROLL, CLOSECLICK, MODAL, 
                                    CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="refreshWindow(key,pagekey);">',
                                 CAPTIONPADDING,4, CAPTION,label+' laboratory service',
                                 MIDX,0, MIDY,0, 
                                 STATUS,label+' laboratory service');                            */
}


function enableSearch(){
    var rowSrc, list;
    document.getElementById("search").value="";
    list = $('ServiceList');
    dBody=list.getElementsByTagName("tbody")[0];
    rowSrc = '<tr><td colspan="10" style="">No such laboratory service exists...</td></tr>';
    dBody.innerHTML = null;
    dBody.innerHTML += rowSrc;
    
    $("pageShow").innerHTML = '';
    //startAJAXSearch2('*', 0);
    /*
    if (document.getElementById("parameterselect").value!=0){
        //document.getElementById("search").disabled = false;       //enable textbox for searching
        document.getElementById("search").readOnly = false;       //enable textbox for searching
        document.getElementById("search_img").disabled = false;   //enable image
        document.getElementById("addnewitem").style.display = '';       // display add button 
        startAJAXSearch2('*', 0);
        document.getElementById('search').focus();
    }else{
        //document.getElementById("search").disabled = true;       //disable textbox for searching
        document.getElementById("search").readOnly = true;       //disable textbox for searching
        document.getElementById("search_img").disabled = true;   //disable image
        document.getElementById("addnewitem").style.display = 'none';       // hide add button 
        document.getElementById('key').value = '';
        document.getElementById('pagekey').value = '';
    } */   
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

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="paramselect" id="paramselect" onSubmit="">
    <div id="tabFpanel">
        <div align="center" style="display:">
            <table width="100%" cellpadding="4" border="0">
                <tr>
                    <td width="28%"><span><strong>Enter the search key</strong></span></td>
                    <td>
                        <input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" />
                        <input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" /><br />
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="mode" value="search">
    <input type="hidden" name="key" id="key">
    <input type="hidden" name="pagekey" id="pagekey">
</form>
<br>
<div  style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:75%; background-color:#e5e5e5">
<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr class="nav">
            <th colspan="10">
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
<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:305px; width:75%; background-color:#e5e5e5">
<table id="ServiceList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="38%" align="left">Test</th>
            <th width="18%" align="left">Code</th>
            <th width="18%" align="left">Code #</th>
            <th width="18%" align="left">Charge</th>    
            <th width="8%" align="center">Edit</th>
        </tr>
    </thead>
    <tbody id="ServiceList-body">
        <tr>
            <td colspan="10">No such laboratory test exists...</td>
        </tr>
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

<script>
document.body.onLoad = preSet();
</script>