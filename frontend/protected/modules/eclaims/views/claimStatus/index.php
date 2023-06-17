<?php

/**
 * Main view of Claim Status function where all the claims are listed
 * and that can also be filtered
 *
 * @author  Mary Joy L. Abuyo
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

$this->setpageTitle('Check Claim Status');

// var_dump($claim->search());die;

$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;


Yii::app()->clientScript->registerCss('_claimStatus-css', <<<CSS
    td > p.case-rate-p {
        margin-bottom: 0;
    }
    td > hr.case-rate-hr {
        margin: 3px 0;
        border-top-color: #BEBEBE;
    }
    .searchI{
            font-size: 25px;
            width: 60%;
          border: 1px solid #b7b7b7;

    }
    .searchStatus{
        width: 63%
    }
    .grid-view-loading
{
    background-position: center bottom;
    background-color: #f9f9f9;
}
CSS
);

Yii::app()->clientScript->registerScript('re-install-date-picker', "
    function reinstallDatePicker1(id, data) {
        reinstallDatePicker2();
        $('#discharge_date').datepicker({
            'format':'yyyy-mm-dd'
        });
    }

    function reinstallDatePicker2(id, data) {
        $('#admission_dt').datepicker({
            'format':'yyyy-mm-dd'
        });
    }
");

Yii::app()->clientScript->registerScript('search', "
    $('.search-form form').submit(function(){
        $('#claim').yiiGridView('select', {
            data: $(this).serialize()
        });
    return false;
    });
");
/*added by MARK April 21, 2017*/
$js = <<<JAVASCRIPT
jQuery(document).ready(function(){
   jQuery('#custom-search-data').click(function(e) {
     e.preventDefault();
      send();
    })
  
   function send(){
     jQuery('#myModal').modal('hide');
     var datas=jQuery("#seg-search-eclaims-form").serialize();
      console.log(datas);   
              $.ajax({
               type: 'GET',
                url: 'index.php?r=eclaims/claimStatus/index/SearchNew',
               data:datas,
               beforeSend: function() {
                    Alerts.loading({
                        'title': 'Please wait',
                        content: 'Searching Check Claim Status...'
                    });
                },
                success:function(data){
                            console.log(data);   
                            window.location.href ="index.php?r=eclaims/claimStatus/index&search="+"true"+"&"+datas;
                          },
               error: function(data){
                     alert("Error occured.please try again");
                },
                complete: function() {
                       Alerts.close();
                },
             
              dataType:'html'
              });


   }
   var GetData = jQuery('#ifSearch').val();
    if (GetData ==""){
        jQuery('#button-back').hide();
    }
});

 function getOtherDisable(id){
     var ids = jQuery(id).attr("id");
        jQuery('.searchI').each(function() {
            if (this.id == ids) {
                jQuery("#"+ids).prop('readonly', false);
                jQuery("#"+ids).attr("placeholder","Search "+ids);
            }
            else{
                jQuery("#"+this.id).prop('readonly', true);  
                jQuery("#"+this.id).val('');  
                jQuery("#"+this.id).attr("placeholder","Click me to search "+this.id);  
            }
        });
 }
 function OnBlurData(){
     jQuery('.searchI').each(function() {
        jQuery("#"+this.id).prop('readonly', false);
      });

 }
 /*END added by MARK April 21, 2017*/
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);


$this->beginWidget(
    'bootstrap.widgets.TbBox', array(
        'title' => 'List of Claims',
        'headerIcon' => 'icon-th-list',
        'headerButtons' => array(
        array(
            'class' => 'bootstrap.widgets.TbButton',
            'label' => 'Generate',
            'type' => 'success',
            'icon' => 'fa fa-folder',
            'url' => '',
            'htmlOptions' => array(
                    'data-toggle' => 'modal',
                    'data-target' => '#myModalGenerate',
                ),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButton',
            'label' => 'Search',
            'type' => 'primary',
            'icon' => 'fa fa-search',
            'url' => '',
             'htmlOptions' => array(
                    'data-toggle' => 'modal',
                    'data-target' => '#myModal',
                ), 
        ),
         array(
            'class' => 'bootstrap.widgets.TbButton',
            'label' => 'Back',
            'type' => 'success',
            'icon' => 'fa fa-arrow-left',
            'url' => 'index.php?r=eclaims/claimStatus/index',
            'id' => 'button-back',
        ),

    ),
    )
);

