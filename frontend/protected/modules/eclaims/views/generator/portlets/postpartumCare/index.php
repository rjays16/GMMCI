<?php
use SegHEIRS\components\web\ClientScript;
use SegHEIRS\components\web\Controller;

\Yii::import('bootstrap.widgets.TbActiveForm');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui.js');

Yii::app()->clientScript->registerScriptFile('js/jquery/ui/jquery.livequery.min.js');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'postpartum-care-information',
    'type' => \TbActiveForm::TYPE_VERTICAL,
    'htmlOptions' => array(
        'data-url-save-postpartum-care' =>  $this->createUrl('postpartumCare/savePostpartumCare'),
        // 'data-url-save-perineal-wound-care' =>  $this->createUrl('perinealWoundCare/savePerinealWoundCare'),
        'data-encounter_nr' =>  $encounter->encounter_nr,
        'data-pid' =>  $model['person']->pid,

    ),
    // 'action' => $this->createUrl('medicalHistory.newSaveMedHistory', array(
    //     // 'encounter_no' => $model_latestEncounter['encounter_no'],
    //     // 'spin' => $model_latestEncounter['spin'],
    // ))
));

?>

<div class="row-fluid">
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/perineal_wound_care',
            array(
                'form' => $form,
                'perinealwoundcare' => $perinealwoundcare,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/signs_of_maternal',
            array(
                'form' => $form,
                'signsofmaternal' => $signsofmaternal,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/breastfeeding_nutrition',
            array(
                'form' => $form,
                'breastfeedingnutrition' => $breastfeedingnutrition,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/family_planning',
            array(
                'form' => $form,
                'familyplanning' => $familyplanning,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/provided_family_planning',
            array(
                'form' => $form,
                'providedfamilyplanning' => $providedfamilyplanning,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/referred_partner_physician',
            array(
                'form' => $form,
                'referredpartnerphysician' => $referredpartnerphysician,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span6">

        <?php echo $this->renderPartial(
            'portlets/postpartumCare/view/schedule_next_postpartum',
            array(
                'form' => $form,
                'schedulenextpostpartum' => $schedulenextpostpartum,
                'ynlist' => $ynlist,
            )
        ); ?>
    </div>
</div>
<hr>

<div class="row-fluid">
    <div class="span12">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'icon' => 'fa fa-save',
                'label' => 'Save',
                'htmlOptions' => array(
                    'id' => 'save-postpartum-care',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>
    </div>
</div>

<?php
$this->endWidget();

?>

<script>
    $("#save-postpartum-care").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     savePostpartumCare();
        // } else {
        //     return false;
        // }

        Swal.fire({
            title: 'Save Postpartum Care',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                savePostpartumCare();
            } else {
                return false;
            }
        })
    });

    function savePostpartumCare() {
        const $form = $("#postpartum-care-information");
        const url = $form.data('url-save-postpartum-care');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const is_done_perineal_wound_care = $("#is_done_perineal_wound_care").val();
        const remarks_perineal_wound_care = $("#remarks_perineal_wound_care").val();
        const is_done_signs_of_maternal = $("#is_done_signs_of_maternal").val();
        const remarks_signs_of_maternal = $("#remarks_signs_of_maternal").val();
        const is_done_breastfeeding_nutrition = $("#is_done_breastfeeding_nutrition").val();
        const remarks_breastfeeding_nutrition = $("#remarks_breastfeeding_nutrition").val();
        const is_done_family_planning = $("#is_done_family_planning").val();
        const remarks_family_planning = $("#remarks_family_planning").val();
        const is_done_provided_family_planning = $("#is_done_provided_family_planning").val();
        const remarks_provided_family_planning = $("#remarks_provided_family_planning").val();
        const is_done_referred_partner_physician = $("#is_done_referred_partner_physician").val();
        const remarks_referred_partner_physician = $("#remarks_referred_partner_physician").val();
        const is_done_schedule_next_postpartum = $("#is_done_schedule_next_postpartum").val();
        const remarks_schedule_next_postpartum = $("#remarks_schedule_next_postpartum").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                is_done_perineal_wound_care: is_done_perineal_wound_care,
                remarks_perineal_wound_care: remarks_perineal_wound_care,
                is_done_signs_of_maternal: is_done_signs_of_maternal,
                remarks_signs_of_maternal: remarks_signs_of_maternal,
                is_done_breastfeeding_nutrition: is_done_breastfeeding_nutrition,
                remarks_breastfeeding_nutrition: remarks_breastfeeding_nutrition,
                is_done_family_planning: is_done_family_planning,
                remarks_family_planning: remarks_family_planning,
                is_done_provided_family_planning: is_done_provided_family_planning,
                remarks_provided_family_planning: remarks_provided_family_planning,
                is_done_referred_partner_physician: is_done_referred_partner_physician,
                remarks_referred_partner_physician: remarks_referred_partner_physician,
                is_done_schedule_next_postpartum: is_done_schedule_next_postpartum,
                remarks_schedule_next_postpartum: remarks_schedule_next_postpartum,

            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Updating changes. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                // console.log(response['message']);
                if (response['status']) {
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    // alert(response['message']);
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
                } else {
                    // toastr.success('Past Medical History updated');
                    // alert(response['message']);
                    Swal.fire(
                        'Failed to save!',
                        response['message'],
                        'error'
                    )
                }
            },

            error: () => {
                Swal.fire(
                    'Please contact your administrato!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
    }
</script>