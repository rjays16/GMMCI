<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'modules/registration_admission/ajax/reg-insurance.common.php');
require_once($root_path.'include/care_api_classes/class_insurance.php');
require_once($root_path.'include/inc_environment_global.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

ob_start();
?>

<style>
input[type=text] {
  width: 100%;
}
table tr td {
  color: #000;
}

#municipality_autocomplete, #barangay_autocomplete {
  padding-bottom:1.75em;
}
</style>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/yahoo/yahoo.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/yui-2.7/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/yui-2.7/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/connection/connection-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/animation/animation-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/autocomplete/autocomplete-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/flexigrid/lib/jquery/jquery.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

//if (isset($_POST['submitted'])) {
//  $person_insurance = new PersonInsurance();
//  $details_array = array('pid'=>$_POST['pid'],
//                         'hcare_id'=>$_POST['fnr'],
//                         'insurance_nr'=>$_POST['inr'],
//                         'member_lname'=>$_POST['last_name'],
//                         'member_fname'=>$_POST['first_name'],
//                         'member_mname'=>$_POST['middle_name'],
//                         'street_name'=>$_POST['street_name'],
//                         'brgy_nr'=>$_POST['barangay_nr'],
//                         'mun_nr'=>$_POST['municipality_nr']);
//  if ($person_insurance->save_member_details_info($details_array)) {
//    echo '<script>
//            window.parent.prepareAdd('.$details_array['hcare_id'].');
//            window.parent.cClick();
//          </script>';
//  }
//  else {
//    $smarty->assign('sysErrorMessage', 'Error in saving member details.');
//  }
//}

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