$this->widget(
    'bootstrap.widgets.TbExtendedGridView', array(
        'id'=>'claim',
        'type'=>'striped bordered condensed',
        'dataProvider'=> !empty($_GET['search']) ? $claim->searchNews() : $claim->search(),
        // 'filter'=>$claim,
        'fixedHeader' => true,
        'afterAjaxUpdate' => 'reinstallDatePicker1',
        'columns'=>array(
            array(
                'name'=>'transmit_no',
                'header'=>'Transmittal No',
                'htmlOptions'=>array('width'=>'10%'),
             

                ),

              array(
                'name'=>'transmit_dte',
                'type' => 'datetime',
                'header'=>'Transmittal Date',
               

                ),
            array(
                'name'=>'encounter_nr',
                'header'=>'Encounter No',
                'htmlOptions'=>array('width'=>'10%'),
             
            ),
            array(
                'name'=>'claim_series_lhio',
                'header'=>'Claim Series Lhio',
                'htmlOptions'=>array('width'=>'10%'),
              
            ),
            array(
                'header'=>'Patient',
                'name' => 'name_lasted',
                'value' => function($data, $row) {
                    $person = $data['sex'];
                    switch (strtolower($person)) {
                        case 'm':
                            $icon = '<i class="color-blue fa fa-male"></i>';
                            break;
                        case 'f':
                            $icon = '<i class="color-pink fa fa-female"></i>';
                            break;
                        default:
                            $icon = '';
                    }

                    return $data["name_lasted"] .
                        " {$icon} <br/>" .
                       $data["typ_enc"];
                },
                'type' => 'raw',
                'filter'=>CHtml::activeTextField(
                    $claim->getRelatedModel('person'),
                    'name_last',
                    array("placeholder"=>"Enter Last Name")
                ),
            ),
            /* @todo: Fix Filter admission_dt to getAdmissionDt()  */
            array(
                'name'=>'admission_dt',
                'header'=>'Admission Date',
                'type' => 'date',
                'htmlOptions'=>array('width'=>'10%'),
            ),
            array(
                'name'=>'bill_dte',
                'type' => 'datetime',
                'header'=>'Discharge Date',
                'htmlOptions'=>array('width' => '10%'),
            ),
            array(
                'header' => 'Package',
                'value' => function($data, $row) {
                    if(empty($data['package_data_new']))
                        return CHtml::tag('em', array('class'=>'muted'), 'NO PACKAGE IN BILL');

                    $_packages = array();
                    $description = explode("|", $data['package_data_new']);
                    // foreach($data->billing->getCaseRateInOrder() as $caseRate) {
                        $_helpIcon = CHtml::tag('i', array(
                            'class' => 'fa fa-question-circle',
                            'data-title'  =>  $description[1],
                            'data-toggle' => 'tooltip',
                        ), ' ');

                        $_caseRateCode = CHtml::tag('small', array(), end(explode("|", $data['package_data_new'])));

                        $_packages[] = CHtml::tag('p', array('class'=>'case-rate-p text-right'), 
                            $_caseRateCode . ' ' . $_helpIcon
                        );
                    // }
                    $_caseRatesAmout = CHtml::tag('div', array('class' => 'text-right'), 
                        Yii::app()->numberFormatter->formatCurrency(current(explode("|", $data['package_data_new'])), "PHP "));

                    $_formatted = implode("\n", $_packages) . "<hr class='case-rate-hr'>" 
                        . $_caseRatesAmout;
                    return $_formatted;
                },
                'type' => 'raw',
                'htmlOptions'=>array('width' => '10%'),
            ),
            array(
                'name'=>'STATUS',
                'htmlOptions'=>array('width' => '10%'),
                #'filter' => true,

            ),
            array(
                'class'=>'bootstrap.widgets.TbButtonGroupColumn',
                'template'=>'{detail}',
                'buttons' => array(
                    'detail' => array(
                        'encodeLabel' => false,
                        'label' => '<i class="fa fa-check-circle"></i> Check Status',
                        'url' => 'Yii::app()->createUrl("eclaims/claimStatus/viewStatus", array("claim_id" => $data["id"], "enc_nr"=> $data["encounter_nr"], "searchin"=> $_GET["search"],"update_status" => 1,"current_page"=>$_GET["user_page"]))',
                        'options'=>array(
                            'class' => 'viewStatusBtn',
                            'title' => false,
                        ),
                    ),
                ),
            ),
        ),
    )
);
$this->endWidget();


