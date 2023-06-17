<?php


namespace SegHis\modules\eclaims\services\cf4\nodes\services;


use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\models\cf4\Cf4Examination;

class PhysicalExamService
{

    const OTHER_EXAM = 99;

    public $encounter;

    public function __construct(\Encounter $encounter)
    {

        $this->encounter = $encounter;
    }

    public function getPEMISCData()
    {

        $chest = $this->getPhysicalExamination(Cf4Examination::CHEST, false);
        $skin = $this->getPhysicalExamination(Cf4Examination::SKIN, false);
        $heart = $this->getPhysicalExamination(Cf4Examination::HEART, false);
        $heent = $this->getPhysicalExamination(Cf4Examination::HEENT, false);
        $abdomen = $this->getPhysicalExamination(Cf4Examination::ABD, false);
        $rectal = $this->getPhysicalExamination(Cf4Examination::RECTAL, false);
        $neuro = $this->getPhysicalExamination(Cf4Examination::NEURO, false);
        $guie = $this->getPhysicalExamination(Cf4Examination::GUIE, false);

        $physicalExaminations = array(
          'skin' => $skin,
          'chest' => $chest,
          'heart' => $heart,
          'heent' => $heent,
          'abdomen' => $abdomen,
          'neuro' => $neuro,
          'rectal' => $rectal,
          'gu' => $guie,
        );

        foreach ($physicalExaminations as $key => $physicalExamination) {
            if (!$physicalExamination) {
                unset($physicalExaminations[$key]);
            }
        }

        return $physicalExaminations;
    }

    public function getPESpecificData()
    {

        $chest = $this->getPhysicalExamination(Cf4Examination::CHEST, true);
        $skin = $this->getPhysicalExamination(Cf4Examination::SKIN, true);
        $heart = $this->getPhysicalExamination(Cf4Examination::HEART, true);
        $heent = $this->getPhysicalExamination(Cf4Examination::HEENT, true);
        $abdomen = $this->getPhysicalExamination(Cf4Examination::ABD, true);
        $rectal = $this->getPhysicalExamination(Cf4Examination::RECTAL, true);
        $neuro = $this->getPhysicalExamination(Cf4Examination::NEURO, true);
        $guie = $this->getPhysicalExamination(Cf4Examination::GUIE, true);

        $data =  array(
          'pSkinRem' => $skin[0]['remarks'],
          'pHeentRem' => $heent[0]['remarks'],
          'pChestRem' => $chest[0]['remarks'],
          'pHeartRem' => $heart[0]['remarks'],
          'pAbdomenRem' => $abdomen[0]['remarks'],
          'pNeuroRem' => $neuro[0]['remarks'],
          'pRectalRem' => $rectal[0]['remarks'],
          'pGuRem' => $guie[0]['remarks'],
          'pReportStatus' => CF4Helper::getDefaultReportStatus(),
          'pDeficiencyRemarks' => null
        );

        return $data;


    }

    /* Get Examinations */
    /*@param string $examination*/
    /*@return array $data*/
    public function getPhysicalExamination($examination, $others = false)
    {
        $exam = Cf4Examination::model()->findByAttributes(
          array('exam_code' => $examination)
        );

        $command = \Yii::app()->db->createCommand();

        $command->from($exam->table_name . ' t');
        $command->select($exam->phic_id . ',remarks');
        $command->where('t.encounter_nr = :encounter_nr AND t.is_deleted != 1');


        if ($others) {
            $command->andWhere($exam->phic_id . ' = :others');
            $command->params['others'] = self::OTHER_EXAM;

        }

        $command->order('created_at DESC');

        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;

        return $command->queryAll();
    }
}