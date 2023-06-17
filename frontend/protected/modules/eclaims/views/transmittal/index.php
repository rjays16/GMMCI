<?php
$errorMessages = array();
$baseUrl = Yii::app()->request->baseUrl;
$this->setPageTitle('Transmittal');

$cs = Yii::app()->clientScript;

$cs = Yii::app()->getClientScript();
$cs->registerScript('_transmittal_index-css', <<<JAVASCRIPT
    function reinstallDatePicker(id, data) {
        $('#transmit_dte').datepicker(  {'format':'yyyy-mm-dd'} );
    }

    var view = {
        getAllMapBtn: function() { return $('.mapBtn'); },
        getAllDetailBtn: function() { return $('.detailBtn'); },
        getAllResponseBtn: function() { return $('.responseBtn'); },
        /**
         * All ajax properties are "Optional"
         * Default: 
         * url: object.attr('href')
         * type: GET
         * dataType: JSON
         */
        doAjax: function(object, options) {
            $.ajax({
                url: options['url'] || object.attr('href'),
                type: options.type || 'GET',
                dataType: options.dataType || 'JSON',
                beforeSend: function() {
                    if(options.preBeforeSend || false) {
                        options.preBeforeSend();
                    }
                    if(options.beforeSend || false) {
                        options.beforeSend();
                    } else {
                        $("#view .modal-body").html('<h1 class="color-lightGray"><i class="fa fa-spin fa-refresh"></i> Loading...</h1>');
                    }
                },
                success: function(data) {
                    if(options.success || false) {
                        options.success(data);
                    }
                }
            });
        },
    };

    var events = {
        onViewDetails: function(object) {
            view.doAjax(object, {
                preBeforeSend: function() {
                    // $("#modal-open-transmittal").show();
                    // $("#view .modal-header h4").html("Transmittal Details");
                    // $("#view").modal();
                      Alerts.loading({
                        'title': 'Please wait',
                        content: 'Opening Transmittal Details Dialog Frame'
                    });
                },
                success: function(data) {
                    if(data["content"] || false) {
                        $("#view .modal-body").html(data["content"]);
                        
                        if(data["urlOpenTransmittal"] || false) {
                            // $("#modal-open-transmittal").attr("href", data["urlOpenTransmittal"]);
                            viewDetailstrn(data["transmittalNumber"]);
                            Alerts.close();
                        }
                    } else {
                        Alerts.error({content: "Somthing went wrong when viewing the transmittal details"});
                    }
                },
                complete: function() {
                       Alerts.close();
                },
            });
        },
        onViewResponse: function(object) {
            view.doAjax(object, {
                preBeforeSend: function() {
                     Alerts.loading({
                        'title': 'Please wait',
                        content: 'Opening PHIC Response Details Dialog Frame'
                    });
                },
                success: function(data) {
                    if(data["content"] || false) {
                         Alerts.close();
                        phicResponse(data["urlOpenTransmittalResponse"]);
                    } else {
                        Alerts.error({content: "Somthing went wrong when viewing the PHIC response details"});
                    }
                },
                complete: function() {
                       Alerts.close();
                },
            });
        },
        onMap: function(object) {
            view.doAjax(object, {
                beforeSend: function() {
                    Alerts.loading({ content: 'Please wait. We are currently mapping the transmittal to the PHIC web service!' }); 
                },
                success: function(data) {
                    if(data == 'true'){
                        /* To many messages. Not sure if a must have feature. */
                        // setFlash('Sucess','Transmittal XML successfully uploaded and mapped. View PHIC Response for details.', 'success');
                        Alerts.warn({ title: 'Success!', content: 'Transmittal successfully mapped.', icon: 'fa-check-circle-o', iconColor: '#2DCC70' });
                    } else if(data == 'false'){
                        Alerts.error({ title: 'Error', content: 'Failed to save the map response. Try to map again. '});
                    } else{
                        Alerts.error({ title: 'Unexpected Error', content: data});
                    }
                    /* Why? */
                    // location.reload();

                    $('#transmittal-grid').yiiGridView('update');
                }
            });
        }
    };

    var listeners = {
        viewListener: function() {
            events.onViewDetails($(this));
            return false;
        },
        viewResponseListener: function() {
            events.onViewResponse($(this));
            return false;
        },
        mapListener: function() {
            events.onMap($(this));
            return false;
        }
    };
    
    var app = {
        init: function() {
            view.getAllDetailBtn().off('click').on('click', listeners.viewListener);
            view.getAllResponseBtn().off('click').on('click', listeners.viewResponseListener);
            view.getAllMapBtn().off('click').on('click', listeners.mapListener);
        }
    };
    app.init();
JAVASCRIPT
);


