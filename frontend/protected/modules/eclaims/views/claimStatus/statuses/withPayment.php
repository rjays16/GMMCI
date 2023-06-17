<?php


Yii::app()->clientScript->registerScript('voucherModal', "
    $('.voucherDetailsBtn').click(function(e){
        e.preventDefault();
        $.ajax({
            'url': $(this).attr('href'),
            'success' : function(data) {
                $('#voucherModal .modal-header h4').text('Voucher Details');
                $('#voucherModal .modal-body').html(data);
                $('#voucherModal').modal();
            }
        });
    })

    $('.voucherSummaryBtn').click(function(e){
            e.preventDefault();
            $.ajax({
                'url': $(this).attr('href'),
                'success' : function(data) {
                    $('#voucherModal .modal-header h4').text('Voucher Summary');
                    $('#voucherModal .modal-body').html(data);
                    $('#voucherModal').modal();
                }
            });
        })
", CClientScript::POS_READY);

?>

<h5>List of Payees</h5>


<?php

$payees = CJSON::decode($claim->status->withPayment->payees_json);
if (empty($payees)) {
    $payees = array();
}
$dp = new CArrayDataProvider($payees);
$columns = array(
    array(
        'name'=>'payeeName',
        'header'=>'Name of Payee',
    ),
    array(
        'header' => 'Voucher',
        'type' => 'raw',
        'value' => function($data, $row, $obj) {
            if ($data['voucherNo']) {
                return "<strong>{$data['voucherNo']}</strong>" .
                    ($data['voucherDate'] ? "<span>Date: {$data['voucherDate']}</span>" : "") .
                    "<br/><small>" .
                        CHtml::link(
                            "Details",
                            array(
                                'claimVoucher/details',
                                'voucher'=> $data->voucherNo,
                                'claim' => $claim->id
                            ),
                            array(
                                'class'=>'voucherDetailsBtn'
                            )
                        ) .
                        ' | ' .
                        CHtml::link(
                            "Summary",
                            array(
                                'claimVoucher/summary',
                                'voucher'=> $data->voucherNo,
                                'claim' => $claim->id
                            ),
                            array(
                                'class'=>'voucherDetailsBtn'
                            )
                        ) .
                    "</small>";
            } else {
                return "<i>None</i>";
            }
        }
    ),
    array(
        'header' => 'Check No',
        'name' => 'checkNo',
    ),
    array(
        'header' => 'Check Date',
        'name' => 'checkDate',
        'type' => 'date'
    ),
    array(
        'name'=>'claimAmount',
        'header'=>'Claim Amount',
        'htmlOptions'=>array(
            'style' => 'text-align:right'
        ),
    ),
);


$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'dataProvider' => $dp,
        'template' => "{items}",
        'type' => 'striped bordered condensed',
        'columns' => $columns,
    )
);


// Modal
$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'voucherModal',
        'htmlOptions' => array(
            'style' => 'width: 1150px; margin-left:-575px; margin-bottom: -150px; '
        ),
    )
);
?>
<div class="modal-header">
<a class="close" data-dismiss="modal">&times;</a>
<h4></h4>
</div>
<div class="modal-body" style="max-height:400px;"></div>
<div class="modal-footer">
<?php
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label' => 'Close',
            'url' => '#',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    );
?>
</div>
<?php $this->endWidget(); ?>