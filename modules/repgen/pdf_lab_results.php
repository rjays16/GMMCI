<?php
//created by EJ 09/13/2014
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_lab_results.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');


class RepGen_LabResults extends RepGen {
    var $pid, $refno, $group_id, $gender, $done, $service_code, $nth_take;
    var $colored = TRUE;

    function RepGen_LabResults () {

        $this->pid = $_GET["pid"];
        $this->refno = $_GET["refno"];
        $this->nth_take = (isset($_GET['nth_take']) ? $_GET['nth_take'] : '0');
        if(isset($_GET["group_id"]) && $_GET["group_id"]!='')
            $this->group_id = $_GET["group_id"];
        else
            $this->group_id = '0';
        if(isset($_GET["service_code"]) && $_GET["service_code"]!='')
            $this->service_code = $_GET["service_code"];
        else
            $this->service_code = '0';

        $lab_results = new Lab_Results();

        if($group_id)
            $this->RepGen("LAB RESULT FOR " + $lab_results->get_group_name($this->group_id));
        if($service_code)
            $this->RepGen("LAB RESULT FOR " + $lab_results->get_service_name($this->service_code));

        $this->PageOrientation = "P";
        $this->FPDF('p','mm','letter');

        if ($this->colored) $this->SetDrawColor(0xDD);

    }

