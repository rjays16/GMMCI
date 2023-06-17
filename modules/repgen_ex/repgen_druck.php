<?php session_start();
require_once("repgen_const.inc");
require_once("repgen_def.inc");

$url_druck = "repgen_druck1.php?".$_SERVER['QUERY_STRING'];


?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Title here!</title>
</head>
<body>
<?php  page_header();
?>
  <form action="<?php echo $url_druck; ?>" method="post" target="_top">
  <input type=hidden name="reportnr" value = "<? echo $_GET['id']; ?>" >
  <BR><BR>
  <input type=submit value="<? echo DRUCKEN; ?>" >
</form>
<?php page_footer();
?>
</body>
</html>

