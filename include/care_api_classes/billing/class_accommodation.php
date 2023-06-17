<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's accommodation.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*	@date created:	June 14, 2007
*	@date updated:	Feb. 15, 2009
*
*****/
require_once('./roots.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_config.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');

class Accommodation {
	var $admission_dtetime;
	var $discharge_dtetime;
	var $n_days;
	var $n_hrs;
	var $room_nr;
	var $type_nr;
	var $type_desc;
	var $room_rate;
	var $source;
    var $mandatory_excess;
    var $accomodation_type; //added by jasper 04/11/2013

	function setAdmissionDteTime($admit_dte, $admit_time) {
		$this->admission_dtetime = strftime("%Y-%m-%d", strtotime($admit_dte)). ' '.strftime("%H:%M:%S",  strtotime($admit_time));
	}

	function setDischargeDteTime($discharge_dte, $discharge_time) {
		$this->discharge_dtetime = strftime("%Y-%m-%d", strtotime($discharge_dte)). ' '.strftime("%H:%M:%S", strtotime($discharge_time));
	}

	function getAdmissionDteTime() {
		return($this->admission_dtetime);
	}

	function getDischargeDteTime() {
		return($this->discharge_dtetime);
	}

	function setActualDays($n) {
		$this->n_days = $n;
	}

	function setExcessHrs($n) {
		$this->n_hrs = $n;
	}

	function getActualDays() {
		return($this->n_days);
	}

	function getExcessHrs() {
		return($this->n_hrs);
	}

	function setRoomNr($nRoomNr) {
		$this->room_nr = $nRoomNr;
	}

	function getRoomNr() {
		return($this->room_nr);
	}

	function setTypeNr($nTypeNr) {
		$this->type_nr = $nTypeNr;
	}

	function getTypeNr() {
		return($this->type_nr);
	}

	function setTypeDesc($sTypeDesc) {
		$this->type_desc = $sTypeDesc;
	}

	function getTypeDesc() {
		return($this->type_desc);
	}

	function setRoomRate($nRmRate) {
		$this->room_rate = $nRmRate;
	}

	function getRoomRate() {
		return($this->room_rate);
	}

	function setSource($src) {
		$this->source = $src;
	}

	function getSource() {
		return($this->source);
	}

    function setExcess($excess) {
        $this->mandatory_excess = $excess;
    }

    function getExcess() {
        return $this->mandatory_excess;
    }

    //added by jasper 04/11/2013
    function setAccomodationType($acmType) {
        $this->accomodation_type = $acmType;
    }

    function getAccomodationType() {
        return $this->accomodation_type;
    }
    //added by jasper 04/11/2013
}

class RoomTypeAccommodation {
	var $bill_dte;
	var $current_enr;
    var $prev_encounter_nr = '';
	var $type_nr;
	var $days_count;
	var $excess_hours;
	var $room_rate;
	var $total_coverage = 0;
	var $available_hplans;
	var $skedvalues;
	var $bPrincipal;
	var $source;
    var $mandatory_excess;
    var $accomodation_type; //added by jasper 04/11/2013
	var $days_defaulted = FALSE;

	var $cutoff_hrs = 0;

	function setCutoffHrs($n_hrs) {
		$this->cutoff_hrs = $n_hrs;
	}

	function setBillDte($b_dte) {
		$this->bill_dte = $b_dte;
	}

	function setCurrentEncounterNr($enr) {
		$this->current_enr = $enr;
	}

    function setPrevEncounterNr($enr) {
        $this->prev_encounter_nr = $enr;
    }

	function getCurrentEncounterNr() {
		return($this->current_enr);
	}

	function setTypeNr($nTypeNr) {
		$this->type_nr = $nTypeNr;
	}

	function getDaysCount() {
		return($this->days_count);
	}

	function setDaysCount($ndays) {
		$this->days_count = $ndays;
	}

	function setRoomRate($nRmRate) {
		$this->room_rate = $nRmRate;
	}

	function addRoomRate($nRmRate) {
		$this->room_rate = $this->getActualCharge() + $nRmRate;
		$this->days_count = 1;
		$this->excess_hours = 0;
		$this->days_defaulted = TRUE;
	}

	function getRoomRate() {
		return($this->room_rate);
	}

	function getExcessHours() {
		return($this->excess_hours);
	}

	function setExcessHours($nhrs) {
		$this->excess_hours = $nhrs ;
	}

	function setSource($src) {
		$this->source = $src;
	}