    function Header() {

        $lab_results = new Lab_Results();
        $dept_obj=new Department;
        $ward_obj = new Ward;

        global $root_path, $db;
        $objInfo = new Hospital_Admin();
        if ($row = $objInfo->getAllHospitalInfo()) {
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name']   = strtoupper($row['hosp_name']);
            $row['hosp_tel']    = strtoupper($row['hosp_tel']);
        }
        else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency']  = "PROVINCE OF CAMIGUIN";
            $row['hosp_name']    = "CAMIGUIN GENERAL HOSPITAL";
            $row['hosp_addr1']   = "Mambajao, Camiguin Province";
        }

$this->Image('../../gui/img/logos/dmc_logo.jpg',40,6,15,15);

$this->SetXY(0,8);
$this->SetFont("Arial", "B", 11);
$this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");

$this->SetFont("Arial", "", 9);
$this->Cell(0, 4, $row['hosp_addr1'], 0, 1, "C");

$this->SetFont("Arial", "", 9);
$this->Cell(0, 4, "Tel. No. ".$row['hosp_tel'], 0, 1, "C");

$this->Ln(6);
$this->SetFont('Arial','',11);
$this->Cell(0, 3, "LABORATORY RESULT FORM", '', 0, 'C');

$this->Ln(6);
$this->SetFont('Arial','B',11);
$this->Cell(0, 3, strtoupper($lab_results->get_group_name($_GET['group_id'])), '', 0, 'C');

$patient = $lab_results->get_patient_data($this->refno, $this->group_id);
if($patient!=NULL){
    extract($patient);
    $ordername = mb_strtoupper($name_last).", ".mb_strtoupper($name_first);
    if($name_middle != null){
        $ordername = $ordername." ".mb_strtoupper($name_middle).".";
    }
}
else{
$sql = "SELECT * from seg_walkin WHERE pid='$this->pid'";
$rs = $db->Execute($sql);
if($rs && $pt = $rs->FetchRow()){
    extract($pt);
}
$ordername = mb_strtoupper($name_last).", ".mb_strtoupper($name_first)." ".mb_strtoupper($name_middle).".";
}
$sql = "SELECT service_date FROM seg_lab_resultdata WHERE refno='$this->refno' AND group_id='$this->group_id' AND (ISNULL(`status`) OR `status`!='deleted') AND nth_take = $this->nth_take;";
$result = $lab_results->exec_query($sql);
if($result)
{
    if($resdata = $result->FetchRow())
        $date =  date("m/d/Y",strtotime(substr($resdata["service_date"], 0, -9)));
}

$this->SetDrawColor(0,0,0);

$this->SetXY(20,50);
$this->SetFont('Arial','B',9);
$this->Cell(14,3,"Name: ",0,0,'L');
$this->SetFont('Arial','', 9);
$this->Cell(60, 3, strtoupper($ordername), 'B', 0, 'C');

$format_age = explode(' ', $age);
if ($format_age[1] == 'years') {
    $format_age[1] = 'Y/O';
}
if ($format_age[2] == 'and') {
    $format_age[2] = '';
}
if ($format_age[4] == 'months') {
    $format_age[4] = 'MOS';
}
$format_age = implode(' ', $format_age);

$this->SetXY(95,50);
$this->SetFont('Arial','B',9);
$this->Cell(12,3,'Age: ', 0,0,'L');
$this->SetFont('Arial','', 9);
$this->Cell(40,3,strtoupper($format_age), 'B',1,'C');

$this->SetXY(150,50);
$this->SetFont('Arial','B',9);
$this->Cell(12,3,'Sex: ', 0,0,'L');
$this->SetFont('Arial','', 9);
$this->Cell(5,3,strtoupper($sex), 'B',1,'C');

// $this->SetXY(10,60);
// $this->SetFont('Arial','B',9);
// $this->Cell(12,3,'Address: ', 0,0,'L');
// $this->SetFont('Arial','', 9);
// $this->Cell(25,3,strtoupper($add), 'B',1,'C');

$this->SetXY(160,40);
$this->SetFont('Arial','B',9);
$this->Cell(12,3,'Date: ', 0,0,'L');
$this->SetFont('Arial','', 9);
$this->Cell(25,3,strtoupper($date), 'B',1,'C');

$sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', CONCAT(name_middle, ' ')), IF(ISNULL(name_last), '', name_last)) as name FROM care_person LEFT JOIN care_personell ON care_personell.pid=care_person.pid WHERE nr='".$request_doctor."'";
$result = $lab_results->exec_query($sql);
if($result!=NULL && $resdata = $result->FetchRow())
{
    $physician = "DR.".$resdata["name"];
}

if ($encounter_type==1){
        $ward = "EMERGENCY ROOM";
}elseif ($encounter_type==2){
        $dept = $dept_obj->getDeptAllInfo($current_dept_nr);
        $ward = strtoupper(strtolower(stripslashes($dept['name_formal'])));
}elseif (($encounter_type==3)||($encounter_type==4)){
        $loc = $ward_obj->getWardInfo($current_ward_nr);
        $ward = strtoupper(strtolower(stripslashes($loc['name'])))." Rm # : ".$current_room_nr;
}else{
     $ward = 'WALK-IN';
}

$this->SetXY(20,55);
$this->SetFont('Arial','B',9);
$this->Cell(20,3,'Physician: ',0,0,'L');
$this->SetFont('Arial','', 9);
$this->Cell(72,3,strtoupper($physician),'B',0,'C');

$this->SetXY(115,55);
$this->SetFont('Arial','B',9);
$this->Cell(13,3,'Room: ', 0,0,'L');
$this->SetFont('Arial','',9);
$this->Cell(62,3,strtoupper($ward), 'B',1,'C');

$base_age = explode(' ', $age);

$norm_type = "none";
                
$this->gender = "p.norm_type = 'none'";

if(strtoupper($sex) == 'M'){
    $norm_type = "male";
    $this->gender .= " OR p.norm_type = 'male'";
}else{
    $norm_type = "female";
    $this->gender .= " OR p.norm_type = 'female'";
}

if($base_age[1] == "months" || $base_age[1] == "month" || $base_age[1] == "day" || $base_age[1] == "days" || $base_age[0]<=0){
    $norm_type = "infant";
    $this->gender .= " OR p.norm_type = 'infant'";
}else if($base_age[1] == "years" || $base_age[1] == "year"){
    if($base_age[0] < 3){
        $norm_type = "infant";
        $this->gender .= " OR p.norm_type = 'infant'";
    }
    else if($base_age[0] >= 3 && $base_age[0] <=15){
        $norm_type = "children";
        $this->gender .= " OR p.norm_type = 'children'";
    }
}


$this->LabResult_body();
}

