<?php /* Smarty version 2.6.0, created on 2018-02-06 18:50:27
         compiled from billing_new/seg-encounter-insurance.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'billing_new/seg-encounter-insurance.tpl', 26, false),)), $this); ?>
<!-- Created by Nick on 9/1/14 -->
<head>
    <?php if (count($_from = (array)$this->_tpl_vars['javascripts'])):
    foreach ($_from as $this->_tpl_vars['script']):
?>
    <?php echo $this->_tpl_vars['script']; ?>

    <?php endforeach; unset($_from); endif; ?>
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
    <script>
        $j(function () {
            preset();
            // added by: syboy 03/16/2016 : meow
            $j('#btn_birth_cert').on("click", function(){
                $j(this).text($j(this).text() == 'Show Birth Certificate' ? 'Hide Birth Certificate' : 'Show Birth Certificate');
                $j("#tbl_birth_cert").slideToggle(500);
                $j("#birthCertData").slideToggle();

            });
            // ended syboy
        });
    </script>
</head>
<div align="center">
    <div align="left" style="width: 90%; margin-top: 5px;">
        <table>
            <tr>
                <td>Billing Type:</td>
                <td><?php echo smarty_function_html_options(array('id' => 'insurance_classes','onchange' => "showAddInsuranceButton();",'class' => 'segInput','name' => 'insurance_classes','options' => $this->_tpl_vars['insurance_classes'],'selected' => $this->_tpl_vars['person_insurance_class']), $this);?>
</td>
                <td>
                    <?php if ($this->_tpl_vars['btnAddInsurance']): ?>
                        <?php if ($this->_tpl_vars['person_insurance_class'] == 3): ?>
                            <button id="btn_add_insurance" class="segButton" onclick="addInsurance();" style="display: none;">Add Insurance</button>
                            <button id="btn_audit_trail" class="segButton" onclick="auditTrail();" onmouseout="nd();" style="display: none;">Audit Trail</button>
                        <?php else: ?>
                            <button id="btn_add_insurance" class="segButton" onclick="addInsurance();">Add Insurance</button>
                            <button id="btn_audit_trail" class="segButton" onclick="auditTrail();" onmouseout="nd();">Audit Trail</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <div id="reason-dialog" style="display: none;">
    <form id="form-reason">
        <fieldset>
            <legend>Reason of deletion:</legend>
            <select id="select-reason" onchange="deleteReason()">
                <option value=""></option>
                <?php echo $this->_tpl_vars['delOptions']; ?>

            </select>
            <br/><br/>
            <input type="hidden" name="delete_reason" id="delete_reason"/>
            <textarea name="delete_other_reason" id="delete_other_reason" rows="5" style="width: 100%; display: none"></textarea>
        </fieldset>
    </form>

</div>
    <table style="width: 90%; margin-top: 5px;">
        <thead>
        <tr>
            <th class="jedPanelHeader" colspan="4">INSURANCE AVAILABLE FOR THIS PERSON</th>
        </tr>
        </thead>
        <tbody id="person_insurance">
        <!-- DATA -->
        </tbody>
    </table>

    <table style="width: 90%; margin-top: 5px;">
        <thead>
        <tr>
            <th class="jedPanelHeader" colspan="4">INSURANCE TO BE USED</th>
        </tr>
        </thead>
        <tbody id="encounter_insurance">
        <!-- DATA -->
        </tbody>
    </table>
    <!-- added by: syboy 03/16/2016 : meow -->
    <hr width="90%" align="center" />
    <button id="btn_birth_cert" class="segButton" style="margin-left: 55px; ">Hide Birth Certificate</button>
    <div id="tbl_birth_cert">
        <table style="width: 90%; margin-top: 5px;" align="center">
            <thead>
            <tr>
                <th class="jedPanelHeader" colspan="4">Birth Certificate</th>
            </tr>
            </thead>
            <tbody id="birthCertData">
            <!-- DATA -->
            </tbody>
        </table>
    </div>    
    <!-- ended syboy -->
</div>
<?php if (count($_from = (array)$this->_tpl_vars['hidden_fields'])):
    foreach ($_from as $this->_tpl_vars['field']):
?>
<?php echo $this->_tpl_vars['field']; ?>

<?php endforeach; unset($_from); endif; ?>