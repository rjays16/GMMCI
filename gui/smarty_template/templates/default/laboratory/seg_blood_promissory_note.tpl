{{foreach from=$javascript_array item=js}}
    {{$js}}
{{/foreach}}
<div align="left" >
{{if $legal_refno}}
{{$form_start}}
<table width="100%" cellpadding="2" cellspacing="2">
  <tr>
    <td class="segPanelHeader">Patient Details</td>
  </tr>
  <tr>
    <td class="segPanel">
      <table>
        <tr>
          <td align="right">Patient Name</td>
          <td>:</td>
          <td><b>{{$patient_info.patient_name}}</b></td>
        </tr>
        <tr>
          <td align="right">Ward</td>
          <td>:</td>
          <td><b>{{$patient_info.ward}}</b></td>
        </tr>
        <tr>
          <td align="right">Blood Type</td>
          <td>:</td>
          <td><b>{{$patient_info.blood_type}}</b></td>
        </tr>
      </table>
    </td> 
    
  </tr>
  <tr>
    <td class="segPanelHeader">Borrower's Details</td>
  </tr>
  <tr>
    <td class="segPanel">
      <table>
        <tr>
          <td align="right" valign="bottom">Borrower's Name</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$borrowers_name}}</td>
        </tr>
        <tr>
          <td align="right" valign="bottom">Date Filed</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$date_filed_display}}{{$date_filed}}</td>
          <td valign="bottom">{{$date_filed_picker}}{{$date_filed_script}}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="segPanelHeader">Borrowed Blood</td>
  </tr>
  <tr>
    <td class="segPanel" align="center">
      
      <table class="segList" border="0" cellpadding="0" cellspacing="0" width="100%" id="blood_item_list">
        <thead>
        <tr>
          <th></th>
          <th></th>
          <th align="left">Date</th>
          <th align="left">No. of Units</th>
          <th align="left">Serial No.</th>
          <th align="left">Date Replaced</th>
          <th align="left">No. of Units Replaced</th>
          <th align="left">Status</th>
          <th align="left">Remarks</th>
        </tr>
        </thead>
        <tbody>
          <tr id="empty_blood_item_row">
            <td colspan="9">Blood item is currently empty...</td>
          </tr>
        </tbody>
      </table>
      {{$add_blood_item}}
     
    </td>
  </tr>
</table>
{{$submit_promissory_note}}
{{$cancel_promissory_note}}
{{$refno}}
{{$submitted}}
{{$iterator}}
{{$mode}}
{{$form_end}}
{{else}}
  Illegal Reference Number. Please ask assistance from your system administrator.
  
{{/if}}

</div>

