<?php

if ($errors) {
    Yii::app()->user->setFlash('error', '<b>We found some errors in uploading!</b> <ul><li>' . implode('</li><li>', $errors) . '</li></ul>');
}

$mapUrl = $this->createUrl('map',array('transmit_no' => $transmittal->transmit_no));
$transmittalUrl = $this->createUrl('index');
#added by monmon : workaround for mapping
Yii::app()->getClientScript()->registerScript('transmittal/upload', <<<SCRIPT
$('#btnMap').click(function(e) {
    $.ajax({
        type: 'GET',
        dataType : 'JSON',
        url : '$mapUrl',
        beforeSend : function(){
            Alerts.loading({
                title: 'Please wait',
                'content': 'We are currently uploading your transmittal to the PHIC server'
            });
        },
        success : function(data){
            if(data === true){
                Alerts.warn({
                    title: 'Success!', 
                    content: 'Transmittal successfully mapped.', 
                    icon: 'fa-check-circle-o', 
                    iconColor: '#2DCC70',
                    callback: function(){
                        location.replace('$transmittalUrl');
                    }
                });
            }
            else if(data === false){
                    Alerts.error({ title: 'Error', content: 'Failed to save the map response. Try to map again. '});
            }
            else{
               Alerts.error({ title: 'Unexpected Error', content: data});
            }
        },

    });
});
SCRIPT
    , CClientScript::POS_READY);
#end monmon
?>

<?php


if (!$transmittal->ext->is_valid_xml) {

?>
    <div class="well well-error">
        <div class="jumbotron">
            <h2>Wait a minute!</h2>
            <p>
                Your transmittal XML doesn't seem to be in the correct format. Please
                fix it first before attempting to upload.
            </p>
        </div>
    </div>


<?php

}

// Uploaded and Mapped
elseif ($transmittal->ext->is_uploaded && $transmittal->ext->is_mapped) {

?>
<div class="well well-success">
    <div class="jumbotron">
        <h2>Success!</h2>
        <p>
            Your transmittal XML was already uploaded and mapped.
        </p>
    </div>
</div>
<?php

}
elseif (!$transmittal->ext->is_uploaded) {
?>

<?php
$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'transmittal-upload'
));

?>
    <input name="action" type="hidden" value="upload" />
    <?php if(!$errors) : ?>
        <div class="well well-success">
            <div class="jumbotron">
                    <h2><i class="fa fa-check-circle"></i> Congratulations!</h2>
                    <p>
                        Your transmittal has been validated by our system and now it is time to
                        upload the transmittal to the PHIC server.
                    </p>
                <?php
                    $this->widget('bootstrap.widgets.TbButton', array(
                        'icon' => 'fa fa-upload',
                        'label' =>'Let\'s upload!',
                        'type' => '',
                        'size' => '',
                        'buttonType' => 'submit'
                    ));
                ?>
            </div>
        </div>
    <?php else : ?>
        <div class="well well-error">
            <div class="jumbotron">
                    <h2><i class="fa fa-times-circle"></i> Sorry!</h2>
                    <p>
                        Some errors were encountered during upload. Please try again.
                    </p>
                <?php
                    $this->widget('bootstrap.widgets.TbButton', array(
                        'icon' => 'fa fa-upload',
                        'label' => 'Try again',
                        'type' => '',
                        'size' => '',
                        'buttonType' => 'submit'
                    ));
                ?>
            </div>
        </div>
    <?php endif ?>

<?php $this->endWidget(); ?>

<?php

} elseif (!$transmittal->ext->is_mapped && $transmittal->ext->is_uploaded) {

?>
<input name="action" type="hidden" value="map" />
<div class="well well-warning">
    <div class="jumbotron">
        <h2>Just one more step...</h2>
        <p>
            Your transmittal was successfully uploaded, however, the next
            operation which involves retrieving the claims mapping was
            somehow interrupted.

        </p>
        <p>
            <?php
                $mapUrl =  $this->createUrl('map', array('transmit_no' => $transmittal->transmit_no));
                $this->widget('bootstrap.widgets.TbButton', array(
                    'label' => $errors ? 'Try again' : 'Proceed!',
                    'type' => 'primary',
                    'id' => 'btnMap',
                    // 'url' => 'Yii::app()->createUrl("eclaims/transmittal/map", array("transmit_no" => $data["transmit_no"]))',
                    // 'url' => $this->createUrl('map', array('transmit_no' => $transmittal->transmit_no)),
                    // 'htmlOptions' => array(
                    //     'ajax' => array(
                    //         'type' => 'POST',
                    //         'url' => "js:$(this).attr('href')",
                    //         'beforeSend' => " function() {
                    //             Alerts.loading({ content: 'Please wait. We are currently mapping the transmittal to the PHIC web service!' }); } ",
                    //         'success' => "function(data) {
                    //             var successCallback = function() {
                    //                 location.reload();
                    //             };

                    //             if(data == 'true'){
                    //                 /* To many messages. Not sure if a must have feature. */
                    //                 setFlash('Sucess','Transmittal XML successfully uploaded and mapped. View PHIC Response for details.', 'success');
                    //                 Alerts.warn({
                    //                     title: 'Success!', 
                    //                     content: 'Transmittal successfully mapped.', 
                    //                     icon: 'fa-check-circle-o', 
                    //                     iconColor: '#2DCC70',
                    //                     callback: successCallback
                    //                 });
                    //             } else if(data == 'false'){
                    //                 Alerts.error({ title: 'Error', content: 'Failed to save the map response. Try to map again. '});
                    //             } else{
                    //                 Alerts.error({ title: 'Unexpected Error', content: data});
                    //             }
                    //         }",
                    //     ),
                    // ),
                ));
            ?>
        </p>
    </div>
</div>
<?php


}

?>

