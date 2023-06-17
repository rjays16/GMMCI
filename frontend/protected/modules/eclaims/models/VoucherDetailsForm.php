<?php
/**
 * 
 *
 * @author Ma. Dulce Amor O. Polinar <dulcepolinar1010@gmail.com>
 * @copyright Copyright &copy; 2014. Segworks Technologies Corporation
 */

class VoucherDetailsForm extends CFormModel{

    public $voucherNo;

    /**
     *
     * @return array
     */
	public function rules()
	{
		return array(
            array('voucherNo', 'required'),
		);
	}

    /**
     *
     * @return array
     */
    public function attributeLabels()
	{
		return array(
            'voucherNo' => 'Voucher Number'
		);
	}

}

