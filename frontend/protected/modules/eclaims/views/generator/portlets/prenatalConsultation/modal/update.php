<!-- The Modal -->
<div class="modal fade" id="myModalDatePrenatalVisits" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Date of Prenatal Visits</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="generic_field">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Prenatal Consultation No:', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[prenatal_consultation_no]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'prenatal_consultation_no_edit',
                                'readOnly' => true
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="row-fluid" id="generic_field">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Date of Visit' . '<span style="color:red;"> *</span>', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::dateField('PrenatalVisits[date_visit]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'date_visit_edit'
                            )
                        );
                        ?>
                    </div>
                    <div class="span6">
                        <?php
                        echo CHtml::label('AOG' . '<span style="color:red;"> *</span>', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[aog]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'aog_edit',
                                'readOnly' => true
                            )
                        );
                        ?>
                    </div>

                </div>
                <div class="row-fluid" id="generic_field">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Weight', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[weight]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'weight_edit'
                            )
                        );
                        ?>
                    </div>
                    <div class="span6">
                        <?php
                        echo CHtml::label('Cardiac Rate', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[cardiac_rate]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'cardiac_rate_edit',
                            )
                        );
                        ?>
                    </div>

                </div>

                <div class="row-fluid" id="generic_field">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Respiratory Rate', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[respiratory_rate]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'respiratory_rate_edit'
                            )
                        );
                        ?>
                    </div>
                    <div class="span6">
                        <?php
                        echo CHtml::label('BP', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[bp]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'bp_edit',
                            )
                        );
                        ?>
                    </div>

                </div>
                <div class="row-fluid" id="generic_field">
                    <div class="span6">
                        <?php
                        echo CHtml::label('Temperature', '',
                            array(
                                'class' => '',
                                'id' => 'gen_label'
                            )
                        );
                        echo CHtml::textField('PrenatalVisits[temperature]', '',
                            array(
                                'step' => 'any',
                                'class' => 'col-md-12 ',
                                'id' => 'temperature_edit'
                            )
                        );
                        ?>
                    </div>
                    

                </div>
               <input type="hidden" name="" id="date_prenatal_visits_edit_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="update-date-prenatal-visits">Update</button>
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
