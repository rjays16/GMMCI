

<div class="row-fluid">
    <div class="span12">

            <?php


            $tabs = array(

                    array(
                        'label' => 'General Survey',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                        'active' => true,
                    ),
                    array(
                        'label' => 'HEENT',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ),  
                    array(
                        'label' => 'SKIN/EXTREMITIES',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ), 
                    array(
                        'label' => 'Chest/Lungs',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ),
                    array(
                        'label' => ' CVS',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ), 
                    array(
                        'label' => 'ABDOMEN',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                        'visible' => $model['person']->sex == 'f' ? true : false,
                    ), 
                    array(
                        'label' => 'NEURO-EXAM',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ), 
                    array(
                        'label' => 'RECTAL',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ),                   
                    array(
                        'label' => 'GU (IE)',
                        'content'     => $this->renderPartial(
                            'generator/physicalExam/view/_presentIllness', array(
                            'encounter' => $encounter,
                            ), true
                        ),
                    ),   
                    
            );

                $this->widget('bootstrap.widgets.TbTabs', array(
                    'encodeLabel' => false,
                    'tabs' => $tabs
                ));

            ?>

    </div>
</div>