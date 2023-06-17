<?php
$LDAdmission='Admission';
$LDAdmitDate='Admission Date';
$LDAdmitTime='Admission Time';
#$LDCaseNr='Admission Number';
$LDCaseNr='Case Number';
$LDTitle='Title';
$LDLastName='Family Name';
$LDFirstName='Given Name';
$LDBday='Date of Birth';
$LDPhone='Phone';
$LDAdmitBy='Admitted By';
$LDSex='Sex';
$LDMale='Male';
$LDFemale='Female';
$LDAddress='Address';
$LDAmbulant='Outpatient';
#$LDStationary='Inpatient';
#--------added by Vanessa----------
#$LDAdmissionOPD = 'Outpatient Consultation';   # burn commented : May 15, 2007
$LDSearchPromptCons = 'Enter a search key (case number, name (last name,first name), or encounter date (MM/DD/YYYY)).';   
	# $LDSearchPromptCons added by pet, april 18, 2008; given name deleted aug.8, 2008
$LDOPDConsultation = 'Outpatient Consultation';   # burn added : May 15, 2007
$LDERConsultation = 'ER Consultation';   # burn added : May 15, 2007
$LDConsultationData='Consultation Data';   # burn added : May 15, 2007
$LDConsultation = "Consultation";   # burn added : May 15, 2007
$LDConsultDate='Consultation Date';   # burn added : May 15, 2007
$LDConsultTime='Consultation Time';   # burn added : May 15, 2007
$LDDirectAdmission = 'Direct Admission';   # burn added : May 24, 2007
$LDInpatientDirectAdmission = 'Inpatient (Direct Admission)';   # burn added : May 24, 2007

$LDAdmissionER = 'Another Encounter for ER';
$LDDept = 'Department';
$LDStationary='ER';
$LDAmbulant2 = 'Inpatient from OPD';
$LDStationary2='Inpatient from ER';
$LDInformant = 'Informant\'s Name';
$LDInfoAdd = 'Informant\'s Address';
$LDInfoRelation = 'Relation To Patient';

#added by VAN 04-28-08
$LDMedico = 'Medico Legal';

$LDRecIns = 'Referrer Institution';
$LDPlsCheckAdmit = 'Pls. check the checkbox if the patient will be admitted.';
$LDPlsSelectCond = 'Pls. select the condition of the patient.';
$LDPlsSelectDisp = 'Pls. select the disposition of the patient.';
$LDPlsSelectRes = 'Pls. select the result of the patient.';
$LDCondition = 'Condition at ER';
$LDResults = 'Results';
$LDDisposition = 'Disposition';
$LDCons1 = 'Conscious';
$LDCons2 = 'Semi-Conscious';
$LDCons3 = 'Unconscious';
#------------------
$LDSelfPay='Self pay';
$LDPrivate='Private Insurance';
$LDInsurance='Health Fund';
$LDDiagnosis='Diagnosis';
$LDRecBy='Referred by';
$LDRecDept='Referrer Department';
$LDTherapy='Therapy';
$LDSpecials='Referrer notes';
#------add 02-26-07----------------
$LDDoctor1 = 'Consulting Physician';
$LDDoctor2 = 'Admitting Physician';
$LDDoctor3 = 'Attending Physician';
$LDPlsSelectDr = 'Please select a physician.';
$LDPlsEnterInsuranceNo='Please enter the Insurance number';
$LDPlsEnterInsuranceCo='Please enter or select a Insurance company name';
#$LDIPDWantEntry='I need to admit a patient in ER';
#edited by VAN 04-16-08
$LDIPDWantEntry='I need to enter a registered patient to ER Consultation';
#$LDOPDWantEntry='I need to admit a patient for Consultation';
$LDOPDWantEntry='I need to enter a registered patient to OPD Consultation';
#------add 02-26-07----------------

$LDOccupation='Occupation';

#added by genz
$segContactNo='Contact No';


$LDPatientSearch='Search patient\'s data';
$LDAdmit='Admission';
$LDSearch='Search';
#$LDArchive='Archive';
#edited by VAN 04-17-08
$LDArchive='Advanced Search';
$LDCatPls='I would like to see the cat, please!';
$LDGoodMorning='Good Morning!';
$LDGoodDay='Hi! Nice to see you!';
$LDGoodAfternoon='Good afternoon!';
$LDGoodEvening='Good Evening';

