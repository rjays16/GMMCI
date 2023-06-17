<?php
/* @var $this Controller */
/*this view is for getpin module to have tabs that separate searching from patient and others*/


$baseUrl = Yii::app()->request->baseUrl;
$this->setPageTitle('PIN Verification Utility');

$encounter = $person->latestEncounter;

if (!empty($_REQUEST['pid'])) {
    if (!empty($encounter->isNewRecord)) {
        $errorMessages[] = 'This patient currently does not have an encounter';
    }
}
// CVarDumper::dump($encounter->isNewRecord);die;
if (!empty($errorMessages)) {
    $listMessages = '<ul><li>' . implode('</li><li>', $errorMessages)
        . '</li></ul>';
    Yii::app()->user->setFlash(
        'warning', '<strong>Warning!</strong> ' . $listMessages
    );
}

Yii::app()->getClientScript()->registerScript(
    'member.pin.walkin', <<<JAVASCRIPT
$('.service-form').submit(function() {
    Alerts.loading({ content: 'Contacting PHIC web service. Please wait...' });
});

$('#check-pin').click(function(e) {
    e.preventDefault();
    $('.service-form:visible').submit();
});

$('#go-to-eligibility').click(function() {
    var _button = $(this);
    if(_button.hasClass('disabled')) 
        return false;
    Alerts.loading({ content: 'Redirecting to Verify Eligibity Page. Please wait...' });
});

$('#reflect-insurance-billing').click(function(e) {
    var _button = $(this);
    if(_button.hasClass('disabled')) 
        return false;
    e.preventDefault();

    if (!_button.data('multiple-encounter')) {
         Alerts.confirm({
            title: "Are you sure?",
            content: _button.data('alert-message'),
            callback: function(result) {
                if(result) {
                    window.location = _button.attr('href');
                    Alerts.loading({ content: 'Adding insurance to the billing. Please wait...' });
                }
            }
        });
    }
   
});

// added by JOY @ 02-22-2018
$('#search-results-container').off('click', '.add-to-tray').on('click', '.add-to-tray', function(e) {
   var _button = $(this);
   if(_button.hasClass('disabled')) 
    return false;
    e.preventDefault();

 
    Alerts.confirm({
        title: "Are you sure you want to add insurance to this encounter?",
        content: _button.data('alert-message'),
        callback: function(result) {
            if(result) {
                window.location = _button.attr('href');
                Alerts.loading({ content: 'Adding insurance to the billing. Please wait...' });
            }
        }
    });
}); // end by JOY
$('#walkin-tab > a').on('click', function() {
    $('#go-to-eligibility').hide();
});   
$('#search-tab > a').on('click', function() {
    $('#go-to-eligibility').show();
});
JAVASCRIPT
    , CClientScript::POS_READY
);
?>


    <div class="row-fluid">
        <div class="span12">

            <?php
            // Mod by Jeff 02-14-18 for button disabled depend on condition.
            Yii::import('bootstrap.widgets.TbButton');
            if ($countActiveEnc > 1) {
                $insuranceButton = array(
                    'class'       => 'bootstrap.widgets.TbButton',
                    'id'          => 'reflect-insurance-billing',
                    'buttonType'  => TbButton::BUTTON_BUTTON,
                    'type'        => TbButton::TYPE_INFO,
                    'icon'        => 'fa fa-plus',
                    'label'       => 'Add Insurance',
                    'url'         => '#',
                    'htmlOptions' => array(
                        'class'                   => 'bootstrap.widgets.TbButton',
                        'data-toggle'             => 'modal',
                        'data-target'             => '#caseno-modal',
                        'data-tooltip'            => 'tooltip',
                        'title'                   => 'List of Case Nos.',
                        'data-multiple-encounter' => true,
                    ),
                );
            } else {
                $insuranceButton = array(
                    'class'       => 'bootstrap.widgets.TbButton',
                    'id'          => 'reflect-insurance-billing',
                    'buttonType'  => TbButton::BUTTON_LINK,
                    'type'        => TbButton::TYPE_INFO,
                    'icon'        => 'fa fa-plus',
                    'label'       => 'Add Insurance',
                    'url'         => $this->createUrl(
                        'manageInsuranceToBilling', array(
                            'pid'       => $person->pid,
                            'encounter' => $person->activeInsuranceEncounter->encounter_nr,
                            'action'    => 'add',
                        )
                    ),
                    'disabled'    => empty($person->activeInsuranceEncounter)
                        || $hasFinalBill
                        || empty($person->phicMember2->insurance_nr),
                    'htmlOptions' => array(
                        'data-alert-message' => 'Add this insurance to the billing record of the patient.',
                    ),
                );
            }


            if ($countActiveEnc == 1
                && !empty($person->activeInsuranceEncounter->encounterInsurance)
            ) {
                $insuranceButton = CMap::mergeArray(
                    $insuranceButton, array(
                        'type'        => TbButton::TYPE_DANGER,
                        'id'          => 'removerInsurance',
                        'icon'        => 'fa fa-minus',
                        'label'       => 'Remove Insurance',
                        'url'         => '#',
                        'disabled'    => $hasFinalBill,
                        'htmlOptions' => array(
                            'data-alert-message' => 'Remove this insurance from the billing record of the patient.',
                            'data-toggle'        => 'modal',
                            'data-target'        => (!$hasFinalBill)
                                ? '#riModal'
                                : '#',
                        ),
                    )
                );
            }
            $box = $this->beginWidget(
                'application.widgets.SegBox',
                array(
                    'title'         => 'Get PIN',
                    'headerIcon'    => 'icon-cog',
                    'htmlOptions'   => array('class' => ''),
                    'headerButtons' => array(
                        array(
                            'class'      => 'bootstrap.widgets.TbButton',
                            'id'         => 'go-to-eligibility',
                            'buttonType' => TbButton::BUTTON_LINK,
                            'type'       => TbButton::TYPE_PRIMARY,
                            'icon'       => 'fa fa-link',
                            'label'      => 'Go To Eligibility',
                            'url'        => $this->createUrl(
                                'eligibility/index', array(
                                    'id' => $person->pid,
                                )
                            ),
                            'disabled'   => empty($person->pid),
                        ),
                        $insuranceButton,
                        array(
                            'class'       => 'bootstrap.widgets.TbButton',
                            'id'          => 'check-pin',
                            'buttonType'  => TbButton::BUTTON_BUTTON,
                            'type'        => TbButton::TYPE_SUCCESS,
                            'icon'        => 'fa fa-check',
                            'loadingText' => 'Checking PIN ...',
                            'label'       => 'Check PIN',
                            'htmlOptions' => array(
                                'class' => 'getpinButton',
                            ),
                        ),
                    ),
                )
            );

            ?>


            <?php
            $this->widget(
                'bootstrap.widgets.TbTabs', array(
                    'tabs' => array(
                        array(
                            'label'       => 'Search records',
                            'active'      => (@$_POST['tab'] !== 'walkin'),
                            'itemOptions' => array(
                                'id' => 'search-tab',
                            ),
                            'content'     => $this->renderPartial(
                                'tabs/searchPatient', array(
                                'model'        => $model,
                                'member'       => $member,
                                'person'       => $person,
                                'hasFinalBill' => $hasFinalBill,
                            ), true
                            ),
                        ),
                        array(
                            'label'       => 'Walk-in',
                            'active'      => (@$_POST['tab'] == 'walkin'),
                            'itemOptions' => array(
                                'id' => 'walkin-tab',
                            ),
                            'content'     => $this->renderPartial(
                                'tabs/walkin', array(
                                'model'  => $model,
                                'member' => $member,
                                'person' => $person,
                            ), true
                            ),
                        ),
                    ),
                )
            );
            ?>

            <?php $this->endWidget(); /* box */ ?>

        </div>
    </div>

    <!-- Added by jeff 01-19-18 for reason of remove insurance. -->
    <!-- Modal -->
    <div id="riModal" class="modal hide fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                    <h4 class="modal-title">Reason for Removing of
                        Insurance</h4>
                </div>
                <!-- Modal Content -->
                <div class="modal-body">
                    <form id="reason-form">
                        <div class="form-group">
                            <label for="sel1">Select reason:</label>
                            <select class="form-control" id="riModalSelect"
                                    name="riModalSelect">
                                <option>Not compensable</option>
                                <option>Admitted from other hospital</option>
                                <option>Regular HD from other hospital</option>
                                <option>Correction/Wrong entry of issuance no.
                                </option>
                                <option>Encoding of Pin/Insurance Number for
                                    POC/HSM
                                </option>
                                <option>Not qualified for PHIC</option>
                                <option>Others</option>
                            </select>
                            <div>
                                <textarea id="riModalTextArea"
                                          name="riModalTextArea"
                                          placeholder="type reason/s here. . ."
                                          style="width: 515px;"></textarea>
                                <input type="hidden" id="pid" name="pid"
                                       value="<?php echo $_GET['pid']; ?>">
                                <input type="hidden" id="urlData" name="urlData"
                                       value="<?php echo $baseUrl; ?>">
                                <input type="hidden" id="action" name="action"
                                       value="remove">

                                <!-- jeff 02-04-18 for getting encounter from selected trashCan -->
                                <input type="hidden" id="get_enc" name="get_enc"
                                       value="<?php echo $person->activeInsuranceEncounter->encounter_nr; ?>">

                            </div>
                        </div>
                </div>
                <!-- Modal buttons -->
                <div class="modal-footer">
                    <button type="submit" id="riModalSubmit"
                            class="btn btn-default"
                            style="background-color: #00a50e; width: 120px; color: #fff; border-radius: 15px 50px; border:none; text-shadow: none;">
                        <i class="fa fa-check"></i> Submit
                    </button>
                    <button type="button" id="riModalClose"
                            class="btn btn-default" data-dismiss="modal"
                            style="background-color: #ff3e45; width: 120px; color: #fff; border-radius: 15px 50px; border:none; text-shadow: none;">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Scripts for Modal Process -->
    <script type="text/javascript">
        // For open and close textarea
        $("#riModalTextArea").hide();
        $("#riModalSelect").change(function () {
            var val = $("#riModalSelect").val();
            if (val == "Others") {
                $("#riModalTextArea").show();
            } else {
                $("#riModalTextArea").hide();
            }
        });


        // Ajax for modal processes
        $("#reason-form").submit(function (event) {

            event.preventDefault();

            var r_encounter = $('.removeInsurance');
            var encounter_nr = r_encounter.data('encounter');

            var r_choice = $('#riModalSelect').val();
            var r_field = $('#riModalTextArea').val();
            var r_enc = $('#get_enc').val();

            var data = $(this).serializeArray();
            data.push({name: 'encounter', value: r_enc});

            if (r_choice == 'Others' && r_field == '') {
                alert("Please input other reason for removing of insurance.");
            }
            else {
                var url = '/index.php?r=eclaims/member/manageInsuranceToBilling';
                var baseUrlD = $('#urlData').val();
                var title = "Failed";
                $.ajax({
                    url: baseUrlD + url,
                    type: 'GET',
                    dataType: 'JSON',
                    data: data,
                    success: function (data) {
                        $("#riModal").modal('toggle');
                        if (data.bool) {
                            title = "Success!";
                        }
                        Alerts.alert({
                            icon: 'fa-check',
                            title: title,
                            content: data.message,
                            callback: function (result) {
                                Alerts.close();
                                location.reload();
                            }
                        });
                    },
                    beforeSend: function () {
                        Alerts.loading({
                            'title': 'Please wait...',
                            content: 'Removing of PhilHealth Insurance to the billing through Eclaims.'
                        });
                    },
                    complete: function () {
                        // Alerts.close();
                    },
                });
            } // end sa else
        });

        // jeff
        function addEncToModal($enc) {
            $('#get_enc').val($enc);
        }

    </script>
    <!-- End jeff -->

<?php

$this->widget(
    'eclaims.widgets.eclaims.EncounterList', array(
        'pid'      => $_REQUEST['pid'],
        'active'   => true,
        'template' => array('add', 'delete')
    )
);