	function getSource() {
		return($this->source);
	}

    function setExcess($excess) {
        $this->mandatory_excess = $excess;
    }

    function getExcess() {
        return $this->mandatory_excess;
    }

	function isDaysDefaulted() {
		return($this->days_defaulted);
	}

	function getTotalCoverage() {
		return($this->total_coverage);
	}

    //added by jasper 04/11/2013
    function setAccomodationType($acmType) {
        $this->accomodation_type = $acmType;
    }

    function getAccomodationType() {
        return $this->accomodation_type;
    }
    //added by jasper 04/11/2013

	function getHCareSkedPerRoomType($nbsked_id, $nroom_type) {
		global $db;

/*		$strSQL = "select * from seg_hcare_roomtype ".
				  "   where hcare_id   = $nhcare_id and ".
				  "         benefit_id = $nbenefit_id and ".
				  "         roomtype_nr = $nroom_type";*/

		$strSQL = "select * from seg_hcare_roomtype ".
				  "   where bsked_id    = $nbsked_id and ".
				  "         roomtype_nr = $nroom_type";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$this->skedvalues = array();

				while ($row = $result->FetchRow()) {
					$this->skedvalues['rateperday']             = $row['rateperday'];
					$this->skedvalues['amountlimit']            = $row['amountlimit'];
					$this->skedvalues['dayslimit']              = $row['dayslimit'];
					$this->skedvalues['year_dayslimit']         = $row['year_dayslimit'];
					$this->skedvalues['year_dayslimit_alldeps'] = $row['year_dayslimit_alldeps'];
				}
			}
		}
	}

	function isPersonPrincipal() {
		global $db;

		$this->bPrincipal = false;
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select is_principal ".
				  "   from care_person_insurance as cpi inner join care_encounter as ce on cpi.pid = ce.pid ".
				  "   where encounter_nr = '".$this->current_enr."'".$filter;

		if ($result = $db->Execute($strSQL))
			if ($result->RecordCount())
				while ($row = $result->FetchRow()) {
					if ($row['is_principal'])
						$this->bPrincipal = true;
					else
						$this->bPrincipal = false;
				}

		return($this->bPrincipal);
	}

	function getPreviousConfineDays($nyear) {
		global $db;

		$ndays = 0;
        $filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or ce.encounter_nr = '$this->prev_encounter_nr'";
		if ($this->bPrincipal)
			$strSQL = "select sum(confine_days) as tdays ".
					  "   from seg_confinement_tracker as sct ".
					  "   where current_year = ". $nyear. " ".
					  "      and exists (select * from care_encounter as ce ".
					  "             where ce.pid = sct.pid and (ce.encounter_nr = '".$this->current_enr."'".$filter."))";
		else
			$strSQL = "select sum(confine_days) as tdays ".
					  "   from seg_confinement_tracker as sct1 ".
					  "   where exists (select principal_pid ".
					  "                    from seg_confinement_tracker as sct0 ".
					  "                    where current_year = ".$nyear." ".
					  "                       and exists (select * from care_encounter as ce ".
					  "                                   where ce.pid = sct0.pid and (ce.encounter_nr = '".$this->current_enr."'".$filter.")) ".
					  "                       and sct0.principal_pid = sct1.principal_pid) ".
					  "      and sct1.current_year = ".$nyear;

		if ($result = $db->Execute($strSQL))
			if ($result->RecordCount())
				while ($row = $result->FetchRow()) {
					$ndays = $row['tdays'];
				}

		return($ndays);
	}

	function computeTotalCoverage($n_drate = 0) {
	    global $db;

		$totalCoverage = 0;
		$bPerDay = false;
        $filter = '';

		// Select all benefits categorized in billable area 'AC' based on room type.
/*		$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
				  "   from ((care_insurance_firm as ci inner join ".
				  "         seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
				  "            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
				  "            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
				  "   where hb.bill_area = 'AC' and (bs.basis & 2) and si.encounter_nr = '".$this->current_enr."' ".
				  "      and exists (select * from seg_hcare_roomtype as sr ".
                  "                 where sr.hcare_id = ci.hcare_id and ".
                  "                    sr.benefit_id = hb.benefit_id and ".
				  "                    sr.roomtype_nr = ".$this->type_nr.") ".
				  "   order by priority";	*/

		if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
				  "   from ((care_insurance_firm as ci inner join ".
				  "            (select * from seg_hcare_bsked as shb ".
				  "                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
				  "                   and (shb.basis & 2) ".
				  "                   and (select max(effectvty_dte) as latest ".
				  "                           from seg_hcare_bsked as shb2 ".
				  "                           where shb2.hcare_id = shb.hcare_id ".
				  "                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
				  "            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
				  "            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
				  "   where hb.bill_area = 'AC' and (si.encounter_nr = '".$this->current_enr."'".$filter.") ".
				  "      and exists (select * from seg_hcare_roomtype as sr ".
                  "                 where sr.bsked_id = bs.bsked_id and ".
				  "                    sr.roomtype_nr = ".$this->type_nr.") ".
				  "   order by priority";

		if ($result = $db->Execute($strSQL)) {
            $this->available_hplans = array();

			if ($result->RecordCount()) {
				$actualdays = $this->days_count;

				while ($row = $result->FetchRow()) {
					$nhcare_id   = $row['hcare_id'];		// Insurance id
					$nbenefit_id = $row['benefit_id'];		// Health benefit id
					$nbsked_id   = $row['bsked_id'];		// Benefit schedule id

					$this->getHCareSkedPerRoomType($nbsked_id, $this->type_nr);

					$ndays = $this->days_count; 			// No. of days of stay.
					$nhrs  = $this->excess_hours;			// Excess hours.

					if (($ndays > $this->skedvalues['dayslimit']) && ($this->skedvalues['dayslimit'] > 0)) {
						$ndays = $this->skedvalues['dayslimit'];
						$nhrs  = 0;			// Cannot anymore cover for the extra hours ....
					}

					if ($actualdays <= $DAYS_IN_YEAR) {
						// Check if there is a limit in total number of days in a year for this benefit ....
						if (($this->skedvalues['year_dayslimit'] > 0) || ($this->skedvalues['year_dayslimit_alldeps'] > 0)) {
							// .... there is.
							$bPrincipalPID = $this->isPersonPrincipal();  // Check if admitted patient is principal insurance holder.
							$nprevdays = $this->getPreviousConfineDays(date('Y', $startdte));

							if ($bPrincipalPID)
								$nprevdays = $this->skedvalues['year_dayslimit'] - $nprevdays;
							else
								$nprevdays = $this->skedvalues['year_dayslimit_alldeps'] - $nprevdays;

							if ($nprevdays >= 0) {
								if ($ndays >= $nprevdays) {
									$ndays = $nprevdays;
									$nhrs  = 0;			// Cannot anymore cover for the extra hours ....
								}
							}
							else {
								$ndays = 0;
								$nhrs  = 0;			// Cannot anymore cover for the extra hours ....
							}
						}
					}

					$bPerDay = ($this->skedvalues['rateperday'] > 0);

					if ($nhrs > $this->cutoff_hrs) $ndays++;
					$nCoverage = $ndays * $this->skedvalues['rateperday'];

//					if ($nhrs > 0) {
//						$nCoverage += ($nhrs * ($this->skedvalues['rateperday'] / 24));
//					}

					if ((($nCoverage > $this->skedvalues['amountlimit']) && ($this->skedvalues['amountlimit'] > 0)) || ($this->skedvalues['rateperday'] == 0)) {
						$nCoverage = $this->skedvalues['amountlimit'];
						$bPerDay = false;
					}

					$nCharge = $this->getActualCharge * (1 - $n_drate);
					if ($nCoverage > $nCharge) $nCoverage = $nCharge;

					if ($nCoverage > 0) {
						$objCoverage = new HCareCoverage;

						$objCoverage->setID($nhcare_id);
						$objCoverage->setDesc($row['name']);
						$objCoverage->setCoverage($nCoverage);
						$objCoverage->setDaysCovered($ndays);

						$this->available_hplans[] = $objCoverage;

						$totalCoverage += $nCoverage;
					}
				}	// while loop
			}	// if ... else ... RecordCount()
		}	// if ... else ... Execute()

		$this->total_coverage = $totalCoverage;

		return($totalCoverage);
	}		// function getTotalCoverage

	function getActualCharge() {
		$ndays = $this->days_count;
		$nhrs  = $this->excess_hours;

		if ($nhrs > $this->cutoff_hrs) $ndays++;
		$ncharge = $ndays * $this->room_rate;

//		if ($nhrs > $this->cutoff_hrs) {
//			$ncharge += ($nhrs * ($this->room_rate / 24));
//		}

		// Return actual charge ...
		return($ncharge);
	}
}
?>
