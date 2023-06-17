<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
header('Content-Type: text/html; charset=iso-8859-1');
/***
*  This routine will return the member relations in an option html element.
*
*/
function getMemberRelations($reltn) {
	global $db;

	$html = '<select class="segInput required-verify required-save" id="patient-relation" name="pPatientIs" >';
    $strselected = ($reltn === '') ? ' selected="selected"' : "";
	$html .= '<option value=""'.$strselected.'>--Select relation to member--</option>';

    $strSQL = "SELECT * FROM seg_relationtomember";
	$result = $db->Execute($strSQL);

	if ($result) {
		while ($row = $result->FetchRow()) {
            $strselected = ($reltn === $row['relation_code']) ? ' selected="selected"' : "";
			$html .= '<option value="'.$row['relation_code'].'"'.$strselected.'>'.$row['relation_desc'].'</option>';
		}
	}
	$html .= '</select><br/>';

	return $html;
}

/***
*  This routine will get the membership types in an option html element.
*
*/
function getMembershipTypes($memtype, &$disableEmployerFields) {
	global $db;

	$html = '<select class="segInput required-verify required-save" id="membership-type" name="pMembershipType" >';
    $strselected = ($memtype === '') ? ' selected="selected"' : "";
	$html .= '<option value=""'.$strselected.'>--Select membership type--</option>';

	$strSQL = "SELECT memcategory_code,memcategory_desc,is_employer_info_required FROM seg_memcategory ORDER BY memcategory_desc";
    $result = $db->Execute($strSQL);
	if ($result !== false) {
		while ($row = $result->FetchRow()) {
            $isSelected = false;
            if ($memtype == $row['memcategory_code']) {
                $isSelected = true;
                $disableEmployerFields = ($row['is_employer_info_required']=='0');
            }
			$html .= sprintf(
                '<option value="%s" data-employer-required="%s" %s>%s</option>',
                htmlentities($row['memcategory_code']),
                htmlentities($row['is_employer_info_required']),
                $isSelected ? ' selected="selected"' : '',
                htmlentities($row['memcategory_desc'])
            );
		}
	}
	$html .= '</select><br/>';

	return $html;
}

// Process page parameters
$id = !empty($_GET["id"]) ? $_GET["id"] : null;
$provider = isset($_GET["provider"]) ? $_GET["provider"] : null;
$pid = isset($_GET["pid"]) ? $_GET["pid"] : null;
$encounter_nr = isset($_GET["encounter_nr"]) ? $_GET["encounter_nr"] : null;
$isPrincipal = (@$_GET["principal"] != 0) ? true : false;

$lname = '';
$fname = '';
$mname = '';
$suffx = '';
$bdate = '';
$gender = '';

$reltn = '';
$mtype = '';
$empno = '';
$empnm = '';


$person = new Person();
if (!$person->preloadPersonInfo($pid)) {
    // error
}

$info = $person->getMemberInsuranceInfo($pid, $provider);
if ($isPrincipal) {
    $lname = $person->Lastname();
    $fname = $person->FirstName();
    $mname = $person->MiddleName();
    $suffx = $person->Suffix();
    $bdate = date("m-d-Y", strtotime($person->BirthDate()));
} else {
    $lname = @$info['member_lname'];
    $fname = @$info['member_fname'];
    $mname = @$info['member_mname'];
    $suffx = @$info['suffix'];
    if (@$info['birth_date'] == '')
        $bdate = date('m-d-Y');
    else
        $bdate = date('m-d-Y',strtotime(@$info['birth_date']));
    
    
}

if (empty($id)) {
    $id = @$info['insurance_nr'];
}
$reltn = @$info["relation"];
$empno = @$info["employer_no"];
$empnm = @$info["employer_name"];
$gender = @$info["sex"];

if (!empty($encounter_nr)) {
    $encounter = new Encounter;
    $mtype = $encounter->getMemberType($encounter_nr);
} else {
    $mtype = @$info['member_type'];
}



