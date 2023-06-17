{{*created by cha 05-20-2009*}}
{{$sFormStart}}
    <div style="padding:10px;width:95%;border:0px solid black">
    {{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

    {{*------------code for from and to text fields---------*}}
    <font class="warnprompt"><br></font>
    <table border="0" width="30%" class="Search">
      <tbody>
        <tr>
          <td class="segPanelHeader">Search existing OR series</td>
        </tr>
        <tr>
          <td class="segPanel" align="left" style="white-space:nowrap">{{$sFromOrNo}} {{$sToOrNo}}</td>
        </tr>
          {{*---------code for buttons-------*}}
          <tr>
            <td class="segPanel" align="center">
              <img class="segSimulatedLink" id="search" name="search" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 alt="Search data" align="absmiddle"  onclick="startAJAXSearch(this.id,0); return false;" />
            </td>
          </tr>   
          {{*---------endcode for buttons--------*}}           
      </tbody>
    </table>
</div>
{{$sTable}}
{{$sFormEnd}} 
{{$sTailScripts}}