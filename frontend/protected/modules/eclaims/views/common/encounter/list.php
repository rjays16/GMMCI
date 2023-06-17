<?php
/**
 * Created by PhpStorm.
 * User: ger
 * Date: 8/4/2018
 * Time: 7:11 PM
 */
/*@property $service EncounterService*/

Yii::app()->getClientScript()->registerScript('searchEncounter', <<<JAVASCRIPT
$("#searchEncounterform").submit(function (event) {
  event.preventDefault();

  $.fn.yiiGridView.update("encounter-search-grid", {
    data: $(this).serialize(),
  });
});

$(".selectInsurance").live({
  click: function () {
    var url = $(this).data('url');
    var encounter = $(this).data('id');

    Alerts.loading({
      'title': 'Please wait...',
      content: 'Loading Patient Data'
    });

    setTimeout(function () {
      window.location.href = url + '&id=' + encounter;
    }, 2500);
  }
});


JAVASCRIPT
    , CClientScript::POS_READY);


$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id'          => 'caseno-modal',
        'fade'        => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            // 'data-dismiss' => false,
            'style'         => 'width:800px;margin-left:-400px;max-height:100%;',
        ),
    )
);

?>
<script>


</script>
<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h5><i class="fa fa-list"></i> List of Encounters - <?php echo $service->person->fullname; ?> </h5>
</div>

<div class="modal-body" style="max-height:100%;">
    <div class="row-fluid">
        <?php

        $columns = array(
            array(
                'header' => 'Encounter #',
                'type'   => 'raw',
                'value'  => function ($data) {
                    $value = $data->encounter_nr;

                    return $value;
                },
            ),

            array(
                'header'      => 'Type',
                'type'        => 'raw',
                'value'       => function ($data) {
                    $name = $data->type->name;

                    return $name;
                },
                'htmlOptions' => array('style' => 'width: 50px'),
            ),

            array(
                'header' => 'Confinement Date',
                'type'   => 'raw',
                'value'  => function ($data) {
                    /* @var $data EclaimsEncounter */
                    $discharge = $data->getDischargeDate();
                    $admission = date("M d, Y h:i A", strtotime($data->getAdmissionDt()));

                    if (empty($discharge)) {
                        $discharge = "Present";
                    } else {
                        $discharge = date("M d, Y h:i A", strtotime($data->getDischargeDate()));
                    }

                    return $admission . ' To ' . $discharge;
                },
            ),

            array(
                'header'            => Yii::t('ses', 'Action'),
                'headerHtmlOptions' => array('style' => 'text-align:center; width: 50px;'),
                'htmlOptions'       => array('style' => 'align:center; text-align: center;'),
                'value'             => function ($data) use ($service, $template) {
                    Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
                        'size'        => 'small',
                        'htmlOptions' => array(
                            'class' => 'col-md-12',
                            'style' => 'text-align:center;',
                        ),
                        'buttons'     => array(
                            'add'    => array(
                                'label'       => '',
                                'icon'        => 'icon-check',
                                'visible'     =>
                                    !$service->CheckEncounterExist($data->encounter_nr) && in_array('add', $template),
                                'linkOptions' => array('style' => 'text-align:left'),
                                'url'         => Yii::app()->createUrl("eclaims/member/manageInsuranceToBilling", array("pid" => $_GET["pid"], "encounter"
                                                                                                                              => $data["encounter_nr"], "action" => "add")),
                                'htmlOptions' => array(
                                    'class' => 'btn-success remove-to-tray',
                                    'title' => 'Add insurance to this encounter',
                                ),
                            ),
                            'remove' => array(
                                'label'       => '',
                                'icon'        => 'icon-trash',
                                'visible'     => $service->CheckEncounterExist($data->encounter_nr) && in_array('delete', $template),
                                'linkOptions' => array('style' => 'text-align:left'),
                                'url'         => '',
                                'htmlOptions' => array(
                                    'class'              => 'btn-danger remove-to-tray removeInsurance',
                                    'title'              => 'Remove insurance to this encounter',
                                    'data-dismiss'       => 'modal',
                                    'data-alert-message' => 'Remove this insurance from the billing record of the patient.',
                                    'data-toggle'        => 'modal',
                                    'data-target'        => '#riModal',
                                    'data-id'            => $data['encounter_nr'],
                                    'data-encounter'     => $data['encounter_nr'],
                                ),
                            ),
                            'select' => array(
                                'label'       => '',
                                'icon'        => 'fa fa-list',
                                'visible'     => in_array('select', $template),
                                'linkOptions' => array('style' => 'text-align:left'),
                                'url'         => '',
                                'htmlOptions' => array(
                                    'class'    => 'selectInsurance',
                                    'data-id'  => $data['encounter_nr'],
                                    'data-url' => Yii::app()->getController()->createUrl('eligibility/index'),
                                ),
                            ),
                        ),
                    ));
                },
            ),


        );

        $template = "{items}
      {summary}
      <div class='pull-right'>
        {pager}
      </div>
    ";


        /* @var $form TbActiveForm */
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'                   => 'searchEncounterform',
            'type'                 => 'horizontal',
            'enableAjaxValidation' => false,
            'htmlOptions'          => array(
                'class' => 'searchEncounter',
            ),
        ));

        ?>


        <div class="row-fluid">
            <div class="span12">

                <?php
                echo $form->textFieldGroup($model, 'encounter_nr', array(
                    'widgetOptions' => array(
                        'id' => 'person_search',
                    ),
                    'placeholder'   => 'Search Encounter Number',

                    'append'             => ' <i class="fa fa-search"></i>',
                    'wrapperHtmlOptions' => array(
                        'class' => 'col-md-7',
                    ),
                    'labelOptions'       => array(
                        'label' => '', 'class' => 'hidden',
                    ),
                ));

                $this->endWidget();
                ?>
            </div>
        </div>


        <div class="row-fluid">
            <div class="span12">
                <?php
                $this->widget(
                    'bootstrap.widgets.TbGridView',
                    array(
                        'id'           => 'encounter-search-grid',
                        'type'         => 'striped bordered hover',
                        'dataProvider' => $service->displayEncounters(),
                        'columns'      => $columns,
                        'template'     => $template,
                    )
                );
                ?>
            </div>
        </div>


    </div>
</div>


<div class="modal-footer">
    <?php $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label'       => 'Close',
            'url'         => '#',
            'icon'        => 'fa fa-close',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    ); ?>
</div>
<?php $this->endWidget(); ?>


<script type="text/javascript">
    $(".removeInsurance").each(function (index) {
        $(this).on("click", function () {
            $("#get_enc").val($(this).data('id'));
        });
    });
</script>

