<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/15/2019
 * Time: 3:24 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;


use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class CourseWardService extends XmlWriter
{

    public $document;

    public $encounter;

    public $data;

    /* Initializes Class for Courseward Service*/
    public function __construct(
      \DOMDocument $document,
      \EclaimsEncounter $encounter,
      $data
    ) {
        $this->data = $data;
        $this->document = $document;
        $this->encounter = $encounter;
    }

    public function generateHeader()
    {
        $header = $this->_createNode(
          $this->document,
          'COURSEWARDS',
          array()
        );

        return $header;
    }

    public function generateNode()
    {
        $header = $this->generateHeader();
        $data = $this->getCourseWards();

        if (empty($data)) {
            $data = array(1);
        }
        foreach ($data as $datum) {
            /* Generate COURSEWARD NODE*/
            $this->appendNode(
              $header,
              $courseward,
              'COURSEWARD',
              array(
                'pHciCaseNo' => $this->encounter->encounter_nr,
                'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
                'pDateAction' => empty($datum['date_action']) ? null :
                  date('Y-m-d', strtotime($datum['date_action'])),
                'pDoctorsAction' => empty($datum['doctor_action']) ? null : $datum['doctor_action'],
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => '',
              )
            );
        }


        /*APPEND THE WHOLE DOCUMENT */
        $this->document->appendChild($header);

        return $header;
    }


    public function getCourseWards()
    {

        $command = \Yii::app()->db->createCommand();
        $command->select('cw.date_action , cw.doctor_action');
        $command->from('seg_cf4_course_in_the_ward cw');

        $command->where('cw.encounter_nr = :encounter_nr AND cw.is_deleted != 1');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;

        $result = $command->queryAll();

        return $result;
    }


}