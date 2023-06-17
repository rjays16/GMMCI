<?php
# created by JOY @ 02-21-2018
/**
 * @var CDataProvider $dataProvider
 */
Yii::import('eclaims.models.EclaimsEncounterInsurance');
Yii::import('bootstrap.widgets.MyButtonColumn');
Yii::import('bootstrap.widgets.ButtonColumn');


$columns = array(
    array(
        'header' => 'Encounter #',
        'type' => 'raw',
        'value' => function($data) {
            $value = $data['encounter_nr'];
            return $value;
        },
        'htmlOptions' => array( 'style' => 'width: 60px', 'id' => 'enc_nr'),
    ),
    array(
        'header' => 'Encounter Type',
        'type' => 'raw',
        'value' => function($data) {
            $name = $data['enc_type'];
            return $name;
        },
        'htmlOptions' => array( 'style' => 'width: 50px' ),
    ),
     array(
        'header' => 'Confinement Date',
        'type' => 'raw',
        'value' => function($data) {
            $admit_dt = date("M d, Y h:i A", strtotime($data['admission_dt']));
            if ($data['discharge_time'] == "") {
                $discharge_dt = 'present';
            }else{
                $discharge_dt = date("M d, Y h:i A", strtotime($data['discharge_time']));
            }
             $hiddenField = CHtml::hiddenField('encounter_nr'.$data['encounter_nr'], $data['encounter_nr'] , array(
                'data-id' => 'encounter_nr'
            ));

            return $admit_dt ." to ". $discharge_dt;
        },
        'htmlOptions' => array( 'style' => 'width: 50px' ),
    ),
     /* Mod by jeff 02-24-18 for adding and remove of encounters dynamic selection. */
     array(
        'header'=>Yii::t('ses', 'Action'),
        'headerHtmlOptions'=> array('style' => 'text-align:center; width: 50px;'),
        'htmlOptions' =>array('style' => 'align:center; text-align: center;'),
        'value' => function($data) {
            Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
                'size' => 'small',
                'htmlOptions'=> array(
                    'class'=>'col-md-12',
                    'style' => 'text-align:center;'
                ),
                'buttons' => array(
                    'add' => array(
                        'label' => '',
                        'icon' => 'icon-check',
                        'visible' => !EclaimsEncounterInsurance::model()->CheckEncounterExist($data['encounter_nr']),
                        'linkOptions' => array('style' => 'text-align:left'),
                        'url' =>  Yii::app()->createUrl("eclaims/member/manageInsuranceToBilling", array("pid" => $_GET["pid"], "encounter"=> $data["encounter_nr"],"action"=>"add")),
                        'htmlOptions' => array(
                                        'class' => 'btn-success remove-to-tray', 
                                        'title' => 'Add insurance to this encounter',
                                            )
                    ),
                   'remove' => array(
                        'label' => '',
                        'icon' => 'icon-trash',
                        'visible' => EclaimsEncounterInsurance::model()->CheckEncounterExist($data['encounter_nr']),
                        'linkOptions' => array('style' => 'text-align:left'),
                        'url' =>  '',
                        'htmlOptions' => array(
                            'class' => 'btn-danger remove-to-tray removeInsurance',
                            'title' => 'Remove insurance to this encounter', 
                            'data-dismiss'       => 'modal',
                            'data-alert-message' => 'Remove this insurance from the billing record of the patient.',
                            'data-toggle'        => 'modal',
                            'data-target'        => '#riModal',
                            'data-id'            => $data['encounter_nr'],  
                            'data-encounter'     => $data['encounter_nr']
                        )
                    ),             
                )
            ));
        },
    ),
);

$template = "{items}
<div class='pull-right'>
    {summary}
    <div class='span12'>{pager}
    </div>
</div>
";
?>

<div id="search-results-container" class="row-fluid">
    <div class="span12">
        <?php
            $this->widget(
                'bootstrap.widgets.TbGridView',
                array(
                    'id' => 'item-search-grid',
                    // 'fixedHeader' => true,
                    'type' => 'striped bordered hover',
                    'ajaxUrl' => array('searchGrid'),
                    'dataProvider' => $dataProvider,
                    'template' => $template,
                    'columns' => $columns,
                )
            );
        ?>
    </div>
</div>

<!-- Added by jeff 02-04-18 -->
<script type="text/javascript">
  $( ".removeInsurance" ).each(function(index) {
    $(this).on("click", function(){
        $("#get_enc").val($(this).data('id'));
    });
});
</script>