$LDNewForm='I need an empty form please.';

$LDForceSave='Save anyway';
$LDSaveData='Save data';
$LDResetData='Reset data';
$LDReset='Reset';
$LDSave='Save';
$LDCancel='Cancel';

$LDCancelClose='Cancel and back to start page';
$LDCloseWin='Close admission window';
$LDError='Information is missing in the input field marked <font color=red>red</font>!';
$LDErrorS='Some information are missing in the input fields marked with <font color=red>red</font>!';

$fieldname=array('Patient no.','Lastname','Firstname','Date of birth','Options');
//$LDEntryPrompt='Enter a search key (given name, family name, or date of birth)';
$LDEntryPrompt='Enter a search key (e.g., family name or date of birth)';	
//given name deleted in the text above to suit VAS' search code changes; pet, aug.5,2008
$LDSEARCH='SEARCH';
$LDAdmWantEntry='I need to admit a patient';
$LDAdmWantSearch='I am looking for a patient';
$LDAdmWantArchive='I need to research in the archive';

/**************** note the ' ~nr~ ' must not be erased it will be replaced by the script with the number of search results ******/
$LDSearchFound='The search found <font color=red><b>~no.~</b></font> relevant data.';

$LDShowData='Show data';
$LDPatientData='Admission Data';
$LDBack2Admit='Back to admission';
$LDBack2Search='Back to search';
$LDBack2Archive='Back to archive';

$LDFrom='from';
$LDTo='to';
$LDUpdateData='Update data';
$LDNewArchive='New research in archive';
$LDAdmArchive='Admission - Archive';

/************** note: do not erase the ' ~nr~ ' it will be replaced by the script with a number **************/
$LDFoundData='I found ~no.~ relevant data!<br>Please click the right one.';

$LDClk2Show='Click to show the data';

$LDElements=array(
								'',
								'Lastname',
								'Firstname',
								'Date of birth',
								'Patient no.',
								'Admission date'
								);
$LDSearchKeyword='Search keyword or condition';
$LDMEDOCS='Medical Documentation System (Medocs)';
$LDMedocsSearchTitle='Medocs - Document search';
$LDHideCat='Click to hide the cat';
$LDNewDocu='Document the following patient';
$LDExtraInfo='Extra information';
$LDMedAdvice='Medical Advice';
#$LDMedocs='Medocs';
$LDMedocs='Medical History';

$LDYes='Yes';
$LDNo='No';

$LDEditOn='Documented on';
$LDEditBy='Documented by';
$LDKeyNr='Key number';
$LDDocSearch='Search a medocs document';

$LDMedDocOf='Medocs document of';
$LDMedocsElements=array(
								'',
								'Lastname',
								'Firstname',
								'Date of birth',
								'Patient No.',
								'Document No.',
								'Department',
								'Date',
								'Time'
								);
$LDStartNewDoc='Start a new medocs document';
$LDNoMedocsFound='No medocs document of the patient found!';
$LDAt='at';		
		
#$LDDept='Dept';
$LDRoomNr='Room No.';
$LDAdmitType='Admission Type';		
$LDCivilStat='Civil Status';
$LDInsuranceNr='Insurance No.';

#--added by VAN
$LDInsuranceList='Insurances';

$LDNameAddr='Name & Address';
$LDBillInfo='Billing info';
$LDAdmitDiagnosis='Admission diagnosis';
$LDInfo2='Info to';
$LDPrintDate='Print date';
$LDReligion='Religion';
$LDTherapyType='Therapy type';
$LDTherapyOpt='Therapy option';
$LDServiceType='Service type';

$LDClick2Print='Click the barcode labels to print';

$LDEnterDiagnosisNote='Attach links to diagnosis related notes & publications:';
$LDEnterTherapyNote='Attach links to therapy related notes & publications:';
$LDSeeDiagnosisNote='Diagnosis related notes & publications:';
$LDSeeTherapyNote='Therapy related notes & publications:';
$LDMakeBarcodeLabels='Make barcode labels';

$LDPlsEnterDept='<b>Please enter your department, clinic, or work area.</b><br>(e.g. PLOP, Internal Med2, or M4A, etc.)';
$LDOkSaveNow='OK save now';

