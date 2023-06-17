<?php
/**
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright Copyright &copy; 2013-2014. Segworks Technologies Corporation
 * @since 1.0
 * 
 * @package eclaims.views.transmittal
 * 
 * @var $this TransmittalController
 * @var $details[] Array
 */
$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;

Yii::app()->clientScript->registerCss('_claimStatus-css', <<<CSS
.grid-view-loading
{
    background-position: center bottom;
}
.items{
       background-color: #FFFFFF;
  font-family: verdana;
  font-style: normal;
  font-weight: normal;
  font-size: 13px;
  font-variant: normal;
  cursor: text;
}
.items thead tr th{
       background-color: #FFFFFF;
       color: #000000;
  font-family: verdana;
  font-style: normal;
  font-weight: 5px;
  font-size: 13px;
  font-variant: normal;
  cursor: text;
}
.odd:hover{
      background-color: #000000;
  color: #378C34;
  font-family: verdana;
  font-style: normal;
  font-weight: normal;
  font-size: 13px;
  font-variant: normal;
  cursor: pointer;
}
.even:hover{
      background-color: #dddddd;
  color: #378C34;
  font-family: verdana;
  font-style: normal;
  font-weight: normal;
  font-size: 13px;
  font-variant: normal;
  cursor: pointer;
}
CSS
);

$gridDataProvider = new CArrayDataProvider($details);

$this->widget('bootstrap.widgets.TbGridView', array(
    'type' => 'striped condensed bordered',
    'dataProvider' => $gridDataProvider,
    'type' => 'striped condensed bordered hover',
    'summaryText' => 'Displaying {start} - {end} of {count} Transmittals',
    'columns' => array(
        array('name' => 'memberType', 'header' => 'Member Category'),
        array('name' => 'fullName', 'header' => 'Full Name'),
        array('name' => 'id', 'header' => 'Encounter Number'),
        array('name' => 'encounterType', 'header' => 'Encounter Type'),
        // array('name' => 'bill', 'header' => 'Billing Number', 'value' => function($data) {
            
        //     return empty($data["bill"]) ? CHtml::tag('em', array('class'=>'muted'), 'NO FINAL BILL') : $data["bill"];

        // }, 'type' => 'html'),

        array('name' => 'Bill Date', 'value' => function($data) {
            
            $_formatted = Yii::app()->dateFormatter->formatDateTime($data["billDate"]);
            $_value = empty($data["billDate"]) ? CHtml::tag('em', array('class'=>'muted'), 'NO FINAL BILL') : $_formatted;
            return $_value;

        }, 'type' => 'html'),

        array(
            'name' => 'amount',
            'header' => 'Amount',
            'value' => function($data) {
                
                $_formatted = Yii::app()->numberFormatter->formatCurrency($data["amount"], "PHP ");
                $_value = empty($data["amount"]) ? CHtml::tag('em', array('class'=>'muted'), 'NO FINAL BILL') : $_formatted;
                return $_value;
                
            },
            'type' => 'html',
            'htmlOptions' => array('style' => 'text-align:right')
        ),
    ),
));