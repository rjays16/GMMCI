<?php

require_once('roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');


interface ReportDataSource {
    public function toArray();
}


class ReportGenerator {

    protected $_dataSource;

    public function __construct(&$dataSource) {
        $this->_dataSource = $dataSource;
    }

}


/**
 * see if the java extension was loaded.
 */
function checkJavaExtension()
{
    if(!extension_loaded('java'))
    {
        $sapi_type = php_sapi_name();
        $port = (isset($_SERVER['SERVER_PORT']) && (($_SERVER['SERVER_PORT'])>1024)) ? $_SERVER['SERVER_PORT'] : '8080';
        if ($sapi_type == "cgi" || $sapi_type == "cgi-fcgi" || $sapi_type == "cli")
        {
            require_once(java_include);
            return true;
        }
        else
        {
            if(!(@require_once(java_include)))
            {
                require_once(java_include);
            }
        }
    }
    if(!function_exists("java_get_server_name"))
    {
        return "The loaded java extension is not the PHP/Java Bridge";
    }

    return true;
}

function seg_ucwords($str) {
    $words = preg_split("/([\s,.-]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);
    $words = @array_map('ucwords',$words);
    return implode($words);
}

/**
 * convert a php value to a java one...
 * @param string $value
 * @param string $className
 * @returns boolean success
 */
function convertValue($value, $className){
    // if we are a string, just use the normal conversion
    // methods from the java extension...
    try{
        if ($className == 'java.lang.String'){
            $temp = new Java('java.lang.String', $value);
            return $temp;
        }else if ($className == 'java.lang.Boolean' ||
                    $className == 'java.lang.Integer' ||
                    $className == 'java.lang.Long' ||
                    $className == 'java.lang.Short' ||
                    $className == 'java.lang.Double' ||
                    $className == 'java.math.BigDecimal')
        {
            $temp = new Java($className, $value);
            return $temp;
        }else if ($className == 'java.sql.Timestamp' ||
            $className == 'java.sql.Time')
        {
            $temp = new Java($className);
            $javaObject = $temp->valueOf($value);
            return $javaObject;
        }else if ($className == "java.util.Date"){
            #$temp = new Java('java.text.DateFormat');
            $temp = new Java('java.text.SimpleDateFormat("MM/dd/yyyy")');
            $javaObject = $temp->parse($value);
            return $javaObject;
        }
    }catch (Exception $err){
        echo (  'unable to convert value, ' . $value .
                ' could not be converted to ' . $className);
        return false;
    }

    echo (  'unable to convert value, class name '.$className.
    ' not recognised');
    return false;
}


$x = checkJavaExtension();

$report = 'DeathCertificate';
$compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
$report = $compileManager->compileReport(realpath(java_resource.$report.'.jrxml'));
java_set_file_encoding("ISO-8859-1");
$fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

$params = new Java("java.util.HashMap");

$start = microtime(true);

$db->SetFetchMode(ADODB_FETCH_ASSOC);

$pid = $_GET['id'];

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_DeathCert = new DeathCertificate($pid);

require_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

if ($row = $objInfo->getAllHospitalInfo()) {
        $row['hosp_agency'] = mb_strtoupper($row['hosp_agency']);
        $row['hosp_name']   = mb_strtoupper($row['hosp_name']);
}
else {
        $row['hosp_country'] = "Republic of the Philippines";
        $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
        $row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
        $row['hosp_addr1']   = "Malaybalay, Bukidnon";
        $row['mun_name']     = "Malaybalay";
        $row['prov_name']    = "Bukidnon";
        $row['region_name']  = "Region X";
}

$doctor_address = $row['hosp_name']." - ".$row['mun_name'];

if ($pid){
    if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
        echo '<em class="warn"> Sorry, the page cannot be displayed!</em>';
        exit();
    }
    extract($basicInfo);
    //print($brgy_nr);
    //GET BARANGAY INFO
    $brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
    //print ($brgy_info);
    if($brgy_info){
        $brgy_row = $brgy_info->FetchRow();
    }
    //print ($brgy_row['brgy_name'] . $brgy_row['mun_name']);
    //exit();
}

$DeathCertInfo = $obj_DeathCert->getDeathCertRecord($pid);

//modified by jasper 01/09/2013

