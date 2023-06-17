<?php
/* @var $this Controller */
$cs = Yii::app()->getClientScript();
$cs->registerScript('eclaims-main', <<<SCRIPT

$('#eclaims-config-form-submit').click(function(e) {
    e.preventDefault();
    var form = $('#eclaims-config-form');
    var data=form.serialize();
    $.ajax({
        type: 'POST',
        url: form.prop('action'),
        data:data,
        beforeSend: function() {
            $(this).button('loading');
            Alerts.loading({
                'title': 'Please wait',
                content: 'Saving service configuration...'
            });
        },
        success: function(data){
            var modal = $('#eclaims-config-modal');
            var content = modal.find('.modal-body')
            content.html(data);
        },
        error: function() { // if error occured
            alert("Error occured.please try again");
        },
        complete: function() {
            Alerts.close();
            $(this).button('reset');
        },
        dataType:'html'
    })
});
SCRIPT
    , CClientScript::POS_READY);
?>

<?php $this->beginContent('//layouts/main'); ?>


<div id="content">
    <?php echo $content; ?>
</div><!-- content -->


<!-- Generic modal -->
<?php

$configUrl = CJSON::encode(Yii::app()->createUrl('eclaims/config/update'));
$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'eclaims-config-modal',
        'fade' => false,
        'events' => array(
            'show' => <<<SCRIPT
js:function() {
    var modal = $(this);
    var content = modal.find('.modal-body')

    content.html('<h1><i class="fa fa-spin fa-refresh color-lightGray"></i></h1>');
    $.ajax({
        url: {$configUrl},
    }).done(function(data) {
        content.html(data);
    });
}
SCRIPT
        ),
        'htmlOptions' => array(
            'class' => ''
        )
    )
);

?>
    <input type="" value="<?php echo Yii::app()->session['sess_login_userid']; ?>" name="" id="user_name">
  
    <div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5>Service Configuration</h5>
    </div>

    <div class="modal-body"></div>

    <div class="modal-footer">
        <?php
            $this->widget('bootstrap.widgets.TbButton',
                array(
                    'id' => 'eclaims-config-form-submit',
                    'buttonType' => 'button',
                    'type' => 'success',
                    'icon' => 'fa fa-save',
                    'loadingText' => 'Saving ...',
                    'label' => 'Update',
                    'htmlOptions' => array(
                        'class' => 'getpinButton',
                    )
                )
            );

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

<?php $this->endContent(); ?>