{{foreach from=$javascript_array item=js}}
    {{$js}}
{{/foreach}}
{{$form_start}}
<table width="100%">
  <tr>
    <td class="segPanelHeader">Add Blood Item</td>
  </tr>
  <tr>
    <td class="segPanel">
      <table width="100%" cellpadding="0">
        <tr>
          <td align="right" valign="bottom">Date</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$date_borrowed_display}}{{$date_borrowed}}</td>
          <td valign="bottom">{{$date_borrowed_picker}}{{$date_borrowed_script}}</td>
        </tr>
        <tr>
          <td align="right" valign="bottom">No. of Units</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$no_of_units}}</td>
          <td></td>
        </tr>
        <tr>
          <td align="right" valign="bottom">Serial Number</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$serial_number}}</td>
          <td></td>
        </tr>
        <tr>
          <td align="right" valign="bottom">Date Replaced</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$date_replaced_display}}{{$date_replaced}}</td>
          <td valign="bottom">{{$date_replaced_picker}}{{$date_replaced_script}}</td>
        </tr>
        <tr>
          <td align="right" valign="bottom">No. of Units Replaced</td>
          <td valign="bottom">:</td>
          <td valign="bottom">{{$no_of_units_replaced}}</td>
          <td></td>
        </tr>
        <tr>
          <td align="right" valign="bottom">Status</td>
          <td valign="bottom">:</td>
          <td valign="bottom"><select name="item_status" id="item_status">{{html_options options=$item_status}}</select></td>
          <td></td>
        </tr>
        <tr>
          <td align="right" valign="middle">Remarks</td>
          <td valign="middle">:</td>
          <td valign="bottom">{{$remarks}}</td>
          <td></td>
        </tr>
        
      </table>
      <table>
        <tr>
          <td colspan="4">{{$add_blood_item}}</td>
         
        </tr>
      </table>
    </td>
  </tr>
  
</table>
{{$form_end}}