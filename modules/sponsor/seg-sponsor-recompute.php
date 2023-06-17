<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/sponsor-compute.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',FALSE);
$smarty->assign('bHideCopyright',TRUE);

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$title = "Auto-compute billing payment";

# Title in the title bar 
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad="init()"');

include_once($root_path."include/care_api_classes/sponsor/class_grant_account.php");
$ac = new SegGrantAccount();

include_once($root_path."include/care_api_classes/sponsor/class_billing_info.php");
$bc = new SegBillingInfo();
$totalBill = $bc->getTotalBill($_REQUEST['nr']);
if (!$totalBill) die('Unable to retrieve billing information...');

# Collect javascript code
ob_start()

?>
<style type="text/css">
<!--
  .displayTotals {
    text-align:right;
    font-family:Arial; 
    font-size:16px; 
    font-weight:bold;
  }
  
  .displayTotalsLink {
    font-family:Arial; 
    font-size:16px; 
    font-weight:bold;
    cursor:pointer;
    color:#000066;
  }

  span.displayTotalsLink:hover {
    text-decoration:underline;
    color:#660000;
  }
  
  .priorityIndicator {
    font: bold 10px Tahoma;
    background-color: #43609c;
    border-color:1px solid #768bb7;
    padding: 2px 4px;
    color: white;
    -moz-border-radius: 4px;
  }
  
  ul.sortable {
    width: 100%;
    list-style: none;
    margin: 0;
    display: block;
    border-collapse: collapse;
  }
  
  ul.sortable li {
    margin: 0;
    width: 100%;
  }
  
-->
</style>  
<script language="javascript" >
<!--
var AJAXTimerID=0;
var isLoading=false;

function init() {
  xajax.call('populateAutoComputedEntries',{ parameters:['<?= $_REQUEST['nr'] ?>'] });
}

function startLoading() {
  if (!isLoading) {
    isLoading = 1;
    return overlib('<span style="font:bold 12px Tahoma">Computing billing items...</span><br/><img src="../../images/ajax_bar.gif"/>',
      WIDTH,300, TEXTPADDING,5, BORDER,0,
      STICKY, CLOSECLICK, MODAL,
      NOCLOSE, TIMEOUT, 0, OFFDELAY, 0,
      CAPTION,'Loading', 
      MIDX,0, MIDY,0,
      STATUS,'Loading');
  }
}

function doneLoading() {
  if (isLoading) {
    setTimeout('cClick()', 500);
    isLoading = 0;
  }
}

function parseFloatEx(x) {
  var str = x.toString().replace(/\,|\s/,'')
  return parseFloat(str)
}

function autoCompute() {
  if (confirm('This will reset previous amounts set for this billing payment. Proceed?')) {
    var accounts = new Array(), amounts=new Array(), status=new Array();
    var aa = $$('[name=accounts]'),
        mm = $$('[name=amounts]'),
        pp = $$('[name=status]')

    for (i=0;i<aa.length;i++) {
      accounts.push(aa[i].value);
      if (mm[i].value==0)
        amounts.push(-1);
      else
        amounts.push(parseFloatEx(mm[i].value));
      status.push(pp[i].value);
    }
    startLoading();
    xajax.call('autoCompute', { parameters: ['<?= $_REQUEST['nr'] ?>', accounts, amounts, status] } );
  }
}

function formatNumber(num,dec) {
  var nf = new NumberFormat(num);
  if (isNaN(dec)) dec = nf.NO_ROUNDING;
  nf.setPlaces(dec);
  return nf.toFormatted();
}

function addAccount() {
  if (!$('select-account').value) {
    alert('Select a payment account/guarantor...');
    $('select-account').focus();
    return false;
  }
  while (true) {
    amount=prompt('Enter amount:');
    if (amount===null) return false;
    if (!isNaN(amount)) break;
  }
  
  add( { id: $('select-account').value, name: $('select-account').options[$('select-account').selectedIndex].text, amount:amount, status:0, FLAG:1 } )
}

