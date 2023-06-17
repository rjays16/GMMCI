<?php
require 'xml.php';

$data = array(
	'name' => 'eCLAIMS',
	'attributes' => array('pUserName'=>'','pUserPassword'=>'','pHospitalCode'=>'950102','pHospitalEmail'=>'dmcenter01@yahoo.com'),
	'children' => array(
		array(
			'name' => 'eTRANSMITTAL',
			'attributes' => array('pHospitalTransmittalNo'=>'xcxcxc','pTotalClaims'=>'3'),
			'children' => array(
				array(
					'name' => 'CLAIM',
					'attributes' => array('pClaimNumber'=>'2015500149','pTrackingNumber'=>'','pPhilhealthClaimType'=>'ALL-CASE-RATE','pPatientType'=>'I','pIsEmergency'=>'N'),
					'children' => array(
						array(
							'name' => 'CF1',
							'attributes' => array('pMemberPIN'=>'13651151','pMemberLastName'=>'BANDOY1Ñ','pMemberFirstName'=>'TIMOTEO, JR.1','pMemberSuffix'=>'.','pMemberMiddleName'=>'ABAYA1','pMemberBirthDate'=>'11-07-1966','pMemberShipType'=>'S','pEmployerName'=>'11','pPEN'=>'e1','pPatientIs'=>'M','pMailingAddress'=>'B/LUNGSOD12?ACACIA, DAVAO CITY 8000 DAVAO DEL SUR','pZipCode'=>'8000','pMemberSex'=>'M','pLandlineNo'=>'0','pMobileNo'=>'0','pEmailAddress'=>'sample@email.com','pPatientPIN'=>'13651151','pPatientLastName'=>'BANDOY1','pPatientFirstName'=>'TIMOTEO, JR.1?','pPatientSuffix'=>'.','pPatientMiddleName'=>'ABAYA1','pPatientBirthDate'=>'11-07-1966','pPatientSex'=>'M'),
							'children' => array()
						),
						array(
							'name' => 'CF2',
							'attributes' => array('pPatientReferred'=>"N",'pReferredIHCPAccreCode'=>"0",'pAdmissionDate'=>"03-02-2015",'pAdmissionTime'=>"06:15:00PM",'pDischargeDate'=>"03-02-2015",'pDischargeTime'=>"06:15:34PM",'pDisposition'=>"I",'pExpiredDate'=>"",'pExpiredTime'=>"",'pReferralIHCPAccreCode'=>"",'pReferralReasons'=>"",'pAccommodationType'=>"N"),
							'children' => array(
								array(
									'name' => 'DIAGNOSIS',
									'attributes' => array('pAdmissionDiagnosis'=>''),
									'children' => array(
										array(
											'name' => 'DISCHARGE',
											'attributes' => array('pDischargeDiagnosis' => 'NONE'),
											'children' => array(
												array(
													'name' => 'ICDCODE',
													'attributes' => array('pICDCode' => 'T51.2'),
													'children' => array()
												)
											)
										),
										array(
											'name' => 'DISCHARGE',
											'attributes' => array('pDischargeDiagnosis' => 'NONE'),
											'children' => array(
												array(
													'name' => 'RVSCODES',
													'attributes' => array(
														'pRVSCode' => '11000',
														'pRelatedProcedure' => 'DEBRIDEMENT OF EXTENSIVE ECZEMATOUS OR INFECTED SKIN',
														'pProcedureDate' => '03-02-2015',
														'pLaterality' => 'N'
													),
													'children' => array()
												)
											)
										)
									)
								),
								array(
									'name' => 'SPECIAL',
									'attributes' => array(),
									'children'=>array(
										array(
											'name' => 'PROCEDURES',
											'attributes' => array(),
											'children' => array(
												array(
													'name' => 'DEBRIDEMENT',
													'attributes' => array(),
													'children' => array(
														array(
															'name' => 'SESSIONS',
															'attributes' => array('pSessionDate' => '03-02-2015'),
															'children' => array()
														)
													)
												)
											)
										)
									)
								),
								array(
									'name' => 'PROFESSIONALS',
									'attributes' => array(
										'pDoctorAccreCode' => 'NONE',
										'pDoctorLastName' => 'NONE',
										'pDoctorFirstName' => 'NONE',
										'pDoctorMiddleName' => 'NONE',
										'pDoctorSuffix' => 'NONE',
										'pWithCoPay' => 'N',
										'pDoctorCoPay' => '0.00',
										'pDoctorSignDate' => 'NONE'
									),
									'children' => array()
								),
								array(
									'name' => 'CONSUMPTION',
									'attributes' => array('pEnoughBenefits'=>'Y'),
									'children' => array(
										array(
											'name' => 'BENEFITS',
											'attributes' => array('pTotalHCIFees'=>5300.00,'pTotalProfFees'=>'0.00','pGrandTotal'=>'5300.00'),
											'children' => array()
										)
									)
								)
							)//end CF2 children
						),//end CF2 Node
						array(
							'name' => 'ALLCASERATE',
							'attributes' => array(),
							'children' => array(
								array(
									'name' => 'CASERATE',
									'attributes' => array('pCaseRateCode'=>'CR1368','pICDCode'=>'','pRVSCode'=>'11000','pCaseRateAmount'=>'10540.00'),
									'children' => array()
								)
							)
						),
						array(
							'name' => 'DOCUMENTS',
							'attributes' => array(),
							'children' => array(
								array(
									'name' => 'DOCUMENT',
									'attributes' => array('pDocumentType'=>'CAB','pDocumentURL'=>'NONE'),
									'children' => array()
								)
							)
						)
					)//end CLAIM children
				),//end CLAIM Node
				array(
					'name' => 'CLAIM',
					'attributes' => array('pClaimNumber'=>'2015000305','pTrackingNumber'=>'','pPhilhealthClaimType'=>'ALL-CASE-RATE','pPatientType'=>'I','pIsEmergency'=>'N'),
					'children' => array(
						array(
							'name' => 'CF1',
							'attributes' => array('pMemberPIN'=>'13651151','pMemberLastName'=>'BANDOY1&amp;NTILDE','pMemberFirstName'=>'TIMOTEO, JR.1&amp;NTILDE','pMemberSuffix'=>'.','pMemberMiddleName'=>'ABAYA1&amp;NTILDE','pMemberBirthDate'=>'11-07-1966','pMemberShipType'=>'S','pEmployerName'=>'11','pPEN'=>'e1','pPatientIs'=>'M','pMailingAddress'=>'B/LUNGSOD12?ACACIA, DAVAO CITY 8000 DAVAO DEL SUR','pZipCode'=>'8000','pMemberSex'=>'M','pLandlineNo'=>'0','pMobileNo'=>'0','pEmailAddress'=>'sample@email.com','pPatientPIN'=>'13651151','pPatientLastName'=>'BANDOY1&amp;NTILDE','pPatientFirstName'=>'TIMOTEO, JR.1?','pPatientSuffix'=>'.','pPatientMiddleName'=>'ABAYA1?','pPatientBirthDate'=>'11-07-1966','pPatientSex'=>'M'),
							'children' => array()
						),
					)
				),
				array(
					'name' => 'CLAIM',
					'attributes' => array('pClaimNumber'=>'2015000304','pTrackingNumber'=>'','pPhilhealthClaimType'=>'ALL-CASE-RATE','pPatientType'=>'I','pIsEmergency'=>'N'),
					'children' => array()
				),
				array(
					'name' => 'CLAIM',
					'attributes' => array('pClaimNumber'=>'2015000304','pTrackingNumber'=>'','pPhilhealthClaimType'=>'ALL-CASE-RATE','pPatientType'=>'I','pIsEmergency'=>'N'),
					'children' => array()
				),
			)//end eTRANSMITTAL children
		)//end eTRANSMITTAL node
	)//end eCLAIMS children
);

$rules = array(
	'pPatientType' => array('in'=>array('I','O')),
	'pPatientBirthDate' => array('dateFormat'=>'F d, Y'),
	'pMemberLastName' => array('enye'),
	'eCLAIMS' => array(
		'attributes' => array('pUserName','pUserPassword','pHospitalCode','pHospitalEmail'),
		'hasOne' => array('eTRANSMITTAL')
	),
);

$xml = new Xml($data,'eCLAIMS','eClaimsDef_1.7.3.dtd',$rules);

$text = $xml->toString();

$errors = $xml->getErrors();

if(!empty($errors)){
	var_dump($xml->getErrors());
}else{
	header("Content-Type:text/xml");
	echo $text;
}