$LD_ddpMMpyyyy='dd.mm.yyyy';
$LD_yyyyhMMhdd='yyyy-mm-dd';
$LD_MMsddsyyyy='mm/dd/yyyy';
/* 2002-10-13 EL */
$LDPlsSelectPatientFirst='Please find the patient first.';
/* 2002-11-30 EL */
$LDPatientRegister='Person Registration';
$LDRegDate='Registration Date';
$LDRegTime='Registration Time';
$LDRegBy='Registered By';
$LDName2='Second Name';
$LDName3='Third Name';
$LDNameMid='Middle Name';
$LDNameMaiden='Maiden Name';
$LDNameOthers='Other Names';
$LDStreet='Street';
$LDStreetNr='No.';
$LDTownCity='Town/City';
$LDProvState='Province/State';
$LDRegion='Region';
$LDCountry='Country';
#$LDCitizenship='Citizenship';
#edited by VAN 04-17-08
$LDCitizenship='Country of Nationality';
$LDCivilStatus='Civil Status'; /* Civil status = married, single, divorced, widow */
$LDCivilStatOther=' Specific civil status stated: ';	//added by pet for death certificate, 06-21-08
$LDCivilStatSel=' Pls. select a specific civil status: ';	//added by pet for death certificate, 06-21-08

#added by VAN 04-26-08
$LDChild = 'Child';

$LDSingle='Single';
$LDMarried='Married';
$LDDivorced='Divorced';
$LDWidowed='Widowed';
$LDSeparated='Separated';
$LDCellPhone='Cellphone';
$LDFax='Fax';
$LDEmail='Email';
$LDZipCode='Zip';
$LDPhoto='Photo';
/* 2002-12-02 EL*/
$LDPatientRegisterTxt='Register patient, search registrations, archive research';
#$LDAdmitNr='Admission No.';
$LDAdmitNr='Case No.';
$LDAdmitNr2='Patient ID';
$LDPatient='Patient';
$LDVisit='Visit';
$LDVisitTxt='Ambulatory or outpatient admission';
$LDAdmissionTxt='Inpatient admission, search, research';
$LDImmunization='Immunization';
$LDESE='Enter, search, edit';
$LDImmunizationTxt=$LDESE.' immunization report';
#$LDDRG='DRG (composite)';
$LDDRG='ICD 10 / ICPM';
$LDDRGTxt=$LDESE.' DRG (Diagnosis related groups)';
$LDProcedures='Procedures';
$LDProceduresTxt=$LDESE.' therapy procedures';
$LDPrescriptions='Prescriptions';
$LDPrescriptionsTxt=$LDESE.' Prescriptions';
/* 2002-12-03 EL*/
$LDDiagXResults='Diagnostic Results';
$LDDiagXResultsTxt='Search, research, display diagnostic results or reports';
$LDAppointments='Appointments';
$LDAppointmentsTxt=$LDESE.', research appointments or schedules';
$LDPatientDev='Development';
$LDPatientDevTxt=$LDESE.', display reports on patient\'s development';
$LDWtHt='Weights & Heights';
$LDWtHtTxt=$LDESE.' weight, height & head circumference';
$LDPregnancies='Pregnancies';
$LDPregnanciesTxt=$LDESE.' pregnancy information';
$LDBirthDetails='Birth Details';
$LDBirthDetailsTxt=$LDESE.' birth details';
/* 2002-12-07 EL*/
$LDInsuranceBurn='Insurance';  # burn added: August 30, 2006
$LDInsuranceClass='Insurance Class';  # burn added: August 30, 2006

