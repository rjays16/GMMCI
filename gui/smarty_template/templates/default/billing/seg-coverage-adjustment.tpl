{{* form.tpl  Form template for insurance coverage adjustment *}}

<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

	function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'');
	return (isNaN(str)) ? 0 : parseFloat(str);
	}

	function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
	}

	function loadItems() {
	$('contentArea').hide();
	$('ajaxLoader').show();
	$('save').disabled = true;
	url = "ajax/seg-coverage-adjustment-areas.php?userck={{$sUserCK}}&nr={{$sEncounterNr}}&bnr={{$sBillNr}}&billdt={{$sBillDte}}&force={{$sForce}}";
	new Ajax.Request(url,
		{
		method: 'get',
		onSuccess:
			function(transport) {
			var content = $('contentArea');
			content.update(transport.responseText);
			var hCares = $$('[name="hcare"]');
			var items = $$('[name="items"]');
			$('ajaxLoader').hide();
			$('contentArea').appear( {duration: 1.0} );

//            $('mode').value = mode;
			$('save').disabled = (hCares.length==0) || (items.length==0) || ($('force_startdate').value == '1') || ($('bill_nr').value != '');
			calculateCoverage();
			},
		onFailure:
			function(transport) {
			var content = $('contentArea');
			content.update('<div style="text-align:center;vertical-align:middle;height:60px"><h4>Data not available.</h4></div>');
			$('ajaxLoader').hide();
			$('contentArea').show();
			$('save').disabled = true;
			}
		}
	);
	}

	function calculateCoverage(editItem, autoItem) {
	var hCares = $$('[name="hcare"]');
	var items = $$('[name="items"]');
//    var limit=[], hcareCoverages=[],
	var limit=new Object, hcareCoverages=new Object,
		totalExcess=0, totalCost=0, totalMax=0;

	if (editItem) {
		var ehcid=editItem.getAttribute('hcareId'),
//        erefs=editItem.getAttribute('refSource'),
		eitem=editItem.getAttribute('itemCode');
		$('apply_'+ehcid+'_'+eitem).checked = (parseFloatEx( editItem.value ) > 0.0) || ($('apply_'+ehcid+'_'+eitem).checked);
	}

	/*
	if (autoItem) {
		var erefs=autoItem.getAttribute('refSource'),
		eitem=autoItem.getAttribute('itemCode');
	}
	*/

	hCares.each( function(n) { limit[n.getAttribute('hcareId')+'_'+n.getAttribute('itemCode')] = parseFloatEx(n.value); hcareCoverages[n.getAttribute('hcareId')]=0 } );

	items.each (
		function (item) {
		var coverage;
		var apply;
		var cost=parseFloatEx(item.value);
		var maxcoverage=parseFloatEx(item.getAttribute('maxCoverage'));
		var applyCount=0;
		var runningCoverage=0;
		var runningCost=cost;
		var currentCoverage=0;
		var totalCoverage=0;
		//alert(runningCost)

		var auto=(autoItem && item.getAttribute('refSource')==autoItem.getAttribute('refSource') && item.getAttribute('itemCode')==autoItem.getAttribute('itemCode'));
		totalCost += cost;
		totalMax  += maxcoverage;

		apply=$$('[name="apply_'+item.getAttribute('itemCode')+'"]');
		apply.each(
			function(n) {
			if (n.checked || auto) {
				applyCount++;
				runningCoverage+=limit[n.getAttribute('hcareId')+'_'+n.getAttribute('itemCode')];
				currentCoverage += parseFloatEx( $('coverage_'+n.getAttribute('hcareId')+'_'+item.getAttribute('itemCode')).value );
				//runningCost-= parseFloatEx( $('coverage_'+n.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).value );
			}
			}
		);

		apply.each(
			function (n) {
			var hcid = n.getAttribute('hcareId');
			//var coverage = cost*(parseFloat(limit[n.getAttribute('hcareId')])/runningCoverage);
			var coverage;
			var coverageElement = $('coverage_'+hcid+'_'+item.getAttribute('itemCode'));

			if ((parseFloatEx(coverageElement.value) > 0) || ((parseFloatEx(coverageElement.value) == 0) && n.checked)) {
				coverage = parseFloatEx(coverageElement.value);
				if (coverage > runningCost) coverage=runningCost;
			}
			else {
				coverage = cost-currentCoverage;
			}

			if (auto) coverage=cost*(parseFloat(limit[n.getAttribute('hcareId')+'_'+n.getAttribute('itemCode')])/runningCoverage);
			//
			if (n.checked || auto) {
				n.checked = true;
				if (limit[n.getAttribute('hcareId')+'_'+n.getAttribute('itemCode')] <= coverage) {
				coverage = limit[n.getAttribute('hcareId')+'_'+n.getAttribute('itemCode')];
				}
				limit[n.getAttribute('hcareId')+'_'+n.getAttribute('itemCode')] -= coverage;
				hcareCoverages[n.getAttribute('hcareId')] += coverage;
			}
			else {
				coverage=0;
			}

//            if (coverage==0) $('apply_'+hcid+'_'+item.getAttribute('itemCode')).checked=false;
			totalCoverage += coverage;
			runningCost -= coverage;
			coverageElement.value = formatNumber(coverage,2);
			//coverageElementLabel.update(formatNumber(coverage,2));
			//coverageElementLabel.setStyle({color:(coverage>0?'#00a000':'#c00000')});
			}
		);

		var excess,
			excessElement=$('excess_'+item.getAttribute('itemCode')),
			excessElementShow = excessElement.next();
		excess = cost-totalCoverage;
		excessElement.value=excess;
		excessElementShow.update(formatNumber(excess,2));
		totalExcess += excess;
		}
	);

	hCares.each(
		function(n) {
		var tc = $('total_coverage_'+n.getAttribute('hcareId'))
		tc.update( formatNumber(hcareCoverages[n.getAttribute('hcareId')],2) );
		}
	)
	$('total_cost').update( formatNumber(totalCost,2) );
	$('total_coverage').update( formatNumber(totalMax,2) );
	$('total_excess').update( formatNumber(totalExcess,2) );
	}

	function save() {

	var hCares = $$('[name="hcare"]');
	var items = $$('[name="items"]');
	var bulk = [];
	var ndx=0, priority=0;

	items.each (
		function(item) {
		priority++;
		hCares.each (
			function (h) {
			if (item.getAttribute('itemCode') == h.getAttribute('itemCode')) {
				var coverage = $('coverage_'+h.getAttribute('hcareId')+'_'+item.getAttribute('itemCode'));
				var apply=$('apply_'+h.getAttribute('hcareId')+'_'+item.getAttribute('itemCode'));

				if (apply.checked && parseFloatEx(coverage.value) >= 0) {
					var array = new Array(4);
	//              array[0] = item.getAttribute('refSource');
					array[0] = item.getAttribute('itemCode');
					array[1] = h.getAttribute('hcareId');
					array[2] = parseFloatEx( coverage.value );
					array[3] = priority;
					bulk[ndx] = array;
					ndx++;
				}
			}
			}
		);
		}
	)

//    var mode = $('mode').value;
//    if (!mode) mode='M';
	xajax.call('saveCoverage', { parameters : [$('refno').value, bulk] } );
	}

	function moveUp(obj) {
	var p=$(obj).up(1), prev=p.previous();
	if (prev) {
		p.remove();
		prev.up().insertBefore(p, prev);
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
	}
	else {
		return false;
	}
	}

	function setCoverageAdjustedFlag(bflag) {
	window.parent.setCoverageAdjustedFlag(bflag);
	}
-->
</script>
{{$sFormStart}}

<div style="width:95%; padding:4px;">
	<div style="float:right">{{$sSaveButton}}</div></br></br>
	<!--<br style="clear:left" />-->
	<div class="" style="width:100%; border-top:2px solid #4e8ccf;padding:3px 2px">
	<div align="right" style="padding-bottom:4px">

	</div>
	<div id="ajaxLoader" class="lgAjaxLoad" style="height:60px;background-color:white!important;border:0px"></div>
	<div id="contentArea" style="display:none;"></div>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}}
