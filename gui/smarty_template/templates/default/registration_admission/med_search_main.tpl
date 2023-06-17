{{* reg_search_main.tpl  Mainframe for patient/person registration search page *}}

{{$sPretext}}

{{* Never remove the $sJSFormCheck tag from this template *}}
{{$sJSFormCheck}}

<p>

<table class="admit_searchmask_border" border=0 cellpadding=10>
    <tr>
        <td>
            <table class="admit_searchmask" cellpadding="5" cellspacing="5">
            <tbody>
                <tr>
                    <td>
                        <form {{$sFormParams}}>
                            &nbsp;
                            <br>
                            {{$searchprompt}}
                            <br><br>
                            {{* Never rename this input. Redimensioning it is allowed. *}}
                            <input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();">
                            
                            {{* Do not move the sHiddenInputs outside the <form> block *}}
                            &nbsp;{{$sHiddenInputs}}&nbsp;{{$sAllButton}}
                            <p>
                            {{$sCheckBoxFirstName}} {{$LDIncludeFirstName}}
                            </p>
                            
                            <!-- added by VAN 06-25-08-->
                            {{if $sClinics}}
                                {{$sCheckAll}}&nbsp;{{$LDCheckAll}}&nbsp;&nbsp;&nbsp;{{$sCheckYes}}&nbsp;{{$LDCheckYes}}&nbsp;&nbsp;&nbsp;{{$sCheckNo}}&nbsp;{{$LDCheckNo}}
                                <br>
                            {{/if}}    
                            <!-- -->
                            
                        </form>
                    </td>
                </tr>
            </tbody>
            </table>
        </td>
    </tr>
</table>
<p>
{{$sCancelButton}}
<p>

{{$LDSearchFound}}

{{if $bShowResult}}
    <p>
    <table border=0 cellpadding=2 cellspacing=1>
        <tr>
            <td colspan=10>{{$sPreviousPage}}</td>
            <td align=right>{{$sNextPage}}</td>
        </tr>
        
        {{* This is the title row *}}
        <tr class="reg_list_titlebar">
            <td width="17%">{{$LDCaseNr}}</td>
            <td width="15%">{{$LDLastName}}</td>
            <td width="15%">{{$LDFirstName}}</td>
            <td width="15%">{{$LDMiddleName}}</td>
            <td width="4%">{{$LDSex}}</td>
            <td width="5%">{{$LDAge}}</td>
            <td width="12%">{{$LDBday}}</td>
            <td width="11%">&nbsp;{{$LDOptions}}</td> 
        </tr>

        {{* The content of sResultListRows is generated using the reg_search_list_row.tpl template *}}
        {{$sResultListRows}}

        <tr>
            <td colspan=10>{{$sPreviousPage}}</td>
            <td align=right>{{$sNextPage}}</td>
        </tr>
    </table>
    
{{/if}}
<hr>
{{$yhPrevNext}}
{{$sPostText}}