$LDInsuranceCo='Insurance Company';
$LDInsuranceNr_2='Extra Insurance No.';
$LDInsuranceCo_2='Extra Insurance Co.';
$LDBillType='Billing Type';
$LDWard='Ward/Station';
$LDMakeWristBand='Make wristbands';
$LDClickImgToPrint='Click the image to print out.';
$LDPrintPortraitFormat='Set your printer to landscape format.';
/* 2002-12-14 EL */
$LDRegistryNr='HRN';
$LDRedirectToRegistry='Note: Your search will be redirected to the registration module!';
/* 2002-12-24 EL */
$LDSSSNr='SSS No.';
$LDNatIdNr='National ID No.';
$LDEthnicOrigin='Ethnic Origin';
$LDOtherNr='Other Number(s)';
/* 2002-12-25 EL */
$LDSendBill='Send bill to';
$LDContactPerson='Contact person';
$LDOptsForPerson='Options for this person';
$LDSickReport='Confirmation of inability to work';
$LDAnamnesisForm='Anamnesis form';
$LDConsentDec='Consent declaration';
$LDUpdate='Update';
/* 2002-12-29 EL */
$LDGuarantor='Guarantor';
$LDCareServiceClass='Care service class';
$LDRoomServiceClass='Room service class';
$LDAttDrServiceClass='Medical service class';
$LDAdmitClass='Admission class';
/* 2003-02-15 EL*/
$LDEnterSearchKeyword='Please enter a search key.';
$LDSearchFoundData='The search found <font color=red><b>~no.~</b></font> relevant data.';
$LDQuickList='Quicklist';
$LDSeveralInsurances='Patient has several insurances. Click here to edit.';
$LDTop='Top';
$LDInsuranceClass='Insurance class';
$LDRecordsHistory='DB Record\'s History';
/* 2003-02-16 EL*/
$LDNotYetAdmitted='Not yet admitted';
$LDPatientCurrentlyAdmitted='Patient is currently admitted!';
$LDOptions='Options';
/** note the ' ~nr~ ' must not be erased it will be replaced by the script with the number of search results ******/
$LDSearchFoundAdmit='I found <font color=red><b>~nr~</b></font> relevant admission data.';
$LDPatientNr='Patient Nr.';
$LDNoRecordYet='~tag~ has no ~obj~ yet.';
$LDNoRecordFor='No ~obj~ record for ~tag~ yet.';
$LDRegistrationNr='Registration No.';
$LDDate='Date';
$LDType='Type';
$LDMedicine='Medicine';
$LDTiter='Titer';
$LDRefreshDate='Refresh date';
$LDReportingDept='Reporting Dept';
$LDReportNr='Report Nr.';
$LDDelivery='Delivery';
$LDTime='Time';
$LDClass='Class';
$LDOutcome='Outcome';
$LDNrOfFetus='No. of Fetuses';
$LDDetails='Details';
/* 2003-03-02 */
$LDDosage='Dosage';
$LDAppType='Application type';
$LDAppBy='Application by';
$LDNotes='Notes';
$LDEnterNewRecord='Enter new record';
$LDPrescription='Prescription';
$LDDrugClass='Drug Class';
$LDPrescribedBy='Prescribed by';
$LDPharmOrderNr='Pharmacy Order Number';
#$LDEncounterNr='Encounter No.';
$LDEncounterNr='Case No.';	#edited by pet, 2008-04-18, for consistency
$LDValue='Value';
$LDUnit='Unit';
$LDWeight='Weight';
$LDHeight='Height';
$LDMeasuredBy='Measured by';
$LDSickUntil='Unable to work until (inclusive)';
$LDStartingFrom='Starting from';
$LDConfirmedOn='Confirmed on';
$LDInsurersCopy='Insurer\'s copy';
$LDDiagnosis2='Diagnosis';
/* 2003-03-03*/
$LDBy='By';
$LDSendCopyTo='Send copy to';
/* 2003-03-05 EL*/
$LDAndSym='&';
$LDReports='Reports';
$LDRefererDiagnosis='Referer Diagnosis';
$LDRefererRecomTherapy='Referer recommended therapy';
$LDShortNotes='Short Notes';
/* 2003-03-08 EL */
$LDCreateNewAppointment='Create new appointment';
$LDDepartment='Department';
$LDRemindPatient='Remind patient';
$LDRemindBy='Remind by';
$LDMail='Mail';
$LDPurpose='Purpose';
$LDClinician='Clinician';
$LDPhysician='Physician';
$LDBackToOptions='Back to options';
$LDStatus='Status';
/* 2003-03-08 EL*/
$LDUrgency='Urgency';
$LDNormal='Normal';
$LDPriority='Priority';
$LDUrgent='Urgent';
$LDEmergency='Emergency';
/* 2003-03-09 EL*/
$LDCancelReason='Reason for cancellation';
$LDSureCancelAppt='Are you sure you want to cancel this appointment?';
$LDEnterCancelReason='Enter the reason for cancellation.';
$LDpending='pending';
$LDcancelled='cancelled';
/* 2003-03-10 EL */
$LDGotMedAdvice='Did the patient receive medical advice?';
/* 2003-03-15 EL */
$LDShowDocList='Show document list';
$LDScheduleNewAppointment='Schedule New Appointment';
/* 2003-04-04 EL */
$LDNoPendingApptThisDay='There is no pending appointment for this day.';
$LDNoPendingApptToday='There is no pending appointment today.';
/* 2003-04-27 EL */
#$LDOptsForPatient='Options for this patient';
#edited by VAN 02-12-08
$LDOptsForPatient='Options for this person';
/* 2003-05-06 EL */
$LDRegisterNewPerson='Register a new person';
/* 2003-05-17 EL */
//$LDEnterPersonSearchKey='Enter a search key (PID no., given name, family name, or date of birth).';
$LDEnterPersonSearchKey='Enter a search key (Health Record Number, Family Name, or Date of Birth).';
$LDPersonData='Personal data';
/* 2003-05-26 EL*/
$LDDiagnoses='Diagnoses';
$LDCreateNewForm='Create a form for';
$LDOtherRecords='Other records';
/*2003-06-17 El*/
$LDFullForm='Full form';
$LDAllContents='All contents';
$LDAllText='Dynamic contents only';
$LDDataOnly='Encounter relevant data only';
/*2003-06-21 EL*/
$LDChartsRecords='Charts folder';
# 2003-07-26 EL
$LDMode='Mode';
$LDPatientIsDischarged='This patient is already discharged.';
$LDShow='Show';
$LDPlannedEncType='Planned admission type';
# 2003-08-01 EL
#$LDListEncounters='Encounters\' list';
$LDListEncounters='List of Case Nos.';	#edited by pet, 2008-04-18, for consistency
$LDDischarged='Discharged';
$LDDischargeDate='Discharge date';
# 2003-08-04 EL
$LDCancelThisAdmission='Cancel this admission';
$LDInsShortID[1]='PRIV';  // privately paid insurance
$LDInsShortID[2]='COM'; // Common state sponsored insurance
$LDInsShortID[3]='SP';    // self pay, direct pay
# 2003-08-26 EL
$LDMeasurements='Measurements';
#2003-08-28 eL
$LDPlsEnterReferer='Please enter referring physician.';
$LDPlsEnterRefererDiagnosis='Please enter referral diagnosis.';
$LDPlsEnterRefererTherapy='Please enter referrer\'s recommended therapy.';
$LDPlsEnterRefererNotes='Please enter referrer\'s notes.';
$LDPlsSelectAdmissionType='Please select admission type.';
$LDForInpatient='For inpatient';
$LDForOutpatient='For outpatient';
#2003-09-18 EL
$LDPersonSearch='Search a person.';
#2003-09-24 EL
$LDShowing='Showing';
$LDPrevious='Previous';
$LDNext='Next';
$LDAdvancedSearch='Advanced Search';

