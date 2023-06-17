{{*created by cha 06-11-2009*}}
{{$sFormStart}}
    <div style="padding:10px;width:95%;border:0px solid black">
    {{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

    {{*------------code for from and to text fields---------*}}
    <font class="warnprompt"><br></font>
    <table border="0" width="75%" class="Search">
        <tbody class="submenu">
            {{*---------code for buttons-------*}}
            <tr>
                <td align="left" width="10%">
                <input id="surgeons" name="surgeons" type="button" value="Surgeons" onclick="listORPersonnel(this.id); return false;"></input>
                <input id="asstsurgeons" name="asstsurgeons" type="button" value="Assistant Surgeons" onclick="listORPersonnel(this.id); return false;"></input>                                                                                              
                <input id="anesth" name="anesth" type="button" value="Anesthesiologists"  onclick="listORPersonnel(this.id); return false;"></input>
                <input id="nurses" name="nurses" type="button" value="Nurses"  onclick="listORPersonnel(this.id); return false;"></input>
                </td>
                
            </tr>   
            {{*---------endcode for buttons--------*}}           
        </tbody>
    </table>
    
    <div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:300px; width:75%; background-color:#e5e5e5">
    <table id="ORSchedList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
    <thead>
            <th rowspan="3" width="1%"></th>
            <th rowspan="3" width="10%" align="left"></th>
            <th rowspan="3" width="5%" align="center"></th>
            <th rowspan="3" width="15%" align="left"></th>
            <th rowspan="3" width="5%" align="center"> </th>
    </thead>
    <tbody id="ORSchedList-body">
        <tr><td colspan="6" style="">No OR Personnel selected..</td></tr>
    </tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
    
<br/>
</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}} 
{{$sTailScripts}}