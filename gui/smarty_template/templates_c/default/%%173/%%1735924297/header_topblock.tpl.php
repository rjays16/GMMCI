<?php /* Smarty version 2.6.0, created on 2018-04-24 13:09:21
         compiled from common/header_topblock.tpl */ ?>
 <table cellspacing="0"  class="titlebar" style="border:1px solid #cfcfcf;margin-bottom:10px" cellpadding="0">    
 <tr valign=middle  class="titlebar" >
  <td bgcolor="<?php echo $this->_tpl_vars['top_bgcolor']; ?>
" valign="middle" width="1">
    &nbsp;<?php echo $this->_tpl_vars['sTitleImage']; ?>
&nbsp;<font size="3" color="<?php echo $this->_tpl_vars['top_txtcolor']; ?>
" style="white-space:nowrap"><?php echo $this->_tpl_vars['sToolbarTitle']; ?>
</font>
     <?php if ($this->_tpl_vars['Subtitle']): ?>
      - <?php echo $this->_tpl_vars['Subtitle']; ?>

     <?php endif; ?>
  </td>
<?php if ($this->_tpl_vars['QuickMenu']): ?>
	<td class="quickmenu" bgcolor="<?php echo $this->_tpl_vars['top_bgcolor']; ?>
" align=right valign="middle">
		<ul>
<?php if (count($_from = (array)$this->_tpl_vars['QuickMenu'])):
    foreach ($_from as $this->_tpl_vars['qmId'] => $this->_tpl_vars['qItem']):
?>
	<?php if ($this->_tpl_vars['qItem']['label'] != "|"): ?>
		  <li>
				<a href="<?php echo $this->_tpl_vars['qItem']['url']; ?>
">
					<span><img <?php echo $this->_tpl_vars['qItem']['icon']; ?>
 align="absmiddle"/></span>
					<?php echo $this->_tpl_vars['qItem']['label']; ?>

				</a>
			</li>
	<?php else: ?>
		  <li class="separator"></li>
	<?php endif; ?>
<?php endforeach; unset($_from); endif; ?>
		</ul>
	</td>
<?php else: ?>
  <td bgcolor="<?php echo $this->_tpl_vars['top_bgcolor']; ?>
" align=right valign="middle" style="">
  	<?php if ($this->_tpl_vars['pbAux2']): ?>
		<a href="<?php echo $this->_tpl_vars['pbAux2']; ?>
"><img <?php echo $this->_tpl_vars['gifAux2']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 /></a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['pbAux1']): ?>
		<a href="<?php echo $this->_tpl_vars['pbAux1']; ?>
"><img <?php echo $this->_tpl_vars['gifAux1']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 /></a>
	<?php endif; ?>
	<!-- Hide Back buttons =)  AJMQ/Oct 03 2007
	<?php if ($this->_tpl_vars['pbBack']): ?>
		<a href="<?php echo $this->_tpl_vars['pbBack']; ?>
">
			<img <?php echo $this->_tpl_vars['gifBack2']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
	-->
	<!---hide for the meantime...pet, apr22,2008-----
	<?php if ($this->_tpl_vars['pbHelp']): ?>
		<a href="<?php echo $this->_tpl_vars['pbHelp']; ?>
">
			<img <?php echo $this->_tpl_vars['gifHilfeR']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
	---pet---------------------til here only-------->
	<?php if ($this->_tpl_vars['breakfile']): ?>
		<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
" <?php echo $this->_tpl_vars['sCloseTarget']; ?>
>
			<img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
  </td>
<?php endif; ?>
 </tr>
 </table>