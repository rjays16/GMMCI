<style type="text/css">
<!--
body {
	background-color: #EBF0FE;
}
-->
</style><div align="center">{{* reg_search_main.tpl  Mainframe for patient/person registration search page *}}
  
  {{$sPretext}}
  
  {{* Never remove the ff: 2 tags from this template *}}
  {{$sJSGetHelp}}
  
  {{* Never remove the $sJSFormCheck tag from this template *}}
  {{$sJSFormCheck}}
  <br />
  <table border=0 align="center" cellpadding=2 class="reg_searchmask_border">
    <tr>
      <td>
        <table align="center" cellpadding="5" cellspacing="5" class="reg_searchmask">
          <tbody>
            <tr>
              <td>
                <form {{$sFormParams}}>
                &nbsp;
                <br><table width="100%" border="0" >
  <tr>
    <td bgcolor="#EBF0FE">{{$searchprompt}}</td>
  </tr>
</table>

                
                <br> 
				<table width="100%" border="0" class="reg_searchmask">
                  <tr>
                    <td width="35%">{{* Never rename this input. Redimensioning it is allowed. *}}
                      <input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch(this.value);" onBlur="DisabledSearch(this.value);" value="">
                      <input type="hidden" id="debug" value="" disabled="disabled" />
                    </td>
                    <td>{{* Do not move the sHiddenInputs outside the <form>
                    block *}}
                    &nbsp;{{$sHiddenInputs}}
                    </form>			  </td>
            </tr>
        </table>
               {{$LDTipsTricks}} <br> 
                
                <!-- commented out by pet due to changes in vanessa's search codes; aug.5,2008
                  {{$sCheckBoxFirstName}} {{$LDIncludeFirstName}}
                  -->
      </td>
    </tr>
          </tbody>
  </table>	    </td>
	  </tr>
    <tr>
      <td>{{$sCancelButton}} </td>
    </tr>
    </table>
</div>

<p align="center">

{{$LDSearchFound}}

{{if $bShowResult}}
<p align="center">
<div align="center">
	<table border=0 cellpadding=2 cellspacing=1>
		
		{{* This is the title row *}}
		<tr>
			<td colspan=8>{{$sPreviousPage}}</td>
			<td align=right colspan="2">{{$sNextPage}}</td>
		</tr>
		<tr class="reg_list_titlebar">
			<td width="10%">{{$LDRegistryNr}}</td>
			<td width="2%">{{$LDSex}}</td>
			<td width="11%">{{$LDLastName}}</td>
			<td width="*">{{$LDFirstName}}</td>
			<td width="11%">{{$LDMiddleName}}</td>
			<td width="5%">{{$LDBday}}</td>
			<td width="15%">{{$segBrgy}}</td>
			<td width="10%">{{$segMuni}}</td>
			<td width="3%">{{$LDZipCode}}</td>
			<td width="3%">{{$LDOptions}}</td>
		</tr>

		{{* The content of sResultListRows is generated using the reg_search_list_row.tpl template *}}
		{{$sResultListRows}}

		<tr>
			<td colspan=8>{{$sPreviousPage}}</td>
			<td align=right colspan="2">{{$sNextPage}}</td>
		</tr>
	</table>
  {{/if}}
  {{$yhPrevNext}}
  {{$sPostText}}</div>