#added by VAN 06-20-08
$LDComprehensiveSearch = 'Comprehensive Search';
#$LDAdmission = 'Admission Date';
$LDPatType = 'Patient Type';
$LDLocation = 'Location';
$LDDetails = 'Details';

#2003-10-28 EL
$LDIncludeFirstName='Search for given names too.';
$LDTipsTricks='Tips & tricks';
#2003-12-06 EL
$LDPrintPDFDoc='Make PDF document';
$LDDeathDate='Death date';

# 2003-10-14 NDHC
$LDITA='Intratracheal anesthesia';
$LDLA='Local anesthesia';
$LDAS='Analgesic sedation';
$LDOral='Oral';
$LDAnticoagulant='Anticoagulant';
$LDHemolytic='Hemolytic';
$LDDiuretic='Diuretic';
$LDAntibiotic='Antibiotic';
$LDMask='Mask';
$LDIntravenous='Intravenous';
$LDSubcutaneous='Subcutaneous';
$LDPreAdmission='Pre-admission';
#2004-01-01 EL
$LDPersonDuplicate='This person seems to be registered already.';
$LDSimilarData='The following listed person has similar personal data.';
$LDSimilarData2='The following listed persons have similar personal data.';
$LDPlsCheckFirst='Please check it out first before you decide the next step.';
$LDPlsCheckFirst2='Please check them out first before you decide the next step.';
$LDShowDetails='Show details';