?>
<!-- /*added by MARK April 21, 2017*/ -->
<input type="hidden" name="" id="ifSearch" value="<?php echo $_REQUEST['search']; ?>">
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'myModal')
); ?>
 

    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Search Claim Status by:</h4>
    </div>
 
    <div class="modal-body">

       <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'seg-search-eclaims-form',
            'enableAjaxValidation'=>false,
                'htmlOptions'=>array(
                                       'onsubmit'=>"return send();",
                                       'onkeypress'=>" if(event.keyCode == 13){ send(); } "
                                     ),
            )); ?>

            <center>
            <small>(You can select only one field)</small><br>
                    <input class="searchI" type="text" id="encounter_nr" placeholder="Encounter No." onfocusout="OnBlurData();" onclick="getOtherDisable(this);" name="encounter_nr_new_data" value="">
                    <br>
                    <input class="searchI" type="text" id="transmit_no" placeholder="Transmittal No."   onfocusout="OnBlurData();" onclick="getOtherDisable(this);" name="transmit_no_new_data" value="">
                    <br>
                    <input class="searchI" type="text" id="claim_series_lhio" placeholder="Claim Series Lhio"   onfocusout="OnBlurData();" onclick="getOtherDisable(this);" name="claim_series_lhio" value="">
                    <br>
                      <input class="searchI" type="text" id="patient_lastname" placeholder="Patient Lastname"   onfocusout="OnBlurData();" onclick="getOtherDisable(this);" name="patient_lastname" value="">
                    <br>
                    <?php 
                    $this->widget(
                    'bootstrap.widgets.TbDatePicker',
                        array(
                            'name' => 'Transmittal_date',
                            'htmlOptions' => array('class'=>'searchI',
                                            'placeholder'=>'Transmit Date',
                                            'onfocusout' =>"OnBlurData()",
                                            'onclick'=>"getOtherDisable(this)",
                             ),
                        )
                    );
                    ?>
                    <br>
                    <?php 
                    $this->widget(
                    'bootstrap.widgets.TbDatePicker',
                        array(
                            'name' => 'admission_date',
                            'htmlOptions' => array('class'=>'searchI',
                                            'placeholder'=>'Admission Date',
                                            'onfocusout' =>"OnBlurData()",
                                            'onclick'=>"getOtherDisable(this)",
                             ),
                        )
                    );
                    ?>
                    <br>
                    <?php 
                    $this->widget(
                    'bootstrap.widgets.TbDatePicker',
                        array(
                            'name' => 'discharge_date',
                            'htmlOptions' => array('class'=>'searchI',
                                            'placeholder'=>'Discharge Date',
                                            'onfocusout' =>"OnBlurData()",
                                            'onclick'=>"getOtherDisable(this)",
                             ),
                        )
                    );
                   echo CHtml::dropdownlist('status',$selectedvalue,
                    array(''=>'-- SELECT STATUS --',
                        'PENDING'=> 'PENDING',
                        'IN PROCESS' => 'IN  PROCESS',
                        'RETURN' => 'RETURN',
                         'DENIED' =>'DENIED',
                         'WITH VOUCHER' => 'WITH VOUCHER',
                         'VOUCHERING' => 'VOUCHERING',
                         'WITH CHEQUE' => 'WITH CHEQUE',
                         'CLAIM SERIES NOT FOUND'=>'CLAIM SERIES NOT FOUND'),
                    array('class' => 'searchStatus',));
                    ?>
                   

        
            </center>
    </div>


    
    <div class="modal-footer">
    <?php
      $this->widget('bootstrap.widgets.TbButton',
                array(
                    'id' => 'custom-search-data',
                    'buttonType' => 'submit',
                    'type' => 'primary',
                    'icon' => 'fa fa-search',
                    'loadingText' => 'Saving ...',
                    'label' => 'Search',
                    'htmlOptions' => array(
                        'class' => 'getpinButton',
                    )
                )
            );

     ?>
      <?php $this->endWidget(); ?>
        <?php 
         $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'label' => 'Close',
                    'url' => '#',
                    'htmlOptions' => array('data-dismiss' => 'modal'),
                )
            );
        ?>

    </div>
    <?php $this->endWidget(); ?>
<!-- /*END added by MARK April 21, 2017*/ -->
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'myModalGenerate')
); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Generate Report:</h4>
    </div>
    <div class="modal-body">
                    <label><b>Date From</b></label>
                    <?php 
                    $this->widget(
                    'bootstrap.widgets.TbDatePicker',
                        array(
                            'name' => 'dateFrom',
                            'id' => 'dateFrom',
                            'htmlOptions' => array('class'=>'searchI',
                                            'placeholder'=>'Please Select Date From',
                             ),
                        )
                    );      
                     ?>
                    <label><b>Date to</b></label>
                    <?php 
                    $this->widget(
                    'bootstrap.widgets.TbDatePicker',
                        array(
                            'name' => 'dateTo',
                            'id' => 'dateTo',
                            'htmlOptions' => array('class'=>'searchI',
                                            'placeholder'=>'Please Select Date To',       
                             ),
                        )
                    );      
                     ?>

            </div>
    <div class="modal-footer">
        <button id="show_modal_generate" class="btn btn-primary fa fa-check">&nbsp;Generate</button>
        <button id="close_modal_generate" class="btn btn-default">&nbsp;Cancel</button>
    </div>
    <?php $this->endWidget(); ?>

<script type="text/javascript">
        $("#show_modal_generate").click(function () {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();

            if (
                dateFrom != '' && 
                dateTo != ''
                ) {
                    if (dateFrom > dateTo) {
                        alert('Please select a date To that is greater than date from');
                    }else{
                        $("#myModalGenerate").modal('hide');
                        var nleft = (screen.width - 680)/2;
                        var ntop = (screen.height - 520)/2;
                        window.open("modules/billing/reports/eclaimsStatusReport.php?dateFrom="+dateFrom+"&dateTo="+dateTo, "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                    }
                }else{
                    alert('Please dont leave the important fields');
                }
        });

        $("#close_modal_generate").click(function () {
            $("#myModalGenerate").modal('hide');
        });
        
</script>