<body>
<div align="left">
<table width="100%" align="left">
  <tr>
    <td class="segPanelHeader">Member Details Info</td>
  </tr>
  <tr>
    <td class="segPanel">
      {{$form_start}}
      <table width="100%">

        <tr>
          <td width="80px">Last Name</td>
          <td width="3px">:</td>
          <td>{{$last_name}}</td>
        </tr>
        <tr>
          <td>First Name</td>
          <td>:</td>
          <td>{{$first_name}}</td>
        </tr>
        <tr>
          <td>Middle Name</td>
          <td>:</td>
          <td>{{$middle_name}}</td>
        </tr>
        <tr>
          <td>Street</td>
          <td>:</td>
          <td>{{$street_name}}</td>
        </tr>
        <tr>
          <td>Barangay</td>
          <td>:</td>
          <td class="yui-skin-sam">
            <div id="barangay_autocomplete">
            {{$barangay}}
            <div id="barangay_container"></div>
            </div>
          </td>
        </tr>
        <tr>
          <td>Municipality</td>
          <td>:</td>
          <td class="yui-skin-sam">      
            <div id="municipality_autocomplete">
              {{$municipality}}
              <div id="municipality_container"></div>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="3">
            {{$submit_details}}
            {{$submitted}}
            {{$pid}}
            {{$fnr}}
            {{$inr}}
            {{$barangay_nr}}
            {{$municipality_nr}}
            
          </td>
        </tr>
      </table>
      {{$form_end}}
    </td>
  </tr>
  
</table>
</div>
{{foreach from=$javascript_array item=js}}
    {{$js}}
{{/foreach}}
</body>