function LabResult_body(){
    global $db;

    $lab_results=new Lab_Results();

    if($this->service_code){
        $sql = "SELECT p.*, r.result_value, r.unit, s.name as group_name
        FROM seg_lab_result_params AS p
        LEFT JOIN seg_lab_services AS s ON s.service_code = p.service_code
        LEFT JOIN seg_lab_result AS r ON (r.param_id = p.param_id AND r.refno='$this->refno' AND (ISNULL(r.status) OR r.status!='deleted') AND r.`nth_take` = $this->nth_take)
        LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id 
        WHERE ($this->gender)  AND (p.status <> 'deleted' OR p.`status` IS NULL) AND s.service_code='$this->service_code' ORDER BY pa.order_nr ASC";
    }
    if($this->group_id){
        $sql = "SELECT t.* FROM(SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled
        FROM seg_lab_result_groupparams as gp
        LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
        LEFT JOIN seg_lab_result as r ON (p.param_id = r.param_id AND r.refno='$this->refno' AND (ISNULL(r.status) OR r.status!='deleted') AND r.nth_take = $this->nth_take)
        LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
        LEFT JOIN seg_lab_servdetails AS d ON d.service_code=p.service_code AND d.refno='$this->refno'
        WHERE gp.group_id=$this->group_id AND ($this->gender) AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
        AND (ISNULL(gp.status) OR gp.status!='deleted') AND (r.result_value IS NOT NULL AND r.result_value != '') 
        UNION SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled
        FROM seg_lab_result_groupparams as gp
        LEFT JOIN seg_lab_result_group as g ON g.service_code = gp.service_code
        LEFT JOIN seg_lab_result_params as p ON p.service_code = g.service_code_child
        LEFT JOIN seg_lab_result as r ON (p.param_id = r.param_id AND r.refno='$this->refno' AND (ISNULL(r.status) OR r.status!='deleted') AND r.nth_take = $this->nth_take)
        LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
        LEFT JOIN seg_lab_servdetails AS d ON (d.service_code=g.service_code OR d.service_code=p.service_code) AND d.refno='$this->refno'
        WHERE gp.group_id=$this->group_id AND ($this->gender) AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
        AND (ISNULL(gp.status) OR gp.status!='deleted') AND (r.result_value IS NOT NULL AND r.result_value != '') 
        ORDER BY order_nr, order2) t LEFT JOIN seg_lab_result_param_assignment AS pa ON t.param_id=pa.param_id GROUP BY t.name ORDER BY pa.order_nr, t.order2";
    }
  
    $result = $lab_results->exec_query($sql);
    //var_dump($sql);die;
    /*if ($result) {
    $total_results_count = $result->RecordCount();
    }
    else {
    $this->SetFont('Arial','',20);
    $this->SetXY(90,50);
    $this->Cell(25,3,'(NO DATA)', 0,0,'L');   
    }*/

    if($result) {
        $numres = $result->RecordCount();
        if($this->group_id)
            $sql = "SELECT 
                      COUNT(result_value) as numres
                    FROM
                      seg_lab_result 
                    WHERE refno = $this->refno 
                      AND result_value != '' 
                      AND result_value IS NOT NULL ";

        $rs = $lab_results->exec_query($sql);

        if($rs!=NULL && $val = $rs->FetchRow()) $numres = $val["numres"];

        //display the results
        $results = array();
        $has_normal_values = 0;

        while($result!=NULL && $value = $result->FetchRow()){
            //assign values
            $serv_code = $value['service_code'];
            $serv_name = $value['name'];
            $results[] = array('name'=>$value['name'],
                                'group_name'=>$value['group_name'],
                                'result_value'=>$value['result_value'],
                                'SI_lo_normal'=>$value['SI_lo_normal'],
                                'SI_hi_normal'=>$value['SI_hi_normal'],
                                'SI_unit'=>$value['SI_unit']);

            if($value['SI_lo_normal'] || $value['SI_hi_normal'] || $value['SI_unit']){
                $has_normal_values = 1;
            }

        }

         //set result headers
        $this->SetFont('Arial','B',9);
        $addX = 0;
        if(!$has_normal_values){
            $addX = 40;
        }
        
        //added by Sarah for SPUTUM AFB X2 Sept. 8, 2015
        if($serv_code == 'SPUT2'){
            $x = 20;
            $y = 65;
            $this->SetXY($x,$y);
            $this->SetWidths(array(50, 40, 40, 40));
            $this->RowWithBorder(array("SPECIMEN", "1ST", "2ND", "3RD"));
            $j = 0; //counter for group
            $k = 0; //counter for result
            $l = 0; //counter for result in the table
            for($i=0; $i<count($results); $i++){
                $group_name = $results[$i]["group_name"];
                $result_value = $results[$i]["result_value"];

                $group_name_current = $group_name;
                if ($group_name_old != $group_name_current ) {
                    $gname[$j]=$group_name_current;
                    $j++;
                    $group_name_old = $group_name_current;
                }
                if ($group_name_old == $group_name_current ) {
                    $res[$k]=$result_value;
                    $k++;
                    $group_name_old = $group_name_current;
                }
               
               
            }
            for($i=0; $i<3; $i++){
                $this->SetXY($x,$y+5);
                $this->RowWithBorder(array($gname[$i], $res[$l], $res[$l+1], $res[$l+2]));
                $y=$y+5;
                $l=$l+3;
            }

        }
        //end Sarah Sept. 8,2015
        else{
            $this->SetXY(20,62);
            $this->Cell(25,3,'EXAMINATIONS', 0,0,'L');
            if($serv_name=='RBS'){
            $this->SetXY(60,62);
            $this->Cell(25,3,'FINDING', 0,0,'L');
            }
            $this->SetXY(95.5+$addX,62);
            $this->Cell(25,3,'RESULTS', 0,0,'L');
            $this->SetXY(155,62);
            if($has_normal_values){
                $this->Cell(27,3,'NORMAL VALUES', 0,0,'L');
            }

            $x = 20;
            $y = 70;

            for($i=0; $i<count($results); $i++){

                $name = $results[$i]["name"];
                $group_name = $results[$i]["group_name"];
                $result_value = $results[$i]["result_value"];
                $SI_lo_normal = $results[$i]["SI_lo_normal"];
                $SI_hi_normal = $results[$i]["SI_hi_normal"];
                $SI_unit = $results[$i]["SI_unit"];

                if($serv_name=='RBS'){
                    if($result_value<$SI_lo_normal){
                        $this->SetXY(60,70);
                        $this->Cell(25,3,"LOW", 0,0,'L');
                    }
                    else if($result_value>$SI_hi_normal){
                        $this->SetXY(60,70);
                        $this->Cell(25,3,"HIGH", 0,0,'L');
                    }
                    else{
                        $this->SetXY(60,70);
                        $this->Cell(25,3,"NORMAL", 0,0,'L');
                    }
                }
                if($has_normal_values){
                    $norm_values = $SI_lo_normal." - ".$SI_hi_normal."".$SI_unit;
                }
                     $this->SetFont('Arial','', 9);

                //if no groups
                if (!$group_name) {
                $this->SetXY($x,$y);
                $this->Cell(1,1,$name, '',1,'L'); 
                $this->SetXY($x+76+$addX,$y);
                $this->Cell(25,3,$result_value, 'B',1,'L');
                $this->SetXY($x+140,$y+1);
                $this->Cell(1,1,$norm_values, '',1,'L');   
                }

                //if has groups
                else {
                $this->SetXY($x,$y);
                $group_name_current = $group_name;
                if ($group_name_old == $group_name_current ) {
                    $this->Cell(1,1,'', '',1,'L');
                    $y = $y-5;
                }
                else {
                    $this->Cell(1,1,$group_name_current, '',1,'L');
                    $group_name_old = $group_name_current;
                }
                $this->SetXY($x+10,$y+5);
                $this->Cell(1,1,$name, '',1,'L'); 
                $this->SetXY($x+76+$addX,$y+3);
                $this->Cell(25,3,$result_value, 'B',1,'L');
                $this->SetXY($x+140,$y+4);

                $this->Cell(1,1,$norm_values, '',1,'L');
                $y = $y+5;
                }
                 
                //collect result y axis
                $y = $y+5;
            }
        }
    }
}

