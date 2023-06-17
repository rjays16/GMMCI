<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <?php
            $baseUrl = Yii::app()->request->baseUrl;
            $cs = Yii::app()->clientScript;
            $cs->registerCssFile($baseUrl . '/css/bootstrap/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css');
            $cs->registerScriptFile($baseUrl . '/css/bootstrap/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js');
            $cs->registerCssFile($baseUrl . '/css/frontend/or.css');
        ?>
        <script>
            var base_url = "<?= $baseUrl ?>";

            //moving menu
            var documentHeight = 100;
            var topPadding = 100;

            $(document).ready(function(){

                initDatetimeFields();

                //moving menu init
                var offset = $("#action-menu").parent().offset();
                documentHeight = $("#action-menu").parent().parent().height();
                $(window).scroll(function() {
                    var sideBarHeight = $("#action-menu").parent().height();
                    if ($(window).scrollTop() > offset.top) {
                        var newPosition = ($(window).scrollTop() - offset.top) + topPadding;
                        var maxPosition = documentHeight - (sideBarHeight + 100);
                        if (newPosition > maxPosition) {
                            newPosition = maxPosition;
                        }
                        $("#action-menu").parent().stop().animate({
                            marginTop: newPosition
                        });
                    } else {
                        $("#action-menu").parent().stop().animate({
                            marginTop: 60
                        });
                    }
                });
            });

            function initDatetimeFields(){
                //datetime picker init
                $(".datetime_field").parent().attr('class','controls input-append date form_datetime');
                $(".datetime_field").parent().attr('style','display:inherit;');
                $(".form_datetime").append('<span class="add-on"><i class="icon-remove"></i></span><span class="add-on"><i class="icon-th"></i></span>');
                $(".form_datetime").datetimepicker({
                    format: "MM dd, yyyy HH:ii P",
                    showMeridian: true,
                    autoclose: true,
                    todayBtn: true
                });
            }
        </script>
    </head>
    <body style="padding: 20px;">
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
        <h1>Create OrRequest</h1>
        <div id="wrapper" class="row-fluid" style="padding: 0;">
            <div class="span12">
                <?php echo $content; ?>
            </div>
        </div>
    </body>
</html>