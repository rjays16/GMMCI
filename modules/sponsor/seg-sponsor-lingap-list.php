<?php

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Lingap::List of entries");
 
 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Lingap::List of entries");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="init()"');

 # Collect javascript code
 ob_start()

?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/modalbox/modalbox.js"></script>
<link rel="stylesheet" href="<?=$root_path?>css/themes/default/modalbox.css" type="text/css" media="screen" />

<script language="javascript" type="text/javascript">
<!--
  var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";
  
  function init() {
    disableControls($('controls-controlnr'),true);
    disableControls($('controls-date'),true);
    disableControls($('controls-patient'),true);
  }
  
  function disableControls(container, disable) {
    container.select('input, select').each(
      function(obj) {
        obj.disabled = disable;
      }
    );
    
    container.select('img').each(
      function(obj) {
        obj.style.display = disable ? 'none' : '';
      }
    );
  }

  function openPDF(ref) {
    window.open('seg-pharma-order.php'+URL_FORWARD+'&target=print&ref='+ref,'openPDF',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
  }
  
  function selpayorOnChange() {
    var optSelected = $('selpayor').options[$('selpayor').selectedIndex];
    var spans = document.getElementsByName('selpayoroptions');
    
    for (var i=0; i<spans.length; i++) {
      if (optSelected) {
        if (spans[i].getAttribute("segOption") == optSelected.value) {
          spans[i].style.display = "";
        }
        else
          spans[i].style.display = "none";
      }
    }
  }
  
  function seldateOnChange() {
    var optSelected = $('seldate').options[$('seldate').selectedIndex]
    var spans = document.getElementsByName('seldateoptions')
    for (var i=0; i<spans.length; i++) {
      if (optSelected) {
        if (spans[i].getAttribute("segOption") == optSelected.value) {
          spans[i].style.display = ""
        }
        else
          spans[i].style.display = "none"
      }
    }
  }
  
  function confirmDelete(id) {
    Effect.Fade('o'+id, { duration: 0.5, from: 1, to: 0.5});
    var html = '<div class="MB_alert"><p>Delete this Lingap entry?</p><input class="segButton" type="button" onclick="Modalbox.hide();xajax.call(\'deleteOrder\',{ parameters:['+id+']} )" value="Delete" /><input class="segButton" type="button" onclick="Modalbox.hide()" value="Cancel" /></div>';
    Modalbox.show(html, {title: 'Confirm delete', width: 300, overlayOpacity: .4, beforeHide: function() { cancelDelete(id) } });
  }
  
  function prepareDelete(id) {
    var node = $('o'+id);    
    if (node) {
      Effect.Fade(node, { duration: 0.5, to: 0});
    }
  }
  
  function cancelDelete(id) {
    var node = $('o'+id);    
    if (node) {
      Effect.Appear(node, { duration: 0.5});
    }
  }

  function lateAlert(msg, timeout) {
    setTimeout(function() { alert(msg) }, timeout);
  }
  
  function pSearchClose() {
    cClick();
  }
  
  function deleteItem(id) {
    var dform = document.forms[0]
    $('delete').value = id
    dform.submit()
  }
  
  function validate() {
  }
  
  function addEntry(details) {
    list = $('llst');
    if (list) {    
      var dBody=list.select("tbody").first();
      if (dBody) {
        if (!details) details = { FLAG: false};
        if (details['FLAG']) {
          var date=details["date"],
            nr=details["nr"],
            id=nr,
            name=details["name"],
            items=details["items"],
            total=details["total"],
            encoder=details["encoder"],
            status=details["status"],
            disabled=(details["disabled"]=='1');

          var dRows = dBody.select("tr");
          var alt = (dRows.length%2>0) ? 'alt':'';
          var disabledAttrib = disabled ? 'disabled="disabled"' : "";
          
          var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
            new Element('td', { class:'centerAlign' } ).update(
              new Element('span', { id: 'ri_date_'+id}).update(date)
            )
          ).insert(
            new Element('td', { class:'centerAlign' } ).update(
              new Element('span', { id: 'ri_nr_span_'+id, style:'color:#660000' }).update(nr)
            ).insert(
              new Element('input', { id:'ri_nr_'+id, type:'hidden', value:nr } )
            )
          ).insert(
            new Element('td', { class:'leftAlign' } ).update(
              new Element('span', { id: 'ri_name_'+id }).update(name).setStyle( { font:'bold 11px Tahoma' } )
            )
          ).insert(
            new Element('td', { class:'leftAlign' } ).update(
              new Element('span', { id: 'ri_items_'+id }).update( items 
              ).setStyle( { font:'bold 11px Tahoma', color:'#060' } )
            )
          ).insert(
            new Element('td', { class:'leftAlign' } ).update(
              new Element('span', { id: 'ri_total_'+id}).update(total)
            )
          ).insert(
            new Element('td', { class:'leftAlign' } ).update(
              new Element('span', { id: 'ri_encoder_'+id}).update(encoder)
            )
          ).insert(
            new Element('td', { class:'centerAlign' }).update(
              new Element('img',{ id:'ri_edit_'+id, src:'<?= $root_path ?>images/cashier_edit.gif', class:'segSimulatedLink', border:0, title:'Edit entry' }
              ).setStyle( { margin:'1px' }
              ).observe( 'click',
                function(event) {
                }
              )
            ).insert(
              new Element('img',{ id:'ri_delete_'+id, src:'<?= $root_path ?>images/cashier_delete.gif', class:'segSimulatedLink', border:0, title:'Delete entry' }
              ).setStyle( { margin:'1px' }
              ).observe( 'click',
                function(event) {
                }
              )
            )
          );
          dBody.insert(row);
        }
        else {
          dBody.update('<tr><td colspan="8">List is currently empty...</td></tr>');
        }
        return true;
      }
    }
    return false;
  }
