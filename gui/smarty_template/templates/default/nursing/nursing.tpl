     <blockquote>
      <TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
      <TBODY>
        <TR>
          <TD>
            <TABLE cellSpacing="0" cellPadding="0" width="600" class="submenu_group">
            <TBODY>
  
            {{$LDNursingStations}}
  <!--
            <TR>
              <TD colspan=3>
                <table border=0 cellpadding=1 cellspacing=1>
                  {{$tblWardInfo}}
                </table>
              </TD>
            </TR>
  -->
            {{include file="common/submenu_row_spacer.tpl"}}
  
            {{$LDQuickView}}
  
            {{include file="common/submenu_row_spacer.tpl"}}
  
            {{$LDSearchPatient}}
  
            {{include file="common/submenu_row_spacer.tpl"}}
  
            {{$LDArchive}}
  
            {{include file="common/submenu_row_spacer.tpl"}}
  
            {{$LDStationMan}}
            
            <!--commented by VAN 01-28-08-->
            <!--
            {{include file="common/submenu_row_spacer.tpl"}}
            
            {{$LDStationMan1}}
            -->    
            {{include file="common/submenu_row_spacer.tpl"}}
  
            {{$LDNursesList}}
  
            {{include file="common/submenu_row_spacer.tpl"}}
  
            {{$LDNews}}
  
            </TBODY>
            </TABLE>
          </TD>
        </TR>
      </TBODY>
      </TABLE>

      <p>
      <a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}} /></a>
      </p><p>
      </p></blockquote>
