<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','or.php');
$local_user='ck_op_pflegelogbuch_user';
require_once($root_path.'include/inc_front_chain_lang.php');
?>
<?php html_rtl($lang); ?>
<!-- Generated by AceHTML Freeware http://freeware.acehtml.com -->
<!-- Creation date: 08.12.2001 -->
<head>
<?php echo setCharSet(); ?>
<title></title>

</head>
<frameset cols="16%,*">
<?php if($mode=="cont") : ?>
  <frame name="OPMINPUTFRAME" src="op-logbuch-container-input.php?sid=<?php  echo "$sid&lang=$lang&op_nr=$op_nr&enc_nr=$enc_nr&dept_nr=$dept_nr&saal=$saal&pday=$pday&pmonth=$pmonth&pyear=$pyear"; ?>">
  <frame name="OPMLISTFRAME" src="op-logbuch-container-list.php?sid=<?php  echo "$sid&lang=$lang&op_nr=$op_nr&enc_nr=$enc_nr&dept_nr=$dept_nr&saal=$saal&pday=$pday&pmonth=$pmonth&pyear=$pyear"; ?>">
<?php else : ?>
  <frame name="OPMINPUTFRAME" src="op-logbuch-material-input.php?sid=<?php  echo "$sid&lang=$lang&op_nr=$op_nr&enc_nr=$enc_nr&dept_nr=$dept_nr&saal=$saal&pday=$pday&pmonth=$pmonth&pyear=$pyear"; ?>">
  <frame name="OPMLISTFRAME" src="op-logbuch-material-list.php?sid=<?php  echo "$sid&lang=$lang&op_nr=$op_nr&enc_nr=$enc_nr&dept_nr=$dept_nr&saal=$saal&pday=$pday&pmonth=$pmonth&pyear=$pyear"; ?>">
<?php endif ?>
<noframes>
<body>


</body>
</noframes>
</frameset>
</html>
