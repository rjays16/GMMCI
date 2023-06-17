<?php
/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 *
 */

$this->setPageTitle('Get Voucher Details');
Yii::import('bootstrap.widgets.TbButton');

if (!$voucher) {
	  Yii::app()->user->setFlash('warning', '<strong></strong><i class="fa fa-exclamation-circle"></i> Please enter a voucher number to view its details and summary');
}

Yii::app()->clientScript->registerScript('voucherModal', "
	$('#getdetails-form').submit(function() {
        Alerts.loading({ content: 'Contacting PHIC web service. Please wait...' });
	});

	$('#voucherDetailsBtn').click(function(e){
		e.preventDefault();
		$.ajax({
			'type': 'POST',
			'url': $(this).data('url'),
			'success' : function(data) {
				$('#voucherModal .modal-header h4').html('Voucher Details');
				$('#voucherModal .modal-body p').html(data);
				$('#voucherModal').modal();
			}
		});
	})

	$('#voucherSummaryBtn').click(function(e){
			e.preventDefault();
			$.ajax({
				'type': 'POST',
				'url': $(this).data('url'),
				'success' : function(data) {
					$('#voucherModal .modal-header h4').html('Voucher Summary');
					$('#voucherModal .modal-body p').html(data);
					$('#voucherModal').modal();
				}
			});
		})
", CClientScript::POS_READY);
?>

<div class="row-fluid">
    <div class="span12">
    <?php
        $activeForm = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'getdetails-form',
            'type' => 'horizontal',
            'enableAjaxValidation' => false,
        ));
    ?>
    <?php
        $box = $this->beginWidget(
            'application.widgets.SegBox',
            array(
                'title' => 'Get Voucher Details',
                'headerIcon' => 'icon-th-list',
                'htmlOptions' => array('class' => ''),
    			'headerButtons' => array(
    				array(
    					'class' => 'bootstrap.widgets.TbButtonGroup',
    					'buttons' => array(
    						array(
    							'label' => 'view voucher summary',
    							'icon' => 'fa fa-chart',
    							'type' => 'inverse',
    							'disabled'=> trim( empty($model->voucherNo) ) || (!$status),
    							'htmlOptions' => array(
                                    'id'=>'voucherSummaryBtn',
                                    'data-url' => Yii::app()->createUrl('eclaims/claimVoucher/viewVoucherInfo', array('data-voucher-no'=>$model->voucherNo, 'is_summary'=>1)),
                                ),
                                'buttonType' => TbButton::BUTTON_BUTTON,
    						)
    					)
    				),
    				array(
    					'class' => 'bootstrap.widgets.TbButtonGroup',
    					'buttons' => array(
    						array(
    							'label' => 'view voucher details',
    							'icon' => 'fa fa-list',
    							'type' => 'inverse',
    							'disabled'=> trim( empty($model->voucherNo) ) || (!$status),
    							'htmlOptions' => array(
                                    'id'=>'voucherDetailsBtn',
                                    'data-url' => Yii::app()->createUrl('eclaims/claimVoucher/viewVoucherInfo', array('data-voucher-no'=>$model->voucherNo, 'is_summary'=>0)),
                                ),
                                'buttonType' => TbButton::BUTTON_BUTTON,
    						),
    					),
    				),
    			),

                'footer' => CHtml::tag('tag',
                    array(
                        'class' => ''
                    ),
                    $this->widget('bootstrap.widgets.TbButton',
                        array(
                            'buttonType' => 'submit',
                            'type' => 'primary',
                            'icon' => 'fa fa-check-circle',
                            'loadingText' => 'Getting Voucher Details ...',
                            'label' => 'Get Voucher Details',
                            'htmlOptions' => array(
                                'class' => 'getvoucherBtn',
                            )
                        ),
                        true
                    )
                ),

                'htmlFooterOptions' => array(
                    'class' => 'bootstrap-table-widget'
                )
            )
        );
    ?>
        <?php echo $activeForm->errorSummary($model) ?>
        <?php echo $activeForm->textFieldRow($model, 'voucherNo', array(
                'class' => 'input-xlarge',
                'placeholder' => ''
            ));
        ?>
    <?php $this->endWidget(); ?>
    <?php $this->endWidget(); ?>
    </div>
</div>

<?php
$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'voucherModal',
        'htmlOptions' => array(
            'style' => 'width: 1100px; margin-left:-550px; margin-bottom: -150px;'
        ),
    )
);
?>
<div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4></h4>
    </div>
    <div class="modal-body" style="max-height:400px">
        <p></p>
    </div>
    <div class="modal-footer">
        <?php
            $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'label' => 'Close',
                    'url' => '#',
                    'type' => 'inverse',
                    'htmlOptions' => array('data-dismiss' => 'modal'),
                )
            );
        ?>
    </div>
<?php $this->endWidget(); ?>