function Footer()
{
    $lab_results = new Lab_Results();
    $sql = "SELECT remarks, med_tech_pid, pathologist_pid, service_code FROM seg_lab_resultdata WHERE refno='$this->refno' AND group_id='$this->group_id' AND nth_take=$this->nth_take AND (ISNULL(`status`) OR `status`!='deleted');";

    $result = $lab_results->exec_query($sql);
    if($result)
    {
        if($person = $result->FetchRow())
        {
            $pathologist_id = $person["pathologist_pid"];
            $med_tech_pid = $person["med_tech_pid"];
            //added by Sarah Sept. 8, 2015
            if($person['service_code'] == 'SPUT2'){
                $strSQL = "SELECT remarks FROM seg_lab_services WHERE service_code = '".$person['service_code']."'";
                         $res = $lab_results->exec_query($strSQL);
                         if($result!=NULL && $data = $res->FetchRow()){
                            $remarks = $data['remarks'];
                         }
                $this->SetDrawColor(0,0,0);
                $this->SetXY(20,85);
                $this->SetFont('Arial','', 9);
                $this->Cell(120,5,$remarks, '',1,'C'); 
            }
            else{
                $remarks = $person['remarks'];
                $this->SetDrawColor(0,0,0);
                $this->SetXY(20,130);
                $this->SetFont('Arial','B', 11);
                $this->Cell(20,5,"Remarks: ", '',0,'L'); 
                $this->Cell(60,5,$remarks, '',1,'L'); 

            }
            //end Sarah Sept. 8, 2015
            $sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', name_middle), '. ', IF(ISNULL(name_last), '', name_last), ', ', IF(ISNULL(title), '', title)) as name from care_person WHERE care_person.pid = '".$pathologist_id."'";
        
            $result = $lab_results->exec_query($sql);
            if($result!=NULL && $person = $result->FetchRow())
                $pathologist = $person["name"];
            else
                $pathologist = "";

            $sql = "SELECT fn_get_personell_title_other('$pathologist_id') AS title";
            $result = $lab_results->exec_query($sql);
            if($result!=NULL && $person = $result->FetchRow())
                $pathologist_title = $person["title"];
            else
                $pathologist_title = "";

            $sql = "SELECT fn_get_pid_name('$med_tech_pid') as name";
            $result = $lab_results->exec_query($sql);
            if($result!=NULL && $person = $result->FetchRow())
                $examiner = $person["name"];
            else
                $examiner = "";

            $sql = "SELECT fn_get_personell_title_other('$med_tech_pid') AS title";
            $result = $lab_results->exec_query($sql);
            if($result!=NULL && $person = $result->FetchRow())
                $examiner_title = $person["title"];
            else
                $examiner_title = "";

            $sql = "SELECT license_nr FROM care_personell WHERE pid = '$med_tech_pid'";
            $result = $lab_results->exec_query($sql);
            if($result!=NULL && $person = $result->FetchRow())
                $med_tech_lic = $person["license_nr"];
            else
                $med_tech_lic = "";

            $sql = "SELECT license_nr FROM care_personell WHERE pid = '$pathologist_id'";
            $result = $lab_results->exec_query($sql);
            if($result!=NULL && $person = $result->FetchRow())
                $patho_lic = $person["license_nr"];
            else
                $patho_lic = "";
        }
    }

   
    $this->SetXY(20,143);
    $this->SetFont('Arial','', 9);
    $this->MultiCell(20,5,'',"B","C","0");
    $this->SetXY(20,148);
    $this->Cell(10,5,"",0,1,'C');
    $this->SetFont('Arial','', 9);
    $this->SetXY(10,152);
    $this->Cell(40,5,"Lab No.",0,0,'C');

    $this->SetXY(50,143);
    $this->SetFont('Arial','B', 9);
    $this->MultiCell(70,5,strtoupper($pathologist."".$pathologist_title),"B","C","0");
    $this->SetXY(55,148);
    $this->SetFont('Arial','',9);
    $this->Cell(60,5,"Lic No. ".$patho_lic,0,1,'C');
    $this->SetFont('Arial','', 9);
    $this->SetXY(55,152);
    $this->Cell(60,5,strtoupper("PATHOLOGIST"),0,0,'C');

    $this->SetXY(132,143);
    $this->SetFont('Arial','B', 9);
    $this->MultiCell(58,5,strtoupper($examiner.", ".$examiner_title),"B","C","0");
    $this->SetXY(140,148);
    $this->SetFont('Arial','',8);
    $this->Cell(60,5,"Lic No. ".$med_tech_lic,0,1,'C');
    $this->SetFont('Arial','', 9);
    $this->SetXY(140,152);
    $this->Cell(60,5,strtoupper("EXAMINER"),0,0,'C');
    }
}

$report = new RepGen_LabResults();
$report->AliasNbPages();
$report->Report();
