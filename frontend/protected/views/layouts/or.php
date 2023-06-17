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
<div id="wrapper" class="row-fluid" style="padding: 0;">
    <?php
        $this->widget(
            'bootstrap.widgets.TbNavbar',
            array(
                'type'=>'inverse',
                'brand'=>'OR Module',
                'brandUrl'=>array("main/index"),
                'collapse'=>true,
                'fixed'=>false,
                'items'=>array(
                    array(
                        'class' => 'bootstrap.widgets.TbMenu',
                        'items'=>array(
                            array('label' => 'Home', 'url' => array("main/index"), 'active' => ($this->activeMenu==="home")?true:false),
                            array(
                                'label' => 'OR Requests',
                                'url' => "index.php?r=or/orRequest/index"
                            ),
                            array(
                                'label' => 'Managers',
                                'items' => array(
                                    array('label' => 'OR Technique', 'url' => ("index.php?r=or/orTechnique/admin")),
                                    array('label' => 'OR Checklist', 'url' => ("index.php?r=or/orChecklist/admin")),
                                    array('label' => 'Anesthesia','url' => ("index.php?r=or/orAnesthesia/admin")),
                                    array('label' => 'Packages','url' => ("index.php?r=or/packages/index")),
                                    )
                                ),
                            array(
                                'label' => 'Register New Born',
                                'url' => "modules/registration_admission/patient_register.php?ntid=false&lang=en&ptype=newborn&from=ipd&checkintern=1"
                            )
                        )
                    )
                ),
            )
        );
    ?>
    <?php if(isset($this->breadcrumbs)):?>
        <?php
        $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
            'homeLink' => false,
            'links'=>$this->breadcrumbs,
        ));
        ?>
    <?php endif?>

    <div class="span9">
        <?php echo $content; ?>
    </div>

    <?php if(!empty($this->menu)): ?>
        <?php
            $box = $this->beginWidget(
                'bootstrap.widgets.TbBox',
                array(
                    'id'=>'action-menu',
                    'title' => 'Actions',
                    'headerIcon' => 'icon-th-list',
                    'htmlOptions' => array('class' => 'span2', 'style'=>'clear:none;margin-top:60px;width:18%;')
                )
            );
            $this->widget(
                'bootstrap.widgets.TbMenu',
                array(
                    'type' => 'list',
                    'items' => $this->menu
                )
            );
            $this->endWidget();
        ?>
    <?php endif; ?>
</div>
<br class="clear" style="clear: both;" />
<footer id="page-footer">
        <div class="row-fluid">
            <div class="span6">
                Copyright &copy; 2014. <a href="www.segworks.com">Segworks Technologies Corporation</a>.

                All rights reserved.<br/>
                <span class="page-load-time">
                    Page load time
                    <span class="page-load-time-value"><?php echo sprintf('%ss', Yii::getLogger()->getExecutionTime()); ?></span>
                </span>
            </div>
            <div class="span6 right">
                <?php $this->widget('bootstrap.widgets.TbMenu', array(
                    'type' =>'pills', // '', 'tabs', 'pills' (or 'list')
                    'items'=>array(
                        array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
                        array('label'=>'Contact Us', 'url'=>array('/site/contact')),
                    ),
                    'htmlOptions' => array('class' => 'pull-right')
                )); ?>
            </div>
        </div><!-- footer -->
    </footer><!-- footer -->

<!-- Loading notification -->
<div id="messageLoading" class="messageBox" style="display:none">
    <div class="messageTitle"><i class="fa fa-gear fa-spin" style="color:rgb(0, 125, 196)"></i> <span>Please wait</span></div>
    <div class="messageContent"></div>
</div>

<!-- Error notification -->
<div id="messageError" class="messageBox" style="display:none">
    <div class="messageTitle"><i class="fa fa-warning" style="color:#c00"></i> <span>Error</span></div>
    <div class="messageContent"></div>
    <div class="messageAction">
        <button class="btn messageButtonOk"><i class="fa fa-check-circle"></i> OK</button>
    </div>
</div>

</body>
</html>