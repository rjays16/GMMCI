<?php //$box = $this->beginWidget(
//    'bootstrap.widgets.TbBox',
//    array(
//        'title' => 'Request Details',
//        'headerIcon' => 'icon-th-list',
//        'htmlOptions' => array('class' => 'bootstrap-widget-table')
//    )
//);?>
<?php
//$this->widget(
//    'bootstrap.widgets.TbDetailView',
//    array(
//        'data'=>$model,
//        'attributes' => array(
//            'or_refno',
//            array(
//                'name'=>'dept_nr',
//                'value'=>CHtml::encode($model->department->name_formal)
//            ),
//            array(
//                'name'=>'trans_type',
//                'value'=>CHtml::encode($model->typeText)
//            ),
//            array(
//                'name'=>'is_urgent',
//                'value'=>CHtml::encode($model->urgentText)
//            ),
//            array(
//                'name'=>'date_requested',
//                'value'=>CHtml::encode($model->dateRequestedText)
//            ),
//            array(
//                'name'=>'patient_name',
//                'value'=>CHtml::encode($model->encounter->person->fullName)
//            ),
//            array(
//                'name'=>'patient_age',
//                'value'=>CHtml::encode($model->encounter->person->age)
//            ),
//            array(
//                'name'=>'patient_address',
//                'value'=>CHtml::encode($model->encounter->person->address)
//            ),
//            array(
//                'label'=>'Date and Time of Operation',
//                'value'=>CHtml::encode($model->orPreOpDetail->operationDateText)
//            )
//        ),
//    )
//);
//?>
<?php //$this->endWidget(); ?>

<?php $collapse = $this->beginWidget('bootstrap.widgets.TbCollapse'); ?>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse"
               data-parent="#accordion2" href="#collapse-detail">
                Request Detail (click to show or hide)
            </a>
        </div>
        <div id="collapse-detail" class="accordion-body collapse">
            <div class="accordion-inner">

                <?php
                $this->widget(
                    'bootstrap.widgets.TbDetailView',
                    array(
                        'data'=>$model,
                        'attributes' => array(
                            'or_refno',
                            array(
                                'name'=>'dept_nr',
                                'value'=>CHtml::encode($model->department->name_formal)
                            ),
                            array(
                                'name'=>'trans_type',
                                'value'=>CHtml::encode($model->typeText)
                            ),
                            array(
                                'name'=>'is_urgent',
                                'value'=>CHtml::encode($model->urgentText)
                            ),
                            array(
                                'name'=>'date_requested',
                                'value'=>CHtml::encode($model->dateRequestedText)
                            ),
                            array(
                                'name'=>'patient_name',
                                'value'=>CHtml::encode($model->encounter->person->fullName)
                            ),
                            array(
                                'name'=>'patient_age',
                                'value'=>CHtml::encode($model->encounter->person->age)
                            ),
                            array(
                                'name'=>'patient_address',
                                'value'=>CHtml::encode($model->encounter->person->address)
                            ),
                            array(
                                'label'=>'Date and Time of Operation',
                                'value'=>CHtml::encode($model->orPreOpDetail->operationDateText)
                            ),
                            array(
                                'label'=>'Remarks',
                                'value'=>CHtml::encode($model->remarks)
                            ),
                            array(
                                'label'=>'Ward',
                                'value'=>CHtml::encode($model->encounter->curWardName)
                            )
                        ),
                    )
                );
                ?>
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>
<br />