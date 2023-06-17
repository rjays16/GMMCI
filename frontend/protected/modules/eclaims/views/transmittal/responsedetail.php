<?php

/*START added By MArk April 23, 2017*/
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
/*END added By MArk April 23, 2017*/

  /* Upload Claim Response Details */
  echo "<h6>XML File Transmission Response</h6>";
    $gridDataProvider = new CArrayDataProvider($details["upload"]);
        $this->widget('bootstrap.widgets.TbGridView', array(
         'type' => 'striped condensed bordered',
         'dataProvider' => $gridDataProvider,
         'columns' => array(
            array('name' => 'hospital_code', 'header' => 'Hospital Code'),
            array('name' => 'transmit_no', 'header' => 'Hospital Transmittal Number'),
            array('name' => 'no_claim', 'header' => 'Total Claims'),
            array('name' => 'transmit_dte', 'header' => 'Transmission Date & Time', 'type' => 'datetime',),
            array('name' => 'control_no', 'header' => 'Transmission Control Number'),
            array('name' => 'ticket_no', 'header' => 'Receipt Ticket Number')
         ),
       ));

    /* Map Claim Response Details */
  if(!empty($details["map"])) {
    echo "<h6>Claims Mapping Response</h6>";
    $gridDataProvider = new CArrayDataProvider($details["map"]);

        $this->widget('bootstrap.widgets.TbGridView', array(
         'type' => 'striped condensed bordered',
         'dataProvider' => $gridDataProvider,
         'columns' => array(
            array('name' => 'claim_no', 'header' => 'Claim Number'),
            array('name' => 'name_full', 'header' => 'Patient Full Name'),
            array('name' => 'admission_dt', 'header' => 'Admission Date & Time', 'type' => 'datetime',),
            array('name' => 'discharge_dt', 'header' => 'Discharge Date & Time', 'type' => 'datetime',),
            array('name' => 'claim_series_lhio', 'header' => 'Claim Series Number'),
         ),
       ));
  }