-->
</script>

<?php
#added by bryan Sept 18,2008
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', TRUE);
$llst = &$listgen->createList(
  array(
    'LIST_ID' => 'llst',
    'COLUMN_HEADERS' => array('Date','Ctrl No.','Name','Items', 'Total', 'Encoder', ''),
    'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE),
    'AJAX_FETCHER' => 'populateLingapEntries',
    'INITIAL_MESSAGE' => "No entries found...",
    'ADD_METHOD' => 'addEntry',
    'FETCHER_PARAMS' => array(),
    'COLUMN_WIDTHS' => array("10%", "10%", "20%", "35%", "10%", "10%", "5%")
  )
);
$smarty->assign('sLingapList',$llst->getHTML());

$smarty->assign('sSearchResults',$rows);
$smarty->assign('sRootPath',$root_path);

$ctrlCheckHTML = "<input type=\"checkbox\" id=\"chkcontrolnr\" name=\"chkcontrolnr\" ".($_REQUEST['chkcontrolnr'] ? 'checked="checked"' : '') ."onclick=\"disableControls($('controls-controlnr'), !this.checked)\" />";
$smarty->assign('sControlNrCheckbox', $ctrlCheckHTML);

$ctrlHTML = '
  <input class="segInput" name="control_nr" id="control_nr" type="text" size="20" value="'. $_REQUEST['name'] .'">
';
$smarty->assign('sControlNr', $ctrlHTML);


$patientcheckHTML = "<input type=\"checkbox\" id=\"chkpayor\" name=\"chkpayor\" ".($_REQUEST['chkpayor'] ? 'checked="checked"' : '') ." onclick=\"disableControls($('controls-patient'), !this.checked)\" />";
$smarty->assign('sPatientCheckbox', $patientcheckHTML);

$patientHTML = '
<select class="segInput" name="selpayor" id="selpayor" onchange="selpayorOnChange()">
  <option value="name" '. ($_REQUEST["selpayor"]=="name" ? 'selected="selected"' : '') .'>Patient name</option>
  <option value="patient" '. ($_REQUEST['selpayor']=='patient' ? 'selected="selected"' : '') .'>Patient</option>
  <option value="inpatient" '. ($_REQUEST['selpayor']=='inpatient' ? 'selected="selected"' : '') .'>Patient w/ encounter</option>
</select>
<span name="selpayoroptions" segOption="name" '. (($_REQUEST['selpayor']=='name') ? '' : 'style=""' ) .'>
  <input class="segInput" name="name" id="name" type="text" size="20" value="'. $_REQUEST['name'] .'">
  <input type="hidden" name="name_old" value="'. $_REQUEST['name'] .'" >
