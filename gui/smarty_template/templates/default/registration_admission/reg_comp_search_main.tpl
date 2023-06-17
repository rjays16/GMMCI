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
        <table align="center" border="0" cellpadding="5" cellspacing="5" class="reg_searchmask">
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
                {{$sSearchKey}}
				<input type="hidden" name="enctype" id="enctype"/>
				<!--{{$sKeyPost}}-->
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
                <br><br>
				  {{$sCheckAll}}&nbsp;{{$LDCheckAll}}&nbsp;&nbsp;&nbsp;{{$sCheckER}}&nbsp;{{$LDCheckER}}&nbsp;&nbsp;&nbsp;{{$sCheckOPD}}&nbsp;{{$LDCheckOPD}}&nbsp;&nbsp;&nbsp;{{$sCheckIPD}}&nbsp;{{$LDCheckIPD}}	  
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
		<tr class="reg_list_titlebar">
            <td width="10%"><strong><font color="#000066">{{$LDCaseNr}}</font></strong></td>
			<td width="15%"><strong><font color="#000066">{{$LDRegistryNr}}</font></strong></td>
			<td width="2%"><strong><font color="#000066">{{$LDSex}}</font></strong></td>
			<td width="15%"><strong><font color="#000066">{{$LDLastName}}</font></strong></td>
			<td width="*"><strong><font color="#000066">{{$LDFirstName}}</font></strong></td>
			<td width="5%"><strong><font color="#000066">{{$LDBday}}</font></strong></td>
			<td width="5%"><strong><font color="#000066">{{$LDAdmission}}</font></strong></td>
			<td width="10%"><strong><font color="#000066">{{$LDLocation}}</font></strong></td>
			<td width="5%"><strong><font color="#000066">{{$LDDischarge}}</font></strong></td>
			<td width="5%"><strong><font color="#000066">{{$LDOptions}}</font></strong></td>
            <td width="2%" align="center"><strong><font color="#000066">{{$LDOptions2}}</font></strong></td>
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