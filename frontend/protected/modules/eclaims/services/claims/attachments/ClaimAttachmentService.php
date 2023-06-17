<?php


namespace SegHis\modules\eclaims\services\claims\attachments;

use ClaimAttachment;
use Encounter;
use SegHis\modules\eclaims\helpers\cf4\CF4Helper;

class ClaimAttachmentService
{

    public function cloudStorageFormat(
        $details,
        $documentType
    ) {
        $encounter       = Encounter::model()->findByPk($details->encounter_nr);
        $claimAttachment = new ClaimAttachment();
        $attachments     = $claimAttachment->findAllByAttributes(array(
            'encounter_nr' => $details->encounter_nr,
            'transmit_no'  => $details->transmit_no,
        ));
        $sequence        = str_pad(count($attachments)+1, 2, 0, STR_PAD_LEFT);

        $admissionDate = date('Ymd', strtotime($encounter->encounter_date));
        $format        = array(CF4Helper::getAccreditationCode(), $details->encounter_nr, $admissionDate, $documentType, $sequence);
        return implode(";", $format);
    }
}