if ($DeathCertInfo){
    extract($DeathCertInfo);
    $delivery_method_tmp= substr(trim($DeathCertInfo['delivery_method']),0,1);
    $delivery_method_info = substr(trim($DeathCertInfo['delivery_method']),4);
    $death_manner_tmp = substr(trim($DeathCertInfo['death_manner']),0,1);
    $death_manner_accident = substr(trim($DeathCertInfo['death_manner']),4);
    $death_manner_info = substr(trim($DeathCertInfo['death_manner']),4);
    $attendant_type_tmp = substr(trim($DeathCertInfo['attendant_type']),0,1);
    $attendant_type_others = substr(trim($DeathCertInfo['attendant_type']),4);
    $corpse_disposal_tmp= substr(trim($DeathCertInfo['corpse_disposal']),0,1);
    $corpse_disposal_others = substr(trim($DeathCertInfo['corpse_disposal']),3);
    $is_autopsy = substr(trim($DeathCertInfo['is_autopsy']),0,1);
    $tmp_death_cause = unserialize($DeathCertInfo['death_cause']);
    $cemetery_name_address = $DeathCertInfo['cemetery_name_address'];

    //print_r($DeathCertInfo);
    //initialize array
    $data[0]['age_at_death'] = "";
    $data[0]['age_days'] = "";
    $data[0]['age_hours'] = "";
    $data[0]['age_min_sec'] = "";
    $data[0]['age_month'] = "";
    $data[0]['age_of_mother'] = "";
    $data[0]['attendant_a'] = "";
    $data[0]['attendant_b'] = "";
    $data[0]['attendant_c'] = "";
    $data[0]['attendant_d'] = "";
    $data[0]['attendant_e'] = "";
    $data[0]['attendant_type_others'] = "";
    $data[0]['attended_from'] = "";
    $data[0]['attended_to'] = "";
    $data[0]['birth_rank'] = "";
    $data[0]['birth_type'] = "";
    $data[0]['birthDay'] = "";
    $data[0]['birthMonth'] = "";
    $data[0]['birthYear'] = "";
    $data[0]['burial_date_issued'] = "";
    $data[0]['burial_number'] = "";
    $data[0]['cause1'] = "";
    $data[0]['cause2'] = "";
    $data[0]['cause3'] = "";
    $data[0]['cause4'] = "";
    $data[0]['cause5'] = "";
    $data[0]['cause_death_a'] = "";
    $data[0]['cause_death_b'] = "";
    $data[0]['cause_death_c'] = "";
    $data[0]['cause_death_d'] = "";
    $data[0]['certification_address'] = "";
    $data[0]['certification_address1'] = "";
    $data[0]['certification_date'] = "";
    $data[0]['certification_name'] = "";
    $data[0]['certification_position'] = "";
    $data[0]['certification_sign'] = "";
    $data[0]['citizenship'] = "";
    $data[0]['civil_status'] = "";
    $data[0]['corpse_disposal'] = "";
    $data[0]['date_birth'] = "";
    $data[0]['death_autopsy'] = "";
    $data[0]['death_external_cause'] = "";
    $data[0]['death_external_cause_place'] = "";
    $data[0]['death_place_basic'] = "";
    $data[0]['death_place_mun'] = "";
    $data[0]['death_place_prov'] = "";
    $data[0]['deathcert_have'] = "";
    $data[0]['deathcert_havenot'] = "";
    $data[0]['deathcert_time'] = "";
    $data[0]['deathDay'] = "";
    $data[0]['deathMonth'] = "";
    $data[0]['deathYear'] = "";
    $data[0]['delivery_method'] = "";
    $data[0]['embalmer_address1'] = "";
    $data[0]['embalmer_address2'] = "";
    $data[0]['embalmer_dead_name'] = "";
    $data[0]['embalmer_expiry_date'] = "";
    $data[0]['embalmer_issued_on'] = "";
    $data[0]['embalmer_issued_place'] = "";
    $data[0]['embalmer_license_no'] = "";
    $data[0]['embalmer_name'] = "";
    $data[0]['embalmer_sign'] = "";
    $data[0]['embalmer_title'] = "";
    ///$data[0]['f_name_first'] = "";
    //$data[0]['f_name_last'] = "";
    //$data[0]['f_name_middle'] = "";
    $data[0]['father_name'] = "";
    $data[0]['informant_address'] = "";
    $data[0]['informant_date_sign'] = "";
    $data[0]['informant_name'] = "";
    $data[0]['informant_relation'] = "";
    $data[0]['informant_sign'] = "";
    $data[0]['interval_death_a'] = "";
    $data[0]['interval_death_b'] = "";
    $data[0]['interval_death_c'] = "";
    $data[0]['late_affiant_ack_day'] = "";
    $data[0]['late_affiant_ack_month'] = "";
    $data[0]['late_affiant_ack_place'] = "";
    $data[0]['late_affiant_ack_sign'] = "";
    $data[0]['late_affiant_ack_year'] = "";
    $data[0]['late_affiant_address1'] = "";
    $data[0]['late_affiant_address2'] = "";
    $data[0]['late_affiant_comtax_date'] = "";
    $data[0]['late_affiant_comtax_nr'] = "";
    $data[0]['late_affiant_comtax_place'] = "";
    $data[0]['late_affiant_name'] = "";
    $data[0]['late_attended1'] = "";
    $data[0]['late_attended2'] = "";
    $data[0]['late_attendedby'] = "";
    $data[0]['late_buried_date'] = "";
    $data[0]['late_cemetery'] = "";
    $data[0]['late_ddate'] = "";
    $data[0]['late_dead_name'] = "";
    $data[0]['late_death_cause'] = "";
    $data[0]['late_officer_address'] = "";
    $data[0]['late_officer_name'] = "";
    $data[0]['late_officer_sign'] = "";
    $data[0]['late_officer_title'] = "";
    $data[0]['late_place_death'] = "";
    $data[0]['late_reason1'] = "";
    $data[0]['late_reason2'] = "";
    //$data[0]['m_name_first'] = "";
    //$data[0]['m_name_last'] = "";
    //$data[0]['m_name_middle'] = "";
    $data[0]['mother_name'] = "";
    $data[0]['maternalcondition_a'] = "";
    $data[0]['maternalcondition_b'] = "";
    $data[0]['maternalcondition_c'] = "";
    $data[0]['maternalcondition_d'] = "";
    $data[0]['maternalcondition_e'] = "";
    $data[0]['name_address_cementery'] = "";
    $data[0]['name_first'] = "";
    $data[0]['name_last'] = "";
    $data[0]['name_middle'] = "";
    $data[0]['occupation'] = "";
    $data[0]['postmortem_address1'] = "";
    $data[0]['postmortem_address2'] = "";
    $data[0]['postmortem_cause1'] = "";
    $data[0]['postmortem_cause2'] = "";
    $data[0]['postmortem_date'] = "";
    $data[0]['postmortem_name'] = "";
    $data[0]['postmortem_sign'] = "";
    $data[0]['postmortem_title'] = "";
    $data[0]['pregnancy_length'] = "";
    $data[0]['preparedby_date_sign'] = "";
    $data[0]['preparedby_name'] = "";
    $data[0]['preparedby_position'] = "";
    $data[0]['preparedby_sign'] = "";
    $data[0]['receivedby_date_sign'] = "";
    $data[0]['receivedby_name'] = "";
    $data[0]['receivedby_position'] = "";
    $data[0]['receivedby_sign'] = "";
    $data[0]['registered_date'] = "";
    $data[0]['registered_name'] = "";
    $data[0]['registered_position'] = "";
    $data[0]['registered_sign'] = "";
    $data[0]['registry_nr'] = "";
    $data[0]['religion'] = "";
    $data[0]['remarks_annotation'] = "";
    $data[0]['residence_place'] = "";
    $data[0]['reviewed_date'] = "";
    $data[0]['reviewed_name'] = "";
    $data[0]['sex'] = "";
    $data[0]['subscribed_sworn_place'] = "";
    $data[0]['subscribed_sworn_day'] = "";
    $data[0]['subscribed_sworn_month'] = "";
    $data[0]['subscribed_sworn_year'] = "";
    $data[0]['transfer_date'] = "";
    $data[0]['transfer_number'] = "";
    //initialize array

    //print_r ($data);
    #PAGE 1
    #data

    if(stristr($row['mun_name'], 'city') === FALSE){
        $data[0]['death_place_prov'] = trim($row['prov_name']);
    }
    //$data[0]['death_place_mun'] = trim($row['mun_name']);
    $data[0]['death_place_prov'] = strtoupper($death_place_prov)." ";
    $data[0]['death_place_mun'] = strtoupper($death_place_mun)." ";
    $data[0]['registry_nr'] = strtoupper($registry_nr)." ";

    #NAME
    $name_first = str_replace(" ","  ",trim($name_first));
    $data[0]['name_first'] = strtoupper(mb_strtoupper(stripslashes($name_first)))." ";

    $name_middle = str_replace(" ","  ",trim($name_middle));
    $data[0]['name_middle'] = strtoupper(mb_strtoupper(stripslashes($name_middle)))." ";

    $name_last = str_replace(" ","  ",trim($name_last));
    $data[0]['name_last'] = strtoupper(mb_strtoupper(stripslashes($name_last)))." ";

    #SEX
    if ($sex=='m')
        $gender = "Male";
    elseif ($sex=='f')
        $gender = "Female";

    $data[0]['sex'] = strtoupper($gender)." ";

    #DATE OF DEATH
    $arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");

    $deathDay = date("d",strtotime($death_date));
    $deathMonth = date("F",strtotime($death_date));
    $deathYear = date("Y",strtotime($death_date));

    $data[0]['deathDay'] = strtoupper($deathDay)." ";
    $data[0]['deathMonth'] = strtoupper($deathMonth)." ";
    $data[0]['deathYear'] = strtoupper($deathYear)." ";

    #DATE OF BIRTH
    $birthDay = date("d",strtotime($date_birth));
    $birthMonth = date("F",strtotime($date_birth));
    $birthYear = date("Y",strtotime($date_birth));

    $data[0]['birthDay'] = strtoupper($birthDay)." ";
    $data[0]['birthMonth'] = strtoupper($birthMonth)." ";
    $data[0]['birthYear'] = strtoupper($birthYear)." ";

    #AGE OF DEATH
    //routine from vanessa saren
    $date_birth_tmp = @formatDate2Local($date_birth,$date_format);
    if (($death_date!='0000-00-00')  && ($death_date!=""))
        $death_date_tmp = @formatDate2Local($death_date,$date_format);
            else
                $death_date_tmp='';
            $ageYear = $person_obj->getAge($date_birth_tmp,'',$death_date_tmp);

            if (is_numeric($ageYear) && ($ageYear>=0)){
                if ($ageYear<1){
                    $ageMonth = intval($ageYear*12);
                    $ageDay = (($ageYear*12)-$ageMonth) * 30;
                    if(($ageMonth == 0) && (round($ageDay)<1)){
                        # under 1 day
                        if ($age_at_death)
                            list($ageHours,$ageMinutes,$ageSec) = explode(":",$age_at_death);
                        $ageMonth = ''; # set age in months as empty
                        $ageDay = '7'; # set age in days as empty

                    }else{
                        # under 1 year but above 1 day
                        $ageMonth = intval($ageYear*12);
                        $ageDay = round((($ageYear*12)-$ageMonth) * 30);
                    }
                    $ageYear = ''; # set age in years as empty
                }else{
                    # above 1 year
                    $ageYear = number_format($ageYear, 2);
                }
            }else{
            #    echo "false :  ageYear ='".$ageYear."' <br>\n";
            }


        #added by VAN 08-13-08
        if ($ageYear==0){
            $ageYear = "";

            if ($ageMonth==0){
                $ageMonth = "";
                if ($ageDay==0){
                    $ageDay = "";

                    if ($ageHours==0)
                        $ageHoursMinutesSec = "00 / ".$ageMinutes." / ".$ageSec;
                    elseif(($ageHours==0)&&($ageMinutes==0))
                        $ageHoursMinutesSec = "00 / 00 / ".$ageSec;
                    else
                        $ageHoursMinutesSec = "";
                }else{
                    $ageHoursMinutesSec = "";
                }
            }else{
                $ageDay = "";
                $ageHoursMinutesSec = "";
            }
        }else{
            $ageYear =  number_format(floor($ageYear));
            $ageMonth = "";
            $ageDay = "";
            $ageHoursMinutesSec = "";
        }

        $ageHours = $death_hour;
        $ageMinutes = $death_min;
        $ageSec = $death_sec;
        $ageHoursMinutesSec = "";

        if ((trim($ageHours."".$ageMinutes."".$ageSec)!="")&&(trim($ageHours."".$ageMinutes."".$ageSec)!="000")){
                #if (($ageYear==0)&&(trim($ageHours."".$ageMinutes."".$ageSec)=="000")){

            #$ageHoursMinutesSec = $ageHours." hrs ".$ageMinutes." min ".$ageSec." sec";
            $ageHoursMinutesSec = $ageHours." / ".$ageMinutes." / ".$ageSec;

            if ($ageHours<10)
                $ageHours = '0'.$ageHours;

            if ($ageMinutes<10)
                $ageMinutes = '0'.$ageMinutes;

            if (($ageSec<10)||($ageSec==0))
                $ageSec = '0'.$ageSec;

        #echo "e = ".$ageHoursMinutesSec;
        }

    //routine from vanessa saren

    $data[0]['age_at_death'] = strtoupper($age_at_death)." ";
    $data[0]['age_month'] = strtoupper($ageMonth)." ";
    $data[0]['age_days'] = strtoupper($ageDay)." ";
    $data[0]['age_hours'] =strtoupper( ($ageHours=="0" ? "" : $ageHours))." ";
    $data[0]['age_min_sec'] = strtoupper(($ageSec=="0" ? "" : $ageSec))." ";

    #END OF AGE

    #PLACE OF DEATH
    if ($death_place_basic)
        $death_place = mb_strtoupper($death_place_basic).", ";
    else
        $death_place = "";

    $deathplace = mb_strtoupper($death_place)." ".mb_strtoupper($death_place_mun)." ,".mb_strtoupper($death_place_prov).", PHILIPPINES";
    $data[0]['death_place_basic'] = strtoupper($deathplace)." ";

    #CIVIL STATUS
    $data[0]['civil_status'] = strtoupper(ucwords($civil_status))." ";

    #RELIGION
    $data[0]['religion'] = strtoupper(ucwords($religion_name))." ";

    #CITIZNESHIP
    $hdcitz_obj = $obj_DeathCert->getCitizenship2($dcitizenship);
    if (empty($dcitizenship))
        $hdcitz_obj['citizenship'] = "FILIPINO";

    $data[0]['citizenship'] = strtoupper($hdcitz_obj['citizenship'])." ";

    #RESIDENCE
    $m_address = trim($street_name);

    if ((stristr($brgy_row['brgy_name'], 'barangay') === FALSE) && (stristr($brgy_row['brgy_name'], 'brgy') === FALSE)){
        if (!empty($m_address) && !empty($brgy_row['brgy_name']))
            $m_address = $m_address.", ".ucwords(trim($brgy_row['brgy_name']));
        else
            $m_address = $m_address." ".ucwords(trim($brgy_row['brgy_name']));
    }

    if (!empty($m_address) && !empty($street_name)){
        $m_address = $m_address.", ".ucwords(trim($brgy_row['mun_name']));
    }else{
        $m_address = $m_address." ".ucwords(trim($brgy_row['mun_name']));
    }

    #added by VAN 08-05-08
    if(stristr($brgy_row['mun_name'], 'city') === FALSE || !empty($prov_name)){
        if (!empty($m_address)){
            $m_address = $m_address.", ".(!empty($prov_name) ? strtoupper(trim($prov_name)) : strtoupper(trim($brgy_row['prov_name'])) );
        }else{
            $m_address = $m_address.", ".(!empty($prov_name) ? strtoupper(trim($prov_name)) : strtoupper(trim($brgy_row['prov_name'])) );
        }
    }


    if(!empty($country_citizenship)){
        $m_address = $m_address.",".trim(strtoupper($country_citizenship)); 
    }

    $data[0]['residence_place'] = strtoupper($m_address)." ";

    #OCCUPATION
    $data[0]['occupation'] = strtoupper($occupation_name)." ";

    #MOTHER'S NAME
    if ($mother_maiden_fname=="" || $mother_maiden_fname==null) {
        //$data[0]['m_name_first'] = $mother_fname;
        //$data[0]['m_name_middle'] = $mother_mname;
        //$data[0]['m_name_last'] = $mother_lname;
        $mother_name = mb_strtoupper(stripslashes(trim($mother_fname))) . " " . mb_strtoupper(stripslashes(trim($mother_mname))) . " " . mb_strtoupper(stripslashes(trim($mother_lname)));
    } else {
        //$data[0]['m_name_first'] = $mother_maiden_fname;
        //$data[0]['m_name_middle'] = $mother_maiden_mname;
        //$data[0]['m_name_last'] = $mother_maiden_lname;
        $mother_name = mb_strtoupper(stripslashes(trim($mother_maiden_fname))) . " " . mb_strtoupper(stripslashes(trim($mother_maiden_mname))) . " " . mb_strtoupper(stripslashes(trim($mother_maiden_lname)));
    }
    $data[0]['mother_name'] = strtoupper($mother_name)." ";

    #FATHER'S NAME
    if ((($father_fname=="N/A") || ($father_lname=="n/a")) &&
        (($father_mname=="N/A") || ($father_mname=="n/a")) &&
        (($father_lname=="N/A") || ($father_lname=="n/a"))
        ){
        $f_name_first  = "n/a";
        $f_name_middle  = "";
        $f_name_last  = "";
        $father_name = "";
    }else{
        //if ((stristr($father_fname,'JR')) || (stristr($father_fname,'SR'))){
        //    $father_name = trim($father_fname);
        //}else{
        //    $father_name = str_replace(",","",trim($father_fname));
        //}

        $f_name_first  = mb_strtoupper(stripslashes(trim($father_fname)));
        $f_name_middle = mb_strtoupper(stripslashes(trim($father_mname)));
        $f_name_last  =  mb_strtoupper(stripslashes(trim($father_lname)));
        $father_name = $f_name_first . " " . $f_name_middle . " " . $f_name_last;
    }
    $data[0]['father_name'] = $father_name." ";

    //$data[0]['f_name_first'] = $f_name_first;
    //$data[0]['f_name_middle'] = $f_name_middle;
    //$data[0]['f_name_last'] = $f_name_last;

    #CAUSE OF DEATH -fill out the form manually

    #MATERNAL CONDITION
    if ($maternal_condition==1)
        $data[0]['maternalcondition_a'] = "X";
    elseif ($maternal_condition==2)
        $data[0]['maternalcondition_b'] = "X";
    elseif ($maternal_condition==3)
        $data[0]['maternalcondition_c'] = "X";
    elseif ($maternal_condition==4)
        $data[0]['maternalcondition_d'] = "X";
    elseif ($maternal_condition==5)
        $data[0]['maternalcondition_e'] = "X";

    #DEATH BY NON NATURAL CAUSES
    $data[0]['death_external_cause']  = strtoupper(($death_manner_info!="" ? $death_manner_info : ""))." ";
    $data[0]['death_external_cause_place'] = strtoupper($place_occurrence)." ";

    #AUTOPSY
    if ($is_autopsy==2) {
      $data[0]['death_autopsy'] = strtoupper("No")." ";
    }
    else {
      $data[0]['death_autopsy'] = strtoupper("Yes")." ";
    }

    #ATTENDANT
    if ($attendant_type_tmp==1)
        $data[0]['attendant_a'] = "X";
    elseif ($attendant_type_tmp==2)
        $data[0]['attendant_b'] = "X";
    elseif ($attendant_type_tmp==3)
        $data[0]['attendant_c'] = "X";
    elseif ($attendant_type_tmp==4)
        $data[0]['attendant_d'] = "X";
    elseif ($attendant_type_tmp==5) {
        $data[0]['attendant_e'] = "X";
        $data[0]['attendant_type_others'] = strtoupper($attendant_type_others)." ";
    }

    #IF ATTENDED
    $attendedFromDateYear = strtoupper(date("y",strtotime($attended_from_date)));
    $attendedFromDateDay = strtoupper(date("d",strtotime($attended_from_date)));
    $attendedFromDateMonth = strtoupper(date("m",strtotime($attended_from_date)));

    $attendedToDateYear = strtoupper(date("y",strtotime($attended_to_date)));
    $attendedToDateDay = strtoupper(date("d",strtotime($attended_to_date)));
    $attendedToDateMonth = strtoupper(date("m",strtotime($attended_to_date)));

    $data[0]['attended_from'] = strtoupper($attendedFromDateMonth . "/" . $attendedFromDateDay . "/". $attendedFromDateYear)." ";
    $data[0]['attended_to'] = strtoupper($attendedToDateMonth . "/" . $attendedToDateDay . "/". $attendedToDateYear)." ";

    #CERTIFICATION OF DEATH
    // echo $death_cert_attended;exit();
    if ($death_cert_attended=='0')
        $data[0]['deathcert_havenot'] = "X";
    if ($death_cert_attended=='1'){
        $data[0]['deathcert_have'] = "X";

        // if (($death_time !='00:00:00') && ($death_time!=""))
        // if ($death_time!="")
            $death_time = convert24HourTo12HourLocal($death_time);

        // else
            // $death_time = '';

            $data[0]['deathcert_time'] = strtoupper($death_time)." ";
    }
        $doctor = $pers_obj->get_Person_name($attendant_name);

        $middleInitial = "";
        if (trim($doctor['name_middle'])!=""){
            $thisMI=split(" ",$doctor['name_middle']);
            foreach($thisMI as $value){
                if (!trim($value)=="")
                $middleInitial .= $value[0];
            }
            if (trim($middleInitial)!="")
            $middleInitial .= ". ";
        }
        $doctor_name = $doctor["name_first"]." ".$doctor["name_2"]." ".$middleInitial.$doctor["name_last"];
        if (!empty($attendant_name))
            #$doctor_name = "Dr. ".ucwords(mb_strtolower($doctor_name));
            $doctor_name = mb_strtoupper($doctor_name).", MD";

    if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
        $tempYear = date("Y",strtotime($attendant_date_sign));
        $tempMonth = date("F",strtotime($attendant_date_sign));
        $tempDay = date("d",strtotime($attendant_date_sign));

        $attendant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
    }else{
        $attendant_date_sign = '';
    }

    $data[0]['certification_name'] = strtoupper($doctor_name)." ";
    $data[0]['certification_position'] = strtoupper($attendant_title)." ";

    //$attendant_address = substr_replace(trim($attendant_address)," ",20,1);
    $data[0]['certification_address'] =strtoupper($attendant_address)." ";
    //removed by jasper 04/01/2013
    if (strlen($attendant_address)>65) {
      $data[0]['certification_address1'] = strtoupper(seg_ucwords(trim(substr($attendant_address, 65))));
      //$data[0]['certification_address1'] = wordwrap($attendant_address, 36);
    }

    $data[0]['certification_date'] = strtoupper($attendant_date_sign)." ";
    
    #BURIAL/CREMATION PERMIT

    #INFORMANT
    if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
        $tempYear = date("Y",strtotime($informant_date_sign));
        $tempMonth = date("F",strtotime($informant_date_sign));
        $tempDay = date("d",strtotime($informant_date_sign));

        $informant_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
    }else{
        $informant_date_sign = '';
    }

    $data[0]['informant_name'] = strtoupper($informant_name)." ";
    $data[0]['informant_relation'] = strtoupper(ucwords(strtolower($informant_relation)))." ";
    $data[0]['informant_address'] = strtoupper($informant_address)." ";
    $data[0]['informant_date_sign'] = strtoupper($informant_date_sign)." ";

    #PREPARED BY
    if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
        $tempYear = date("Y",strtotime($encoder_date_sign));
        $tempMonth = date("F",strtotime($encoder_date_sign));
        $tempDay = date("d",strtotime($encoder_date_sign));

        $encoder_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
    }else{
        $encoder_date_sign = '';
    }

    $data[0]['preparedby_name'] = mb_strtoupper($encoder_name)." ";
    $data[0]['preparedby_position'] = strtoupper($encoder_title)." ";
    $data[0]['preparedby_date_sign'] = strtoupper($encoder_date_sign)." ";

    #RECEIVED BY
    if (($receivedby_date!='0000-00-00') && ($receivedby_date!="")){
        $tempYear = date("Y",strtotime($receivedby_date));
        $tempMonth = date("F",strtotime($receivedby_date));
        $tempDay = date("d",strtotime($receivedby_date));

        $receivedby_date_sign = $tempDay."  ".$tempMonth."  ".$tempYear;
    }else{
        $receivedby_date_sign = '';
    }

    $data[0]['receivedby_name'] = mb_strtoupper($receivedby_name)." ";
    $data[0]['receivedby_position'] = strtoupper($receivedby_title)." ";
    $data[0]['receivedby_date_sign'] = strtoupper($receivedby_date_sign)." ";


    # AGES 0 to 7 Days

