<?php
    /* Fail Upload Response View */
$gridDataProvider = new CArrayDataProvider($uploadErrors);

        $this->widget('bootstrap.widgets.TbGridView', array(
         'type' => 'striped condensed bordered',
         'dataProvider' => $gridDataProvider,
         'columns' => array(
            array('name' => 'errCode', 'header' => 'Error Code'),
            array('name' => 'errDesc', 'header' => 'Error Description'),
         ),
       ));
?>

<?php
    /* 
     //Detail View Format
    
    foreach($uploadErrors as $error) { 
        $content = array();
        foreach($error as $index => $value) {
            $content[] = array('name' => $index, 'label' => $index);   
        }
        ?>
        <div class="row-fluid">
            <div class="span6">
                <?php
                    $this->widget('bootstrap.widgets.TbDetailView',array(
                        'data' => $error,
                        'type'=>'striped bordered condensed',
                        'itemTemplate'=>"<tr class=\"{class}\"><th style=\"width:1%\">{label}</th><td>{value}</td></tr>\n",
                        'attributes'=> $content,
                    ));
                ?>
            </div>
        </div>
    <?php
    }
    */
?>



