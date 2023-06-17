<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php
        $baseUrl = Yii::app()->request->baseUrl;
        $cs = Yii::app()->clientScript;
        /* @var $cs CClientScript */
        $cs->registerCssFile($baseUrl . '/css/frontend/style.css')
            ->registerCssFile($baseUrl . '/css/frontend/animate.css')
            ->registerScriptFile($baseUrl . '/js/mustache.js')
            ->registerScriptFile($baseUrl . '/js/jquery/jquery.blockUI.js')
            ->registerScriptFile($baseUrl . '/js/frontend/alert.js')
            ->registerScript('animate',
<<<SCRIPT

SCRIPT
);
    ?>
</head>

<body>

<div id="page">
    <?php
        $this->widget('bootstrap.widgets.TbNavbar', array(
            'brand' => '',
            'brandUrl' => false,
            'brandOptions' => array(
            ),
            'fluid' => true,
            'items' => $this->menu,
        ));
    ?>

	<?php if(isset($this->breadcrumbs)):?>
		<?php
            $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                //'homeLink' => CHtml::link(Yii::t(null, 'e-Claims'), array('site')),
                'homeLink' => false,
                'links'=>$this->breadcrumbs,
            ));
        ?>
	<?php endif?>

    <div class="container-fluid">

        <!-- Page title -->
        <div class="row-fluid">
            <div class="span6">
<?php
    if (!empty($this->pageTitle)) {

        $pageIcon = (!empty($this->pageIcon)) ?
            CHtml::tag('i', array('class' => $this->pageIcon), '') :
            '';

        $subTitle = (!empty($this->pageSubTitle)) ?
            '<small><span class="separator"></span>' . $this->pageSubTitle . '</small>':
            '';

        $pageTitle = strtr('{icon} {title} {subtitle}', array(
            '{icon}' => $pageIcon,
            '{title}' => $this->pageTitle,
            '{subtitle}' => $subTitle
        ));

        echo CHtml::tag('h1', array('class' => 'page-title'), $pageTitle, true);
    }
?>

            </div>
        </div>


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

        <?php echo $content; ?>

        <div class="push"></div>

    </div>

</div><!-- page -->

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