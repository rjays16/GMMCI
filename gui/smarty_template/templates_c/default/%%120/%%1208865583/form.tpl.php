<?php /* Smarty version 2.6.0, created on 2018-04-24 13:28:31
         compiled from medocs/form.tpl */ ?>

<?php if ($this->_tpl_vars['bSetAsForm']): ?>
<?php echo $this->_tpl_vars['sDocShotcuts']; ?>

<?php echo $this->_tpl_vars['sDocsJavaScript']; ?>

<form method="post" id="entryform" name="entryform" onsubmit="return false">
<?php endif; ?>
<!-- chkForm(this) -->
<table border=0 cellpadding=2 width=100%>
    <?php if ($this->_tpl_vars['sDiagnosisNotes']): ?>
    <tr bgcolor='#f6f6f6'>
            <!-- <td><?php echo $this->_tpl_vars['LDExtraInfo']; ?>
<br />(<?php echo $this->_tpl_vars['LDInsurance']; ?>
)</br></td>  -->
            <td width="25%"> Admitting Diagnosis</td>
        <td width="75%">
                            <!--<textarea name='aux_notes' id='aux_notes' type='hidden' cols=80 rows=3 wrap='physical' readonly="readonly"></textarea> -->
                    <?php echo $this->_tpl_vars['txtAreaDiagnosis']; ?>

                                                 </td>
    </tr>
    <?php endif; ?>
    <!--edited by VAN -->
    <?php if ($this->_tpl_vars['sAdmittedOpd_b']): ?>
         <tr bgcolor="#f6f6f6">
            <td> &nbsp;&nbsp; Admission Date/Time</td>
                <td>
                    <input type="text" size="10" maxlength="10" id="txtAdmissionDate" name="txtAdmissionDate" value="<?php echo $this->_tpl_vars['sAdmissionDate']; ?>
" readonly />
                    <input type="text" size="10" maxlength="10" id="txtAdmissionTime" name="txtAdmissionTime" value="<?php echo $this->_tpl_vars['sAdmissionTime']; ?>
" readonly />
                </td>
        </tr>

         <tr bgcolor='#f6f6f6'>
         <td><FONT  color='red'>*</font> Encoded <?php echo $this->_tpl_vars['LDBy']; ?>
 </td>
         <td>
            <?php if ($this->_tpl_vars['bSetAsForm']): ?>
                <input type='text' name='personell_name' size=50 maxlength=60 value='<?php echo $this->_tpl_vars['TP_user_name']; ?>
' readonly />
            <?php else: ?>
                <?php echo $this->_tpl_vars['sAuthor']; ?>

            <?php endif; ?>         </td>
         </tr>
        <?php if ($this->_tpl_vars['sSetDeptDischarged']): ?>
             <tr bgcolor="#f6f6f6">
                <td><font color="red">*</font> <?php echo $this->_tpl_vars['sDocLabel']; ?>
 Physician &amp; Department </td>
                <td>
                    <?php echo $this->_tpl_vars['sDoctorInputF']; ?>
 <?php echo $this->_tpl_vars['sDeptInputF']; ?>
                </td>
             </tr>
         <?php endif; ?>
     <?php endif; ?>
    <!-- start -->
    <?php if ($this->_tpl_vars['sAdmittedOpd_a']): ?>
        <tr bgcolor="#f6f6f6">
            <td> &nbsp;&nbsp; Admission Date/Time</td>
            <td>
                <input type="text" size="10" maxlength="10" id="txtAdmissionDate" name="txtAdmissionDate" value="<?php echo $this->_tpl_vars['sAdmissionDate']; ?>
" readonly />
                <input type="text" size="10" maxlength="10" id="txtAdmissionTime" name="txtAdmissionTime" value="<?php echo $this->_tpl_vars['sAdmissionTime']; ?>
" readonly />
            </td>
            </tr>
        <tbody id="bodyDischarge2">
        <?php if ($this->_tpl_vars['sSetDischarged']): ?>
             <tr bgcolor='#f6f6f6'>
             <td><FONT  color='red'>*</font>  Discharge Date/Time</td>
             <td>
                <?php if ($this->_tpl_vars['bSetAsForm']): ?>
                     <br>
                     <input type='text' name='date_text_d' size=10 maxlength=10 <?php echo $this->_tpl_vars['sDateValidateJs_d']; ?>
 />
                     <?php echo $this->_tpl_vars['sDateMiniCalendar_d']; ?>

                    <input type='text' id='time_text_d' name='time_text_d' size="4" maxlength="5" <?php echo $this->_tpl_vars['sFormatTime']; ?>
 />
                    <select id='selAMPM' name="selAMPM">
                        <option value="A.M.">A.M.</option>
                        <option value="P.M.">P.M.</option>
                    </select>
                <?php else: ?>
                    <?php echo $this->_tpl_vars['sDate']; ?>

                <?php endif; ?>             </td>
             </tr>

         <?php endif; ?>
        </tbody>
         <?php if ($this->_tpl_vars['sSetDeptDischarged']): ?>
             <tr bgcolor="#f6f6f6">
                <td><font color="red">*</font> <?php echo $this->_tpl_vars['sDocLabel']; ?>
 Physician &amp; Department </td>
                <td>
                    <?php echo $this->_tpl_vars['sDoctorInputF']; ?>
 <?php echo $this->_tpl_vars['sDeptInputF']; ?>
                </td>
             </tr>
         <?php endif; ?>
         <tr bgcolor='#f6f6f6'>
         <td><FONT  color='red'>*</font>  Encoded <?php echo $this->_tpl_vars['LDBy']; ?>
 </td>
         <td>
            <?php if ($this->_tpl_vars['bSetAsForm']): ?>
                <input type='text' name='personell_name' size=50 maxlength=60 value='<?php echo $this->_tpl_vars['TP_user_name']; ?>
