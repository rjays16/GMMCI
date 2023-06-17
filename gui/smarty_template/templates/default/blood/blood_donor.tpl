{{*created by cha 08-24-2009*}}
{{$sFormStart}}

<div style="width:100%">    
         Donate Blood
         <div style="padding:10px;width:95%;border:0px solid black">
         <font class="warnprompt"><br></font>
         <table border="0" width="100%">
         <tbody class="submenu">
            <tr>
                <td align="right" width="140"><b>Donor:</b></td>
                <td width="70%">{{$donorName}}</td>
            </tr>   
            <tr>
                <td align="right" width="140"><b>Date:</b></td>
                <td>{{$donateDate}}{{$donateDateIcon}}</td>
            </tr>
            <tr>
              <td align="right" width="140"><b>Quantity:</b></td>
              <td width="70%">
                <input type="text" size="5" id="donate_qty"/>
                {{$sSelectUnit}}
              </td>
            </tr>          
        </tbody>
        </table>
        </div>   
</div>
{{$sFormEnd}}
{{*$sTable*}}