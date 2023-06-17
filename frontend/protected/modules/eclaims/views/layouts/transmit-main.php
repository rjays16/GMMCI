<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php $this->beginContent('eclaims.views.layouts.ec-main'); ?>


<?php

/* @var $this Controller */

$this->setPageTitle('View Transmittal');

?>

<div class="row-fluid">
    <div class="span12">
        <?php
            $box = $this->beginWidget(
                'application.widgets.SegBox',
                array(
                    'title' => 'Transmittal',
                    'headerIcon' => 'fa fa-send-o',
                )
            );

        ?>


<?php

$this->widget('bootstrap.widgets.TbAlert', array(
        'block' => true,
        'fade' => true,
        'closeText' => '&times;', // false equals no close link
        'events' => array(),
        'htmlOptions' => array(),
        'userComponentId' => 'user',
        'alerts' => array( // configurations per alert type
            // success, info, warning, error or danger
            'success' => array('closeText' => '&times;'),
            'info', // you don't need to specify full config
            'warning' => array('block' => false, 'closeText' => false),
            'error' => array('block' => false, 'closeText' => false)
        ),
));

?>

            <?php


                $tabs = array(
                    'details' => array(
                        'label' => 'Details'
                    ),
                    'attachments' => array(
                        'label' => '<i class="fa "></i> Attachments',
                    ),
                    'generate' => array(
                        'label' => '<i class="fa "></i> Generate e-Claim',
                    ),
                    'upload' => array(
                        'label' => '<i class="fa "></i> Upload',
                    ),
                    'done' => array(
                        'label' => '<i class="fa "></i> Done',
                    )
                );

                $normalizedTabs = array();
                foreach ($tabs as $id => $tab) {
                    $active = ($id == $this->action->id);
                    $normalizedTabs[] = array(
                        'label' => $tab['label'],
                        'url' => $active ? '#' : $this->createUrl($id, array('id' => $_GET['id'])),
                        'active' => $active,
                        'content' => $active ? $content : null,
                    );
                }

                $this->widget('bootstrap.widgets.TbTabs', array(
                    'encodeLabel' => false,
                    'tabs' => $normalizedTabs
                ));
            ?>

        <?php $this->endWidget(); /* Box */ ?>
    </div>
</div>



<?php $this->endContent(); ?>