' readonly />
            <?php else: ?>
                <?php echo $this->_tpl_vars['sAuthor']; ?>

            <?php endif; ?>         </td>
         </tr>
    <?php endif; ?>
    <!-- end -->
        <?php if ($this->_tpl_vars['sSetConsult']): ?>
    <tr bgcolor='#f6f6f6'>
        <td><font color="red">*</font>Consulting Doctor & Department</td>
        <td>
            <?php echo $this->_tpl_vars['consultingDoc']; ?>
 <?php echo $this->_tpl_vars['consultingDept']; ?>

        </td>
    </tr>
    <?php endif; ?>

     <?php if ($this->_tpl_vars['bSetUpdate']): ?>}
        <tr bgcolor='f6f6f6'>
            <td><font color=red>*</font> Department</td>
            <td>
                <?php echo $this->_tpl_vars['sDeptInput1']; ?>
            </td>
            </tr>
        <tr bgcolor='f6f6f6'>
            <td><font color=red>*</font> Attending Doctor</td>
            <td><?php echo $this->_tpl_vars['sDoctorInput1']; ?>
</td>
        </tr>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['bSetAsForm']): ?>
        <tr bgcolor="#f6f6f6">
            <td valign="top">
                <table width="200" border="0" bordercolor="#F6F6F6">
                        <tr>
                                <td height="100" valign="top"><font color="red">*</font><?php echo $this->_tpl_vars['LDDiagnosis']; ?>
</td>
                            </tr>
                    </table>            </td>
                <td><?php if ($this->_tpl_vars['sSetDeptDiagnosis']): ?> <?php echo $this->_tpl_vars['sDoctorInputD']; ?>
 <?php echo $this->_tpl_vars['sDeptInputD']; ?>
 <br /> <?php endif; ?>
                 <?php echo $this->_tpl_vars['codeControl1']; ?>
</td>
        </tr>
        
        <!--  start-->


        <!--<tr bgcolor='f6f6f6'>
            <td valign="top">
                <table width="200" border="0" bordercolor="#F6F6F6">
                    <tr>
                        <td height="100" valign="top"> Operations Notes</td>
                    </tr>
                </table>            </td>
                <!--<td><?php if ($this->_tpl_vars['sSetDeptTherapy']): ?>    <br /> <?php endif; ?>
                        </td>-->
                    <!--<td>
                        <textarea name="aux_notes_p" id="aux_notes_p" cols="80" rows="3" wrap="physical" readonly="readonly"></textarea>
                    </td>-->
        <!--</tr>-->

        <!--  end--><!--  end-->

        <!-- OPERATION AREA -->
        <tr bgcolor='f6f6f6'>
            <td valign="top">
                <table width="200" border="0" bordercolor="#F6F6F6">
                    <tr>
                        <td height="100" valign="top"><font color="red">*</font> Operations</td>
                    </tr>
                </table>            </td>
				<td><?php if ($this->_tpl_vars['sSetDeptTherapy']): ?> <?php echo $this->_tpl_vars['sDoctorInputP']; ?>
 <?php echo $this->_tpl_vars['sDeptInputP']; ?>
 &nbsp;
                        <br/> <input type='text' name='date_text_p' id='date_text_p' size=10 maxlength=10 <?php echo $this->_tpl_vars['sDateValidateJs_p']; ?>
 />  <?php echo $this->_tpl_vars['sDateMiniCalendar_p'];  echo $this->_tpl_vars['sTimeP']; ?>
  <br /> <?php endif; ?>
                        <?php echo $this->_tpl_vars['codeControl2']; ?>
</td>
        </tr>
        
        <!-- notification -->
        <tr bgcolor="#f6f6f6">
            <td valign="top">
                <table width="200" border="0" bordercolor="#F6F6F6">
                    <tr>
                        <td height="100" valign="top">Notification</td>
                    </tr>
                </table>            
            </td>
            <td> <?php echo $this->_tpl_vars['codeControl_Notification']; ?>
 </td>
        </tr>
        <!-- -->
        
    <tr bgcolor="#f6f6f6">
            <td colspan="2"><?php echo $this->_tpl_vars['sCheckDischarge']; ?>
