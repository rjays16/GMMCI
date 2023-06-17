<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{$check_date_string}}
{{$or_delivery_css}}
{{foreach from=$javascript_array item=js}}
    {{$js}}
{{/foreach}}

</head>
<body>

  <div class="or_delivery_popup" id="dr_patient_popup">  <!-- start of dr_patient_popup -->
   <div class="header">Select Patient</div>
   <div class="body">
     <div id="select_or">
       <div id="delivery_personell" align="left">
         <div id="search_bar" align="left">
          {{$search_field}}

          {{$search_button}}
         </div>
         <div id="navigation">
          <div class="group"><select name="number_of_pages">{{html_options options=$number_of_pages}}</select></div>
          <div id="button_separator"></div>
          <div class="group">
            <div id="first" class="button"><span></span></div>
            <div id="prev" class="button"><span></span></div>
          </div>
          <div id="button_separator"></div>
          <div class="group"><span id="control">Page {{$page_number}} of <span></span></span></div>
          <div id="button_separator"></div>
          <div class="group">
            <div id="next" class="button"><span></span></div>
            <div id="last" class="button"><span></span></div>
          </div>
          <div id="button_separator"></div>
          <div class="group">
            <div id="reloader" class="pre_load button loading"><span></span></div>
          </div>
          <div id="button_separator"></div>
          <div class="group"><span id="page_stat">Processing, please wait...</span></div>
         </div>
         <table id="delivery_patient_table" align="left"></table>
       </div>
     </div>
   </div>
  </div> <!-- end of dr_patient_popup -->
  <div id="or_delivery">
    {{$form_start}}
    <fieldset>
      <legend>Patient Details</legend>
      <br/>
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td class="left">Patient Name</td>
          <td class="right">{{$patient_name}}</td>
        </tr>
        <tr>
          <td class="left">Patient Age</td>
          <td class="right">{{$patient_age}}</td>
        </tr>
        <tr>
          <td class="left">Date Admitted</td>
          <td class="right">{{$date_admitted}}</td>
        </tr>
        <tr>
          <td class="left">Room/Ward</td>
          <td class="right">{{$room_ward}}</td>
        </tr>
        <tr>
          <td class="left">Bed No.</td>
          <td class="right">{{$bed_num}}</td>
        </tr>
         <tr>
          <td class="left">Hospital No.</td>
          <td class="right">{{$hosp_num}}</td>
        </tr>
        <tr>
          <td class="left">Date of Confinement</td>
          <td class="right">{{$date_confinement}}</td>
        </tr>
        <tr>
          <td class="left">Attending Physcian</td>
          <td class="right">{{$physician}}</td>
        </tr>
        <tr>
          <td class="left">Gravida</td>
          <td class="right">{{$gravida}}</td>
        </tr>
        <tr>
          <td class="left">Para</td>
          <td class="right">{{$para}}</td>
        </tr>
        <tr>
          <td class="left">Abortion</td>
          <td class="right">{{$abortion}}</td>
        </tr>
        <tr>
          <td class="left">Blood Type</td>
          <!--<td class="right">{{html_radios name="blood_type" id="blood_type" options=$blood_type separator=""}}</td>-->
          <td class="right">
          			<input type="radio" name="blood_type" id="blood_type" value="A">A
                <input type="radio" name="blood_type" id="blood_type" value="B">B
                <input type="radio" name="blood_type" id="blood_type" value="O">O
                <input type="radio" name="blood_type" id="blood_type" value="AB">AB
          </td>
        </tr>
        <tr>
          <td class="left">Prenatal Care Serology</td>
          <!--<td class="right">{{html_radios name="prenatal_care" options=$prenatal_care separator=""}}</td>-->
          <td class="right">
          			<input type="radio" name="prenatal_care" id="prenatal_care" value="1">Yes
                <input type="radio" name="prenatal_care" id="prenatal_care" value="0">No
          </td>
        </tr>
        <tr>
          <td class="left">Complications of Pregnancy</td>
          <td class="right">{{$pregnancy_complications}}</td>
        </tr>
      </table>
    </fieldset>

    <fieldset>
      <legend>Labor Details</legend>
      <br/>
      <table width="750">
      	<tr>
      		<td class="left">Heart</td>
      		<td class="right">{{$heart}}</td>
      		<td class="left">Lungs</td>
      		<td class="right">{{$lungs}}</td>
      		<td class="left">BP</td>
      		<td class="right">{{$bp_1}}</td>
      		<td class="left">Pulse</td>
      		<td class="right">{{$pulse_1}}</td>
      	</tr>
      	</table>
      	<table width="750">
				<tr>
					<td class="left" style="width:150px">General Condition</td>
					<!--<td class="right">{{html_radios name="general_condition" options=$general_condition separator=""}}</td>-->
					<td class="right">
          			<input type="radio" name="general_condition" id="general_condition" value="good" onclick="check_others(this.value);">Good
          			<input type="radio" name="general_condition" id="general_condition" value="fair" onclick="check_others(this.value);">Fair
          			<input type="radio" name="general_condition" id="general_condition" value="critical" onclick="check_others(this.value);">Critical
          			<input type="radio" name="general_condition" id="general_condition" value="febrile" onclick="check_others(this.value);">Febrile
          			<input type="radio" name="general_condition" id="general_condition" value="morbid" onclick="check_others(this.value);">Morbid
          			<input type="radio" name="general_condition" id="general_condition" value="others" onclick="check_others(this.value);">Others
          			<input type="text" name="general_condition_others" id="general_condition_others" style="display:none"/>
          </td>
				</tr>
				<tr>
					<td class="left">Membrane Ruptured</td>
					<!--<td class="right">{{html_radios name="membrane_ruptured" options=$membrane_ruptured separator=""}}</td>-->
					<td class="right">
          			<input type="radio" name="membrane_ruptured" id="membrane_ruptured" value="spontaneous">Spontaneous
          			<input type="radio" name="membrane_ruptured" id="membrane_ruptured" value="artificial">Artificial
          			<input type="radio" name="membrane_ruptured" id="membrane_ruptured" value="cervix dilates">Cervix Dilates
          </td>
				</tr>
				<tr>
					<td class="left"></td>
					<td class="right">{{$cervix_cm}}cm <!--{{html_radios name="cervix_condition" options=$cervix_condition separator=""}}-->
          			<input type="radio" name="cervix_condition" id="cervix_condition" value="premature">Premature
          			<input type="radio" name="cervix_condition" id="cervix_condition" value="early">Early
          			<input type="radio" name="cervix_condition" id="cervix_condition" value="late">Late
          </td>
				</tr>
				<tr>
					<td class="left">Labor Onset</td>
					<!--<td class="right">{{html_radios name="labor_onset" options=$labor_onset separator=""}}</td>-->
					<td class="right">
          			<input type="radio" name="labor_onset" id="labor_onset" value="induced">Induced
          			<input type="radio" name="labor_onset" id="labor_onset" value="spontaneous">Spontaneous
          </td>
				</tr>
				<tr>
					<td class="left">Date of Onset</td>
					<td class="right">{{$onset_date_time}} am/pm</td>
				</tr>
				<tr>
					<td class="left">Full Dilatation</td>
					<td class="right">{{$dilation_date_time}} am/pm</td>
				</tr>
				<tr>
					<td class="left">Child born</td>
					<td class="right">{{$childborn_date_time}} am/pm</td>
				</tr>
				<tr>
					<td class="left">Ergonovine</td>
					<td class="right">{{$ergonovine_date_time}} am/pm</td>
				</tr>
				<tr>
					<td class="left">Labor Duration</td>
					<td class="right" align="left">
            <select name="labor_duration_hour">{{html_options options=$hour}}</select>hrs.
            <select name="labor_duration_minute">{{html_options options=$minute}}</select>min.
          </td>
				</tr>
				<tr>
					<td class="left">Delivery Spontaneous</td>
					<!--<td class="right">{{html_radios name="delivery_spont" options=$delivery_spont separator=""}}</td>-->
					<td class="right">
          			<input type="radio" name="delivery_spont" id="delivery_spont" value="1">Yes
          			<input type="radio" name="delivery_spont" id="delivery_spont" value="0">No
          </td>
				</tr>
				<tr>
					<td class="left">Blood Loss</td>
					<td class="right">{{$blood_given}} cc. Blood given</td>
				</tr>
				<tr>
					<td class="left">Operative</td>
					<td class="right">{{$operative}}</td>
				</tr>
				<tr>
					<td class="left">Episiotomy</td>
					<td class="right">{{$episiotomy}}</td>
				</tr>
				<tr>
					<td class="left">Perineal Tear</td>
					<!--<td class="right">{{html_radios name="perineal_tear" options=$perineal_tear separator=""}}</td>-->
					<td class="right">
          			<input type="radio" name="perineal_tear" id="perineal_tear" value="1">Yes
          			<input type="radio" name="perineal_tear" id="perineal_tear" value="0">No
          </td>
				</tr>
				<tr>
					<td class="left">Analgesic Given</td>
					<td class="right">{{$analgesic_given}}</td>
				</tr>
				<tr>
					<td class="left">Anesthesia Given</td>
					<td class="right">{{$anesthesia_given}}</td>
				</tr>
				<tr>
					<td class="left">Complications</td>
					<td class="right">{{$complications}}</td>
				</tr>
				</table>
				<table width="750">
				<thead>
          <tr>
            <th width="140" align="left">Postpartum Examination</th>
          </tr>
        </thead>
        <tr>
        	<td class="left">Fundus</td>
					<td class="right">{{$fundus}}</td>
        </tr>
        <tr>
        	<td class="left">Umbiculus</td>
					<td class="right">{{$umbiculus}}</td>
        </tr>
        <tr>
          <td class="left">Vital Signs</td>
          <td class="right">BP {{$post_bp}} Temp {{$post_temp}} Pulse {{$post_pulse}} Resp Rate {{$post_resprate}}</td>
        </tr>
        <tr>
					<td class="left">Bleeding</td>
					<!--<td class="right">{{html_radios name="bleeding" options=$bleeding separator=""}}</td>-->
					<td class="right">
          			<input type="radio" name="bleeding" id="bleeding" value="normal">Normal
          			<input type="radio" name="bleeding" id="bleeding" value="moderate">Moderate
          			<input type="radio" name="bleeding" id="bleeding" value="excessive">Excessive
          </td>
				</tr>
     		</table>
     		<br/>
     		<table width="750">
     		 <tr>
     		 	<td class="left">Delivered By</td>
     		 	<td class="right">
     		 	<input type="text" name="deliver_dr" id="deliver_dr"/> M.D.
     		 	</td>
     		 </tr>
     		 </table>
     </fieldset>
    {{$pid}}
    {{$encounter_nr}}
    {{$ref_no}}      <!-- Added by CHA 10-09-09 -->
    {{$submit_dr_record}}
    {{$cancel_dr_record}}
    {{$or_delivery_record_report}}	<!-- Added by CHA 11-10-09 -->
    {{$form_end}}
  </div>

</body>
</html>