<?php session_start();
/* Print a report
 * repgen_druck.php for PHP Report Generator
   Bauer, 5.2.2002
   Version 0.2
*/
// this has to be an own page, because otherwise we could not get Content-type application/pdf
require_once("repgen.inc");
if (!isset($reportnr))$reportnr=$id;
srand(time()); $r = rand(1,1000);
$file = "tmp/file".$r.".pdf";
page_header();
create_report($_GET['database'],$_GET['host'],$_GET['user'],$_GET['password'],$_POST['reportnr'], $file);
?>
<BR><BR><BR><BR><BR><BR><BR><BR>
<H1><a href=<?php echo $file; ?>> Click on this link to get the
the PDF-file</A></H1>
</body>
</html>



