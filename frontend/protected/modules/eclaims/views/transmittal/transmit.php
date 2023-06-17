<?php

/* @var $this Controller */

$this->setPageTitle('View Transmittal');

$checkIcon = '<i class="fa fa-check" style="color:#080"></i>';
$warnIcon = '<i class="fa fa-warning" style="color:#FFD21F"></i>';
?>

<div class="row-fluid">
    <div class="span12">

            <?php


                $tabs = array(
                    'details' => array(
                        'label' => 'Details'
                    ),
                    'attachments' => array(
                        'label' => 'Step 1. Attachments ' . ($transmittal->isValidAttachments() ? $checkIcon : $warnIcon),
                    ),
                    'generate' => array(
                        'label' => 'Step 2. Generate e-Claim ' . ($transmittal->ext->is_valid_xml ? $checkIcon : $warnIcon),
                    ),
                    'upload' => array(
                        'label' => 'Step 3. Upload ' . (($transmittal->ext->is_uploaded && $transmittal->ext->is_mapped) ? $checkIcon : $warnIcon),
                    )
                );

                $normalizedTabs = array();
                foreach ($tabs as $id => $tab) {
                    $active = ($id == $this->action->id);
                    $normalizedTabs[] = array(
                        'label' => $tab['label'],
                        'url' => $active ? '#' : $this->createUrl($id, array('id' => $_GET['id'])),
                        'active' => $active,
                        'content' => $active ?
                            $this->renderPartial('transmit/'.$this->action->id, array(
                                'transmittal' => $transmittal,
                                'errors' => @$errors
                            ), true) :
                            null,
                    );
                }

                $this->widget('bootstrap.widgets.TbTabs', array(
                    'encodeLabel' => false,
                    'tabs' => $normalizedTabs
                ));
            ?>

    </div>
</div>