# 2004-05-22 KB
$LDNr='No.';
$LDOtherHospitalNr='Other Hospital No.';
$LDSelectOtherHospital = 'Select other hospital to change the number';
$LDNoNrNoDelete = 'no number = delete';

# 2007-02-21 segworks
$segIcdCode='ICD10 code';
$segIcpmCode='ICPM code';
#$segIcpmDesc='Icpm Description';
$segIcpmDesc='Procedure';
# 2007-03-02 burn
$segBirthplace='Place of Birth';

# 2007-03-09 burn
$segMotherName="Mother's Name";
$segFatherName="Father's Name";
$segSpouseName="Spouse's Name";
$segGuardianName="Guardian's Name";
$segFamilyBackground='Family Background:';

#added by VAN 05-19-08
$segFName="First Name";
$segMName="Middle Name";
$segLName="Last Name";

#added by VAN 05-01-08
$segEmployer = "Employer";
/*
					lang_en_place.php 
*/
$LDPlace='Place';
$LDAddress='Address';
$LDStreet='Street';
$LDStreetNr='Street Nr.';
$LDZipCode='ZIP Code';
$LDProvince='Province';
$LDCity='City';
$LDTown='Town';
$LDCityTown='City/Town';
$LDRegion='Region';
$LDCountry='Country';
$LDData='Data';
$LDNewData='New data';
$LDNewDataTxt='Enter new address data';
$LDUpdateData='Update data';
$LDListAll='List all';
$LDManager='Manager';
$LDNeedEmptyFormPls='I need an empty form please.';
$LDEnterAllFields='Please fill up all fields marked with <font color="red"><b>*</b></font>';
$LDPlsCheckData='Please check the data.';
$LDPlsEnterInfo='Please enter the information';
$LDListAllTxt='List all available address data';
$LDSearch='Search';
$LDSearchTxt='Search for an address data';
$LDNewCityTown='New City/Town';
#$LDPlsEnterInfo='Please enter the information';
$LDAlertNoCityTownName='The city/town\'s name is missing.';
$LDCityTownName='City/Town\'s Name';
$LDISOCountryCode='ISO Country Code';
$LDUNECELocalCode='UNECE Location Code';
$LDUNECELocalCodeType='UNECE Location Code Type';
$LDUNECEModifier='UNECE Modifier';
$LDUNECECoordinates='UNECE Coordinates';
$LDWebsiteURL='Info Website URL';
$LDCityTownExists='The given city/town\'s name is already existing.';
$LDDataNoSave='The entered data cannot be saved.';
$LDPlsChangeName='Please enter a different name.';
$LDAddressNameExists='The address data is already existing.';
$LDAddressInfoSaved='The address data was successfully saved.';
$LDSearchPrompt='Please enter an address data or local code.';
#2004-09-02
$LDWrongUneceLocCode = 'UNECE location code type accepts only numbers between 0 and 99.';
$LDEnterZero = 'If you do not know the value, please enter 0.';
$LDEnterISOCountryCode = 'Please enter the ISO country code.';
$LDEnterQMark ='If you do not know the code, please enter a question mark (?).';

# 2007-03-14
$segCitizenship='Country of Nationality';

# 2007-02-20
# Address Menu
$segAddressMenu='Address Menu';
$segRegionMngr='Region Manager';
$segRegionMngrTxt='Enter, list, edit, & update Region data';
$segProvinceMngr='Province Manager';
$segProvinceMngrTxt='Enter, list, edit, & update Province data';
$segMuniCtyMngr='Municipality/City Manager';
$segMuniCityMngrTxt='Enter, list, edit, & update Municipality/City data';
$segBrgyMngr='Barangay Manager';
$segBryMngrTxt='Enter, list, edit, & update Barangay data';

# 2007-02-20
# Manager

$segHouseNoStreet='House No./Street';

# $LDTown
$segBrgy='Barangay';
# $LDListAllTxt
$segBrgyListAllTxt='List all available barangay data';
# $LDNewDataTxt
$segBrgyNewDataTxt='Enter new barangay data';
# $LDListAllTxt
$segBrgyListAllTxt='List all available barangay data';
# $LDSearchTxt
$segBrgySearchTxt='Search for a barangay data';
# $LDNewCityTown
$segNewBrgy='New Barangay';
# $LDAlertNoCityTownName
$segAlertNoBrgyName='The barangay\'s name is missing.';
# $LDCityTownName
$segBrgyName='Barangay\'s Name';
# $LDCityTownExists
$segBrgyExists='The given barangay\'s name is already existing in the same municipality/city.';
# $LDAddressInfoSaved
$segBrgyInfoSaved='The barangay data was successfully saved.';
# $LDSearchPrompt
$segBrgySearchPrompt='Please enter a barangay\'s name.';


