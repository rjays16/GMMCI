<?php

/* @var $this Controllee */
$this->breadcrumbs[] = $transmittal->transmit_no;

?>

<legend><h5>Information</h5></legend>

<div class="row-fluid">
    <div class="span6">
        <?php
            $this->widget('bootstrap.widgets.TbDetailView',array(
                'data' => $transmittal,
                'type'=>'striped condensed bordered',
                'itemTemplate'=>"<tr class=\"{class}\"><th style=\"width:30%\">{label}</th><td>{value}</td></tr>\n",
                'attributes'=>array(
                    array('name' => 'transmit_no'),
                    array('name' => 'transmit_dte', 'type' => 'datetime'),
                    array('name' => 'Status'),
                ),
            ));
        ?>
    </div>
</div>

<legend><h5>Claims</h5></legend>

<?php

$details = array();
foreach ($transmittal->details as $detail) {
    $details[] = $detail->toArray();
}

$this->renderPartial('transmittalDetail', array('details' => $details));