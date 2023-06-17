<?php
require("./roots.php");
class SegRequestSource {


	var $tb_type_request_src = "seg_type_request_source";

	function SegRequestSource()
	{
		$this->coretable = $this->tb_type_request_src;
	}

	function getAllRequestSource()
	{
		global $db;
		$this->sql = "SELECT id, source_name FROM $this->tb_type_request_src ORDER BY source_name ASC ";
		$this->result = $db->Execute($this->sql);
		if($this->result!==FALSE) {
			return $this->result;
		} else {
			$this->error_msg = $db->ErrorMsg();
			return FALSE;
		}
	}

	public static function getSourceNursingWard()
	{
		return "WARD";
	}

	public static function getSourceIPDClinics()
	{
		return "IPD";
	}

	public static function getSourcePHSClinics()
	{
		return "PHS";
	}

	public static function getSourceOPDClinics()
	{
		return "OPD";
	}

	public static function getSourceERClinics()
	{
		return "ER";
	}

	public static function getSourceLaboratory()
	{
		return "LD";
	}

	public static function getSourceSpecialLab()
	{
		return "SPL";
	}

	public static function getSourceBloodBank()
	{
		return "BB";
	}

	public static function getSourceRadiology()
	{
		return "RD";
	}

	public static function getSourceDialysis()
	{
		return "RDU";
	}

	public static function getSourceIndustrialClinic()
	{
		return "IC";
	}

	public static function getSourceOR()
	{
		return "OR";
	}

	public static function getSourceInpatientPharmacy()
	{
		return "IP";
	}

	public static function getSourceMurangGamot()
	{
		return "MG";
	}

	public static function getSourceDoctor()
	{
		return "DOCTOR";
	}

}
