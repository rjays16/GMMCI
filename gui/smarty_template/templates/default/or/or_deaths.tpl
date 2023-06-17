<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{$check_date_string}}
{{$or_main_css}}
{{foreach from=$javascript_array item=js}}
    {{$js}}
{{/foreach}}
   
</head>
<body>
     
<div id="or_main_schedule" align="left">
  {{$form_start}}
  
  <div id="toggler" onclick="toggle_details()">Request Details [Please click this bar to hide/unhide the request details]</div>
  <fieldset id="request_details">                           
  <table>
    <tr>
      <td>Department</td>
      <td>:</td>
      <td><span class="value">{{$or_request_department}}</span></td>      
    </tr>
    <tr>
      <td>Operating Room</td>
      <td>:</td>
      <td><span class="value">{{$or_op_room}}</span></td>
    </tr>
    <tr>
      <td>Transaction</td>
      <td>:</td>
      <td><span class="value">{{$or_transaction_type}}</span></td>
    </tr>
    <tr>
      <td>Priority</td>
      <td>:</td>
      <td><span class="value">{{$or_request_priority}}</span></td>
    </tr>
    <tr>
      <td>Date Requested</td>
      <td>:</td>
      <td><span class="value">{{$or_request_date}}</span></td>
    </tr>
    <tr>
      <td>Consent Signed</td>
      <td>:</td>
      <td><span class="value">{{$or_consent_signed}}</span></td>
    </tr>                           
    <tr>
      <td>Case</td>
      <td>:</td>
      <td><span class="value">{{$or_request_case}}</span></td>
    </tr>                           
    <tr>
      <td>Patient Name</td>
      <td>:</td>
      <td><span class="value">{{$patient_name}}</span></td>
    </tr>
    <tr>
      <td>Patient Gender</td>
      <td>:</td>
      <td><span class="value">{{$patient_gender}}</span></td>
    </tr>
    <tr>
      <td>Patient Age</td>
      <td>:</td>
      <td><span class="value">{{$patient_age}}</span></td>
    </tr>
    <tr>
      <td>Patient Address</td>
      <td>:</td>
      <td><span class="value">{{$patient_address}}</span></td>
    </tr>                                              
    <tr>
      <td>Estimated length of operation</td>
      <td>:</td>
      <td><span class="value">{{$or_est_op_length}}</span></td>
    </tr>  
    <tr>
      <td>Case classification</td>
      <td>:</td>
      <td><span class="value">{{$or_case_classification}}</span></td>
    </tr>
    <tr>
      <td>Pre-operative diagnosis</td>
      <td>:</td>
      <td><span class="value">{{$pre_operative_diagnosis}}</span></td> 
    </tr>
    <tr>
      <td>Operation procedure</td>
      <td>:</td>
      <td><span class="value">{{$operation_procedure}}</span></td>    
    </tr>
    <tr>
      <td>Special requirements</td>
      <td>:</td>
      <td><span class="value">{{$or_special_requirements}}</span></td>    
    </tr>
  </table>  
  </fieldset>
  <br/>
  <fieldset id="post_operative">
    <legend>OR Death Details</legend>
    <table>
      <tr>
        <td valign="bottom"><label>Date and time of death:</label></td>
        <td valign="bottom">{{$death_date_display}}{{$death_date_value}}</td>
        <td>{{$death_dt_picker}}</td>
        <td>{{$death_calendar_script}}</td>
      </tr>
      
      <tr>
        <td><label>Cause of death:</label></td>
        <td colspan="2">{{$cause_of_death}}</td>
      </tr>
      
      <tr>
        <td><label>Other Details:</label></td>
        <td>{{html_radios name="patient_classification" options=$patient_classification separator="<br/>" selected=$patient_classification_selected}}</td>
      </tr>
      
      <tr>
        <td></td>
        <td>{{html_radios name="death_time_range" options=$death_time_range separator="<br/>" selected=$death_time_range_selected}}</td>
      </tr>
    </table>
  </fieldset>

  {{$submit_or_death}}
  {{$cancel_or_death}}
  {{$mode}}
  {{$submitted}}
  {{$or_main_refno}}
  {{$refno}}
  {{$form_end}}
</div>

</body>
</html>