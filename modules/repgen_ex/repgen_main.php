<?php session_start(); 
/* Main Routine repgen_main.php for PHP Report Generator 
   Bauer, 22.5.2001 
   Version 0.1 
*/ 
global $database, $host, $user, $password,$dbtyp; 
 
require_once("repgen_const.inc"); 
require_once("repgen_def.inc"); 
require_once("repgen.inc"); 
  
if (!(empty($database1) || empty($host1)|| empty($user1) || empty($password1)) && empty($msg)) { 
  // test, if you can connect to database 
  $db = new DB_Repgen; 
  $db->set_variables($database1,$host1,$user1,$password1); 
  $db->Halt_On_Error = "report"; 
  $db->connect($database1,$host1,$user1,$password1); 

      // if I cannot connect, I continue with index.php and a message in $msg 
  if  (empty($db->Errno)) {    
      $db->query("select * from reports where id = '0000000000'"); 
      $db->Halt_On_Error = "yes"; 
      // there is a connection 
      session_register("database"); 
      session_register("host"); 
      session_register("user"); 
      session_register("password"); 
      $database = $database1; 
      $host     = $host1; 
      $user     = $user1; 
      $password = $password1; 
 
  // first there is all work to be done because a submit buttons has been punched 
     while ( is_array($HTTP_POST_VARS) 
       && list($key, $val) = each($HTTP_POST_VARS)) { 
      switch ($key) { 
    ## Create Report 
      case "create": 
            // create a new report Id 
            $dbn = new DB_Repgen; 
            $dbn->connect($database,$host,$user,$password); 
            $dbn->query("select typ,id from reports where typ = 'info' order by id"); 
            while($dbn->next_record()){$n = $dbn->f("id");} 
            $n = $n +1; 
            $url=REPGENDIR."/repgen_create.php?".SID; 
            $url.="&id_neu=$n&short=&long=&author=&group=&sql=&print_format=&print_size=&report_type=&id=&group_type="; 
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_create.php 
            exit; 
           break; 
           
      case "select": 
            // select a stored report for altering 
            $url=REPGENDIR."/repgen_select.php?".SID; 
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_select.php 
            exit; 
 
          break; 
 
 
      default: 
 
      break; 
      } 
     } 
   }   // of if (empty($db->Errno) 
 
 } // of if variables set 
?> 

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">  
<html> 

 
<?php page_header(); ?> 
<strong><? echo FIRST ?></strong> 
<?php 
if (!empty($msg)) {   // Error Message: No connect to database 
    my_error($msg); 
    $msg=NULL; 
    } 
?> 
<br> <br> 
<form action="index.php?<?=SID?>" method = "post"> 
<TABLE> 
<TR> <TD align = "right"> <? echo DATABASE;?> </TD> <TD> <input type=text name="database1" value ="<? echo $database1;?>"></TD></TR> 
<TR> <TD align = "right"> <? echo HOST;?> </TD> <TD> <input type="text" name="host1" value = "<? echo $host1; ?>" ></TD></TR> 
<TR> <TD align = "right">  <? echo USER;?> </TD> <TD> <input type="text" name="user1" value = "<? echo $user1; ?>"></TD></TR> 
<TR> <TD align = "right"> <? echo PASS;?> </TD> <TD> <input type=password name="password1" value="<?echo $password1;?>"></TD></TR> 
</table> 
 <br> 
 
 <input type="submit" value="<? echo CREATE;?>" name="create"> 
 <br> <br> 
<br> 
 
 <input type="submit" value= "<?echo SELECT;?>" name="select" > 
 </form> 
 </center> 
<? page_footer(); ?> 
</body> 
</html> 
