<?
/*
This file is to be included from the main index file for ajax efficiency.
-When you want to use ajax instead of a simple HREF Tag, please comment linkAsAjax() line (find this line below and comment it: $newPaging->linkAsAjax('post', 'testcontainer', 'refresher.php', "");)
*/
require_once('./roots.php');
require_once($root_path.'include/care_api_classes/class_pagination.php');

/*
TRY PLAYING WITH THESE VARIABLES:
RECORD LIMIT PER PAGE = 10 ($limitpp)
PAGES PER GROUP = 10 ($pagespergroup)
SPACER = spacer between page numbers
*/
$pagespergroup = 10;
$limitpp = 10; 
$spacer = ' | ';

//no need to edit anything beyond this line except for all query variables:
//change $query, $orderby to reflect your own; change $fetchnow->lname to suite your own column name

$startat = 0;
$mul=0;

foreach($_REQUEST as $key=>$val){
	$$key=$val;
}


$query = "SELECT pid, name_first, name_middle, name_last FROM care_person where name_last <> ''";
$orderby = " ORDER BY name_last, name_middle, name_first";

$newPaging = new CLS_PAGING($query, $orderby, $limitpp, '');
// $newPaging->linkAsAjax('post', 'testcontainer', 'refresher.php', "");

$res = $newPaging->showPageRecs($startat);
while ($fetchnow = $res->FetchRow()) {
	echo $newPaging->getRecNum($ctr, $mul).'.) '.$fetchnow["name_first"].', '.$fetchnow["name_middle"].', '.$fetchnow["name_last"].'<br>';
	$ctr++;
}


//'First', 'prev', 'next' and 'Last' are variables that can be changed!
//showFirst, showPrev, showNext and showLast are optional functions
/*echo $newPaging->showFirst('First')."&nbsp;".
	 $newPaging->showPrev('prev', $mul)."&nbsp;".
	 $newPaging->showPages($spacer, $pagespergroup, $mul)."&nbsp;".
	 $newPaging->showNext('next', $mul)."&nbsp;".
	 $newPaging->showLast('Last');
*/	 
echo $newPaging->showFirst('First')."&nbsp;".
	 $newPaging->showPrev('prev', $mul)."&nbsp;".	
	 $newPaging->showNext('next', $mul)."&nbsp;".
	 $newPaging->showLast('Last');	 

?>