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
      <td>Order Date</td>
      <td>:</td>
      <td><span class="value">{{$requested_date}}</span></td>
      
    </tr>
    <tr>
      <td>Transaction Type</td>
      <td>:</td>                                                  
      <td><span class="value">{{$transaction_type}}</span></td>
    </tr>
    
    <tr>
      <td>Priority</td>
      <td>:</td>
      <td><span class="value">{{$priority}}</span></td>
    </tr>
    
    
   
  </table>
  <table width="100%" cellspacing="2" cellpadding="2">
    
    <tr>
      <td class="segPanel" align="center" colspan="3">
        <table class="segList" width="100%" id="supplies-list">
          <thead>
            <tr>
              <th width="10%" nowrap="nowrap" align="left">Item No.</th>
              <th width="*" nowrap="nowrap" align="left">Item Description</th>
              <th width="10%" align="right" nowrap="nowrap">Qty</th>
              <th width="10%" align="right" nowrap="nowrap">Price</th>
              <th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
              <th width="10%" align="center" nowrap="nowrap">Status</th>
              <th width="20%" align="center" nowrap="nowrap">Remarks</th>
            </tr>
          </thead>
          <tbody>
            {{$rows}}
          </tbody>
        </table>
        
      </td>
    </tr>
  </table>
  <table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
    <tr>
      <td width="*" align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
      <td width="17%" id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">{{$total_price}}</th>
    </tr>
  </table>
  <br/>
  {{$charge_submit}}{{$charge_cancel}}
</fieldset>
{{$is_submitted}}
{{$pharma_area}}
{{$encounter_nr}}
{{$issc}}
{{$refno}}
{{$pharma_refno}}
{{$mode}}
{{$discount}}
{{$discountid}}
{{$form_end}}

</div>
{{$populate_script}}