$segWrongZipCode = 'ZIP Code accepts only numbers.';
$segWrongZipCodeLength = 'ZIP Code must be AT LEAST 4 digits.';
$segZipCodeExists='The given municipality/city\'s zipcode is already '.
						'<br> assigned to another municipality/city.';
# $LDTown
$segMunicipality='Municipality';
# $LDCityTown
$segMuniCity='Municipality/City';
# $LDListAllTxt
$segMuniCityListAllTxt='List all available municipality/city data';
# $LDNewDataTxt
$segMuniCityNewDataTxt='Enter new municipality/city data';
# $LDListAllTxt
$segMuniCityListAllTxt='List all available municipality/city data';
# $LDSearchTxt
$segMuniCitySearchTxt='Search for a municipality/city data';
# $LDNewCityTown
$segNewMuniCity='New Municipality/City';
# $LDAlertNoCityTownName
$segAlertNoMuniCityName='The municipality/city\'s name is missing.';
# $LDCityTownName
$segMuniCityName='Municipality/City\'s Name';
# $LDCityTownExists
$segMuniCityExists='The given municipality/city\'s name is already existing in the same province.';
# $LDAddressInfoSaved
$segMuniCityInfoSaved='The municipality/city data was successfully saved.';
# $LDSearchPrompt
$segMuniCitySearchPrompt='Please enter a municipality/city\'s name.';

# $LDTown
$segProvince='Province';
# $LDListAllTxt
$segProvinceListAllTxt='List all available province data';
# $LDNewDataTxt
$segProvinceNewDataTxt='Enter new province data';
# $LDListAllTxt
$segProvinceListAllTxt='List all available province data';
# $LDSearchTxt
$segProvinceSearchTxt='Search for a province data';
# $LDNewCityTown
$segNewProvince='New Province';
# $LDAlertNoCityTownName
$segAlertNoProvinceName='The province\'s name is missing.';
# $LDCityTownName
$segProvinceName='Province\'s Name';
# $LDCityTownExists
$segProvinceExists='The given province\'s name is already existing in the same region.';
# $LDAddressInfoSaved
$segProvinceInfoSaved='The province data was successfully saved.';
# $LDSearchPrompt
$segProvinceSearchPrompt='Please enter a province\'s name.';

$segRegionShortName='Region\'s Short Name';
$segAlertNoRegionShortName='The region\'s short name is missing.';
$segRegionShortNameExists='The given region\'s short name is already existing.';
# $LDTown
$segRegion='Region';
# $LDListAllTxt
$segRegionListAllTxt='List all available region data';
# $LDNewDataTxt
$segRegionNewDataTxt='Enter new region data';
# $LDListAllTxt
$segRegionListAllTxt='List all available region data';
# $LDSearchTxt
$segRegionSearchTxt='Search for a region data';
# $LDNewCityTown
$segNewRegion='New Region';
# $LDAlertNoCityTownName
$segAlertNoRegionName='The region\'s name is missing.';
# $LDCityTownName
$segRegionName='Region\'s Name';
# $LDCityTownExists
$segRegionExists='The given region\'s name is already existing.';
# $LDAddressInfoSaved
$segRegionInfoSaved='The region data was successfully saved.';
# $LDSearchPrompt
$segRegionSearchPrompt='Please enter a region\'s name.';

/*
$LDTown='Town';
$LDCityTown='City/Town';
$LDSearchTxt='Search for an address data';
$LDNewCityTown='New City/Town';
$LDAlertNoCityTownName='The city/town\'s name is missing.';
$LDCityTownName='City/Town\'s Name';
$LDCityTownExists='The given city/town\'s name is already existing.';
$LDAddressNameExists='The address\'s is already existing.';
$LDAddressInfoSaved='The address data was successfully saved.';
$LDSearchPrompt='Please enter an address\' name or local code';
*/
?>