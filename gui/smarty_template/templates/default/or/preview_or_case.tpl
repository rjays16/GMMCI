<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{$check_date_string}}
{{$or_main_css}}
{{foreach from=$javascript_array item=js}}
    {{$js}}
{{/foreach}}
<style>
select {
  border: 1px #CAC9B9 solid;
  margin: 0px;
}
</style>
</head>
<body>



<div id="or_main_schedule" align="left">
  {{$form_start}}
  
  <div id="toggler" onclick="toggle_details()">Request Details [Please click this bar to hide/unhide the request details]</div>
  <fieldset id="request_details" style="display:block;">                           
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
  {{if $status eq 'pre_op' || $status eq 'post'}}
    <div id="toggler" onclick="toggle_pre_op()">Pre-operative Details [Please click this bar to hide/unhide the pre_operative details]</div>
  {{else}}
    <div id="toggler" style="background:#CD3838">No pre-operatioon details is available yet</div>
  {{/if}}
  <fieldset id="pre_op_details" style="display:none">
    <table>
      <tr>
        <td>
          <table>
            {{$pre_op_table}}
          </table>
        </td>
      </tr>
      <tr>
        <td>
          Vital Signs Record:
          <table class="segList" width="100%" id="vital_sign_list">
          <thead>
            <tr>
              <th align="left" nowrap="nowrap" >Date Taken</th>
              <th align="left" nowrap="nowrap">Temperature</th>
              <th align="left" nowrap="nowrap">Pulse Rate</th>
              <th align="left" nowrap="nowrap">Respiratory Rate</th>
              <th align="left" nowrap="nowrap">Blood Pressure</th>
            </tr>
          </thead>
          <tbody>
            {{$vital_signs_table}}
          </tbody>
        </table>
        </td>
      </tr>
      
    </table>
    
  </fieldset>                            
  
  <br/>
  {{if $status eq 'post'}}
    <div id="toggler" onclick="toggle_post_op()">Post-operation Details [Please click this bar to hide/unhide the post-operatioon details]</div>
  {{else}}
    <div id="toggler" style="background:#CD3838">No post-operatioon details is available yet</div>
  {{/if}}
  <fieldset id="post_operative_details" style="display:none">
    <table width="100%">
      <tr>
        <td width="20%">Time Started</td>
        <td width="1%">:</td>
        <td><span class="value">{{$post_time_started}}</span></td>
      </tr>
      
      <tr>
        <td>Time Finished</td>
        <td>:</td>
        <td><span class="value">{{$post_time_finished}}</span></td>
      </tr>
      
      <tr>
        <td>Post Operative Diagnosis</td>
        <td>:</td>
        <td><span class="value">{{$post_operative_diagnosis}}</span></td>
      </tr>
      
      <tr>
        <td>Operation Performed</td>
        <td>:</td>
        <td><span class="value">{{$operation_performed}}</span></td>
      </tr>
      
      <tr>
        <td>O.R. Technique</td>
        <td>:</td>
        <td><span class="value">{{$or_technique}}</span></td>
      </tr>
      
      <tr>
        <td>Transferred to</td>
        <td>:</td>
        <td><span class="value">{{$transferred_to}}</span></td>
      </tr> 
      
      <tr>
        <td>Surgeon</td>
        <td>:</td>
        <td><span class="value">{{$surgeons}}</span></td>
      </tr>
      
      <tr>
        <td>Assistant Surgeon</td>
        <td>:</td>
        <td><span class="value">{{$assistant_surgeons}}</span></td>
      </tr>   
      
      <tr>
        <td>Anesthesiologist</td>
        <td>:</td>
        <td><span class="value">{{$anesthesiologists}}</span></td>
      </tr>   
      
      <tr>
        <td>Scrub Nurse</td>
        <td>:</td>
        <td><span class="value">{{$scrub_nurses}}</span></td>
      </tr>   
      
      <tr>
        <td>Circulating Nurse</td>
        <td>:</td>
        <td><span class="value">{{$circulating_nurses}}</span></td>
      </tr>
      
      <tr>
        <td colspan="3" width="100%">
          Anesthesia Procedures
          <table id="anesthesia_procedure_list" class="segList" width="100%">
            <thead id="anesthesia_procedure_list_header">
              <tr>
                <th>Anesthesia</th>
                <th>Anesthetics</th>
                <th>Time Begun</th>
                <th>Time Ended</th>
              </tr>
            </thead>
            <tbody>
              {{$anesthesia_procedures}}
            </tbody>
          </table>
        </td>
      </tr>
      
      <tr>
        <td>Intra Operative</td>
        <td>:</td>
        <td><span class="value">{{$anesthetic_intra_operative}}</span></td>
      </tr>
      <tr>
        <td>Post Operative</td>
        <td>:</td>
        <td><span class="value">{{$anesthetic_post_operative}}</span></td>
      </tr>
      <tr>
        <td>Status of patient before <br/>leaving the OR theater</td>
        <td>:</td>
        <td><span class="value">{{$anesthetic_patient_status}}</span></td>
      </tr>
      
      <tr>
        <td colspan="3">
          ICPM
          <table id="order-list" class="segList" width="100%">
            <thead>
              <tr>
                <th>Code</th>
                <th>Description</th>
                <th>RVU</th>
                <th>Multiplier</th>
                <th>Charge</th>
              </tr>  
            </thead>
            <tbody>
              {{$icpm}}
            </tbody>
          </table>
        </td>
      </tr>
      
      <tr>
        <td colspan="3">
          Medicines and Supplies
          <table class="segList" width="100%" id="supplies-list">
            <thead>
              <tr>
                <th width="*" nowrap="nowrap" align="left">Particular</th>
                <th width="15%" nowrap="nowrap" align="left">Price</th>
                <th width="10%" align="center" nowrap="nowrap">Quantity</th>
                <th width="10%" align="right" nowrap="nowrap">Total</th>

              </tr>
            </thead>
            <tbody>
            {{$medicines_and_supplies}}
            </tbody>
          </table>
       
          <table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
            <tbody>
              <tr>
                <td align="right" style="padding:4px"><strong>Net Total</strong></th>
                <td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">{{$net_total}}</td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>   
      
      <tr>
        <td colspan="3">
          Equipments
          <table class="segList" width="100%" id="equipment_list">
            <thead>
              <tr>
                <th width="*" nowrap="nowrap" align="left">Equipment</th>
                <th width="15%" align="left" nowrap="nowrap">Price</th>
                <th width="10%" align="center" nowrap="nowrap">Number of Usage</th>
                <th width="10%" align="right" nowrap="nowrap">Total</th>
              </tr>
            </thead>
            <tbody>
              {{$equipments}}
            </tbody>
          </table>
        
          
          <table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
            <tbody>
              <tr>
                <td align="right" style="padding:4px"><strong>Net Total</strong></th>
                <td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">{{$net_total_equipments}}</td>
              </tr>
            </tbody>
          </table>
        </td>  
      </tr>
      <!--
      <tr>
        <td colspan="3">
          Sponge Count
          <table class="segList" width="100%" id="sponge_list" cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th>Sponge Type</th>
                <th>Initial Count</th>
                <th align="center" width="180px">
                  <table>
                    <tr>
                      <td colspan="3" align="center"><b>First Count</b></td>
                    </tr>
                    <tr>
                      <td align="center" width="52px">On Table</td>
                      <td align="center" width="52px">On Floor</td>
                      <td align="center" width="52px">TTL</td>
                    </tr>
                  </table>
                </th>
                <th align="center">
                  <table>
                    <tr>
                      <td colspan="3" align="center"><b>Second Count</b></td>
                    </tr>
                    <tr>
                      <td align="center" width="52px">On Table</td>
                      <td align="center" width="52px">On Floor</td>
                      <td align="center" width="52px">TTL</td>  
                    </tr>
                  </table>
                </th>
              </tr>
            </thead>
            <tbody id="sponge_item_tbody">
              {{$sponges}}
            </tbody>
          </table>
        </td>
      </tr>
      
      <tr>
        <td>Sponge Count</td>
        <td>:</td>
        <td><span class="value">{{$sponge_count}}</span></td>
      </tr>
      --> 
      <tr>
        <td>Needles</td>
        <td>:</td>
        <td><span class="value">{{$needle_count}}</span></td>
      </tr>
      
      <tr>
        <td>Instruments</td>
        <td>:</td>
        <td><span class="value">{{$instrument_count}}</span></td>
      </tr>
    </table>
  </fieldset>                                       
  </div>             
  {{$or_main_cancel}}

</div>

</body>
</html>