// Send back the contact form HTML
$disableEmployerFields = false;
?>
<div class="modal-top"></div>
<div class="modal-content">
    <h1 class="modal-title">Membership Details</h1>
    <div class="modal-loading"></div>
    <div class="modal-message"></div>
    <form id="member-form" action="#">
        <label for="member-id">Insurance No</label>
        <input type="text" id="member-id" maxlength="12" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               class="segInput required-verify required-save" name="pPIN" value="<?= htmlentities($id) ?>" 
               onkeypress="return numericOnly(event)"/>
        <label for="member-lastname">Last Name:</label>
        <input type="text" id="member-lastname" class="segInput required-get required-verify required-save" name="pMemberLastName" value="<?= htmlentities($lname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-firstname">First Name</label>
        <input type="text" id="member-firstname" class="segInput required-get required-verify required-save" name="pMemberFirstName" value="<?= htmlentities($fname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-middlename">Middle Name</label>
        <input type="text" id="member-middlename" class="segInput required-get required-verify" name="pMemberMiddleName" value="<?= htmlentities($mname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-gender">Gender</label>
        <select class="segInput required-verify required-save" id="member-gender" name="pMemberGender">
            <option value="">-SELECT-</option>
            <option value="m" <?= ($gender=='m') ? "selected" : "" ?>>Male</option>
            <option value="f" <?= ($gender=='f') ? "selected" : "" ?>>Female</option>
        </select>
        <!-- <label for="member-suffix">Suffix</label>
        <input type="text" id="member-suffix" class="segInput" name="pMemberSuffix" value="<?= htmlentities($suffx) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/> -->
        <label for="member-birthdate">Birthdate</label>
        <input type="text" id="member-birthdate" class="segInput required-get required-verify required-save" name="pMemberBirthDate" value="<?= $bdate ?>" style="width:100px" /><br/>
<?php if(!$isPrincipal) { ?>
        <label for="patient-relation">Patient's Relation</label>
        <?= getMemberRelations($reltn); ?>
<?php } else { ?>
        <input type="hidden" id="patient-relation" name="pPatientIs" value="M"/>
<?php } ?>

        <label for="membership-type">Membership Type</label>
        <?= getMembershipTypes($mtype, $disableEmployerFields); ?>
        <!--
        <label for="member-pempno">Employer ID</label>
        <input type="text" id="member-pempno" class="segInput required-verify required-save" name="pPEN" value="<?= htmlentities($empno) ?>" <?= $disableEmployerFields ? 'disabled="disabled"' : '' ?> />
        <label for="member-pempnm">Employer Name</label>
        <input type="text" id="member-pempnm" class="segInput required-verify required-save" name="pEmployerName" value="<?= htmlentities($empnm) ?>" <?= $disableEmployerFields ? 'disabled="disabled"' : '' ?> />
         -->
        <input type="hidden" id="provider" name="provider" value="<?= $provider ?>"/>
        <input type="hidden" id="pid" name="pid" value="<?= $pid ?>" />
        <input type="hidden" id="encounter_nr" name="encounter_nr" value="<?= $encounter_nr ?>" />
    </form>
</div>
<div class="modal-bottom" style="text-align:center">
<!--     <button id="member-get" type="button" class="segButton">
        <img src="../../gui/img/common/default/page_go.png" />Get PIN
    </button>
    <button id="member-verify" type="button" class="segButton">
        <img src="../../gui/img/common/default/report_magnify.png" />Verify
    </button> -->
    <button id="member-save" type="button" class="segButton">
        <img src="../../gui/img/common/default/add.png" />Add Insurance
    </button>
<!--    <button id="member-cancel" type="button" class="segButton">
        <img src="../../gui/img/common/default/cancel.png" />Check SPC
    </button>-->
    <button id="member-cancel" type="button" class="segButton">
        <img src="../../gui/img/common/default/cancel.png" />Close
    </button>
</div>
<script type="text/javascript">
jQuery(function($) {
    $( "#member-birthdate" ).datepicker({
        dateFormat: "mm-dd-yy",
        changeMonth: true,
        changeYear: true
    });
    $('#membership-type').change(function() {
        var $this=$(this);
        var selected = $this.find(':selected');
        $('#member-pempno,#member-pempnm').attr(
            'disabled',
            (selected.data('employer-required') != '0') ?
                null : 'disabled'
        );
    })
});
// add by JC 07-31-2018
function numericOnly(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>