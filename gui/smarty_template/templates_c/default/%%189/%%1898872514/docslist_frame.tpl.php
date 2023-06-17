<?php /* Smarty version 2.6.0, created on 2018-04-24 13:28:16
         compiled from medocs/docslist_frame.tpl */ ?>
	<br>
	<?php if ($this->_tpl_vars['isOpdInpatient']): ?>
		<table border=0 cellpadding=4 cellspacing=1  width= 100%>
			<tr>
				<td align="left" width="10%"><b><?php echo $this->_tpl_vars['segOpdBtn']; ?>
</b></td>
				<td align="left" ><b><?php echo $this->_tpl_vars['segInpatientBtn']; ?>
</b></td>
			</tr>
		</table>
	<?php endif; ?>
	<b><?php echo $this->_tpl_vars['segHeadingPrincipal']; ?>
</b>
	<table border=0 cellpadding=4 cellspacing=1 width=100%>
		<tr class="adm_item">
			<td align="center" width="50%"><b><?php echo $this->_tpl_vars['LDDiagnosis']; ?>
</b></td>
			<td align="center"><b><?php echo $this->_tpl_vars['LDTherapy']; ?>
</b></td>
		</tr>
		<?php echo $this->_tpl_vars['sDocsListRowsPrincipal']; ?>

	</table>
	<br>
    <b><?php echo $this->_tpl_vars['segHeadingOthers']; ?>
</b>
	<table border=0 cellpadding=4 cellspacing=1 width=100%>
		<tr class="adm_item">
			<td align="center" width="50%"><b><?php echo $this->_tpl_vars['LDDiagnosis']; ?>
</b></td>
			<td align="center"><b><?php echo $this->_tpl_vars['LDTherapy']; ?>
</b></td>
		</tr>	
		<?php echo $this->_tpl_vars['sDocsListRowsOthers']; ?>

	</table>
    <!-- notification -->
    <br>
    <b><?php echo $this->_tpl_vars['segHeadingNotification']; ?>
</b>
    <table border=0 cellpadding=4 cellspacing=1 width=100%>
        <tr class="adm_item">
            <td align="center" width="15%"><b>Date</b></td>
            <td align="center"><b>Notification</b></td>
        </tr>
        <?php echo $this->_tpl_vars['sNotificationListRows']; ?>

    </table>
    <br>
    <b><?php echo $this->_tpl_vars['segHeadingOperation']; ?>
</b>
    <table border=0 cellpadding=5 cellspacing=1 width=100%>
        <tr class="adm_item">
            <td align="center" width="*"><b>Operations</b></td>
            <td align="center" width="15%"><b>Code</b></td>
            <td align="center" width="15%"><b>RVU</b></td>
            <td align="center" width="15%"><b>Date of Operations</b></td>
            <td align="center" width="10%"><b>Quantity</b></td>
        </tr>
        <?php echo $this->_tpl_vars['sOperationListRows']; ?>

    </table>
    <br><br>
    <!-- -->
<?php echo $this->_tpl_vars['sDetailsIcon']; ?>