$smarty->assign('form_start', '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate(this)">');
$smarty->assign('form_end', '</form>');
$smarty->assign('last_name', '<input type="text" name="last_name" />');
$smarty->assign('first_name', '<input type="text" name="first_name" />');
$smarty->assign('middle_name', '<input type="text" name="middle_name" />');
$smarty->assign('street_name', '<input type="hidden" name="street_name" />');
$smarty->assign('barangay', '<input type="text" name="barangay" id="barangay" onblur="clearNr(this.id)" />');
$smarty->assign('barangay_nr', '<input type="hidden" name="barangay_nr" id="barangay_nr" />');
$smarty->assign('municipality', '<input type="text" name="municipality" id="municipality" onblur="clearNr(this.id)" />');
$smarty->assign('municipality_nr', '<input type="hidden" name="municipality_nr" id="municipality_nr" />');
$smarty->assign('submit_details', '<input type="submit" value="Submit Details" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('pid', '<input type="hidden" name="pid" value="'.$_GET['pid'].'" />');
$smarty->assign('fnr', '<input type="hidden" name="fnr" value="'.$_GET['fnr'].'" />');
$smarty->assign('inr', '<input type="hidden" name="inr" value="'.$_GET['inr'].'" />');
$smarty->assign('infosrc', '<input type="hidden" name="infosrc" value="2" />');
$smarty->assign('principal', '<input type="hidden" name="principal" value="" />');
$smarty->assign('is_updated', '<input type="hidden" name="is_updated" value="0" />');

ob_start();
?>
<script type="text/javascript">
var plname, pfname, pmname;
var balreadyupdated;

function get_parent_details() {

  var pid = <?=$_GET['pid']?>;
  var fnr = <?=$_GET['fnr']?>;
  var parent_window = window.parent;
  var src = <?=$_GET['src']?>;

  if (src == '1') {
  	J("input[@name='last_name']").attr("disabled", 'disabled');
  	J("input[@name='first_name']").attr("disabled", 'disabled');
  	J("input[@name='middle_name']").attr("disabled", 'disabled');
	}
	else {
  	J("input[@name='last_name']").removeAttr("disabled");
  	J("input[@name='first_name']").removeAttr("disabled");
  	J("input[@name='middle_name']").removeAttr("disabled");
	}

  J("input[@name='last_name']").val(parent_window.document.getElementById('ln_<?=$_GET['fnr']?>').value);
  J("input[@name='first_name']").val(parent_window.document.getElementById('fn_<?=$_GET['fnr']?>').value);
  J("input[@name='middle_name']").val(parent_window.document.getElementById('mn_<?=$_GET['fnr']?>').value);
  J("input[@name='street_name']").val(parent_window.document.getElementById('st_<?=$_GET['fnr']?>').value);
  J("input[@name='barangay_nr']").val(parent_window.document.getElementById('ba_<?=$_GET['fnr']?>').value);
  J("input[@name='municipality_nr']").val(parent_window.document.getElementById('mu_<?=$_GET['fnr']?>').value);
  J("input[@name='fnr']").val(parent_window.document.getElementById('fnr_<?=$_GET['fnr']?>').value);
  J("input[@name='inr']").val(parent_window.document.getElementById('inr_<?=$_GET['fnr']?>').value);
  J("input[@name='infosrc']").val(parent_window.document.getElementById('infosrc_<?=$_GET['fnr']?>').value);
  J("input[@name='principal']").val(parent_window.document.getElementById('principal_<?=$_GET['fnr']?>').value);
  J("input[@name='is_updated']").val(parent_window.document.getElementById('is_updated_<?=$_GET['fnr']?>').value);

  plname = parent_window.document.getElementById('ln_<?=$_GET['fnr']?>').value;
  pfname = parent_window.document.getElementById('fn_<?=$_GET['fnr']?>').value;
  pmname = parent_window.document.getElementById('mn_<?=$_GET['fnr']?>').value;
  balreadyupdated = parent_window.document.getElementById('is_updated_<?=$_GET['fnr']?>').value;

  xajax_get_barangay_municipality_name(parent_window.document.getElementById('ba_<?=$_GET['fnr']?>').value, parent_window.document.getElementById('mu_<?=$_GET['fnr']?>').value);
}

function set_barangay_municipality_name(barangay, municipality) {
  J("input[@name='barangay']").val(barangay);
  J("input[@name='municipality']").val(municipality);
}

function validate() {
 // window.parent.prepareAdd(J("input[@name='fnr']").val());
  var bupdated = '0';
  if (balreadyupdated == '0')
			bupdated = ((plname != J("input[@name='last_name']").val()) || (pfname != J("input[@name='first_name']").val()) || (pmname != J("input[@name='middle_name']").val())) ? '1' : '0';
  else
  	  bupdated = balreadyupdated;

  var array_elements = [
  											{type: 'hidden', name: 'last_name[]', value: J("input[@name='last_name']").val(), id: 'ln_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'first_name[]', value: J("input[@name='first_name']").val(), id: 'fn_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'middle_name[]', value: J("input[@name='middle_name']").val(), id: 'mn_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'street[]', value: J("input[@name='street_name']").val(), id: 'st_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'barangay[]', value: J("input[@name='barangay_nr']").val(), id: 'ba_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'municipality[]', value: J("input[@name='municipality_nr']").val(), id: 'mu_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'fnr[]', value: J("input[@name='fnr']").val(), id: 'fnr_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'inr[]', value: J("input[@name='inr']").val(), id: 'inr_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'infosrc[]', value: J("input[@name='infosrc']").val(), id: 'infosrc_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'principal[]', value: J("input[@name='principal']").val(), id: 'principal_'+J("input[@name='fnr']").val()},
                        {type: 'hidden', name: 'is_updated[]', value: bupdated, id: 'is_updated_'+J("input[@name='fnr']").val()}
                        ];
  var group_div = document.createElement("div");
  group_div.id = 'insurance_' + J("input[@name='fnr']").val();
  for (var i=0; i<array_elements.length; i++) {
    var element = document.createElement('input');
    element.type = array_elements[i].type;
    element.name = array_elements[i].name;
    element.id = array_elements[i].id;
    element.value = array_elements[i].value;
    group_div.appendChild(element);
  }
  var parent_window = window.parent.document.getElementById('row_column'+J("input[@name='fnr']").val());
  var z = window.parent.document.getElementById('insurance_' + J("input[@name='fnr']").val());
  //alert('insurance_' + J("input[@name='fnr']").val());
  if (z) {
    parent_window.removeChild(z);
  }
  parent_window.appendChild(group_div);

  append_edit(J("input[@name='fnr']").val());
  window.parent.cClick();
  return false;
}

function append_edit(id) {
  var parent_window = window.parent.document.getElementById('firm_'+id);
  var z = window.parent.document.getElementById('edit_insurance_'+id);
  if (z) {
//    parent_window.removeChild(z);
    z.parentNode.removeChild(z);
  }
  var img = document.createElement('img');
  img.src = '../../images/edit.gif';
  img.style.cursor = "pointer";
  img.id = 'edit_insurance_'+id;
  img.setAttribute('onclick', 'edit_member_details_info('+id+')');
  parent_window.appendChild(img);
}

function setMuniCity(mun_nr, mun_name) {
    document.getElementById('municipality_nr').value = mun_nr;
    document.getElementById('municipality').value = mun_name;
}

function clearNr(id) {
  if (document.getElementById(id).value == '') {
    switch (id) {
      case "barangay":
        document.getElementById('barangay_nr').value = '';
      break;

      case "municipality":
        document.getElementById('municipality_nr').value = '';
      break;
    }
  }
}
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
?>
<script type="text/javascript">
var J = jQuery.noConflict();

/*********
YAHOO.example.BasicRemote = function() {
    // Use an XHRDataSource -- for barangay
    var brgyDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_brgy_query.php");
    // Set the responseType
    brgyDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    // Define the schema of the delimited results
    brgyDS.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    // Enable caching
    brgyDS.maxCacheEntries = 5;

    // Instantiate the AutoComplete
    var brgyAC = new YAHOO.widget.AutoComplete("barangay", "barangay_container", brgyDS);
    brgyAC.formatResult = function(oResultData, sQuery, sResultMatch) {
        return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
    };
    brgyAC.generateRequest = function(sQuery) {
        return "?query="+sQuery+"&mun_nr="+document.getElementById('municipality_nr').value;
    };

    var munName = YAHOO.util.Dom.get("municipality");
    var brgyName = YAHOO.util.Dom.get("barangay");

    // Define an event handler to populate a hidden form field
    // when an item gets selected
    var brgyNr = YAHOO.util.Dom.get("barangay_nr");
    var brgyHandler = function(sType, aArgs) {
        var bmyAC  = aArgs[0]; // reference back to the AC instance
        var belLI  = aArgs[1]; // reference to the selected LI element
        var boData = aArgs[2]; // object literal of selected item's result data

        // update text input control ...
        brgyNr.value = boData[0];
        brgyName.value = boData[1];
        xajax_getMuniCityandProv(brgyNr.value);
    };
    brgyAC.itemSelectEvent.subscribe(brgyHandler);

    // Use an XHRDataSource --- for municipality or city
    var munDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_municity_query.php");
    // Set the responseType
    munDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    // Define the schema of the delimited results
    munDS.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    // Enable caching
    munDS.maxCacheEntries = 5;

    // Instantiate the AutoComplete
    var munAC = new YAHOO.widget.AutoComplete("municipality", "municipality_container", munDS);
    munAC.formatResult = function(oResultData, sQuery, sResultMatch) {
        return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
    };

    // Define an event handler to populate a hidden form field
    // when an item gets selected
    var munNr = YAHOO.util.Dom.get("municipality_nr");
    var munHandler = function(sType, aArgs) {
        var mmyAC  = aArgs[0]; // reference back to the AC instance
        var melLI  = aArgs[1]; // reference to the selected LI element
        var moData = aArgs[2]; // object literal of selected item's result data

        // update text input control ...
        munNr.value = moData[0];
        munName.value = moData[1];
        //xajax_getProvince(munNr.value);
        brgyNr.value = '';
        brgyName.value = '';
    };
    munAC.itemSelectEvent.subscribe(munHandler);


    return {
        brgyDS: brgyDS,
        munDS: munDS,
        brgyAC: brgyAC,
        munAC: munAC,
    };
}();
****/
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('javascript_array',$sTemp);

$smarty->assign('sMainBlockIncludeFile','registration_admission/member_insurance_details_edit.tpl'); //Assign the member_insurance_details template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame
?>
