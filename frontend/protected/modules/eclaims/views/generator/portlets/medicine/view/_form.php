<div class="row-fluid">
    <div class="span6">
        <label>Complete Drug Description</label>
        <?php
        $url = Yii::app()->createUrl('eclaims/medicine/medicineList');
        $this->widget('bootstrap.widgets.TbSelect2', array(
            'asDropDownList' => false,
            'model' => $model,
            'name' => 'drug_code',
            'options' => array(
                'width' => '100%',
                'placeholder' => 'Search Medicine',
                'dataType' => 'json',
                'id' => 'js:function(data){return data.drug_code;}',
                'ajax' => array(
                    'quietMillis' => 500,
                    'url' => $url,
                    'data' => 'js:function(term, page) { return {t: term};  }',
                    'results' => 'js:function(data, page) { return {results: data}; }',
                ),
                'allowClear' => true,
                'escapeMarkup' => 'js:function (markup) { return markup; }',
                'minimumInputLength' => 2,
                'initSelection' => 'js:function(element, callback){
                                            var id = $(element).val();
                                            if(id !== "") {
                                                $.ajax("' . $url . '", {
                                                    data: {id: id},
                                                    dataType: "json"
                                                }).done(function(data) {
                                                console.log(data);
                                                    callback(data);
                                                });
                                            }
                                        }',
                'formatResult' => 'js:function(data, container, query){
                                            return "<span>"+data.description+"</span>";
                                        }',
                'formatSelection' => 'js:function(data, container){
                                        return data.description;
                                    }'
            ),
            'htmlOptions' => array(
                'data-orig' => $medicine_library->description,
                'class' => 'drug_code',
                'id' => 'drug_code',
                'style' => 'width: 100%',
            ),
        ));
        ?>
    </div>
</div>
<br><br><br><br><br><br><br><br><br><br>
<div class="row-fluid">
    <p>
        <span style="color: #ff0000">Note</span>:
        If Medicine is not available in the list, kindly input the drug description below as required.
    </p>
</div>

<div class="row-fluid">
    <div class="span5">
        <?php
        //    CVarDumper::dump($model, 10, true);die;
        echo CHtml::checkBoxList(
            'is_pndf',
            Cf4Medicine::model(),
            array(
                'is_pndf' => 'Available in the list'
            ),
            array(
                'class' => 'is_pndf',
                'id' => 'is_pndf',
                'labelOptions' => array(
                    'style' => 'display: inline;',
                ),
                'style' => 'margin-top: -3px;'
            )
        );
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span6">

        <?php echo $form->label(Cf4Medicine::model(), 'generic'); ?>
        <?php
        echo $form->textField(Cf4Medicine::model(), 'generic', array(
            'class' => 'input-medium span12 generic',
            'id' => 'generic',
            'placeholder' => 'Generic Name/Salt/Strength/Form/Unit/Package'
        ));
        ?>

    </div>
</div>

<div class="row-fluid">
    <div class="span6">

        <?php
        echo $form->textAreaRow(Cf4Medicine::model(), 'route', array(
            'class' => 'input-medium span12 route',
            'id' => 'route',
            'placeholder' => 'Route'
        ));
        ?>

    </div>
    <div class="span6">

        <?php
        echo $form->textAreaRow(Cf4Medicine::model(), 'frequency', array(
            'class' => 'input-medium span12 frequency',
            'id' => 'frequency',
            'placeholder' => 'Frequency'
        ));
        ?>

    </div>
</div>

<div class="row-fluid">
    <div class="span3">

        <?php
        echo $form->numberFieldRow(Cf4Medicine::model(), 'quantity', array(
            'class' => 'input-medium span12 quantity',
            'id' => 'quantity',
            'placeholder' => 'Quantity'
        ));
        ?>

    </div>
    <div class="span3">

        <?php
        echo $form->numberFieldRow(Cf4Medicine::model(), 'cost', array(
            'class' => 'input-medium span12 cost',
            'id' => 'cost',
            'placeholder' => 'Total Amount'
        ));
        ?>

    </div>
</div>


<div class="row-fluid">
    <div class="span12">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'icon' => 'fa fa-save',
                'label' => 'Save',
                'htmlOptions' => array(
                    'id' => 'add-medicine',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<hr>

<script>
    let $drug_code = $(".drug_code");
    let $generic = $(".generic");
    let $quantity = $(".quantity");
    let $frequency = $(".frequency");
    let $route = $(".route");
    let $cost = $(".cost");
    let $is_pndf = $(".is_pndf");
    let $hidden = 'hidden';

    isPndf();

    function isPndf() {
        if ($is_pndf.is(':checked')) {
            $drug_code.prop("checked", false);
            $drug_code.removeClass($hidden);
            $generic.prop("disabled", true);
        } else {
            $drug_code.prop("checked", true);
            $drug_code.addClass($hidden);
            $generic.prop("disabled", false);
        }
    }

    $is_pndf.on("click", function (e) {
        isPndf();
    });

    $("#add-medicine").on("click", function (e) {
        e.preventDefault();
        if (($drug_code.val() === "" || $drug_code.val() === null) && $is_pndf.is(':checked')) {
            Swal.fire({
                title: 'Select Drug name!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        } else if (!$is_pndf.is(':checked') && $generic.val() === "") {
            Swal.fire({
                title: 'Generic / Drug description cannot be blank!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        } else if ($route.val() === "") {
            Swal.fire({
                title: 'Route cannot be blank!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        } else if ($frequency.val() === "") {
            Swal.fire({
                title: 'Frequency cannot be blank!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        } else if ($quantity.val() === "") {
            Swal.fire({
                title: 'Quantity cannot be blank!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        } else if ($cost.val() === "") {
            Swal.fire({
                title: 'Total amount cannot be blank!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        } else {
            Swal.fire({
                title: 'Save Changes',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveMedicine();
                } else {
                    return false;
                }
            });
        }
    });

    function resetField() {
        $drug_code.val("");
        $generic.val("");
        $quantity.val("");
        $frequency.val("");
        $route.val("");
        $cost.val("");
        $is_pndf.prop("checked", true);
        isPndf();
    }

    function saveMedicine() {
        let $form = $("#medicine-form");
        let url = $form.data('url');
        let encounter_nr = $form.data('encounter_nr');

        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                drug_code: $drug_code.val(),
                generic: $generic.val(),
                quantity: $quantity.val(),
                frequency: $frequency.val(),
                route: $route.val(),
                cost: $cost.val(),
                is_pndf: $is_pndf.is(':checked')
            },
            dataType: 'json',
            beforeSend: () => {
            },
            success: (response) => {
                if (response.status) {
                    Swal.fire({
                        title: 'The data has been saved!',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    resetField();
                } else {
                    Swal.fire({
                        title: response.message,
                        type: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                $.fn.yiiGridView.update("medicine_list");
            },
            error: (response) => {
                Swal.fire({
                    title: 'Something went wrong, Please contact your administrator',
                    text: response.message,
                    type: 'error',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
</script>
