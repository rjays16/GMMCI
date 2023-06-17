<?php /* Smarty version 2.6.0, created on 2017-12-18 13:53:08
         compiled from social_service/social_service_progress_notes.tpl */ ?>

<form id="progress_notes" name="progress_notes" action="Javascript:void(null);" ENCTYPE="multipart/form-data" method="POST"> 
	<div align="left" style="width:100%" class="form-header rounded-borders-top">
	    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	        <tr>
	            <td width="99%" nowrap="nowrap"><h1>Progress Notes</h1></td>
	        </tr>
	    </table>
	</div>
	<div id="tab_form" align="center" style="width:95%;">
		<ul id="prognotes-tabs" class="tabs-nav">
	        <li><a href="#pn_part1" onClick="" segTab="tab0" segSetMode="pn_form"><span>Progress Notes Form</span></a></li>
	        <!-- <li><a href="#pn_part2" onClick="viewpermssion();" id="tab1" segTab="tab1" segSetMode="pn_view"><span>View Progress Notes</span></a></li> -->
	    </ul>
	    <div id="pn_part1" align="center" style="margin-top:10px;width:98%">
	        <table width="98%" border="0" cellspacing="5" cellpadding="0">
	            <tr>
	                <td valign="top">
	                    <div id="notes" align="left" style="width:10%">
	                        <?php echo $this->_tpl_vars['sHiddenInputs']; ?>

	                    </div>
	                    <div id="prognotes_body" class="dashlet" align="left" style="width:100%">
	                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	                            <tr>
	                                <td>&nbsp;</td>
	                            </tr>
	                            <tr>
	                                <td class="segPanel">
	                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Datetime </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['datetime'];  echo $this->_tpl_vars['calendarButton']; ?>
</td>
	                                        <?php echo $this->_tpl_vars['jsDatetime']; ?>

	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Ward </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['ward']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Diagnosis </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['diagnosis']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Referral <strong style="color: red">*</strong></strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['referral']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Informant <strong style="color: red">*</strong> </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['informant']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="*" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Relation to Patient <strong style="color: red">*</strong></strong></td>
	                                           <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['reltopatient']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Purpose <strong style="color: red">*</strong></strong></td>
	                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['purpose']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Action Taken <strong style="color: red">*</strong></strong></td>
	                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['action_taken']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Recommendation <strong style="color: red">*</strong></strong></td>
	                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['recommendation']; ?>
</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="*" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Medical Social Worker </strong></td>
	                                           <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['med_social_worker']; ?>
</td>
	                                        </tr>
	                                    </table>
	                                </td>
	                            </tr>
	                        </table>
	                    </div>

	                    <div id ='submit_tab1' style="text-align:right;">
	                    	 <?php echo $this->_tpl_vars['pn_submit']; ?>

	                    	 <?php echo $this->_tpl_vars['progNotesbtn']; ?>

	                    </div>
	                </td>
	            </tr>
	        </table>
	    </div>
<?php if ($this->_tpl_vars['permission_all'] || $this->_tpl_vars['permission_view']): ?>
	    <!-- <div id="pn_part2" align="center" style="margin-top:10px;width:98% ">
	        <table width="98%" border="0" cellspacing="5" cellpadding="0">
	            <tr>
	                <td valign="top">
	                    <div id="prognotes_body" class="dashlet"  class="dashlet" align="left" style="width:100%">
	                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	                            <tr>
	                                <tr>
	                                    <td class="segPanel">
	                                       <div class="active-area drop-shadow pre-space rounded-borders-all">
	                                           <div id="social_form">
	                                                <table class="data-grid" border="0">
	                                                    <thead>
	                                                        <tr>
	                                                            <th width="">Datetime</th>
	                                                            <th width="">Ward</th>
	                                                            <th width="">Diagnosis</th>
	                                                            <th width="">Referral</th>
	                                                            <th width="">Informant</th>
	                                                            <th width="" >Relation to Patient</th>
	                                                            <th width="">Purpose</th>
	                                                            <th width="">Action Taken</th>
	                                                            <th width="">Recommendation</th>
	                                                            <th width="">Medical Social Worker</th>
	                                                            <th width=""></th>
	                                                        </tr>
	                                                    </thead> -->
	                                                    <!-- <?php echo $this->_tpl_vars['social_form_data']; ?>
 -->
	                                                 <!--    <tbody id="social_form_data" ></tbody>
	                                                </table>
	                                              

	                                           </div> -->
	                                             <!--  <div id ='print_tab1' style="text-align:right;">
	                    					 		 <?php echo $this->_tpl_vars['pn_print']; ?>

	                    					 		
	                 							  </div> -->
	                                              
	                                        </div>
	                                    </td>
	                                </tr>  
	                            </tr>

	                        </table>
	                        <!-- <?php echo $this->_tpl_vars['social_submit']; ?>
 -->
	                    </div>
	                    </div>
	                   <?php endif; ?>
	                </td>
	            </tr>
	        </table> 
	    </div>
	</div>
</form>
<br />
<br />

<?php echo $this->_tpl_vars['sTailScripts']; ?>