</span>
<span name="selpayoroptions" segOption="patient" '. (($_REQUEST['selpayor']=='patient') ? '' : 'style="display:none"') .'>
  <input class="segInput" name="patientname" id="patientname" readonly="readonly" type="text" size="20" value="'. $_REQUEST['patientname'] .'"/>
  <input name="patient" id="patient" type="hidden" value="'. $_REQUEST['patient'] .'"/>
  <img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
    onclick="overlib(
      OLiframeContent(\''. $root_path .'modules/registration_admission/seg-select-enc.php?var_pid=patient&var_name=patientname\', 700, 400, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
        STICKY, SCROLL, CLOSECLICK, MODAL,
        CLOSETEXT, \'<img src='. $root_path .'images/close_red.gif border=0 >\',
        CAPTIONPADDING,2, 
        CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0,
        STATUS,\'Select registered person\'); return false;"
    onmouseout="nd();" />
</span>
<span name="selpayoroptions" segOption="inpatient" '. (($_REQUEST['selpayor']=='inpatient') ? '' : 'style="display:none"') .'>
  <input class="segInput" name="inpatientname" id="inpatientname" readonly="readonly" type="text" size="20" value="'. $_REQUEST['inpatientname'] .'"/>
  <input name="inpatient" id="inpatient" type="hidden" value="'. $_REQUEST['inpatient'] .'"/>
  <img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
  onclick="overlib(
    OLiframeContent(\''. $root_path .'modules/registration_admission/seg-select-enc.php?var_encounter_nr=inpatient&var_name=inpatientname&var_include_enc=1\', 700, 400, \'fSelEnc\', 0, \'auto\'),
      WIDTH,700, TEXTPADDING,0, BORDER,0, 
      STICKY, SCROLL, CLOSECLICK, MODAL,
      CLOSETEXT, \'<img src='. $root_path .'images/close_red.gif border=0 >\',
      CAPTIONPADDING,2, 
      CAPTION,\'Select registered person\',
      MIDX,0, MIDY,0, 
      STATUS,\'Select registered person\'); return false;"
 onmouseout="nd();" />
</span>';
$smarty->assign('sPatient', $patientHTML);

$datecheckHTML = "<input type=\"checkbox\" id=\"chkdate\" name=\"chkdate\" ".($_REQUEST['chkdate'] ? 'checked="checked"' : '') ." onclick=\"disableControls($('controls-date'), !this.checked)\" />";
$smarty->assign('sDateCheckbox', $datecheckHTML);

$dateHTML = '
<select class="segInput" id="seldate" name="seldate" onchange="seldateOnChange()">
  <option value="today" '. ($_REQUEST['seldate']=='today' ? 'selected="selected"' : '') .'>Today</option>
  <option value="thisweek" '. ($_REQUEST['seldate']=='thisweek' ? 'selected="selected"' : '') .'>This week</option>
  <option value="thismonth" '. ($_REQUEST['seldate']=='thismonth' ? 'selected="selected"' : '') .'>This month</option>
  <option value="specificdate" '. ($_REQUEST['seldate']=='specificdate' ? 'selected="selected"' : '') .'>Specific date</option>
  <option value="between" '. ($_REQUEST['seldate']=='between' ? 'selected="selected"' : '') .'>Between</option>
</select>
<span name="seldateoptions" segOption="specificdate" '. (($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"') .'>
  <input class="segInput" name="specificdate" id="specificdate" type="text" size="8" value="'. $_REQUEST['specificdate'] .'"/>
  <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
  <script type="text/javascript">
    Calendar.setup ({
      inputField : "specificdate", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
    });
  </script>
</span>
<span name="seldateoptions" segOption="between" '. (($_REQUEST['seldate']=='between') ? '' : 'style="display:none"') .'>
  <input class="segInput" name="between1" id="between1" type="text" size="8" value="'. $_REQUEST['between1'] .'"/>
  <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
  <script type="text/javascript">
    Calendar.setup ({
      inputField : "between1", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_between1", singleClick : true, step : 1
    });
  </script>
  to
  <input class="segInput" name="between2" id="between2" type="text" size="8" value="'. $_REQUEST['between2'] .'"/>
  <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
  <script type="text/javascript">
    Calendar.setup ({
      inputField : "between2", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_between2", singleClick : true, step : 1
    });
  </script>
</span>';

$smarty->assign('sDate', $dateHTML);

$areacheckHTML = "<input type=\"checkbox\" id=\"chkarea\" name=\"chkarea\" ".($_REQUEST['chkarea'] ? ' checked="checked"' : '') .
($_SESSION['sess_pharma_area'] && strtoupper($_SESSION['sess_pharma_area'])!='ALL' ? ' disabled="disabled" checked="checked"':'')."/>";
$smarty->assign('sAreaCheckbox', $areacheckHTML);

$areaHTML ='<select class="segInput" id="selarea" name="selarea" onchange="" '.
  ($_SESSION['sess_pharma_area'] && strtoupper($_SESSION['sess_pharma_area'])!='ALL' ? 'disabled="disabled"':'').
  '>';

require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();

if (!$_REQUEST['selarea']) {
  $_REQUEST['selarea']=$_SESSION['sess_pharma_area'];
}
while($row=$prod->FetchRow()){
  $checked=strtolower($row['area_code'])==strtolower($_REQUEST['selarea']) ? 'selected="selected"' : "";
  $areaHTML .=  '<option value="'.$row['area_code'].'" '.$checked.'>'.$row['area_name'].'</option>\n';
}
$areaHTML .= '</select>';            
$smarty->assign('sArea', $areaHTML);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();
?>

<br/>

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

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 #added by bryan on Sept 18,2008
 $smarty->assign('sMainBlockIncludeFile','sponsor/lingap_list.tpl');
 
 $smarty->display('common/mainframe.tpl');
