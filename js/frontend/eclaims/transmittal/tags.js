/**
 * [eclaims description]
 *
 * @author Alvin Quinones <ajmquinones@gmail,com>
 *
 */
var eclaims = eclaims || {};
eclaims.transmittal = eclaims.transmittal || {};

/**
 * This tag schema was generated using eclaims/transmit/generateTagSchema
 * based on the PhilHealth eClaims DTD. Please update accordingly...
 *
 * @type {Object}
 */
eclaims.transmittal.tags = {
    '!top': ["eCLAIMS"],
    "eCLAIMS": {
        "children": ["eTRANSMITTAL"],
        "attrs": {
            "pUserName": null,
            "pUserPassword": null,
            "pHospitalCode": null,
            "pHospitalEmail": null
        }
    },
    "eTRANSMITTAL": {
        "children": ["CLAIM"],
        "attrs": {
            "pHospitalTransmittalNo": null,
            "pTotalClaims": null
        }
    },
    "CLAIM": {
        "children": ["CF1", "CF2", "ALLCASERATE", "ZBENEFIT", "CF3", "PARTICULARS", "RECEIPTS", "DOCUMENTS"],
        "attrs": {
            "pClaimNumber": null,
            "pTrackingNumber": null,
            "pCataractPreAuth": null,
            "pPhilhealthClaimType": ["ALL", "CASE", "RATE", "Z", "BENEFIT"]
        }
    },
    "CF1": {
        "attrs": {
            "pMemberPIN": null,
            "pMemberLastName": null,
            "pMemberFirstName": null,
            "pMemberSuffix": null,
            "pMemberMiddleName": null,
            "pMemberBirthDate": null,
            "pMemberShipType": ["S", "G", "I", "NS", "NO", "PS", "PG"],
            "pMailingAddress": null,
            "pZipCode": null,
            "pMemberSex": ["M", "F"],
            "pLandlineNo": null,
            "pMobileNo": null,
            "pEmailAddress": null,
            "pPatientIs": ["M", "S", "C", "P"],
            "pPatientPIN": null,
            "pPatientLastName": null,
            "pPatientFirstName": null,
            "pPatientSuffix": null,
            "pPatientMiddleName": null,
            "pPatientBirthDate": null,
            "pPatientSex": ["M", "F"],
            "pPEN": null,
            "pEmployerName": null
        }
    },
    "CF2": {
        "children": ["DIAGNOSIS", "SPECIAL", "PROFESSIONALS", "CONSUMPTION"],
        "attrs": {
            "pPatientReferred": ["Y", "N"],
            "pReferredIHCPAccreCode": null,
            "pAdmissionDate": null,
            "pAdmissionTime": null,
            "pDischargeDate": null,
            "pDischargeTime": null,
            "pDisposition": ["I", "R", "H", "A", "E", "T"],
            "pExpiredDate": null,
            "pExpiredTime": null,
            "pReferralIHCPAccreCode": null,
            "pReferralReasons": null,
            "pAccommodationType": ["P", "N"]
        }
    },
    "DIAGNOSIS": {
        "children": ["DISCHARGE"],
        "attrs": {
            "pAdmissionDiagnosis": null
        }
    },
    "DISCHARGE": {
        "children": ["ICDCODE", "RVSCODES"],
        "attrs": {
            "pDischargeDiagnosis": null
        }
    },
    "ICDCODE": {
        "attrs": {
            "pICDCode": null
        }
    },
    "RVSCODES": {
        "attrs": {
            "pRelatedProcedure": null,
            "pRVSCode": null,
            "pProcedureDate": null,
            "pLaterality": ["L", "R", "B", "N"]
        }
    },
    "SPECIAL": {
        "children": ["PROCEDURES", "MCP", "TBDOTS", "ABP", "NCP", "HIVAIDS"]
    },
    "PROCEDURES": {
        "children": ["HEMODIALYSIS", "PERITONEAL", "LINAC", "COBALT", "TRANSFUSION", "BRACHYTHERAPHY", "CHEMOTHERAPY", "DEBRIDEMENT", "HEMODIALYSIS", "PERITONIAL", "LINAC", "COBALT", "TRANSFUSION", "BRACHYTHERAPHY", "CHEMOTHERAPY", "DEBRIDEMENT"]
    },
    "HEMODIALYSIS": {
        "children": ["SESSIONS"]
    },
    "PERITONEAL": {
        "children": ["SESSIONS"]
    },
    "LINAC": {
        "children": ["SESSIONS"]
    },
    "COBALT": {
        "children": ["SESSIONS"]
    },
    "TRANSFUSION": {
        "children": ["SESSIONS"]
    },
    "BRACHYTHERAPHY": {
        "children": ["SESSIONS"]
    },
    "CHEMOTHERAPY": {
        "children": ["SESSIONS"]
    },
    "DEBRIDEMENT": {
        "children": ["SESSIONS"]
    },
    "SESSIONS": {
        "attrs": {
            "pSessionDate": null
        }
    },
    "MCP": {
        "attrs": {
            "pCheckUpDate1": null,
            "pCheckUpDate2": null,
            "pCheckUpDate3": null,
            "pCheckUpDate4": null
        }
    },
    "TBDOTS": {
        "attrs": {
            "pTBType": ["I", "M"],
            "pNTPCardNo": null
        }
    },
    "ABP": {
        "attrs": {
            "pDay0ARV": null,
            "pDay3ARV": null,
            "pDay7ARV": null,
            "pRIG": null,
            "pABPOthers": null,
            "pABPSpecify": null
        }
    },
    "NCP": {
        "attrs": {
            "pEssentialNewbornCare": ["Y", "N"],
            "pNewbornHearingScreeningTest": ["Y", "N"],
            "pNewbornScreeningTest": ["Y", "N"],
            "pFilterCardNo": null
        }
    },
    "ESSENTIAL": {
        "attrs": {
            "pDrying": ["Y", "N"],
            "pSkinToSkin": ["Y", "N"],
            "pCordClamping": ["Y", "N"],
            "pProphylaxis": ["Y", "N"],
            "pWeighing": ["Y", "N"],
            "pVitaminK": ["Y", "N"],
            "pBCG": ["Y", "N"],
            "pNonSeparation": ["Y", "N"],
            "pHepatitisB": ["Y", "N"]
        }
    },
    "HIVAIDS": {
        "attrs": {
            "pLaboratoryNumber": null
        }
    },
    "PROFESSIONALS": {
        "attrs": {
            "pDoctorAccreCode": null,
            "pDoctorLastName": null,
            "pDoctorFirstName": null,
            "pDoctorMiddleName": null,
            "pDoctorSuffix": null,
            "pWithCoPay": ["Y", "N"],
            "pDoctorCoPay": null
        }
    },
    "CONSUMPTION": {
        "children": ["BENEFITS", "HCIFEES", "PROFFEES", "PURCHASES"],
        "attrs": {
            "pEnoughBenefits": ["Y", "N"]
        }
    },
    "BENEFITS": {
        "attrs": {
            "pTotalHCIFees": null,
            "pTotalProfFees": null,
            "pGrandTotal": null
        }
    },
    "HCIFEES": {
        "attrs": {
            "pTotalActualCharges": null,
            "pDiscount": null,
            "pPhilhealthBenefit": null,
            "pTotalAmount": null,
            "pMemberPatient": ["Y", "N"],
            "pHMO": ["Y", "N"],
            "pOthers": ["Y", "N"]
        }
    },
    "PROFFEES": {
        "attrs": {
            "pTotalActualCharges": null,
            "pDiscount": null,
            "pPhilhealthBenefit": null,
            "pTotalAmount": null,
            "pMemberPatient": ["Y", "N"],
            "pHMO": ["Y", "N"],
            "pOthers": ["Y", "N"]
        }
    },
    "PURCHASES": {
        "attrs": {
            "pDrugsMedicinesSupplies": ["Y", "N"],
            "pDMSTotalAmount": null,
            "pExaminations": ["Y", "N"],
            "pExamTotalAmount": null
        }
    },
    "ALLCASERATE": {
        "children": ["CASERATE"]
    },
    "CASERATE": {
        "attrs": {
            "pCaseRateCode": null,
            "pICDCode": null,
            "pRVSCode": null
        }
    },
    "ZBENEFIT": {
        "attrs": {
            "pZBenefitCode": ["Z0011", "Z0012", "Z0013", "Z0021", "Z0022", "Z003", "Z0041", "Z0042", "Z0051", "Z0052", "Z0061", "Z0062", "Z0071", "Z0072", "Z0081", "Z0082", "Z0091", "Z0092"]
        }
    },
    "CF3": {
        "children": ["CF3_OLD", "CF3_NEW"]
    },
    "CF3_OLD": {
        "children": ["PHEX", "MATERNITY"],
        "attrs": {
            "pChiefComplaint": null,
            "pBriefHistory": null,
            "pCourseWard": null,
            "pPertinentFindings": null
        }
    },
    "MATERNITY": {
        "children": ["PRENATAL", "DELIVERY", "POSTPARTUM"]
    },
    "PRENATAL": {
        "children": ["CLINICALHIST", "OBSTETRIC", "MEDISURG", "CONSULTATION"],
        "attrs": {
            "pPrenatalConsultation": null,
            "pMCPOrientation": ["Y", "N"],
            "pExpectedDeliveryDate": null
        }
    },
    "CLINICALHIST": {
        "attrs": {
            "pVitalSigns": ["Y", "N"],
            "pPregnancyLowRisk": ["Y", "N"],
            "pLMP": null,
            "pMenarcheAge": null,
            "pObstetricG": null,
            "pObstetricP": null,
            "pObstetric_T": null,
            "pObstetric_P": null,
            "pObstetric_A": null,
            "pObstetric_L": null
        }
    },
    "OBSTETRIC": {
        "attrs": {
            "pMultiplePregnancy": ["Y", "N"],
            "pOvarianCyst": ["Y", "N"],
            "pMyomaUteri": ["Y", "N"],
            "pPlacentaPrevia": ["Y", "N"],
            "pMiscarriages": ["Y", "N"],
            "pStillBirth": ["Y", "N"],
            "pPreEclampsia": ["Y", "N"],
            "pEclampsia": ["Y", "N"],
            "pPrematureContraction": ["Y", "N"]
        }
    },
    "MEDISURG": {
        "attrs": {
            "pHypertension": ["Y", "N"],
            "pHeartDisease": ["Y", "N"],
            "pDiabetes": ["Y", "N"],
            "pThyroidDisaster": ["Y", "N"],
            "pObesity": ["Y", "N"],
            "pAsthma": ["Y", "N"],
            "pEpilepsy": ["Y", "N"],
            "pRenalDisease": ["Y", "N"],
            "pBleedingDisorders": ["Y", "N"],
            "pPreviousCS": ["Y", "N"],
            "pUterineMyomectomy": ["Y", "N"]
        }
    },
    "CONSULTATION": {
        "attrs": {
            "pVisitDate": null,
            "pAOGWeeks": null,
            "pWeight": null,
            "pCardiacRate": null,
            "pRespiratoryRate": null,
            "pBloodPressure": null,
            "pTemperature": null
        }
    },
    "DELIVERY": {
        "attrs": {
            "pDeliveryDate": null,
            "pDeliveryTime": null,
            "pObstetricIndex": null,
            "pAOGLMP": null,
            "pDeliveryManner": null,
            "pPresentation": null,
            "pFetalOutcome": null,
            "pSex": null,
            "pBirthWeight": null,
            "pAPGARScore": null,
            "pPostpartum": null
        }
    },
    "POSTPARTUM": {
        "attrs": {
            "pPerinealWoundCare": ["Y", "N"],
            "pPerinealRemarks": null,
            "pMaternalComplications": ["Y", "N"],
            "pMaternalRemarks": null,
            "pBreastFeeding": ["Y", "N"],
            "pBreastFeedingRemarks": null,
            "pFamilyPlanning": ["Y", "N"],
            "pFamilyPlanningRemarks": null,
            "pPlanningService": ["Y", "N"],
            "pPlanningServiceRemarks": null,
            "pSurgicalSterilization": ["Y", "N"],
            "pSterilizationRemarks": null,
            "pFollowupSchedule": ["Y", "N"],
            "pFollowupScheduleRemarks": null
        }
    },
    "CF3_NEW": {
        "children": ["ADMITREASON", "COURSE"],
        "attrs": {
            "pPatientType": ["I", "O"]
        }
    },
    "ADMITREASON": {
        "children": ["CLINICAL", "LABDIAG", "PHEX"],
        "attrs": {
            "pBriefHistory": null,
            "pReferredReason": null,
            "pIntensive": ["Y", "N"],
            "pMaintenance": ["Y", "N"]
        }
    },
    "CLINICAL": {
        "attrs": {
            "pCriteria": null
        }
    },
    "LABDIAG": {
        "attrs": {
            "pCriteria": null
        }
    },
    "PHEX": {
        "attrs": {
            "pBP": null,
            "pCR": null,
            "pRR": null,
            "pTemp": null,
            "pHEENT": null,
            "pChestLungs": null,
            "pCVS": null,
            "pAbdomen": null,
            "pGUIE": null,
            "pSkinExtremities": null,
            "pNeuroExam": null
        }
    },
    "COURSE": {
        "children": ["WARD"]
    },
    "WARD": {
        "attrs": {
            "pCourseDate": null,
            "pFindings": null,
            "pAction": null
        }
    },
    "PARTICULARS": {
        "children": ["DRGMED", "XLSO", "DRGMED", "XLSO"]
    },
    "DRGMED": {
        "attrs": {
            "pPurchaseDate": null,
            "pDrugCode": null,
            "pPNDFCode": null,
            "pGenericName": null,
            "pBrandName": null,
            "pPreparation": null,
            "pQuantity": null
        }
    },
    "XLSO": {
        "attrs": {
            "pDiagnosticDate": null,
            "pDiagnosticType": ["IMAGING", "LABORATORY", "SUPPLIES", "OTHERS"],
            "pDiagnosticName": null,
            "pQuantity": null
        }
    },
    "RECEIPTS": {
        "children": ["RECEIPT"]
    },
    "RECEIPT": {
        "children": ["ITEM"],
        "attrs": {
            "pCompanyName": null,
            "pCompanyTIN": null,
            "pBIRPermitNumber": null,
            "pReceiptNumber": null,
            "pReceiptDate": null,
            "pVATExemptSale": null,
            "pVAT": null,
            "pTotal": null
        }
    },
    "ITEM": {
        "attrs": {
            "pQuantity": null,
            "pUnitPrice": null,
            "pDescription": null,
            "pAmount": null
        }
    },
    "DOCUMENTS": {
        "children": ["DOCUMENT"]
    },
    "DOCUMENT": {
        "attrs": {
            "pDocumentType": ["CAB", "CAE", "CF1", "CF2", "CF3", "CSF", "COE", "CTR", "DTR", "MBC", "MDR", "MEF", "MMC", "MSR", "MWV", "NTP", "OPR", "ORS", "PAC", "PBC", "PIC", "POR", "SOA", "STR", "TCC", "TYP"],
            "pDocumentURL": null
        }
    }
}