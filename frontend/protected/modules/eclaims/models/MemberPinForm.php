<?php
/**
 * MemberPinForm
 *
 * @author Louie Rinos
 * @copyright Copyright &copy; 2014. Segworks Technologies Corporation
 */

/**
 *
 */
class MemberPinForm extends CFormModel
{

    public $pMemberFirstName;
    public $pMemberMiddleName;
    public $pMemberLastName;
    public $pMemberSuffix;
    public $pMemberBirthDate;

    /**
     *
     * @return type
     */

    public function rules()
    {
        return array(
            array('pMemberFirstName, pMemberLastName, pMemberMiddleName, pMemberBirthDate', 'required'),
            array('pMemberBirthDate', 'date', 'format' => 'mm-dd-yyyy'),
            array(' pMemberSuffix', 'safe')
        );
    }

    /**
     *
     * @return type
     */
    public function attributeLabels()
    {
        return array(
            'pMemberFirstName' => 'First name',
            'pMemberMiddleName' => 'Middle name',
            'pMemberLastName' => 'Last name',
            'pMemberSuffix' => 'Suffix',
            'pMemberBirthDate' => 'Birth date'
        );
    }

    /**
     *
     * @return array
     */
    public function getPinParams()
    {
        #added hospital code
        Yii::import('eclaims.models.HospitalConfigForm');
        $configModel = new HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;

        $result = array(
            'pHospitalCode' => $hospitalCode,
            'pMemberFirstName' => $this->pMemberFirstName,
            'pMemberMiddleName' => $this->pMemberMiddleName,
            'pMemberLastName' => $this->pMemberLastName,
            'pMemberSuffix' => $this->pMemberSuffix,
            'pMemberBirthDate' => $this->pMemberBirthDate,
        );

        array_walk($result, function (&$value, $key) {
            if (empty($value)) {
                $value = '';
            }

            if (mb_detect_encoding($value, 'UTF-8', true) === false) {
                $value = mb_convert_encoding($value, 'UTF-8');
            }

            $value = mb_strtoupper($value);
        });

        return $result;
    }
}