$cs2 = Yii::app()->clientScript;
$js = <<<JAVASCRIPT

function phicResponse(transmit_noURL) {
        jQueryDialogSearch = jQuery('#phic-response-dialog')
                .dialog({
                    modal: true,
                    title: 'PHIC Response Details',
                    width: '80%',
                    height: 400,
                    position: 'center',
                    open: function(){
                        jQuery('#phic-response-dialog-frame').on('load', function(){
                            jQuery('#loadingMessage3').css('display', 'none');
                        });
                        jQuery('#phic-response-dialog-frame').attr('src',"index.php?r=eclaims/transmittal/responseDetails&transmit_no="+transmit_noURL);
                        jQuery('.ui-dialog .ui-dialog-content').css({
                            overflow : 'hidden'
                        });

                    }
                });

        return false;
}
function viewDetailstrn(transmit_noURL) {
        jQueryDialogSearch = jQuery('#transmittal-detail-dialog')
                .dialog({
                    modal: true,
                    title: 'Transmittal Details',
                    width: '80%',
                    height: 400,
                    position: 'center',
                    open: function(){
                        jQuery('#transmittal-details-dialog-frame').on('load', function(){
                            jQuery('#loadingMessage4').css('display', 'none');
                        });
                        jQuery('#transmittal-details-dialog-frame').attr('src',"index.php?r=eclaims/transmittal/viewDetails&transmit_no="+transmit_noURL);
                        jQuery('.ui-dialog .ui-dialog-content').css({
                            overflow : 'hidden'
                        });

                    },
                    buttons: {
                        "Open-Transmittal": function () {
                            var url_transmittal = window.location.href = "index.php?r=eclaims/transmittal/details&id="+transmit_noURL;
                            url_transmittal.focus();
                        },
                        "Close": function () {
                            jQuery(this).dialog("close");
                        }
                    }
                });

        return false;
}

JAVASCRIPT;
$cs2->registerScript('js', $js, CClientScript::POS_HEAD);
$cs2->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs2->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);
// /*END By MArk April 23, 2017*/


$this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'List of Transmittals',
    'headerIcon' => 'fa fa-list'));
?>

