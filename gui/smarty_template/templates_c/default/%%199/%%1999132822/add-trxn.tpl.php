<?php /* Smarty version 2.6.0, created on 2018-01-16 17:34:51
         compiled from dialysis/add-trxn.tpl */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Add Prebill Payment Details</title>
	<?php echo $this->_tpl_vars['bootstrap']; ?>

	<style>
	input[type=text], input[type=number] {
    height: 28px !important;
	}
	.center{
	   text-align: center;   
	}
	</style>
</head>
<body><form id="payform" name="payform" action="" method="POST">
	<table border="0" cellspacing=1 cellpadding=0 width="100%">
	<h4>Add Prebill Payment Details</h2>
	
	<tr>
		<?php echo $this->_tpl_vars['enc_nr']; ?>

		<?php echo $this->_tpl_vars['curr_nr']; ?>

		<td width="10%"><label>Pre-Bill No.</label></td>
		<td width="*" id="prebillno"></td>
	</tr>
	<tr>
		<td width="10%"><label>Total:</label></td>
		<td width="*"><h4 id="total"></h4></td>
	</tr>
	<tr><td width="10%"><label><!-- Less: --></label></td></tr>
	<tr>
		<td width="10%"></td>
		<td>
			<div id="data"></div>
			<div id="msg"></div>
		</td>
	</tr>
</table></form>
</body>
</html>