//11. DATE OF BIRTH   not included in the new format

 if (($ageDay<8) && ($ageDay!="")){

    //12. AGE OF THE MOTHER
        $data[0]['age_of_mother'] = strtoupper($m_age)." ";

    //13. METHOD OF DELIVERY
            #$pdf->SetY(-0.5);
        if ($delivery_method_tmp=="1")
            $data[0]['delivery_method'] = strtoupper("Normal spontaneous vertex")." ";
        elseif ($delivery_method_tmp=="2")
            $data[0]['delivery_method'] = strtoupper($delivery_method_info)." ";

    //14. LENGTH OF PREGNANCY
        $data[0]['pregnancy_length'] = strtoupper($pregnancy_length)." ";

    //15. TYPE OF BIRTH
        if ($birth_type=='1')
            $data[0]['birth_type'] = strtoupper("Single")." ";
        elseif ($birth_type=='2')
            $data[0]['birth_type'] = strtoupper("Twin")." ";
        elseif ($birth_type=='3')
            $data[0]['birth_type'] = strtoupper("Triplet")." ";

    //16. IF MULTIPLE BIRTH, CHILD WAS
        $data[0]['birth_rank'] = strtoupper($birth_rank)." ";

        #CAUSES OF DEATH AGES 0-7 YEARS OLD
       // if (array_key_exists("cause1", $tmp_death_cause))
          $data[0]['cause1'] = strtoupper($tmp_death_cause['cause1'])." ";
       // elseif (array_key_exists("cause2", $tmp_death_cause))
          $data[0]['cause2'] = strtoupper($tmp_death_cause['cause2'])." ";
       // elseif (array_key_exists("cause3", $tmp_death_cause))
          $data[0]['cause3'] = strtoupper($tmp_death_cause['cause3'])." ";
       // elseif (array_key_exists("cause4", $tmp_death_cause))
          $data[0]['cause4'] = strtoupper($tmp_death_cause['cause4'])." ";
       // elseif (array_key_exists("cause5", $tmp_death_cause))
          $data[0]['cause5'] = strtoupper($tmp_death_cause['cause5'])." ";


    } //END OF CHILDEREN AGED 0 TO 7 DAYS
  $data[0]['name_address_cementery'] = strtoupper($cemetery_name_address." ");

