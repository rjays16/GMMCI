<!-- Modals -->
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'populate-personnel', 'htmlOptions'=>array('style' => 'width: 800px; margin-left: -400px'))
); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Select Personnel</h4>
</div>
<div class="modal-body" style="height: 300px;">
    <?php
    $form = $this->beginWidget(
        'bootstrap.widgets.TbActiveForm',
        array(
            'id' => 'search-personnel-form',
            'type' => 'search',
            'htmlOptions' => array('class' => 'well'),
        )
    );
    echo $form->textFieldRow(
        $person,
        'fullName',
        array(
            'class' => 'input-medium',
        ),
        array(
            'prepend' => '<i class="icon-search loader"></i>'
        )
    );
    $this->endWidget();
    //unset($form);
    ?>
    <table id="personnel-search-result" style="display: none; width: 100%;" class="items table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th width="15%">Personnel #</th>
            <th width="*">Full Name</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<div class="modal-footer">
    <?php $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label' => 'Close',
            'url' => '#',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    ); ?>
</div>
<?php $this->endWidget(); ?>