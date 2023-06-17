<?php
/* @var $this Controller */
//die(print_r($this->menu));
?>

<?php $this->beginContent('//layouts/or'); ?>
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
<div id="content">
    <?php echo $content; ?>
</div><!-- content -->
<?php $this->endContent(); ?>