<div class="row-fluid">
    <?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'transmittal-grid',
        'type' => 'striped condensed bordered hover',
        'summaryText' => 'Displaying {start} - {end} of {count} Transmittals',
        'dataProvider' => $transmittal->search(),
        'filter' => $transmittal,
        'afterAjaxUpdate' => 'js:function(id, data) {
            app.init();
            reinstallDatePicker(id, data);
        }',
        'columns' => array(
            array(
                'name' => 'transmit_no',
                'header' => 'Transmittal #',
                'filter' => CHtml::activeTextField($transmittal, 'transmit_no', array(
                    'placeholder' => 'Search by Transmittal No.'
                )),
                'headerHtmlOptions' => array('style' => 'width:15%')),
            array(
                'name' => 'transmit_dte',
                'header' => 'Date/time Created',
                'type' => 'datetime',
                'filter' => $this->widget('bootstrap.widgets.TbDatePicker', array(
                    'model' => $transmittal,
                    'attribute' => 'transmit_dte',
                    'options' => array(
                        'format' => 'yyyy-mm-dd',
                    ),
                    'htmlOptions' => array(
                        'placeholder' => 'Search by Transmittal Date',
                        'id' => 'transmit_dte',
                    ),), true),
                'headerHtmlOptions' => array('style' => 'width:15%')),
            array(
                'name' => 'Status',
                'header' => 'Status',
                'filter' => false,
                'headerHtmlOptions' => array('style' => 'width:10%')),
            array(
                'name' => 'detailsCount',
                'filter' => false,
                'headerHtmlOptions' => array('style' => 'width:7%'),
            ),
            array(
                'name' => 'Details',
                'header' => 'Details',
                'filter' => false,
            ),
            array('header' => Yii::t('ses', 'Actions'),
                'class' => 'bootstrap.widgets.TbButtonGroupColumn',
                'template' => '{detail} {edit} {map} {response}',
                'buttons' => array(
                    'edit' => array(
                        'label' => 'Open transmittal',
                        'icon' => 'fa fa-folder-open',
                        'visible' => '$data->ext->is_uploaded == 0',
                        'url' => 'Yii::app()->getController()->createUrl("details", array("id" => $data["transmit_no"]))',
                    ),
                    /**
                     * This can be accessed if the transmittal has been successfully uploaded but still not mapped.
                     * Accesses the claimsMap web service and returns data
                     * If successful, the claimsMap can be viewed via responseDetails button.
                     */
                    'map' => array(
                        'label' => 'Map Transmittal',
                        'icon' => 'fa fa-sitemap',
                        'visible' => '$data->ext->is_uploaded == 1 && $data->ext->is_mapped == 0',
                        'url' => 'Yii::app()->getController()->createUrl("map", array("transmit_no" => $data["transmit_no"]))',
                        'options' => array(
                            'class' => 'mapBtn',
                        ),
                    ),
                    'detail' => array(
                        'label' => 'View details',
                        'icon' => 'fa fa-list-alt',
                        'url' => 'Yii::app()->getController()->createUrl("viewDetails", array("transmit_no" => $data["transmit_no"]))',
                        'options' => array(
                            'class' => 'detailBtn',
                        ),
                    ),
                    /**
                     * This can be accessed if the transmittal has been successfully uploaded and/or mapped.
                     * Views the upload and map response details via a modal.
                     */
                    'response' => array(

                        'label' => 'PHIC Response',
                        'icon' => 'fa fa-check',
                        'visible' => '$data->ext->is_uploaded == 1',
                        'url' => 'Yii::app()->getController()->createUrl("responseDetails", array("transmit_no" => $data["transmit_no"]))',
                        'options' => array(
                            'class' => 'responseBtn',
                            // 'onclick' =>'js:phicResponse("$data["transmit_no"]);',
                        ),
                    ),
                ),
            ),
        ),
    ));
    ?>
</div>

<?php $this->endWidget(); ?>

<?php
$this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'view',
    'htmlOptions' => array('style' => 'width: 1000px; margin-left:-500px;'),)
);
?>

<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h4></h4>
</div>

<div class="modal-body">
    
</div>
<div class="modal-footer">
    <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'id' => 'modal-open-transmittal',
            'label' => 'Open transmittal',
            'type' => 'primary',
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Close',
            'type' => '',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        ));
    ?>
</div>

<div id="phic-response-dialog" style="display: none;">
<div id="loadingMessage3"><h1>Processing data....</h1></div>
        <iframe id="phic-response-dialog-frame" src="" style="height:100%;width:100%;border:none;">
        </iframe>
</div>

<div id="transmittal-detail-dialog" style="display: none;">
<div id="loadingMessage4"><h1>Processing data....</h1></div>
        <iframe id="transmittal-details-dialog-frame" src="" style="height:100%;width:100%;border:none;">
        </iframe>
</div>

<?php $this->endWidget();