if ($DeathCertInfo['is_late_reg'] == 1){
        
          

   #AFFIDAVIT FOR DELAYED REGISTRATION OF DEATH
   $data[0]['late_affiant_name'] = strtoupper($late_affiant_name)." ";
   $data[0]['late_affiant_address1'] = strtoupper($late_affiant_address)." ";
   $data[0]['late_dead_name'] = strtoupper((strtolower($name_first))." ".ucwords(strtolower($name_middle))." ".ucwords(strtolower($name_last)))." ";

   if (($death_date!='0000-00-00') && ($death_date!="")){
        $tempYear = date("Y",strtotime($death_date));
        $tempMonth = date("F",strtotime($death_date));
        $tempDay = date("d",strtotime($death_date));
    }
   $data[0]['late_ddate'] = strtoupper($tempDay . " " . $tempMonth . " " . $tempYear)." ";
   $data[0]['late_place_death'] = strtoupper($late_place_death)." ";
   $data[0]['late_cemetery'] = strtoupper($cemetery_name_address)." ";

   if (($late_bcdate!='0000-00-00') && ($late_bcdate!="")){
        $tempYear = date("Y",strtotime($late_bcdate));
        $tempMonth = date("F",strtotime($late_bcdate));
        $tempDay = date("d",strtotime($late_bcdate));
    }
   $data[0]['late_buried_date'] = strtoupper($tempDay . " " . $tempMonth . " " . $tempYear) ." ";

   if ($late_is_attended==1) {
      $data[0]['late_attended1'] = "X";
      $data[0]['late_attendedby'] = strtoupper($late_attended_by)." ";
   }
   elseif ($late_is_attended==2) {
      $data[0]['late_attended2'] = "X";
      $data[0]['late_attendedby'] = "";
   }

   $data[0]['late_death_cause'] = strtoupper(ucwords($late_death_cause))." ";
   $data[0]['late_reason1'] = strtoupper(ucwords($late_reason))." ";
}

   //LATE ACKNOWLEDGEMENT DATE
   #RECEIVED BY
    if (($late_sign_date!='0000-00-00') && ($late_sign_date!="")){
        $tempYear = date("Y",strtotime($late_sign_date));
        $tempMonth = date("F",strtotime($late_sign_date));
        $tempDay = date("d",strtotime($late_sign_date));
    }
        //$receivedby_date_sign = $tempDay."  ".$tempMonth."  ".$tempYear;
    //}else{
        //$receivedby_date_sign = '';
   // }
   $data[0]['late_affiant_ack_day'] = strtoupper($tempDay)." ";
   $data[0]['late_affiant_ack_month'] = strtoupper($tempMonth)." ";
   $data[0]['late_affiant_ack_year'] = strtoupper($tempYear)." ";

   $data[0]['late_affiant_ack_place'] = strtoupper($late_sign_place)." ";

   //SUBSCRIBED AND SWORN
   if (($late_officer_date_sign!='0000-00-00') && ($late_officer_date_sign!="")){
        $tempYear = date("Y",strtotime($late_officer_date_sign));
        $tempMonth = date("F",strtotime($late_officer_date_sign));
        $tempDay = date("d",strtotime($late_officer_date_sign));
    }

   $data[0]['subscribed_sworn_day'] = strtoupper($tempDay)." ";
   $data[0]['subscribed_sworn_month'] = strtoupper($tempMonth)." ";
   $data[0]['subscribed_sworn_year'] = strtoupper($tempYear)." ";

   $data[0]['subscribed_sworn_place'] = strtoupper($late_officer_place_sign)." ";

   $data[0]['late_affiant_comtax_nr'] = strtoupper($affiant_com_tax_nr)." ";

   if (($affiant_com_tax_date!='0000-00-00') && ($affiant_com_tax_date!="")){
        $tempYear = date("Y",strtotime($affiant_com_tax_date));
        $tempMonth = date("F",strtotime($affiant_com_tax_date));
        $tempDay = date("d",strtotime($affiant_com_tax_date));
    }
   $data[0]['late_affiant_comtax_date'] = strtoupper($tempDay . " " . $tempMonth . " " . $tempYear)." ";
   $data[0]['late_affiant_comtax_day'] = strtoupper($tempDay)." ";
   $data[0]['late_affiant_comtax_month'] = strtoupper($tempMonth)." ";
   $data[0]['late_affiant_comtax_year'] = strtoupper($tempYear)." ";

   $data[0]['late_affiant_comtax_place'] = strtoupper($affiant_com_tax_place)." ";

   $data[0]['late_officer_name'] = strtoupper($late_officer_name)." ";
   $data[0]['late_officer_title'] = strtoupper($late_officer_title)." ";
   $data[0]['late_officer_address'] = strtoupper($late_officer_address)." ";

   $data[0]['late_officer_name'] = $late_officer_name." ";
   $data[0]['late_officer_title'] = $late_officer_title." ";
   $data[0]['late_officer_address'] = $late_officer_address." ";

          $data[0]['cause_death_a'] = strtoupper($tmp_death_cause['cause6'])." ";
          $data[0]['cause_death_b'] = strtoupper($tmp_death_cause['cause7'])." ";
          $data[0]['cause_death_c'] = strtoupper($tmp_death_cause['cause8'])." ";
          $data[0]['cause_death_d'] = strtoupper($tmp_death_cause['cause9'])." ";

          $data[0]['interval_death_a'] = $tmp_death_cause['interval1']." ";
          $data[0]['interval_death_b'] = $tmp_death_cause['interval2']." ";
          $data[0]['interval_death_c'] = $tmp_death_cause['interval3']." ";

           if (array_key_exists("cause1", $tmp_death_cause)&&!array_key_exists("cause6", $tmp_death_cause)){
              $data[0]['cause_death_a'] = strtoupper($tmp_death_cause['cause1'])." ";
              $data[0]['interval_death_a'] = " ";
           }
           if (array_key_exists("cause2", $tmp_death_cause)&&!array_key_exists("cause7", $tmp_death_cause)){
              $data[0]['cause_death_b'] = strtoupper($tmp_death_cause['cause2'])." ";
              $data[0]['interval_death_b'] = " ";
           }
           if (array_key_exists("cause3", $tmp_death_cause)&&!array_key_exists("cause8", $tmp_death_cause)){
              $data[0]['cause_death_c'] = strtoupper($tmp_death_cause['cause3'])." ";
              $data[0]['interval_death_c'] = " ";
           }

           if (array_key_exists("cause5", $tmp_death_cause)&&!array_key_exists("cause9", $tmp_death_cause)){
                $data[0]['cause_death_d'] = strtoupper($tmp_death_cause['cause5'])." ";
                $data[0]['interval_death_d'] = " ";
            }
            

