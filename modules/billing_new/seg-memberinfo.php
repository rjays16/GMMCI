<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
header('Content-Type: text/html; charset=iso-8859-1');
function getRemarks($encounter_nr, $hcare_id)
{
    global $db;
    $options = '<option value=0>-SELECT-</option>';
    $remarks = $db->GetOne("SELECT remarks FROM seg_encounter_insurance WHERE encounter_nr = " . $db->qstr($encounter_nr) . " AND hcare_id = " . $db->qstr($hcare_id));

    $sql = "SELECT * FROM seg_insurance_remarks_options";
    $rs = $db->Execute($sql);

    if ($rs) {
        if ($rs->RecordCount()) {
            while ($row = $rs->FetchRow()) {
                extract($row);
                $selected = ($remarks == $id) ? 'selected' : '';
                $options .= "<option value='$id' $selected>$title</option>";
            }
        }
    }
    return $options;
}
/***
*  This routine will return the member relations in an option html element.
*
*/
function getMemberRelations($reltn) {
	global $db;
    $select = "'select'";//added by art 1/26/2014
	$html = '<select onkeydown="if(event.keyCode == 13)jumpNext(this,'.$select.')" class="segInput required-verify required-save" id="patient-relation" name="pPatientIs" >';
    $strselected = ($reltn === '') ? ' selected="selected"' : "";
	$html .= '<option onkeydown="if(event.keyCode == 13)jumpNext(this,'.$select.')"  value=""'.$strselected.'>--Select relation to member--</option>';

    $strSQL = "SELECT * FROM seg_relationtomember";
	$result = $db->Execute($strSQL);

	if ($result) {
		while ($row = $result->FetchRow()) {
            $strselected = ($reltn === $row['relation_code']) ? ' selected="selected"' : "";
			$html .= '<option onkeydown="if(event.keyCode == 13)jumpNext(this,'.$select.')" value="'.$row['relation_code'].'"'.$strselected.'>'.$row['relation_desc'].'</option>';
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
	$html = '<select onkeydown="if(event.keyCode == 13)getfocus()" class="segInput required-verify required-save" id="membership-type" name="pMembershipType" >';
    $strselected = ($memtype === '') ? ' selected="selected"' : "";
	$html .= '<option  onkeydown="if(event.keyCode == 13)getfocus()" value=""'.$strselected.'>--Select membership type--</option>';

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
                '<option onkeydown="if(event.keyCode == 13)getfocus()" value="%s" data-employer-required="%s" %s>%s</option>',
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

$reltn = '';
$gender = '';

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
    $suffx = '';//$person->Suffix();
    $bdate = date("m-d-Y", strtotime($person->BirthDate()));
} else {
    $lname = @$info['member_lname'];
    $fname = @$info['member_fname'];
    $mname = @$info['member_mname'];
    $suffx = @$info['suffix'];
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
        <input type="text" id="member-id" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')" class="segInput required-verify required-save" name="pPIN" value="<?= htmlentities($id) ?>" />
        <label for="member-lastname">Last Name:</label>
        <input type="text" id="member-lastname" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')" class="segInput required-get required-verify required-save" name="pMemberLastName" value="<?= htmlentities($lname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-firstname">First Name</label>
        <input type="text" id="member-firstname" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')" class="segInput required-get required-verify required-save" name="pMemberFirstName" value="<?= htmlentities($fname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-middlename">Middle Name</label>
        <input type="text" id="member-middlename" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"class="segInput" name="pMemberMiddleName" value="<?= htmlentities($mname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-suffix">Suffix</label>
        <input type="text" id="member-suffix" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')" class="segInput" name="pMemberSuffix" value="<?= htmlentities($suffx) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-birthdate">Birthdate</label>
        <input type="text" id="member-birthdate" onkeydown="if(event.keyCode == 13)jumpNext(this,'select')" class="segInput required-get required-verify required-save" name="pMemberBirthDate" value="<?= $bdate ?>" style="width:100px" /><br/>
        <label for="member-gender">Gender</label>
        <select class="segInput required-verify required-save" id="member-gender" name="pMemberGender">
            <option value="" <?= ($sex=='' || $sex==NULL) ? "selected" : "" ?>>--SELECT GENDER--</option>
            <option value="f" <?= ($gender=='f') ? "selected" : "" ?>>Female</option>
            <option value="m" <?= ($gender=='m') ? "selected" : "" ?>>Male</option>
        </select>
<?php if(!$isPrincipal) { ?>
        <label for="patient-relation">Patient's Relation</label>
        <?= getMemberRelations($reltn); ?>
<?php } else { ?>
        <input type="hidden" id="patient-relation" name="pPatientIs" value="M"/>
<?php } ?>
        
        <label for="membership-type">Membership Type</label>
        <?= getMembershipTypes($mtype, $disableEmployerFields); ?>
        <label for="member-pempno">Employer ID</label>
        <input type="text" id="member-pempno" class="segInput required-verify required-save" name="pPEN" value="<?= htmlentities($empno) ?>" <?= $disableEmployerFields ? 'disabled="disabled"' : '' ?> />
        <label for="member-pempnm">Employer Name</label>
        <input type="text" id="member-pempnm" class="segInput required-verify required-save" name="pEmployerName" value="<?= htmlentities($empnm) ?>" <?= $disableEmployerFields ? 'disabled="disabled"' : '' ?> />
         <label for="member-remarks">Remarks</label>
        <select class="segInput required-verify required-save" id="member-remarks" name="remarks">
            <?= getRemarks($encounter_nr, $provider) ?>
        </select>

        <input type="hidden" id="provider" name="provider" value="<?= $provider ?>"/>
        <input type="hidden" id="pid" name="pid" value="<?= $pid ?>" />
        <input type="hidden" id="encounter_nr" name="encounter_nr" value="<?= $encounter_nr ?>" />
    
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
//added by art 01/26/2014
function jumpNext(input,type) {    
       $(input).next(type).focus(); 
}
function getfocus()
{
document.getElementById('member-save').focus();
}
//end art

</script>