</td>
    </tr>
    
    <?php if ($this->_tpl_vars['sSetCon']): ?>
    <tbody id="bodyDischarge3">
        <tr bgcolor='#f6f6f6'>
            <!--<td height="88" valign="top"><font color="red">*</font> Condition</td>-->
            <td height="88" valign="top">Condition</td>
            <td>
                <table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
                    <td width="36%" height="80" valign="middle" id="leftTdResult">
                        <?php echo $this->_tpl_vars['rowConditionA']; ?>
                    </td>
                    <td width="64%" valign="middle" id="rightTdResult">
                        <?php echo $this->_tpl_vars['rowConditionB']; ?>
                    </td>
                </table>            </td>
        </tr>
    </tbody>
    <?php endif; ?>
    <tbody id="bodyDischarge">
    <?php if ($this->_tpl_vars['sSetResult']): ?>
        <tr bgcolor='#f6f6f6'>
            <td height="88" valign="top"  ><font color="red">*</font>  Result</td>
            <td>
                <table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
                    <td width="25%" height="80" valign="middle" id="leftTdResult">
                        <?php echo $this->_tpl_vars['rowResultA']; ?>
                    </td>
                    <td width="*" valign="middle" id="rightTdResult">
                        <?php echo $this->_tpl_vars['rowResultB']; ?>
                    </td>
                </table>            </td>
        </tr>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['sSetResult']): ?>
        <tr bgcolor='#f6f6f6' id="rwDisposition">
            <td height="88" valign="top"><font color="red">*</font>  Disposition</td>
            <td>
                <table width="63%" height="84" border="0" cellpadding="1" id="srcDispTable" style="width:100%; font-size:12px">
                    <td width="36%" valign="middle" height="80" id="leftTdDesposition">
                        <?php echo $this->_tpl_vars['rowDispA']; ?>
                    </td>
                    <td width="64%" valign="middle" id="rightTdDesposition">
                        <?php echo $this->_tpl_vars['rowDispB']; ?>
                    </td>
                </table>            </td>
        </tr>
    <?php endif; ?>
    </tbody>
    <?php else: ?>
    <!-- notification -->
    <tr bgcolor='#f6f6f6'>
         <td>Notification</td>
            <td><?php echo $this->_tpl_vars['sNotification']; ?>
</td>
    </tr>
    <!-- -->
     
    <tr bgcolor='#f6f6f6'>
         <td><FONT  color='red'>*</font><?php echo $this->_tpl_vars['LDDiagnosis']; ?>
</td>
            <td><?php echo $this->_tpl_vars['sDiagnosis']; ?>
</td>
     </tr>
                      <!--<textarea name='text_therapy' cols=60 rows=1 wrap='physical'></textarea> -->
        <tr bgcolor='#f6f6f6'>
        <td><FONT  color='red'>*</font>  <?php echo $this->_tpl_vars['LDTherapy']; ?>
</td>
        <td><?php echo $this->_tpl_vars['sTherapy']; ?>
</td>
        </tr>
    <tr bgcolor="#f6f6f6">
            <td colspan="2"><?php echo $this->_tpl_vars['sCheckDischarge']; ?>
</td>
    </tr>

        <?php if ($this->_tpl_vars['sSetResult']): ?>

        <tr bgcolor="#f6f6f6">
            <td><font color='red'>*</font>  Result</td>
            <td><?php echo $this->_tpl_vars['sResult']; ?>
</td>
        </tr>
        <tr bgcolor="#f6f6f6">
            <td><font color='red'>*</font>  Disposition</td>
            <td><?php echo $this->_tpl_vars['sDisposition']; ?>
</td>
        </tr>

    <?php endif; ?>
     <?php endif; ?>

     <?php if ($this->_tpl_vars['sAdmittedOpd_b']): ?>
         <?php if ($this->_tpl_vars['sSetDischarged']): ?>
            <tbody id="bodyDischarge2">
            <tr bgcolor='#f6f6f6' id="rwDischarged">
             <td><FONT  color='red'>*</font>  Discharge Date/Time</td>
             <td>
                <?php if ($this->_tpl_vars['bSetAsForm']): ?>
                     <br>
                     <input type='text' name='date_text_d' size=10 maxlength=10 <?php echo $this->_tpl_vars['sDateValidateJs_d']; ?>
 />
                     <?php echo $this->_tpl_vars['sDateMiniCalendar_d']; ?>

                    <input type='text' id='time_text_d' name='time_text_d' size="4" maxlength="5" <?php echo $this->_tpl_vars['sFormatTime']; ?>
 />
                    <select id='selAMPM' name="selAMPM">
                        <option value="A.M.">A.M.</option>
                        <option value="P.M.">P.M.</option>
                    </select>
                <?php else: ?>
                    <?php echo $this->_tpl_vars['sDate']; ?>

                <?php endif; ?>             </td>
             </tr>
            </tbody>
         <?php endif; ?>
    <?php endif; ?>

</table>
<?php if ($this->_tpl_vars['bSetAsForm']): ?>
    <?php echo $this->_tpl_vars['frmIcd_old']; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['bSetAsForm']): ?>
    <?php echo $this->_tpl_vars['sHiddenInputs']; ?>

    <?php echo $this->_tpl_vars['sTailScripts']; ?>

    <?php echo $this->_tpl_vars['sTailScripts2']; ?>

</form>
<?php endif; ?>