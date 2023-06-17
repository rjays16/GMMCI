{{foreach from=$css_and_js item=script}}
    {{$script}}
{{/foreach}}

<div id="or_charge" align="left">
{{$form_start}}
<fieldset>
  <legend>OR Request Details</legend>
  <table>
    <tr>
      <td>Reference Number</td>
      <td>:</td>
      <td><span class="value">{{$reference_number}}</span></td>
    </tr>
    <tr>
      <td>Department</td>
      <td>:</td>
      <td><span class="value">{{$department}}</span></td>
    </tr>
    <tr>
      <td>Operating Room</td>
      <td>:</td>
      <td><span class="value">{{$operating_room}}</span></td>
    </tr>
    <tr>
      <td>Request Date</td>
      <td>:</td>
      <td><span class="value">{{$request_date}}</span></td>
    </tr>
    <tr>
      <td>Operation Date</td>
      <td>:</td>
      <td><span class="value">{{$operation_date}}</span></td>
    </tr>
    <tr>
      <td>Patient ID</td>
      <td>:</td>
      <td><span class="value">{{$patient_id}}</span></td>
    </tr>
    <tr>
      <td>Patient Name</td>
      <td>:</td>
      <td><span class="value">{{$patient_name}}</span></td>
    </tr>
    <tr>
      <td>Patient Address</td>
      <td>:</td>
      <td><span class="value">{{$patient_address}}</span></td>
    </tr>
  </table>
</fieldset>

<fieldset>
  <legend>OR Charges</legend>
  <table id="or_charge_details">
  
    <tr>
      <td>Requested Date</td>
      <td>:</td>
      <td>{{$requested_date_display}}{{$requested_date}}</td>
      <td>{{$date_time_picker}}</td>
    </tr>
    <tr>
      <td>Transaction Type</td>
      <td>:</td>                                                  
      <td>{{$charge}}{{$cash}}</td>
    </tr>
    
    <tr>
      <td>Priority</td>
      <td>:</td>
      <td>{{html_radios name="priority" options=$priority selected=$default_priority_value}}</td>
    </tr>
    
    
   
  </table>
  <table width="100%" cellspacing="2" cellpadding="2">
    
    <tr>
      <td class="segPanel" align="center" colspan="3">
        <table class="segList" width="100%" id="supplies-list">
          <thead>
            <tr>
              <th width="1%" nowrap="nowrap">&nbsp;</th>
              <th width="10%" nowrap="nowrap" align="left">Item No.</th>
              <th width="*" nowrap="nowrap" align="left">Item Description</th>
              <th width="4%" nowrap="nowrap" align="center">Consigned</th>
              <th width="10%" align="center" nowrap="nowrap">Quantity</th>
              <th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
              <th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
              <th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
              <th width="10%" align="center" nowrap="nowrap">Dosage</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="9">Order list is currently empty...</td></tr>
          </tbody>
        </table>
        {{$supplies_add_button}}{{$supplies_empty_button}}
      </td>
    </tr>
  </table>
  <table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
    <tbody>
      <tr>
        <td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong></th>
        <td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
      </tr>
      <tr>
        <td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong></th>
        <td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
      </tr>
      <tr>
        <td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
        <td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
      </tr>
     </tbody>
  </table>
  <br/>
  {{$charge_submit}}{{$charge_cancel}}
</fieldset>
{{$is_submitted}}
{{$pharma_area}}
{{$encounter_nr}}
{{$issc}}
{{$refno}}
{{$mode}}
{{$discount}}
{{$discountid}}
{{$form_end}}

</div>
{{$populate_script}}