function refreshTotals() {
  var mm = $$('[name=amounts]');
  var amount=0;
  if (mm) {
    for (var i=0;i<mm.length;i++) {
      amount += parseFloatEx(mm[i].value);
    }
  }
  $('show-grant-total').update(formatNumber(amount,2));
}

function clear() {
  var list = $('grant-accounts');
  if (list) {
    var dBody=list.select("tbody")[0];
    if (dBody) dBody.update('');
  }
}

function reprioritize() {
  var pp = $$('[name=priority]');
  var i=1;
  if (pp) {
    for (var i=0;i<pp.length;i++) {
      pp[i].update(i+1);
    }
  }
}

function moveUp(obj) {
  var p=$(obj).up(1), prev=p.previous();
  if (prev) {
    p.remove();
    prev.up().insertBefore(p, prev);
    reclassRows();
  }
  else {
    return false;
  }
}

function moveDown(obj) {
  var p=$(obj).up(1), next=p.next();
  if (next) {
    next.remove();
    p.up().insertBefore(next, p);
    reclassRows();
  }
  else {      
    return false;
  }
}

function reclassRows(startIndex) {
  var list=$('grant-accounts');
  if (typeof(startIndex)=='undefined') startIndex=0;
  if (list) {
    var dBody=list.select("tbody").first();
    if (dBody) {
      var dRows = dBody.select("tr");
      if (dRows) {
        for (i=startIndex;i<dRows.length;i++) {
          
          if (i%2>0)
            dRows[i].addClassName('alt');
          else
            dRows[i].removeClassName('alt');
          
          dRows[i].select('img.priorityUp').first()
            .removeClassName( (i==0) ? 'segSimulatedLink' : 'segDisabledLink')
            .addClassName( (i==0) ? 'segDisabledLink' : 'segSimulatedLink');
            
          dRows[i].select('img.priorityDown').first()
            .removeClassName( (i==dRows.length-1) ? 'segSimulatedLink' : 'segDisabledLink')
            .addClassName( (i==dRows.length-1) ? 'segDisabledLink' : 'segSimulatedLink');
        }
      }
    }
  }
}

function tooltip (text) {
  return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}


function startSplit() {
  var amt2;
  while (amt2 = prompt('Enter amount for the new account')) {
    if (isNaN(amt2) || amt2==0) {
      alert('Invalid amount');
      cClick();
      return false;
    }
    else
      break;
  }
  
  var obj1=$('ol_split').select('#src_split_id').first()
      obj2=$('ol_split').select('#new_split_sel').first();
      
  var id1=obj1.value,
      id2=obj2.options[obj2.selectedIndex].value;
  
  if (!id2) {
    alert('Pleas select a guarantor account...');
    return false;
  }
  
  var amt1=parseFloat($('bga_amount_'+id1).value);
  if (amt1 > amt2) {
    $('bga_amount_'+id1).value = amt1-amt2;
    $('bga_amount_span_'+id1).update(formatNumber(amt1-amt2, 2));
    add ( 
      {
        id: id2,
        name: obj2.options[obj2.selectedIndex].text,
        amount: amt2,
        status: '',
        FLAG: 1
      }, $('bga_row_'+id1)
    );
    reclassRows();
  }
  else {
    alert('Amount entered exceeds original amount...')
  }

  cClick();
  return true;
}


function splitAccount (id) {
  var acct=$('bga_acct_'+id).value,
    name=$('bga_name_'+id).innerHTML,
    amt=$('bga_amount_'+id).value
    
    
  $('src_split_name').update(name);
  $('src_split_id').value = id;
  //$('src_split_amount').value = formatNumber(amt, 2);
      
  overlib(
    '<div id="ol_split">'+$('split_ui').innerHTML+'</div>',
    WIDTH,400, TEXTPADDING,0, BORDER,0,
    STICKY, CLOSECLICK, MODAL,
    CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
    CAPTIONPADDING,2, 
    CAPTION,'Set amount to split',
    MIDX,0, MIDY,0, 
    STATUS,'Set amount to split');
  return false;
}