if ($corpse_disposal_tmp == 1){
    $corpse_disposal = "Burial";
}else if ($corpse_disposal_tmp == 2){
    $corpse_disposal = "Cremation";
}else if ($corpse_disposal_tmp == 3){
    $corpse_disposal = $DeathCertInfo['corpse_others'];
}


    $data[0]['interval_death_c'] = $tmp_death_cause['interval3']." ";

if ($corpse_disposal_tmp == 1){
    $corpse_disposal = "Burial";
}else if ($corpse_disposal_tmp == 2){
    $corpse_disposal = "Cremation";
}else if ($corpse_disposal_tmp == 3){
    $corpse_disposal = $DeathCertInfo['corpse_others'];
}

    $data[0]['corpse_disposal'] = $corpse_disposal." ";
    $data[0]['burial_number'] = $DeathCertInfo['burial_permit']." ";
    $data[0]['burial_date_issued'] = $DeathCertInfo['burial_date_issued'] != "0000-00-00" ? $DeathCertInfo['burial_date_issued'] : ""." ";

     $data[0]['transfer_number'] = $DeathCertInfo['transfer_permit']." ";
    $data[0]['transfer_date'] = $DeathCertInfo['transfer_date_issued'] != "0000-00-00" ? $DeathCertInfo['transfer_date_issued'] : ""." ";

    $data[0]['corpse_disposal'] = strtoupper($corpse_disposal)." ";
    $data[0]['burial_number'] = strtoupper($DeathCertInfo['burial_permit'])." ";
    // $data[0]['burial_date_issued'] = $DeathCertInfo['burial_date_issued'];

     $data[0]['transfer_number'] = strtoupper($DeathCertInfo['transfer_permit'])." ";
    // $data[0]['transfer_date'] = $DeathCertInfo['transfer_date_issued'];
   /*
    $data[0]['age_at_death'] = "63";
    $data[0]['age_days'] = "214";
    $data[0]['age_hours'] = "16";
    $data[0]['age_min_sec'] = "12.20";
    $data[0]['age_month'] = "6";
    $data[0]['age_of_mother'] = "22";
    $data[0]['attendant_a'] = "X";
    $data[0]['attendant_b'] = "X";
    $data[0]['attendant_c'] = "X";
    $data[0]['attendant_d'] = "X";
    $data[0]['attendant_e'] = "X";
    $data[0]['attendant_type_others'] = "Xxxxxx";
    $data[0]['attended_from'] = "xx/xx/xx";
    $data[0]['attended_to'] = "xx/xx/xx";
    $data[0]['birth_rank'] = "XXXXXX";
    $data[0]['birth_type'] = "XXXXXX";
    $data[0]['birthDay'] = "16";
    $data[0]['birthMonth'] = "September";
    $data[0]['birthYear'] = "1980";
    $data[0]['burial_date_issued'] = "xx/xx/xxxx";
    $data[0]['burial_number'] = "XXXXXXXXXX";
    $data[0]['cause1'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause2'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause3'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause4'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause5'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause_death_a'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause_death_b'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['cause_death_c'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['certification_address'] = "XXXXXXXXXXXXXXXXXXXXXXXXX, XXXXXXXXXXX, XXXXXXXXXXXXXXX";
    $data[0]['certification_date'] = "xx/xx/xxxx";
    $data[0]['certification_name'] = "XXXXXXXXXXXXXXXX X. XXXXXXXXXXX";
    $data[0]['certification_position'] = "XXXXXXXXXXXXXX";
    $data[0]['certification_sign'] = "";
    $data[0]['citizenship'] = "XXXXXXXXXXXXXXXXXX";
    $data[0]['civil_status'] = "Divorced";
    $data[0]['corpse_disposal'] = "XXXXXXXXXXXXXXX";
    $data[0]['date_birth'] = "XXXXXXXXXX";
    $data[0]['death_autopsy'] = "Xxx";
    $data[0]['death_external_cause'] = "XXXXXXXXX";
    $data[0]['death_external_cause_place'] = "XXXXXXXXXXX, XXXXXXXXXX";
    $data[0]['death_place_basic'] = "XXXXXXXXXXXXXX, XXXXXXXXXXXXXXXXXXXXXXXX, XXXXXXXXXXXXX";
    $data[0]['death_place_mun'] = "XXXXXXXXXXXXXXXXX";
    $data[0]['death_place_prov'] = "XXXXXXXXXXXXXXXXX";
    $data[0]['deathcert_have'] = "X";
    $data[0]['deathcert_havenot'] = "X";
    $data[0]['deathcert_time'] = "xx:xx:xx";
    $data[0]['deathDay'] = "12";
    $data[0]['deathMonth'] = "September";
    $data[0]['deathYear'] = "2005";
    $data[0]['delivery_method'] = "Xxxxxx Xxxxxxxxxx Xxxxxxxx";
    $data[0]['embalmer_address1'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX, XXXXXXXXXXXXXXXXX, xxxxxxxxxxxxxx";
    $data[0]['embalmer_address2'] = "xxxxxxxxxxxxxxx";
    $data[0]['embalmer_dead_name'] = "XXXXXXXXXXXXXXXXXXXXX X. XXXXXXXXXXXXXXXXX";
    $data[0]['embalmer_expiry_date'] = "xx/xx/xxxx";
    $data[0]['embalmer_issued_on'] = "xx/xx/xxxx";
    $data[0]['embalmer_issued_place'] = "XXXXXXXXXXX XXXXX";
    $data[0]['embalmer_license_no'] = "xxxxxxxx";
    $data[0]['embalmer_name'] = "Xxxxxxx Xxxxxx X. Xxxxxxxx";
    $data[0]['embalmer_sign'] = "";
    $data[0]['embalmer_title'] = "Xxxxxxxxxxxx";
    $data[0]['f_name_first'] = "Xxxxxxxxxxxxxxxxx";
    $data[0]['f_name_last'] = "Xxxxxxxxxxx";
    $data[0]['f_name_middle'] = "Xxxxxxxxxxx";
    $data[0]['informant_address'] = "XxXXXXXXxxxxxxxxxxxxxxxxxxxxxxxxxxx, xxxxxxxxxxxxxxxxxx";
    $data[0]['informant_date_sign'] = "xx/xx/xxxx";
    $data[0]['informant_name'] = "Xxxxxxxxxx Xxxxxxx X. XXXxxxxxxxxxxx";
    $data[0]['informant_relation'] = "Xxxxxxxxxxxxxxxx";
    $data[0]['informant_sign'] = "";
    $data[0]['interval_death_a'] = "XXXXXXXXXXXXXXXXXXXXX";
    $data[0]['interval_death_b'] = "XXXXXXXXXXXXXXXXXXXXX";
    $data[0]['interval_death_c'] = "XXXXXXXXXXXXXXXXXXXXX";
    $data[0]['late_affiant_ack_day'] = "12";
    $data[0]['late_affiant_ack_month'] = "November";
    $data[0]['late_affiant_ack_place'] = "Xxxxxxxxxxxx, Xxxxxxxxxxxx, Xxxxxxxxxxx";
    $data[0]['late_affiant_ack_sign'] = "";
    $data[0]['late_affiant_ack_year'] = "2012";
    $data[0]['late_affiant_address1'] = "Xxxxxxxxxxxxxxxxxxxxxxxxxxxxx, Xxxxxxxxxxxxxxxxx,xxxxxxxxxxxx";
    $data[0]['late_affiant_address2'] = "Xxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['late_affiant_comtax_date'] = "xx/xx/xxxx";
    $data[0]['late_affiant_comtax_nr'] = "CC001234234234234";
    $data[0]['late_affiant_comtax_place'] = "Xxxxxxxxxxxx Xxxx";
    $data[0]['late_affiant_name'] = "Xxxxxxxx xxxxxxxxxx X. Xxxxxxxxxxxxxx";
    $data[0]['late_attended1'] = "X";
    $data[0]['late_attended2'] = "X";
    $data[0]['late_attendedby'] = "Xxxxxxxxxxxx Xxxxxxxxxxxx X. Xxxxxxxxxx";
    $data[0]['late_buried_date'] = "xx/xx/xxxx";
    $data[0]['late_cemetery'] = "Xxxxxxxxxxxxxxx Xxxxxxxxxxxxxxxx Xxxxxxxxx";
    $data[0]['late_ddate'] = "xx/xx/xxxx";
    $data[0]['late_dead_name'] = "Xxxxxxxxxxxxxxxx Xxxxxxxxxxxxxx Xxxxxxxxxxxxx";
    $data[0]['late_death_cause'] = "Xxxxxxxxxxxxxxx Xxxxxxxxxxxxxx";
    $data[0]['late_officer_address'] = "Xxxxxxxxxxxxxxx Xxxxxxxxxxxxxxx, Xxxxxxxxx";
    $data[0]['late_officer_name'] = "Xxxxxxxxxxxxxx Xxxxxxxxxxxx";
    $data[0]['late_officer_sign'] = "";
    $data[0]['late_officer_title'] = "Xxxxxxxxxxxxxxxxx";
    $data[0]['late_place_death'] = "Xxxxxxxxxxxxx Xxxxxxxxxxxxx, Xxxxxxxxxxxx";
    $data[0]['late_reason1'] = "XXXXXXXXXXXXXxxxxxxxxxxxxxxxxxx";
    $data[0]['late_reason2'] = "Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['m_name_first'] = "XxxxxXXxxxxxxxx";
    $data[0]['m_name_last'] = "Xxxxxxxxxxxxxxxx";
    $data[0]['m_name_middle'] = "Xxxxxxxxxxxxx";
    $data[0]['maternalcondition_a'] = "X";
    $data[0]['maternalcondition_b'] = "X";
    $data[0]['maternalcondition_c'] = "X";
    $data[0]['maternalcondition_d'] = "X";
    $data[0]['maternalcondition_e'] = "X";
    $data[0]['name_address_cementery'] = "Xxxxxxxxxxxxxxxxxxxxxxx, Xxxxxxxxxxxxxxx, Xxxxxxxxxxxxxxxxx";
    $data[0]['name_first'] = "Xxxxxxxxxxxxx";
    $data[0]['name_last'] = "Xxxxxxxxxxxxxx";
    $data[0]['name_middle'] = "Xxxxxxxxxxxxxx";
    $data[0]['occupation'] = "Xxxxxxxxxxxxxxxxxxx";
    $data[0]['postmortem_address1'] = "Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['postmortem_address2'] = "Xxxxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['postmortem_cause1'] = "Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['postmortem_cause2'] = "Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['postmortem_date'] = "xx/xx/xxxx";
    $data[0]['postmortem_name'] = "Xxxxxxxxxxxxxxx Xxxxxxxxxxx, Xxxxxxxxxx";
    $data[0]['postmortem_sign'] = "";
    $data[0]['postmortem_title'] = "Xxxxxxxxxxxxxxx";
    $data[0]['pregnancy_length'] = "xx Xxxxxx";
    $data[0]['preparedby_date_sign'] = "xx/xx/xxxx";
    $data[0]['preparedby_name'] = "Xxxxxxxxxxxxxx Xxxxx Xxxxxxxxxxxxxx";
    $data[0]['preparedby_position'] = "Xxxxxxxxxxxxxxxx";
    $data[0]['preparedby_sign'] = "";
    $data[0]['receivedby_date_sign'] = "xx/xx/xxxx";
    $data[0]['receivedby_name'] = "Xxxxxxxxxxxx X. Xxxxxxxxxxxx";
    $data[0]['receivedby_position'] = "Xxxxxxxxxxxxxxxxx";
    $data[0]['receivedby_sign'] = "";
    $data[0]['registered_date'] = "xx/xx/xxxx";
    $data[0]['registered_name'] = "Xxxxxxxxxxxxxx Xxxxxxxxxxxxx X. Xxxxxxxxxxx";
    $data[0]['registered_position'] = "Xxxxxxxxxxxxxxxxxxxx";
    $data[0]['registered_sign'] = "";
    $data[0]['registry_nr'] = "XXXXXXXXXXXXXXXXXXXXXX";
    $data[0]['religion'] = "Xxxxxxxxxxxxxxxxxxxxx";
    $data[0]['remarks_annotation'] = "Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $data[0]['residence_place'] = "Xxxxxxxxxxxxxxx Xxxxxxxxxxxxx, Xxxxxxxxxxxx, XXXXXXXXXX";
    $data[0]['reviewed_date'] = "xx/xx/xxxx";
    $data[0]['reviewed_name'] = "Xxxxxxxxxxxx X. Xxxxxxxxxxxxxxx";
    $data[0]['sex'] = "Xxxxxx";
    $data[0]['subscribed_sworn_place'] = "XxxxxxxxxxXxxxxxxxxxx, Xxxxxxxxxxxx, XXXXXXXXXX";
    $data[0]['subscribed_sworn_day'] = "23";
    $data[0]['subscribed_sworn_month'] = "Sepetember";
    $data[0]['subscribed_sworn_year'] = "2010";
    $data[0]['transfer_date'] = "xx/xx/xxxx";
    $data[0]['transfer_number'] = "XXXXXXXXXXXXXXXXXX";
   */
}

//modified by jasper 01/09/2013
$jCollection = new Java("java.util.ArrayList");
foreach ($data as $i => $row) {
    $jMap = new Java('java.util.HashMap');
    foreach ( $row as $field => $value ) {
        $jMap->put($field, $value);
    }
    $jCollection->add($jMap);
}

$jMapCollectionDataSource = new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
$jasperPrint = $fillManager->fillReport($report, $params, $jMapCollectionDataSource);

$end = microtime(true);

$outputPath  = tempnam(java_tmp, '');
chmod($outputPath, 0777);

$exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
$exportManager->exportReportToPdfFile($jasperPrint, $outputPath);


header("Content-type: application/pdf;");
#header('Content-Transfer-Encoding: binary');
#header('Content-Disposition: attachment; filename="DeathCertificate.pdf"');
readfile($outputPath);

unlink($outputPath);
