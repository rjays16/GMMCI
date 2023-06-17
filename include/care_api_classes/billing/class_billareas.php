<?php
	class ACBill {
			var $days_count;
			var $excess_hours;
			var $accommodation_hist;
			var $acc_roomtype_benefits;
			var $acc_confine_benefits;
			var $acc_confine_coverage;
			var $bPrincipal;

			function makeObject($objBilling) {
				$this->acc_confine_benefits = array_merge((array)$this->acc_confine_benefits, (array)$objBilling->acc_confine_benefits);
				$this->acc_roomtype_benefits = array_merge((array)$this->acc_roomtype_benefits, (array)$objBilling->acc_roomtype_benefits);
				$this->accommodation_hist = array_merge((array)$this->accommodation_hist, (array)$objBilling->accommodation_hist);
				$this->bPrincipal = $objBilling->bPrincipal;
				$this->acc_confine_coverage = $objBilling->acc_confine_coverage;
				$this->days_count = $objBilling->days_count;
				$this->excess_hours = $objBilling->excess_hours;
			}

			function assignBillObject(&$objBilling) {
				$objBilling->acc_confine_benefits = array();
				$objBilling->acc_roomtype_benefits = array();
				$objBilling->accommodation_hist = array();

				$objBilling->acc_confine_benefits = array_merge((array)$objBilling->acc_confine_benefits, (array)$this->acc_confine_benefits);
				$objBilling->acc_roomtype_benefits = array_merge((array)$objBilling->acc_roomtype_benefits, (array)$this->acc_roomtype_benefits);
				$objBilling->accommodation_hist = array_merge((array)$objBilling->accommodation_hist, (array)$this->accommodation_hist);
				$objBilling->bPrincipal = $this->bPrincipal;
				$objBilling->acc_confine_coverage = $this->acc_confine_coverage;
				$objBilling->days_count = $this->days_count;
				$objBilling->excess_hours = $this->excess_hours;
			}
	}

	class MDBill {
			var $medicines_list;
			var $med_product_benefits;
			var $med_confine_benefits;
			var $med_confine_coverage;
			var $total_med_charge;

			function makeObject($objBilling) {
				$this->medicines_list = array_merge((array)$this->medicines_list, (array)$objBilling->medicines_list);
				$this->med_product_benefits = array_merge((array)$this->med_product_benefits, (array)$objBilling->med_product_benefits);
				$this->med_confine_benefits = array_merge((array)$this->med_confine_benefits, (array)$objBilling->med_confine_benefits);
				$this->med_confine_coverage = $objBilling->med_confine_coverage;
				$this->total_med_charge = $objBilling->total_med_charge;
			}

			function assignBillObject(&$objBilling) {
				$objBilling->medicines_list = array();
				$objBilling->med_product_benefits = array();
				$objBilling->med_confine_benefits = array();

				$objBilling->medicines_list = array_merge((array)$objBilling->medicines_list, (array)$this->medicines_list);
				$objBilling->med_product_benefits = array_merge((array)$objBilling->med_product_benefits, (array)$this->med_product_benefits);
				$objBilling->med_confine_benefits = array_merge((array)$objBilling->med_confine_benefits, (array)$this->med_confine_benefits);
				$objBilling->med_confine_coverage = $this->med_confine_coverage;
				$objBilling->total_med_charge = $this->total_med_charge;
			}
	}

	class HSBill {
			var $services_list;
			var $hsp_service_benefits;
			var $srv_confine_benefits;
			var $srv_confine_coverage;
			var $total_srv_charge;

			function makeObject($objBilling) {
				$this->services_list = array_merge((array)$this->services_list, (array)$objBilling->services_list);
				$this->hsp_service_benefits = array_merge((array)$this->hsp_service_benefits, (array)$objBilling->hsp_service_benefits);
				$this->srv_confine_benefits = array_merge((array)$this->srv_confine_benefits, (array)$objBilling->srv_confine_benefits);
				$this->srv_confine_coverage = $objBilling->srv_confine_coverage;
				$this->total_srv_charge = $objBilling->total_srv_charge;
			}

			function assignBillObject(&$objBilling) {
				$objBilling->services_list = array();
				$objBilling->hsp_service_benefits = array();
				$objBilling->srv_confine_benefits = array();

				$objBilling->services_list = array_merge((array)$objBilling->services_list, (array)$this->services_list);
				$objBilling->hsp_service_benefits = array_merge((array)$objBilling->hsp_service_benefits, (array)$this->hsp_service_benefits);
				$objBilling->srv_confine_benefits = array_merge((array)$objBilling->srv_confine_benefits, (array)$this->srv_confine_benefits);
				$objBilling->srv_confine_coverage = $this->srv_confine_coverage;
				$objBilling->total_srv_charge = $this->total_srv_charge;
			}
	}

	class OPBill {
			var $ops_list;
			var $total_RVU;
			var $hsp_ops_benefits;
			var $ops_confine_benefits;
			var $ops_confine_coverage;

			function makeObject($objBilling) {
				$this->ops_list = array_merge((array)$this->ops_list, (array)$objBilling->ops_list);
				$this->hsp_ops_benefits = array_merge((array)$this->hsp_ops_benefits, (array)$objBilling->hsp_ops_benefits);
				$this->ops_confine_benefits = array_merge((array)$this->ops_confine_benefits, (array)$objBilling->ops_confine_benefits);
				$this->ops_confine_coverage = $objBilling->ops_confine_coverage;
				$this->total_RVU = $objBilling->total_RVU;
			}

			function assignBillObject(&$objBilling) {
				$objBilling->ops_list = array();
				$objBilling->hsp_ops_benefits = array();
				$objBilling->ops_confine_benefits = array();

				$objBilling->ops_list = array_merge((array)$objBilling->ops_list, (array)$this->ops_list);
				$objBilling->hsp_ops_benefits = array_merge((array)$objBilling->hsp_ops_benefits, (array)$this->hsp_ops_benefits);
				$objBilling->ops_confine_benefits = array_merge((array)$objBilling->ops_confine_benefits, (array)$this->ops_confine_benefits);
				$objBilling->ops_confine_coverage = $this->ops_confine_coverage;
				$objBilling->total_RVU = $this->total_RVU;
			}
	}

	class PFBill {
			var $proffees_list;
			var $hsp_pfs_benefits;
			var $pfs_confine_benefits;
			var $pfs_confine_coverage;

			function makeObject($objBilling) {
				$this->proffees_list = array_merge((array)$this->proffees_list, (array)$objBilling->proffees_list);
				$this->hsp_pfs_benefits = array_merge((array)$this->hsp_pfs_benefits, (array)$objBilling->hsp_pfs_benefits);
				$this->pfs_confine_benefits = array_merge((array)$this->pfs_confine_benefits, (array)$objBilling->pfs_confine_benefits);
				$this->pfs_confine_coverage = $objBilling->pfs_confine_coverage;
			}

			function assignBillObject(&$objBilling) {
				$objBilling->proffees_list = array();
				$objBilling->hsp_pfs_benefits = array();
				$objBilling->pfs_confine_benefits = array();

				$objBilling->proffees_list = array_merge((array)$objBilling->proffees_list, (array)$this->proffees_list);
				$objBilling->hsp_pfs_benefits = array_merge((array)$objBilling->hsp_pfs_benefits, (array)$this->hsp_pfs_benefits);
				$objBilling->pfs_confine_benefits = array_merge((array)$objBilling->pfs_confine_benefits, (array)$this->pfs_confine_benefits);
				$objBilling->pfs_confine_coverage = $this->pfs_confine_coverage;
			}
	}

	class XCBill {
			var $msc_chrgs_list;
			var $hsp_msc_benefits;
			var $msc_confine_benefits;
			var $msc_confine_coverage;

			function makeObject($objBilling) {
				$this->msc_chrgs_list = array_merge((array)$this->msc_chrgs_list, (array)$objBilling->msc_chrgs_list);
				$this->hsp_msc_benefits = array_merge((array)$this->hsp_msc_benefits, (array)$objBilling->hsp_msc_benefits);
				$this->msc_confine_benefits = array_merge((array)$this->msc_confine_benefits, (array)$objBilling->msc_confine_benefits);
				$this->msc_confine_coverage = $objBilling->msc_confine_coverage;
			}

			function assignBillObject(&$objBilling) {
				$objBilling->msc_chrgs_list = array();
				$objBilling->hsp_msc_benefits = array();
				$objBilling->msc_confine_benefits = array();

				$objBilling->msc_chrgs_list = array_merge((array)$objBilling->msc_chrgs_list, (array)$this->msc_chrgs_list);
				$objBilling->hsp_msc_benefits = array_merge((array)$objBilling->hsp_msc_benefits, (array)$this->hsp_msc_benefits);
				$objBilling->msc_confine_benefits = array_merge((array)$objBilling->msc_confine_benefits, (array)$this->msc_confine_benefits);
				$objBilling->msc_confine_coverage = $this->msc_confine_coverage;
			}
	}
?>
