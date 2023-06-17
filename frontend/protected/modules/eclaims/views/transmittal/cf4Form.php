<?php
$this->setPageTitle('CF4 Generator');
?>


<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <h3><?php echo $person['person']->name_last . ', ' . $person['person']->name_first . ' ' . $person['person']->name_middle ?></h3>
        </div>
        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'PATIENT/S CLINICAL RECORDS',
                'headerIcon'    => 'icon-user',
                'htmlOptions'   => array('class' => 'header-color'),
                'content'     => $this->renderPartial(
                    'generator/personalInfo/info',
                    array(
                        'model' => $person,
                        'encounter' => $encounter,
                    ),
                    true
                ),
                'headerButtons' => array(
                    array(
                        'class'       => 'bootstrap.widgets.TbButton',
                        'buttonType'  => TbButton::BUTTON_BUTTON,
                        'buttonType' => TbButton::BUTTON_LINK,
                        'icon'        => 'fa fa-print',
                        'label'       => 'Print CF4',
                        'htmlOptions' => array(
                            'id'    => 'print_cf4',
                            'data-id' => Yii::app()->createUrl('eclaims/generator/printCf4', array(
                                'encounter_nr' => $encounter->encounter_nr,
                            )),
                        ),
                    ),
                ),

            )
        );
        ?>
        <?php $this->endWidget(); /* box */ ?>

        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Pertinent Signs & Symptoms',
                'headerIcon'    => 'icon-heart',
                'htmlOptions'   => array('class' => ''),
                'content'     => $this->renderPartial(
                    'generator/signsAndSymptoms/index',
                    array(
                        'model' => $person,
                        'encounter' => $encounter,
                    ),
                    true
                ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>

        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Physical Examinations',
                'headerIcon'    => 'icon-file',
                'htmlOptions'   => array('class' => ''),
                'content'     => $this->renderPartial(
                    'generator/physicalExam/index',
                    array(
                        'model' => $person,
                        'encounter' => $encounter,
                    ),
                    true
                ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>


        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Course in the Ward',
                'headerIcon'    => 'icon-list',
                'htmlOptions'   => array('class' => ''),
                'content'     => $this->renderPartial(
                    'generator/courseWard/index',
                    array(
                        'model' => $person,
                        'encounter' => $encounter,
                    ),
                    true
                ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>


        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Drug/Medicine (Outside)',
                'headerIcon'    => 'fa fa-user-md',
                'htmlOptions'   => array('class' => ''),
                'content'     => $this->renderPartial(
                    'generator/medicine/index',
                    array(
                        'model' => $person,
                        'encounter' => $encounter,
                    ),
                    true
                ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>


        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Prenatal Consultation',
                'headerIcon'    => 'fa fa-user-md',
                'htmlOptions'   => array('class' => ''),
                // 'content'     => $this->renderPartial(
                //     'generator/medicine/index',
                //     array(
                //         'model' => $person,
                //         'encounter' => $encounter,
                //     ),
                //     true
                // ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>

        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Delivery Outcome',
                'headerIcon'    => 'fa fa-user-md',
                'htmlOptions'   => array('class' => ''),
                // 'content'     => $this->renderPartial(
                //     'generator/medicine/index',
                //     array(
                //         'model' => $person,
                //         'encounter' => $encounter,
                //     ),
                //     true
                // ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>

        <?php
        Yii::import('bootstrap.widgets.TbButton');
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title'         => 'Postpartum Care',
                'headerIcon'    => 'fa fa-user-md',
                'htmlOptions'   => array('class' => ''),
                // 'content'     => $this->renderPartial(
                //     'generator/medicine/index',
                //     array(
                //         'model' => $person,
                //         'encounter' => $encounter,
                //     ),
                //     true
                // ),
            )
        );
        ?>

        <?php $this->endWidget(); /* box */ ?>


    </div>
</div>



<script>
    $("#print_cf4").on("click", function() {
        const url = $("#print_cf4").data('id');
        window.open(url, '_blank', 'top=50,left=200,width=850,height=500,scrollbars=yes');

    });
</script>

<style type="text/css">
    .header-color {
        background: green;
    }
</style>