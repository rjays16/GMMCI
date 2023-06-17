<!-- The Modal -->
<div class="modal fade" id="myModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Medicine</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="generic_field">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Generic / Drug Description' . '<span style="color:red;"> *</span>', '',
                            array(
                                'class' => 'hidden',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('Medicine[description]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 hidden'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Route' . '<span style="color:red;"> *</span>', '');
                        echo CHtml::textArea('Medicine[route]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 textarea'
                            )
                        );
                        ?>
                    </div>
                    <div class="span6">
                        <?php
                        echo CHtml::label('Frequency' . '<span style="color:red;"> *</span>', '');
                        echo CHtml::textArea('Medicine[frequency]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 textarea'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Quantity' . '<span style="color:red;"> *</span>', '');
                        echo CHtml::numberField('Medicine[quantity]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12'
                            )
                        );
                        ?>
                    </div>
                    <div class="span6">
                        <?php
                        echo CHtml::label('Total Amount' . '<span style="color:red;"> *</span>', '');
                        echo CHtml::numberField('Medicine[tot_amount]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12'
                            )
                        );
                        ?>
                    </div>
                    <input type="hidden" name="" id="Medicine_id">
                    <input type="hidden" name="" id="Medicine_is_pndf">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="update-medicine">Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<style>
    .textarea {
        width: 393px;
    }
</style>