function removeItem(id) {
  var destTable, destRows;
  var table = $('grant-accounts');
  var rmvRow=$('bga_row_'+id);
  if (table && rmvRow) {
    var rndx = rmvRow.rowIndex-1;
    rmvRow.remove();
    if (!document.getElementsByName("amounts") || document.getElementsByName("amounts").length <= 0)
      add({});
    reclassRows(rndx);
  }
  refreshTotals();
}

function addPaid(details) {
  var list = $('paid-grant');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      var lastRowNum = null,
          dRows = dBody.select("tr[name=rows]");
      if (!details) details = { FLAG: false};
      
      if (!dRows.length) {
        dBody.update('');
      }
      if (details['FLAG']) {
        //alt = (dRows.length%2>0) ? ' class="alt"':'';
        var id=dRows.length,
          account=details['id'],
          name=details['name'],
          amount=details["amount"],
          status=details["status"],
          disabled=(details["disabled"]==1);
        alt = (dRows.length%2>0) ? 'alt' : '';
        var disabledAttrib = disabled ? 'disabled="disabled"' : '';
        var showAmount;
        
        if (parseFloatEx(amount)>0) showAmount = formatNumber(amount,2);
        else showAmount = 'EXCESS'
        var row = new Element('tr', { class: alt, name:'rows', id:'bgp_row_'+id , style:'height:22px' } ).update(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'bgp_priority_'+id, name:'priority', class:'priorityIndicator' }).update(dRows.length+1)
          )
        ).insert(
          new Element('td', { class:'centerAlign' })
        ).insert(
          new Element('td', { class:'leftAlign', style:'padding-left:4px' }).update(
            new Element('span', { id: 'bgp_name_'+id, style:'color:#003300' }).update(name)
          ).insert(
            new Element('input', { id: 'bgp_id_'+id, name:'id', type:'hidden', value:id })
          ).insert(
            new Element('input', { id: 'bgp_acct_'+id, name:'accounts', type:'hidden', value:account })
          ).insert(
            new Element('input', { id: 'bgp_status_'+id, name:'status', type:'hidden', value:status })
          ).insert(
            new Element('input', { id: 'bgp_amount_'+id, name:'amounts', type:'hidden', value:amount })
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'bgp_amount_span_'+id, style:'font:bold 11px Tahoma;color:#000066' }).update(showAmount)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'bgp_status_span_'+id, style:'font:bold 11px Tahoma;color:#000066' }).update( status ? '<img title="paid" src="../../images/paid_item.gif" />' : '')
          )
        ).insert(
          new Element('td', { class:'centerAlign' })
        );
        dBody.insert(row);
        reclassRows();
        refreshTotals();
      }
      else {
        if (!details.message)
          dBody.update('<tr><td colspan="10">No payments found...</td></tr>');
        else
          dBody.update('<tr><td colspan="10">'+details.message+'...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

function add(details, insertAfter) {
  var list = $('grant-accounts');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      var lastRowNum = null,
          dRows = dBody.select("tr[name=rows]");
      if (!details) details = { FLAG: false};
      
      if (!dRows.length) {
        dBody.update('');
      }
      if (details['FLAG']) {
        //alt = (dRows.length%2>0) ? ' class="alt"':'';
        var id;
        if (dRows.length>0)
          id=(dRows.max( function(x) { return parseInt(x.getAttribute('rowid')) } ))+1
        else 
          id=0;

        var account=details['id'],
          name=details['name'],
          amount=details["amount"],
          status=details["status"],
          disabled=(details["disabled"]==1);
        alt = (dRows.length%2>0) ? 'alt' : '';
        var disabledAttrib = disabled ? 'disabled="disabled"' : '';
        var showAmount;
        
        if (parseFloatEx(amount)>0) showAmount = formatNumber(amount,2);
        else showAmount = 'EXCESS'
        var row = new Element('tr', { class: alt, name:'rows', id:'bga_row_'+id , style:'height:22px', rowid:id } ).update(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'bga_priority_'+id, name:'priority', class:'priorityIndicator' }).update(dRows.length+1)
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('img', { id:'bga_reorder_up_'+id, src:'../../images/cashier_up_small.gif', class:'segSimulatedLink priorityUp', style:'margin:1px' } 
            ).observe(
              'click', function(event) {
                if (this.hasClassName('segSimulatedLink')) {
                  moveUp(this);
                  reprioritize();
                }
              }
            ).observe(
              'mouseover', function(event) { tooltip('Move item up'); }
            ).observe(
              'mouseout', function(event) { nd(); }
            )
          ).insert(
            new Element('img', { id:'bga_reorder_dn_'+id, src:'../../images/cashier_down_small.gif', class:'segSimulatedLink priorityDown', style:'margin:1px' } 
            ).observe(
              'click', function(event) {
                if (this.hasClassName('segSimulatedLink')) {
                  moveDown(this);
                  reprioritize();
                }
              }
            ).observe(
              'mouseover', function(event) { tooltip('Move item down'); }
            ).observe(
              'mouseout', function(event) { nd(); }
            )
          )
        ).insert(
          new Element('td', { class:'leftAlign', style:'padding-left:4px' }).update(
            new Element('span', { id: 'bga_name_'+id, style:'color:#003300' }).update(name)
          ).insert(
            new Element('input', { id: 'bga_id_'+id, name:'id', type:'hidden', value:id })
          ).insert(
            new Element('input', { id: 'bga_acct_'+id, name:'accounts', type:'hidden', value:account })
          ).insert(
            new Element('input', { id: 'bga_status_'+id, name:'status', type:'hidden', value:status })
          ).insert(
            new Element('input', { id: 'bga_amount_'+id, name:'amounts', type:'hidden', value:amount })
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'bga_amount_span_'+id, style:'font:bold 11px Tahoma;color:#000066' }).update(showAmount)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'bga_status_span_'+id, style:'font:bold 11px Tahoma;color:#000066' }).update( status ? '<img title="paid" src="../../images/paid_item.gif" />' : '')
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('img', { id:'bga_split_'+id, src:'../../images/button_split_small.png', class:'link' } 
            ).observe(
              'click', function(event) {
                splitAccount ( id );
              }
            ).observe(
              'mouseover', function(event) { tooltip('Split amount'); }
            ).observe(
              'mouseout', function(event) { nd(); }
            ).setStyle( 
              {margin:'1px'} 
            )
          ).insert(
            new Element('img', { id:'bga_delete_'+id, src:'../../images/cashier_delete_small.gif', class:'link' } 
            ).observe(
              'click', function(event) {
                if (confirm('Remove this entry?')) {
                  removeItem(id);
                  reprioritize();
                  reclassRows();
                }
              }
            ).observe(
              'mouseover', function(event) { tooltip('Remove'); }
            ).observe(
              'mouseout', function(event) { nd(); }
            ).setStyle( 
              {margin:'1px'} 
            )
          )
        );
        if (insertAfter==null)
          dBody.insert(row);
        else
          insertAfter.insert( { 'after':row });
        reclassRows();
        reprioritize();
        refreshTotals();
      }
      else {
        if (!details.message)
          dBody.update('<tr><td colspan="10">No billing payments assigned yet...</td></tr>');
        else
          dBody.update('<tr><td colspan="10">'+details.message+'...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$accounts = $ac->getAccounts();
$prototype_str = array(
  'payable' => 'Payable accounts',
  'promissory' => 'Promissory accounts',
  'guarantor' => 'Guarantor accounts',
  'corporate' => 'Corporate accounts',
  'deposit' => 'Partial payments',
);
$account_prototypes = array();
while ($row=$accounts->FetchRow()) {
  if (!is_array($account_prototypes[ $row['prototype'] ])) $account_prototypes[ $row['prototype'] ] = array();
  $account_prototypes[ $row['prototype'] ][] = array( $row['account_id'], $row['account_name'] );
}

$options_html = "";
foreach ($account_prototypes as $i=>$ap) {
  if ($ap) {
    if ($prototype_str[$i]) {
      $options_html .= "              <optgroup label=\"{$prototype_str[$i]}\">\n";
      foreach ($ap as $v)
        $options_html .= "                <option value=\"{$v[0]}\">{$v[1]}</option>\n";
      $options_html .= "              </optgroup>\n";
    }
  }
}

# Buffer page output
ob_start();

?>
  <div id="split_ui" style="display:none">
    <table id="" cellpadding="0" cellspacing="0" style="width:100%; font:bold 12px Tahoma">
      <tr>
        <td class="segPanel" align="center" style="padding:10px">
          <div id="src_split_wrapper" style="white-space:nowrap">
            <span>Source account:</span>
            <span id="src_split_name" style="font:bold 11px Tahoma">Account name</span>
            <input id="src_split_id" type="hidden" value=""/>
            <!-- <input id="src_split_amount" type="text" class="segInput" value="0.00" disabled="disabled" style="width:60px; text-align:right"/> -->
          </div>
          <img src="<?= $root_path ?>images/arrow_down.gif" style="margin:4px"/>
          <div id="new_split_wrapper" style="white-space:nowrap">
            <span>Add new account</span>
            <br/>
            <select id="new_split_sel" class="segInput">
              <option value="">- Select account/guarantor -</option>
<?= $options_html ?>
            </select>
            <!-- <input type="text" class="segInput" value="0.00" style="width:60px; text-align:right" onclick="" /> -->
          </div>
          <div style="margin:4px">
            <input type="button" class="segButton" value="Add account" onclick="startSplit()" />
          </div>
        </td>
      </tr>
    </table>
  </div>
  <div style="width:100%" align="left">
    <table width="100%">
      <tr>
        <td width="82%" valign="top">
          <fieldset class="segInput">
            <legend>Paid items</legend>
            <table id="paid-grant" width="100%" class="segList" cellpadding="0" cellspacing="0" border="0">
              <thead>
                <tr>
                  <th width="6%">#</th>
                  <th width="8%"></th>
                  <th width="*">Grant  </th>
                  <th width="15%">Amount</th>
                  <th width="18%">Status</th>
                  <th width="5%"></th>
              </thead>
              <tbody>
                <tr>
                  <td colspan="10">No payments found...</td>
              </tbody>
            </table>
          </fieldset>
          <fieldset class="segInput">
            <legend>Unpaid items</legend>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="30%" valign="top" style="padding:0px 4px">
                  <select id="select-account" class="segInput">
                      <option value="">- Select account/guarantor -</option>
<?= $options_html ?>
                  </select>
                </td>
                <td>
                  <img title="Add Account" class="segSimulatedLink" src="../../images/btn_add_account.gif" border="0" onclick="addAccount()"/>
                  <img title="Compute" class="segSimulatedLink" src="../../images/btn_compute.gif" border="0" onclick="autoCompute()" />
                </td>
              </tr>
            </table>
            <table id="grant-accounts" width="100%" class="segList" cellpadding="0" cellspacing="0" border="0">
              <thead>
                <tr>
                  <th width="6%">#</th>
                  <th width="8%"></th>
                  <th width="*">Grant account</th>
                  <th width="15%">Amount</th>
                  <th width="18%">Status</th>
                  <th width="5%"></th>
              </thead>
              <tbody>
                <tr>                                                                                                  
                  <td colspan="10">No accounts added yet...</td>
              </tbody>
            </table>
          </fieldset>
        </td>
        <td width="*" valign="top">
          <table width="100%" style="font-size: 12px;" border="0" cellspacing="2" cellpadding="1">
            <tbody>
              <tr>
                <td width="40%" align="left" class="segPanelHeader" ><strong>Total accounts</strong></td>
              </tr>
              <tr>
                <td style="background-color:#e0e0e0;margin:1px 10px;text-align:right;padding:2px 4px"><span id="show-grant-total"  class="displayTotals" style="color:#000080;"></span></td>
              </tr>
              <tr style="display:none">
                <td align="left" class="segPanelHeader" ><strong>Total bill</strong></td>
              </tr>
              <tr style="display:none">
                <td style="background-color:#c0c0c0;margin:1px 10px;text-align:right;padding:2px 4px"><span id="show-sub-total"  class="displayTotals" style="color:#000000;"><?= number_format($totalBill,2) ?></span></td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </table>
  </div>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
