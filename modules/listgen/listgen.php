<?php

require('./roots.php');
require_once('listgen.inc.php');
if(!class_exists('ListGen'))
{

define('LISTGEN_VERSION','1.0');
class ListGen
{

var $rootPath;
var $listSettings;

function ListGen($rootPath) {
	$this->rootPath = $rootPath;
	$this->loadDefaults();
}

function loadDefaults() {
	global $LG_DEFAULT_VALUES;
	$this->listSettings = $LG_DEFAULT_VALUES;
}

function setListSettings($item, $value) {
	$this->listSettings[$item] = $value;
}

function getListSettings($item) {
	return $this->listSettings[$item];
}

function printJavascript() {
	print '		<script type="text/javascript" language="javascript" src="'.$this->rootPath.'modules/listgen/listgen.js?t='.time().'"></script>'."\n";
}

function getVersion() {
	return LISTGEN_VERSION;
}

function _set_if_null(&$x, $y) {
	$x = $x ? $x : $y;
}

function createList($id='', $headers='', $sort='', $fetcher = '') {
	$ls = $this->listSettings;
	if (is_array($id)) {
		$ls = array_merge($ls, $id);
	}

	$this->_set_if_null ( $ls['ROOT_PATH'], $this->rootPath );
	$this->_set_if_null ( $ls['LIST_ID'], $id );
	$this->_set_if_null ( $ls['COLUMN_HEADERS'], $headers);
	$this->_set_if_null ( $ls['COLUMN_COUNT'], sizeof($ls['COLUMN_HEADERS']) );
	$this->_set_if_null ( $ls['COLUMN_SORTING'], $sort );
	$this->_set_if_null ( $ls['AJAX_FETCHER'], $fetcher );

	return new LGList($ls);
}

}



class LGList
{

// Initialization properties
var $listId;									// unique identifier for the list object
var $rootPath;								// root path for current script
var $columnCount;							// number of columns for the list
var $columnHeaders;						// string array of the header HTMLs
var	$columnSorting;						// array indicating default sorting (<0=descending, 0=no sorting,>0=ascending, null=not sortable)
var $maxRows;									//
var $emptyMessage;						//

// CSS class values
var $listClass;								//
var $navClass;								//
var $activeClass;							//
var $inactiveClass;						//

// Private properties
var $navMessage;
var $currentPage;
var $lastPage;



/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/

/**
 * Initializes the ListGen object using the settings specified in
 * the $init array
 */
function LGList($init) {
	$this->listId  = $init["LIST_ID"];
	$this->rootPath = $init["ROOT_PATH"];
	$this->columnWidths = $init["COLUMN_WIDTHS"];
	$this->columnHeaders = $init["COLUMN_HEADERS"];
	$this->columnSorting = $init["COLUMN_SORTING"];
	$this->columnCount = $init["COLUMN_COUNT"];
	$this->maxRows = $init["MAX_ROWS"];


	$this->listClass = $init["LIST_CLASS"];
	$this->navClass = $init["NAV_CLASS"];
	$this->activeClass = $init["ACTIVE_CLASS"];
	$this->inactiveClass = $init["INACTIVE_CLASS"];
	$this->emptyMessage = $init["EMPTY_MESSAGE"];
	$this->initialMessage = $init["INITIAL_MESSAGE"];
	$this->ajaxFetcher = $init["AJAX_FETCHER"];
	$this->fetcherParams = $init["FETCHER_PARAMS"];
	$this->reloadOnLoad = $init["RELOAD_ONLOAD"];
	$this->showRefresh = $init["SHOW_REFRESH"];

	$this->addMethod = $init["ADD_METHOD"];
	$this->clearMethod = $init["CLEAR_METHOD"];
	$this->removeMethod = $init["REMOVE_METHOD"];
}

function printList() {
	print $this->getHTML();
}


function _pick($s) {
	return $s ? "'$s'" : NULL;
}

/**
 * Returns the static HTML code of the list object
 */
function getHTML() {
	foreach ($this->columnSorting as $i=>$v)
		$csort[$i] = is_numeric($v) ? $v : 'null';

	$js_properties = array(
		'ajaxFetcher' => $this->_pick($this->ajaxFetcher),
		'sortOrder' => '['.implode(",",$csort).']',
		'maxRows' => $this->_pick($this->maxRows),
		'emptyMessage' => "'".addslashes($this->emptyMessage)."'",
		'initialMessage' => "'".addslashes($this->initialMessage)."'",
		'columnCount' => $this->_pick($this->columnCount),
		'add' => $this->addMethod,
		'clear' => $this->clearMethod,
		'remove' => $this->removeMethod,
		'columnWidths' => "['".explode("','",$this->columnWidths)."']"
	);
	$fp=array();
	if (is_array($this->fetcherParams)) {
		foreach ($this->fetcherParams as $i=>$v) {
			$fp[] = "'$i':'$v'";
		}
	}
	$js_properties['fetcherParams'] = '{ '.implode(",",$fp).' }';



// Table definition
	$out = '
<table id="'.$this->listId.'" class="'.$this->listClass.'" width="100%" border="0" cellpadding="0" cellspacing="0" currentPage="0" lastPage="0" totalRows="0">
	<thead>';

	// Paginator
	if ($this->maxRows) {
		$out .= '
		<tr class="'.$this->navClass.'">
			<th colspan="'.$this->columnCount.'">
				<div id="page-first-'.$this->listId.'" class="'.((isset($this->currentPage) && $this->currentPage>0)?$this->activeClass:$this->inactiveClass).'" style="float:left;padding:1px 3px" onclick="if (!$(this).hasClassName(\''.$this->inactiveClass.'\')) '.$this->listId.'.jump('.$this->listId.'.FIRST_PAGE)">
					<img title="First" src="'.$this->rootPath.'images/start.gif" border="0" align="absmiddle"/>
					<span title="First">First</span>
				</div>
				<div id="page-prev-'.$this->listId.'" class="'.((isset($this->currentPage)&&$this->currentPage>0)?$this->activeClass:$this->inactiveClass).'" style="float:left;padding:1px 3px" onclick="if (!$(this).hasClassName(\''.$this->inactiveClass.'\')) '.$this->listId.'.jump('.$this->listId.'.PREV_PAGE)">
					<img title="Previous" src="'.$this->rootPath.'images/previous.gif" border="0" align="absmiddle"/>
					<span title="Previous">Prev</span>
				</div>
				<div id="page-message-'.$this->listId.'" style="float:left;margin:0 4px; padding:1px 3px">
					<span>'.$this->navMessage.'</span>
				</div>
				<div id="page-next-'.$this->listId.'" class="'.((isset($this->currentPage)&&$this->currentPage<$this->lastPage)?$this->activeClass:$this->inactiveClass).'" style="float:left;padding:1px 3px" onclick="if (!$(this).hasClassName(\''.$this->inactiveClass.'\')) '.$this->listId.'.jump('.$this->listId.'.NEXT_PAGE)">
					<span title="Next">Next</span>
					<img title="Next" src="'.$this->rootPath.'images/next.gif" border="0" align="absmiddle"/>
				</div>
				<div id="page-last-'.$this->listId.'" class="'.((isset($this->currentPage)&&$this->currentPage<$this->lastPage)?$this->activeClass:$this->inactiveClass).'" style="float:left;padding:1px 3px" onclick="if (!$(this).hasClassName(\''.$this->inactiveClass.'\')) '.$this->listId.'.jump('.$this->listId.'.LAST_PAGE)">
					<span title="Last">Last</span>
					<img title="Last" src="'.$this->rootPath.'images/end.gif" border="0" align="absmiddle"/>
				</div>
				<div id="page-refresh-'.$this->listId.'" class="'.$this->activeClass.'" style="float:left; margin-left:10px; padding:1px 3px" onclick="if (!$(this).hasClassName(\''.$this->inactiveClass.'\')) '.$this->listId.'.reload()">
					<span title="Refresh">Refresh</span>
					<img title="Refresh" src="'.$this->rootPath.'images/refresh.gif" border="0" align="absmiddle"/>
				</div>
			</th>
		</tr>';
	}

	// Column headers
	$out .= '
		<tr>';
	foreach ($this->columnHeaders as $i=>$h) 	{
		$out .= '
			<th id="'.$this->listId.'-list-header-'.$i.'" nowrap="nowrap"'.
				($this->columnWidths[$i] ? ('width="'.$this->columnWidths[$i].'" ') : "")
				.'columnIndex="'.$i.'" onmouseoverex="lgSortMouseOver(this)" onmouseoutex="lgSortMouseOut(this)" onclickex="'.$this->listId.'.sort('.$i.')">
				<span>'.$h.'</span>
				<img id="'.$this->listId.'-sortdn-'.$i.'" src="'.$this->rootPath.'images/sort_down2.gif" align="absmiddle" style="display:none">
				<img id="'.$this->listId.'-sortup-'.$i.'" src="'.$this->rootPath.'images/sort_up2.gif" align="absmiddle" style="display:none">
			</th>';
	}

	// Body
	$out .= '
		</tr>
	</thead>
	<tbody id="list-body-'.$this->listId.'" style="'.($this->reloadOnLoad ? 'display:none' : '').'">';

	$out .= '
		<tr><td colspan="'.$this->columnCount.'">'.$this->initialMessage.'</td></th>
';

	$out .= '
	</tbody>
	<tbody id="list-loader-'.$this->listId.'" style="'.($this->reloadOnLoad ? '' : 'display:none').'">
		<tr>
			<td colspan="'.$this->columnCount.'"></td>
		</tr>
	</tbody>
</table>
<div id="list-loader-div-'.$this->listId.'" class="lgAjaxLoad" style="z-level:999; position:absolute"></div>
';

	$out .= '
<script type="text/javascript" language="javascript">
<!--
var '.$this->listId.' = new LGList(\''.$this->listId.'\');
';
	foreach ($js_properties as $i=>$v)
		if ($v)	$out .= $this->listId.".$i=$v;
";
	if ($this->reloadOnLoad) $out.="$this->listId.reload();\n";
	$out.= '
-->
</script>
';
	return $out;
}


/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/


}	//End of ListGen class definition
}

?>