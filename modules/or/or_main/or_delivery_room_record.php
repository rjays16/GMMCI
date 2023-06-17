<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_ward.php'); //load the ward class   

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

    class OR_Delivery_Record extends RepGen {
    
     var $pid;
     var $encounter_nr;
     var $discount_id;
     var $date;
		 var $or_refno;
     
    function OR_Delivery_Record($refno)
    {
        global $db;
        $this->RepGen("DELIVERY ROOM RECORD");
        $this->colored = TRUE;
        #$this->ColumnWidth = array(36,20,60,50,15,20);
        $this->RowHeight = 6;
        #$this->Alignment = array('L','L','L','L','R','C');
        $this->PageOrientation = "P";
        if ($this->colored)    $this->SetDrawColor(0xDD);
        $this->or_refno = $refno;
        #echo $this->or_refno;
    }
    
    function Header()
    {
        global $root_path, $db;
        $objInfo = new Hospital_Admin();
    
        if ($row = $objInfo->getAllHospitalInfo()) {      
          $row['hosp_agency'] = strtoupper($row['hosp_agency']);
          $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
        else {
          $row['hosp_country'] = "Republic of the Philippines";
          $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
          $row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL";
          $row['hosp_addr1']   = "Bukidnon Province";      
        }

        $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,15);  
        $this->SetFont("Arial","I","9");
        $total_w = 165;
        $this->Cell(17,4);
        $this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');
        $this->Cell(17,4);
        $this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
        $this->Ln(2);
        $this->SetFont("Arial","B","10");
        $this->Cell(17,4);
        $this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
        $this->SetFont("Arial","","9");
        $this->Cell(17,4);
        $this->Cell($total_w,4, $row['hosp_addr1'],$border2,1,'C');
        $this->Ln(4);
        $this->SetFont('Arial','B',12);
        $this->Cell(17,5);
        $this->Cell($total_w,4,"DELIVERY ROOM RECORD",$border2,1,'C');
        $this->SetFont('Arial','B',9);
        $this->Cell(17,4);    
        $this->Ln(10);   
        $this->SetTextColor(0);
        
        $this->PrintData();        
    }
    
    function Footer()
    {
        $this->SetY(-23);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }

    function BeforeData() 
    {
        if ($this->colored) {
            $this->DrawColor = array(0xDD,0xDD,0xDD);
        }
        $this->ColumnFontSize = 9;
    }
    
    function BeforeCellRender() 
    {
        $this->FONTSIZE = 8;
        if ($this->colored) {
            if (($this->RENDERPAGEROWNUM%2)>0) 
                $this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
            else
                $this->RENDERCELL->FillColor=array(255,255,255);
        }
    }
    
    function AfterData() 
    {
        global $db;
        
        /*if (!$this->_count) {
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255);
            $this->SetTextColor(0);
            $this->Cell(201, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
        }
        
        $cols = array(); */
    }
    
    function PrintData() 
    {        
      global $db;
      $seg_ops = new SegOps();
      $sql = "select * from seg_or_delivery where refno=".$db->qstr($this->or_refno);
      $result = $db->Execute($sql);
      $row1 = $result->FetchRow();
      $query = "SELECT sos.refno, sos.encounter_nr, cr.info as op_room, sos.pid, cp.name_last, cp.name_first, cp.name_middle,
               CAST(fn_calculate_age(date_birth, NOW()) AS SIGNED INT) AS age, ce.admission_dt, ce.consulting_dr as physician, cp.blood_group as blood_type
               FROM seg_ops_serv sos
               INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
               INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room)
               INNER JOIN care_person cp ON (cp.pid=sos.pid)
               INNER JOIN care_encounter ce ON (ce.encounter_nr=sos.encounter_nr) WHERE sos.refno=".$db->qstr($this->or_refno);
     $result = $db->Execute($query);
     $row2 = $result->FetchRow();
     
      
      //print name
      $this->SetFont('Arial','B',11);
      $this->SetXY(10,40);
      $this->Cell(14, 4, "NAME ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(24,40); 
			$this->Cell(2, 4, $row2['name_last'].",".$row2['name_first']." ".$row2['name_middle'], "", 0, '');
			$this->Line(24, 45, 85, 45);
			
			//print age
			$this->SetXY(86,40);
			$this->SetFont('Arial','B',11);
      $this->Cell(11, 4, "AGE ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(97,40);
			$this->Cell(2, 4, $row2['age'], "", 0, '');
			$this->Line(97, 45, 110, 45);
			
			//print bed #
			$this->SetXY(111,40);
			$this->SetFont('Arial','B',11);
      $this->Cell(18, 4, "BED NO ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(129,40);
			#$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(129, 45, 143, 45);
			
			//print hospital #
			$this->SetXY(144,40);
			$this->SetFont('Arial','B',11);
      $this->Cell(29, 4, "HOSPITAL NO ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(173,40);
			$this->Cell(2, 4, $row2['pid'], "", 0, '');
			$this->Line(173, 45, 210, 45);
			
			//print physician
			$this->SetXY(10,48);
			$this->SetFont('Arial','B',11);
      $this->Cell(47, 4, "ATTENDING PHYSICIAN ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(57,48);
			$this->Cell(2, 4, $row2['physician'], "", 0, '');
			$this->Line(57, 53, 115, 53);
			
			//print date admission
			$this->SetXY(116,48);
			$this->SetFont('Arial','B',11);
      $this->Cell(34, 4, "DATE ADMITTED ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(150,48);
			$this->Cell(2, 4, date("M j,Y H:i:s",strtotime($row2['admission_dt'])), "", 0, '');
			$this->Line(150, 53, 210, 53);
			
			//print labor hours
			$this->SetXY(10,56);
			$this->SetFont('Arial','B',11);
      $this->Cell(37, 4, "HOURS IN LABOR ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(47,56);
			$this->Cell(2, 4, $row1['labor_duration_hour'], "", 0, '');
			$this->Line(47, 61, 76, 61);
			
			//print date confinement
			$this->SetXY(77,56);
			$this->SetFont('Arial','B',11);
      $this->Cell(72, 4, "ESTIMATED DATE OF CONFINEMENT ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(149,56);
			$this->Cell(2, 4, date("M j,Y H:i:s",strtotime($row1['date_confinement'])), "", 0, '');
			$this->Line(149, 61, 210, 61);
			
			//print gavida
			$this->SetXY(10,64);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "GRAVIDA ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(30,64);
			$this->Cell(2, 4, $row1['gravida'], "", 0, '');
			$this->Line(30, 69, 42, 69);
			
			//print para
			$this->SetXY(43,64);
			$this->SetFont('Arial','B',11);
      $this->Cell(14, 4, "PARA ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(57,64);
			$this->Cell(2, 4, $row1['para'], "", 0, '');
			$this->Line(57, 69, 69, 69);
			
			//print abortion
			$this->SetXY(70,64);
			$this->SetFont('Arial','B',11);
      $this->Cell(23, 4, "ABORTION ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(93,64);
			$this->Cell(2, 4, $row1['abortion'], "", 0, '');
			$this->Line(93, 69, 105, 69);
			
			//print prenatal care
			$this->SetXY(106,64);
			$this->SetFont('Arial','B',11);
      $this->Cell(82, 4, "PRENATAL CARE(YES OR NO)SEROLOGY ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(188,64);
			if($row1['prenatal_care'])
				$this->Cell(2, 4, "Yes", "", 0, '');
			else
				$this->Cell(2, 4, "No", "", 0, '');
			$this->Line(188, 69, 210, 69);
			
			//print blood type
			$this->SetXY(10,72);
			$this->SetFont('Arial','B',11);
      $this->Cell(28, 4, "BLOOD TYPE ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(38,72);
			$this->Cell(2, 4, $row1['blood_type'], "", 0, '');
			$this->Line(38, 77, 52, 77);
			
			//print kh
			$this->SetXY(53,72);
			$this->SetFont('Arial','B',11);
      $this->Cell(8, 4, "KH ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(61,72);
			#$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(61, 77, 73, 77);
			
			//print Complication of pregnancy
			$this->SetXY(74,72);
			$this->SetFont('Arial','B',11);
      $this->Cell(65, 4, "COMPLICATION OF PREGNANCY ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(139,72);
			#$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(139, 77, 210, 77);
			$this->SetXY(10,80);
			$this->Cell(2, 4, $row1['pregnancy_complications'], "", 0, '');
			$this->Line(10, 85, 210, 85);
			
			//print heart
			$this->SetXY(10,88);
			$this->SetFont('Arial','B',11);
      $this->Cell(16, 4, "HEART ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(27,88);
			$this->Cell(2, 4, $row1['heart'], "", 0, '');
			$this->Line(27, 93, 48, 93);
			
			//print lungs
			$this->SetXY(49,88);
			$this->SetFont('Arial','B',11);
      $this->Cell(16, 4, "LUNGS ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(67,88);
			$this->Cell(2, 4, $row1['lungs'], "", 0, '');
			$this->Line(65, 93, 86, 93);
			
			//print blood pressure
			$this->SetXY(87,88);
			$this->SetFont('Arial','B',11);
      $this->Cell(8, 4, "BP ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(95,88);
			$this->Cell(2, 4, $row1['bp_1'], "", 0, '');
			$this->Line(95, 93, 120, 93);
			
			//print pulse
			$this->SetXY(121,88);
			$this->SetFont('Arial','B',11);
      $this->Cell(16, 4, "PULSE ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(137,88);
			$this->Cell(2, 4, $row1['pulse_1'], "", 0, '');
			$this->Line(137, 93, 162, 93);
			
			//print general condition
			$this->SetXY(10,96);
			$this->SetFont('Arial','B',11);
      $this->Cell(44, 4, "GENERAL CONDITION ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',12);
			$this->SetXY(62,96);
			if($row1['general_condition']=='good')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(55, 101, 69, 101);
			
			//print general condition - good
			$this->SetXY(70,96);
			$this->SetFont('Arial','B',11);
      $this->Cell(14, 4, "GOOD ", "", 0, 'L');
			$this->SetFont('Arial','',12);
			$this->SetXY(90,96);
			if($row1['general_condition']=='fair')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(83, 101, 98, 101);
			
			//print general condition - fair
			$this->SetXY(99,96);
			$this->SetFont('Arial','B',11);
      $this->Cell(14, 4, "FAIR ", "", 0, 'L');
			$this->SetFont('Arial','',12);
			$this->SetXY(117,96);
			if($row1['general_condition']=='critical')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(110, 101, 125, 101);
			
			//print general condition - critical
			$this->SetXY(126,96);
			$this->SetFont('Arial','B',11);
      $this->Cell(22, 4, "CRITICAL ", "", 0, 'L');
			$this->SetFont('Arial','',12);
			$this->SetXY(153,96);
			if($row1['general_condition']=='febrile')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(146, 101, 161, 101);
			
			//print general condition - febrile
			$this->SetXY(162,96);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "FEBRILE ", "", 0, 'L');
			/*$this->SetFont('Arial','',12);
			$this->SetXY(181,96);
			if($row1['general_condition']=='febrile')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(181, 101, 200, 101);*/
			
			//print general condition - morbid
			$this->SetFont('Arial','',12);
			$this->SetXY(62,104);
			if($row1['general_condition']=='morbid')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(55, 109, 69, 109);
			$this->SetFont('Arial','B',11);
			$this->SetXY(70,104);
      $this->Cell(20, 4, "MORBID ", "", 0, 'L');
      
      //print general condition - others
			$this->SetFont('Arial','',12);
			$this->SetXY(88,104);
			if($row1['general_condition']=='others')
				$this->Cell(2, 4, $row1['general_condition_others'], "", 0, '');
			$this->Line(88, 109, 162, 109);
			$this->SetFont('Arial','B',11);
			$this->SetXY(163,104);
      $this->Cell(20, 4, "OTHERS ", "", 0, 'L');
      
      //print membrane ruptured
			$this->SetXY(10,112);
			$this->SetFont('Arial','B',11);
      $this->Cell(48, 4, "MEMBRANE RUPTURED ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',12);
			$this->SetXY(65,112);
			if($row1['membrane_ruptured']=='spontaneous')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(58, 117, 73, 117);
			
			//print membrane ruptured - spontaneous
			$this->SetXY(74,112);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "SPONTANEOUS ", "", 0, 'L');
			$this->SetFont('Arial','',12);
			$this->SetXY(112,112);
			if($row1['membrane_ruptured']=='cervix dilates')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(105, 117, 120, 117);
			
			//print membrane ruptured - cervix dilates
			$this->SetXY(121,112);
			$this->SetFont('Arial','B',11);
      $this->Cell(45, 4, "CERVIX DILATES ", "", 0, 'L');
			$this->SetFont('Arial','',11);
			$this->SetXY(154,112);
			$this->Cell(2, 4, $row1['cervix_cm'], "", 0, '');
			$this->Line(154, 117, 169, 117);
			$this->SetXY(170,112);
			$this->SetFont('Arial','B',11);
      $this->Cell(6, 4, "cm ", "", 0, 'L');
      
      //print cm - premature
      $this->SetFont('Arial','',12);
			$this->SetXY(182,112);
			if($row1['cervix_condition']=='premature')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(176, 117, 188, 117);
			$this->SetXY(188,112);
			$this->SetFont('Arial','B',11);
      $this->Cell(24, 4, "PREMATURE ", "", 0, 'L');
      
      //print cm - early
      $this->SetFont('Arial','',12);
			$this->SetXY(182,116);
			if($row1['cervix_condition']=='early')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(176, 122, 188, 122);
			$this->SetXY(188,118);
			$this->SetFont('Arial','B',11);
      $this->Cell(24, 4, "EARLY ", "", 0, 'L');
      
      //print membrane ruptured - artificial
      $this->SetFont('Arial','',11);
			$this->SetXY(65,122);
			if($row1['membrane_ruptured']=='artificial')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(58, 128, 73, 128);
			$this->SetXY(74,124);
			$this->SetFont('Arial','B',11);
      $this->Cell(24, 4, "ARTIFICIAL ", "", 0, 'L');
      
      //print cm - late
      $this->SetFont('Arial','',12);
			$this->SetXY(182,122);
			if($row1['cervix_condition']=='late')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(176, 128, 188, 128);
			$this->SetXY(188,124);
			$this->SetFont('Arial','B',11);
      $this->Cell(24, 4, "LATE ", "", 0, 'L');
      
      //print labor onset
      $this->SetXY(10,132);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "LABOR ONSET ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			
			//print labor onset - induced
			$this->SetFont('Arial','',11);
			$this->SetXY(47,132);
			if($row1['labor_onset']=='induced')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(40, 137, 55, 137);
      $this->SetXY(56,132);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "INDUCED", "", 0, 'L');
      
      //print labor onset - spontaneous
			$this->SetFont('Arial','',11);
			$this->SetXY(82,132);
			if($row1['labor_onset']=='spontaneous')
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(75, 137, 90, 137);
      $this->SetXY(91,132);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "SPONTANEOUS", "", 0, 'L');
      
      //print date of onset
			$this->SetXY(130,132);
			$this->SetFont('Arial','B',11);
      $this->Cell(33, 4, "DATE OF ONSET", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(163,132);
			$this->Cell(2, 4, date("M j,Y H:i:s",strtotime($row1['onset_date_time'])), "", 0, '');
			$this->Line(163, 137, 200, 137);
			$this->SetFont('Arial','',10);
			$this->SetXY(203, 132);
			$this->Cell(30, 4, "AM/PM", "", 0, 'L');
			
			//print full dilatation
      $this->SetXY(10,140);
			$this->SetFont('Arial','B',11);
      $this->Cell(37, 4, "FULL DILATATION ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			
			list($date,$time) = split(' ',$row1['dilation_date_time']);
			//print full dilatation - time
			$this->SetFont('Arial','',11);
			$this->SetXY(48,140);
			$this->Cell(2, 4, $time, "", 0, '');
			$this->Line(48, 145, 63, 145);
      $this->SetXY(64,140);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "AM/PM", "", 0, 'L');
      
      //print full dilatation - date
			$this->SetFont('Arial','',11);
			$this->SetXY(78,140);
			$this->Cell(2, 4, date("M j, Y",strtotime($date)), "", 0, '');
			$this->Line(78, 145, 122, 145);
      			
			//print child born
      $this->SetXY(122,140);
			$this->SetFont('Arial','B',11);
      $this->Cell(27, 4, "CHILD BORN ", "", 0, 'L');
      
      list($date,$time) = split(' ',$row1['childborn_date_time']);
      //print child born - time
			$this->SetFont('Arial','',11);
			$this->SetXY(148,140);
			$this->Cell(2, 4, $time, "", 0, '');
			$this->Line(148, 145, 165, 145);
      $this->SetXY(166,140);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "AM/PM", "", 0, 'L');
      
      //print child born - date
			$this->SetFont('Arial','',11);
			$this->SetXY(180,140);
			$this->Cell(2, 4,date("M j, Y",strtotime($date)), "", 0, '');
			$this->Line(180, 145, 213, 145);
			
			//print ergonovine
      $this->SetXY(10,148);
			$this->SetFont('Arial','B',11);
      $this->Cell(29, 4, "ERGONOVINE ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			
			list($date,$time) = split(' ',$row1['ergonovine_date_time']);
			//print ergonovine - time
			$this->SetFont('Arial','',11);
			$this->SetXY(40,148);
			$this->Cell(2, 4, $time, "", 0, '');
			$this->Line(40, 153, 55, 153);
      $this->SetXY(56,148);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "AM/PM", "", 0, 'L');
      
      //print ergonovine - date
			$this->SetFont('Arial','',11);
			$this->SetXY(70,148);
			$this->Cell(2, 4, date("M j, Y",strtotime($date)), "", 0, '');
			$this->Line(70, 153, 107, 153);
			
			//print labor duration
      $this->SetXY(108,148);
			$this->SetFont('Arial','B',11);
      $this->Cell(38, 4, "LABOR DURATION ", "", 0, 'L');
      $this->Cell(1, 4, ":", "", 0, 'R');
      $this->SetXY(146,148);
			$this->Cell(2, 4, $row1['labor_duration_hour'], "", 0, '');
			$this->Line(146, 153, 161, 153);
			
			//print lzbor durztion - hrs
      $this->SetFont('Arial','B',11);
      $this->SetXY(162,148);
      $this->Cell(30, 4, "HRS. ", "", 0, 'L');
			$this->SetFont('Arial','',11);
			$this->SetXY(173,148);
			$this->Cell(2, 4, $row1['labor_duration_min'], "", 0, '');
			$this->Line(173, 152, 185, 152);		
			//print lzbor durztion - min
      $this->SetFont('Arial','B',11);
      $this->SetXY(186,148);
      $this->Cell(30, 4, "MIN. ", "", 0, 'L');
			
			//print delivery spontaneous
      $this->SetXY(10,156);
			$this->SetFont('Arial','B',11);
      $this->Cell(53, 4, "DELIVERY SPONTANEOUS ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R'); 
			
			//print delivery spontaneous - yes
			$this->SetFont('Arial','',11);
			$this->SetXY(72,156);
			if($row1['delivery_spont']==1)
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(65, 161, 80, 161);
      $this->SetXY(81,156);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "YES", "", 0, 'L'); 
      
      //print delivery spontaneous - no
			$this->SetFont('Arial','',11);
			$this->SetXY(97,156);
			if($row1['delivery_spont']==0)
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(90, 161, 105, 161);
      $this->SetXY(106,156);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "NO", "", 0, 'L'); 
      
      //print blood loss
      $this->SetXY(114,156);
			$this->SetFont('Arial','B',11);
      $this->Cell(28, 4, "BLOOD LOSS ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(142,156);
			$this->Cell(2, 4, $row1['blood_given'], "", 0, '');
			$this->Line(142, 161, 157, 161);
			$this->SetFont('Arial','B',11);
			$this->SetXY(158,156);
			$this->Cell(14, 4, "cc ", "", 0, 'L');
			
			//print blood given
      $this->SetXY(165,156);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "BLOOD GIVEN ", "", 0, 'L');
      
      //print OPERATIVE
      $this->SetXY(10,164);
			$this->SetFont('Arial','B',11);
      $this->Cell(25, 4, "OPERATIVE  ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(36,164);
			$this->Cell(2, 4, $row1['operative'], "", 0, '');
			$this->Line(36, 169, 210, 169);
			$this->SetXY(10,172);
			#$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(10, 175, 210, 175);
			
			//print episiotomy
      $this->SetXY(10,180);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "EPISIOTOMY", "", 0, 'L');
      $this->SetXY(35,180);
			$this->Cell(2, 4, $row1['episiotomy'], "", 0, '');
			$this->Line(35, 185, 55, 185);
			
			//print PERINEAL TEAR
      $this->SetXY(56,180);
			$this->SetFont('Arial','B',11);
      $this->Cell(45, 4, "PERINEAL TEAR", "", 0, 'L');
      $this->SetXY(90,180);
			if($row1['perineal_tear']==1)
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(90, 185, 105, 185);
			$this->SetXY(106,180);
			$this->Cell(20, 4, "YES", "", 0, 'L');
      $this->SetXY(116,180);
      if($row1['perineal_tear']==0)
				$this->Cell(2, 4, "X", "", 0, '');
      $this->Line(116, 185, 131, 185);
      $this->SetXY(130,180);
			$this->Cell(20, 4, "NO", "", 0, 'L');
			
			//print analgesic given
      $this->SetXY(140,180);
			$this->SetFont('Arial','B',11);
      $this->Cell(38, 4, "ANALGESIC GIVEN ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(178,180);
			$this->Cell(2, 4, $row1['analgesic_given'], "", 0, '');
			$this->Line(178, 185, 210, 185);
			$this->SetXY(10,188);
			#$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(10, 193, 116, 193);
			
			//print anesthesia given
      $this->SetXY(117,188);
			$this->SetFont('Arial','B',11);
      $this->Cell(42, 4, "ANESTHESIA GIVEN ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(160,188);
			$this->Cell(2, 4, $row1['anesthesia_given'], "", 0, '');
			$this->Line(160, 193, 210, 193);
			$this->SetXY(10,196);
			#$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(10, 201, 116, 201);
			
			//print complications
      $this->SetXY(117,196);
			$this->SetFont('Arial','B',11);
      $this->Cell(35, 4, "COMPLICATIONS ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',11);
			$this->SetXY(153,196);
			$this->Cell(2, 4, $row1['complications'], "", 0, '');
			$this->Line(153, 201, 210, 201);
			
			//print postpartum examination
      $this->SetXY(10,204);
			$this->SetFont('Arial','B',11);
      $this->Cell(58, 4, "POSTPARTUM EXAMINTAION", "", 0, 'L');
      $this->Cell(1, 4, ":", "", 0, 'R'); 
      $this->SetXY(68,204);
			$this->Cell(18, 4, "FUNDUS", "", 0, 'L');
      $this->Cell(1, 4, ":", "", 0, 'R');
      $this->SetFont('Arial','',11);  
      $this->SetXY(86,204);
      $this->Cell(2, 4, $row1['fundus'], "", 0, '');
			$this->Line(86, 209, 150, 209);
			$this->SetFont('Arial','B',11); 
			$this->SetXY(151,204);
			$this->Cell(27, 4, "UMBICULUS", "", 0, 'L');
      $this->Cell(1, 4, ":", "", 0, 'R'); 
      $this->SetFont('Arial','',11); 
      $this->SetXY(178,204);
      $this->Cell(2, 4, $row1['umbiculus'], "", 0, '');
			$this->Line(178, 209, 210, 209);
			
			//print vital signs
			$this->SetXY(10,212);
			$this->SetFont('Arial','B',11);
      $this->Cell(10, 4, "BP ", "", 0, 'L');
      $this->SetXY(17,212);
      $this->SetFont('Arial','B',11);
			$this->Cell(2, 4, $row1['post_bp'], "", 0, '');
			$this->Line(17, 217, 32, 217);
			$this->SetXY(33,212);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "TEMP ", "", 0, 'L');
      $this->SetXY(45,212);
      $this->SetFont('Arial','B',11);
			$this->Cell(2, 4, $row1['post_temp'], "", 0, '');
			$this->Line(45, 217, 60, 217);
			$this->SetXY(61,212);
			$this->SetFont('Arial','B',11);
      $this->Cell(24, 4, "PULSE ", "", 0, 'L');
      $this->SetXY(76,212);
      $this->SetFont('Arial','B',11);
			$this->Cell(2, 4, $row1['post_pulse'], "", 0, '');
			$this->Line(76, 217, 91, 217);
			$this->SetXY(92,212);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "RESP RATE ", "", 0, 'L');
      $this->SetXY(116,212);
      $this->SetFont('Arial','B',11);
			$this->Cell(2, 4, $row1['post_resprate'], "", 0, '');
			$this->Line(116, 217, 131, 217);
			$this->SetXY(130,212);
			$this->SetFont('Arial','B',11);
      $this->Cell(30, 4, "BLEEDING ", "", 0, 'L');
      $this->SetXY(151,212);
      $this->SetFont('Arial','B',11);
      if($row1['bleeding']=="normal")
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(151, 217, 166, 217);
			$this->SetXY(165,212);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "NORMAL ", "", 0, 'L');
      
			$this->SetXY(10,220);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "MODERATE ", "", 0, 'L');
      $this->SetXY(34,220);
      $this->SetFont('Arial','B',11);
      if($row1['bleeding']=="moderate")
				$this->Cell(2, 4, "X", "", 0, '');
			
			$this->Line(34, 225, 49, 225);
			$this->SetXY(50,220);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "EXCESSIVE ", "", 0, 'L');
      $this->SetXY(76,220);
      $this->SetFont('Arial','B',11);
			if($row1['bleeding']=="excessive")
				$this->Cell(2, 4, "X", "", 0, '');
			$this->Line(76, 225, 91, 225);
			
			//print encoder
			$this->SetXY(100,236);
			$this->SetFont('Arial','B',11);
      $this->Cell(20, 4, "DELIVERED BY: ", "", 0, 'L');
      $this->SetXY(132,236);
      $this->SetFont('Arial','B',11);
			$this->Cell(2, 4, $row1['deliver_dr'], "", 0, '');
			$this->Line(132, 241, 200, 241);
			$this->SetXY(201,236);
			$this->Cell(10, 4, "M.D.", "", 0, 'R'); 
			
		}	
}
$rep =& new OR_Delivery_Record($_GET['refno']);
$rep->AliasNbPages();
$rep->Report();

?>