<?php /* Smarty version 2.6.0, created on 2017-11-29 01:04:40
         compiled from common/submenu_data.tpl */ ?>
<blockquote>
<TABLE cellSpacing=0 cellPadding=0 border=0 class="submenu_frame" style="    -moz=border-radius-bottomleft: 4px;    ">
    <TBODY>
    <TR>
        <TD>
            <TABLE cellSpacing=1 cellPadding=3 width=600>
                <TBODY class="submenu">
                    <tr>
                        <td class="submenu_title" colspan="3"><?php echo $this->_tpl_vars['SubMenuTitle']; ?>
</td>
                    </tr>
                   <!--commented by jasper 01/17/13 <?php echo $this->_tpl_vars['segRegionMngr']; ?>
 -->
                      <?php echo $this->_tpl_vars['sNew']; ?>

                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'common/submenu_row_spacer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                   <!-- <?php echo $this->_tpl_vars['segProvinceMngr']; ?>
 -->
                      <?php echo $this->_tpl_vars['sList']; ?>

                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'common/submenu_row_spacer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                      <?php echo $this->_tpl_vars['sSearch']; ?>

<!-----no longer needed, conferred with BKC, 10-26-2007, fdp-----------
                    <?php echo $this->_tpl_vars['segAddress']; ?>

                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'common/submenu_row_spacer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
-----------until here only------------------fdp------------------------>
                </TBODY>
            </TABLE>
        </TD>
    </TR>
    </TBODY>
</TABLE>
<p>
<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
</blockquote>