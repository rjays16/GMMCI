<?php /* Smarty version 2.6.0, created on 2018-05-07 17:39:55
         compiled from inventory/delivery-form.tpl */ ?>
<script type="text/javascript" language="javascript">
<!--
    function openWindow(url) {
        window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
    }
-->
</script>
<?php if ($this->_tpl_vars['bShowQuickKeys']): ?>
<style type="text/css">
<!--
    table.quickKey td.qkimg{
        font:bold 11px Tahoma;
        vertical-align:middle;
    }
    
    table.quickKey td.qktxt {
        width:70px;
        padding:2px 4px;
        font:bold 11px Tahoma;
        vertical-align:middle;
        color:#007000;
    }
-->
</style>

<div style="width:80%">
    <table border="0" cellspacing="1" cellpadding="2">
        <tr>
            <td class="segPanelHeader">Quick keys</td>
        </tr>
        <tr>
            <td style="background-color:#fffeed; border:1px solid #ebeac4">
                <table class="quickKey" cellpadding="0" cellspacing="1" border="0">
                    <tr>

                        <td class="qkimg" nowrap="nowrap" ><img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/shortcut-f2.png" /></td>
                        <td class="qktxt">Add items</td>
                        
                        <td    class="quickKey" nowrap="nowrap"><img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/shortcut-f3.png" /></td>
                        <td class="qktxt">Clear list</td>
                        
                        <td    class="quickKey" nowrap="nowrap"><img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/shortcut-f9.png" /></td>
                        <td class="qktxt">Person select</td>
                        
                        <td    class="quickKey" nowrap="nowrap"><img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/shortcut-f12.png" /></td>
                        <td class="qktxt">Save/Submit</td>

                    </tr>
                </table>    
            </td>
        </tr>
    </table>
</div>
<?php endif; ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />
<?php echo $this->_tpl_vars['sFormStart']; ?>


<div style="width:85% ;position: relative" align="center">
    <table align="center" border="0" cellspacing="1" cellpadding="1" width="100%">  
    <tbody>
    <tr>
        <td colspan="4">
        <table border="0" cellspacing="0" cellpadding="2" align="center" width="100%" >
            <tr>
            <td class="segPanelHeader" colspan="4" nowrap><strong>Delivery Details</strong></td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td>
            <table border="0" cellspacing="1" cellpadding="1" align="center" width="100%">
                <tbody>
                    <tr>
                        <td class="segPanel" width="15%">
                            Ref No.:
                        </td>
                        <td width="40%" class="segPanel" nowrap align="left" width="">
                            <?php echo $this->_tpl_vars['sRefNo']; ?>

                            <?php echo $this->_tpl_vars['sResetRefNo']; ?>

                        </td>                    
                        <td width="15%" class="segPanel" nowrap >
                            Delivery Date:
                        </td>
                        <td width="*" class="segPanel" align="left"  nowrap valign="middle">
                            <?php echo $this->_tpl_vars['sDeliveryDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>

                        </td>                    
                    </tr>
                    <tr>
                        <td class="segPanel" >
                            Received By:
                        </td>
                        <td class="segPanel" nowrap align="left">                        
                            <?php echo $this->_tpl_vars['sReceivingID']; ?>

                            <?php echo $this->_tpl_vars['sReceivingNM']; ?>

                        </td>                    
                        <td class="segPanel" nowrap >
                            Receiving Area:
                        </td>
                        <td class="segPanel" nowrap align="left">
                            <span id="requesting_area"><?php echo $this->_tpl_vars['sSelectArea']; ?>
</span>
                        </td>                
                    </tr>
                    <tr>
                        <td class="segPanel" width="15%">
                            Remarks:
                        </td>
                        <td colspan="3" class="segPanel" nowrap align="left"><?php echo $this->_tpl_vars['sRemarks']; ?>
</td>                                
                    </tr> 
                    <!-- added by ken : for supplier and PO number from FIS -->
                    <tr>
                        <td class="segPanel" width="15%">
                            Supplier:
                        </td>
                        <td class="segPanel" nowrap align="left"><?php echo $this->_tpl_vars['sSupplier']; ?>
</td>
                        <td class="segPanel" width="20%">
                            PO #: <?php echo $this->_tpl_vars['sPoNo']; ?>

                            <?php echo $this->_tpl_vars['sResetPoNo']; ?>

                        </td>
                        <td class="segPanel" nowrap align="left">
                            Invoice #: <?php echo $this->_tpl_vars['sInvoiceNo']; ?>

                        </td>                         
                    </tr>                
                </tbody>
            </table>
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td><?php echo $this->_tpl_vars['sSearchField']; ?>
&nbsp;<?php echo $this->_tpl_vars['sBtnSearch']; ?>
</td></tr>
    <tr><td>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="50%" align="left">
                    <?php echo $this->_tpl_vars['sBtnAddItem']; ?>

                    <?php echo $this->_tpl_vars['sBtnEmptyList']; ?>

                    <?php echo $this->_tpl_vars['sBtnPDF']; ?>

                </td>
                <td width="*" align="right">
                    <?php echo $this->_tpl_vars['sContinueButton']; ?>

                    <?php echo $this->_tpl_vars['sBreakButton']; ?>

                </td>
            </tr>
        </table>
    </td></tr>
    <tr><td colspan="4">
        <table id="delivery-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr id="delivery-list-header">
                    <th colspan="2" width="10%" nowrap="nowrap" align="center">Item No.</th>
                    <th width="1%" nowrap="nowrap" align="center">[FG]</th>
                    <th width="*" nowrap="nowrap" align="left">Description</th>
                    <th width="4%" nowrap="nowrap" align="left">Qty/Pck</th>
                    <th width="12%" nowrap="nowrap" align="left">Expiry</th> 
                    <th width="8%" nowrap="nowrap" align="left">Serial</th>
                    <th width="8%" nowrap="nowrap" align="left">Lot No</th>
                    <th width="8%" nowrap="nowrap" align="left">Supplier</th>
                    <th width="4%" nowrap="nowrap" align="left">Qty</th>
                    <th width="6%" nowrap="nowrap" align="center">Unit</th>
                    <th width="10%" nowrap="nowrap" align="left">U. Price</th>
                    <th width="12%" nowrap="nowrap" align="left">Amount</th>
                </tr>
            </thead>
            <tbody>
<?php echo $this->_tpl_vars['sDeliveryDetails']; ?>

            </tbody>
            <tfoot>
                    <tr>
                        <th align="left" colspan="10" nowrap="nowrap">Items (<span id="count">0</span>)</th>
                        <th align="right" nowrap="nowrap">Subtotal:</th>
                        <th align="left" nowrap="nowrap" style="text-align:right" colspan="2" style="font-weight:normal">
                            <span style="" id="show_subtotal">0.00</span>
                        </th>
                    </tr>
                </tfoot>
        </table>
        </td></tr>
        </tbody>
    </table>
</div>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<div style="width:80%">
<?php echo $this->_tpl_vars['sUpdateControlsHorizRule']; ?>

<?php echo $this->_tpl_vars['sUpdateOrder']; ?>

<?php echo $this->_tpl_vars['sCancelUpdate']; ?>

</div>


<!--</div>-->
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
     