{{* form.tpl  Form template for insurance coverage editor *}}

<script type="text/javascript">
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

function tabClick(obj) {
	if (!obj) return false;
	if ($(obj).hasClassName('segActiveTab') || $(obj).hasClassName('segDisabledTab')) return false;
	var dList = obj.parentNode;
	if (dList) {
		var listItems = dList.getElementsByTagName("LI");
		if (obj) {
			for (var i=0;i<listItems.length;i++) {
				if (obj!=listItems[i]) {
					var element = $(listItems[i]);
					if (element.hasClassName('segActiveTab'))
						element.toggleClassName('segActiveTab');
					tab = listItems[i].getAttribute('segTab');
					if ($(tab))
						$(tab).style.display = "none";
				}
			}
			mode = obj.getAttribute('segMode');
			//if ($(mode)) $(tab).style.display = "block";
			obj.className = "segActiveTab";
			loadItems(mode);
		}
	}
}

function loadItems(mode) {
	$('contentArea').hide();
	$('ajaxLoader').show();
	$('save').disabled = true;
	url = "ajax/coverageItems.php?userck={{$sUserCK}}&nr={{$sEncounterNr}}&bnr={{$sBillNr}}&billdt={{$sBillDte}}&mode="+mode+"&force={{$sForce}}";
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

					$('mode').value = mode;
//					$('save').disabled = (hCares.length==0) || (items.length==0) || ($('force_startdate').value == '1') || ($('bill_nr').value != '');
					$('save').disabled = ($('force_startdate').value == '1') || ($('bill_nr').value != '');
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

function applyToAll(state) {
	var hCares = $$('[name="hcare"]');
	var items = $$('[name="items"]');
	var limit=new Object, hcareCoverages=new Object, totalExcess=0;

	hCares.each( function(n) { limit[n.getAttribute('hcareId')] = parseFloatEx(n.value); hcareCoverages[n.getAttribute('hcareId')]=0 } );

	items.each (
		function (item) {
			var apply;
				var cost=parseFloatEx(item.value) * (1 - parseFloatEx($('discount').value));
			var applyCount=0;
			var runningCoverage=0;
			var runningCost=cost;
			var currentCoverage=0;
			var totalCoverage=0;

			apply=$$('[name="apply_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')+'"]');
			apply.each(
				function(n) {
					if (n.checked) {
							applyCount++;
							runningCoverage+=limit[n.getAttribute('hcareId')];
							currentCoverage += parseFloatEx( $('coverage_'+n.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).value );
							if (state.getAttribute('hcareId') != n.getAttribute('hcareId')) {
									totalCoverage += currentCoverage;
									hcareCoverages[n.getAttribute('hcareId')] += currentCoverage;
							}
					}
				}
			);

			apply.each(
					function(n) {
							var hcid = n.getAttribute('hcareId');
							var coverage;
							var coverageElement = $('coverage_'+hcid+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode'));

							if (state.getAttribute('hcareId') == hcid) {
									if (state.checked) {
											n.checked = true;
											applyCount++;
											runningCoverage+=limit[n.getAttribute('hcareId')];
											currentCoverage += parseFloatEx( $('coverage_'+n.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).value );
									}
									else {
											n.checked = false;
									}

									if (parseFloatEx(coverageElement.value)>0) {
										coverage = parseFloatEx(coverageElement.value);
										if (coverage > runningCost) coverage=runningCost;
									}
									else {
										coverage = cost-currentCoverage;
									}

									if (n.checked) {
										if (limit[n.getAttribute('hcareId')] <= coverage) {
											coverage = limit[n.getAttribute('hcareId')];
										}
										limit[n.getAttribute('hcareId')] -= coverage;
										hcareCoverages[n.getAttribute('hcareId')] += coverage;
									}
									else {
										coverage=0;
									}

									if (coverage==0) $('apply_'+hcid+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).checked=false;
									totalCoverage += coverage;
									runningCost -= coverage;
									coverageElement.value = formatNumber(coverage,2);
							}
					}
			);

			var excess,
					excessElement=$('excess_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')),
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

	$('total_excess').update( formatNumber(totalExcess,2) );
}

function calculateCoverage(editItem, autoItem) {
	var hCares = $$('[name="hcare"]');
	var items = $$('[name="items"]');
	var limit=new Object, hcareCoverages=new Object,
			totalExcess=0, totalCost=0;

	if (editItem) {
		var ehcid=editItem.getAttribute('hcareId'),
			erefs=editItem.getAttribute('refSource'),
			eitem=editItem.getAttribute('itemCode');
		$('apply_'+ehcid+'_'+erefs+'_'+eitem).checked = (parseFloatEx( editItem.value ) > 0.0);
	}

	/*
	if (autoItem) {
		var erefs=autoItem.getAttribute('refSource'),
			eitem=autoItem.getAttribute('itemCode');
	}
	*/

	hCares.each( function(n) { limit[n.getAttribute('hcareId')] = parseFloatEx(n.value); hcareCoverages[n.getAttribute('hcareId')]=0 } );

	items.each (
		function (item) {
			var coverage;
			var apply;
			var cost=parseFloatEx(item.value);
			var applyCount=0;
			var runningCoverage=0;
				var runningCost=cost * (1 - parseFloatEx($('discount').value));
			var currentCoverage=0;
			var totalCoverage=0;
			//alert(runningCost)

			var auto=(autoItem && item.getAttribute('refSource')==autoItem.getAttribute('refSource') && item.getAttribute('itemCode')==autoItem.getAttribute('itemCode'));
			totalCost += cost;

                cost = cost * (1 - parseFloatEx($('discount').value));

			apply=$$('[name="apply_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')+'"]');
			apply.each(
				function(n) {
					if (n.checked || auto) {
						applyCount++;
						runningCoverage+=limit[n.getAttribute('hcareId')];
						currentCoverage += parseFloatEx( $('coverage_'+n.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).value );
						//runningCost-= parseFloatEx( $('coverage_'+n.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).value );
					}
				}
			);

			apply.each(
				function (n) {
					var hcid = n.getAttribute('hcareId');
					//var coverage = cost*(parseFloat(limit[n.getAttribute('hcareId')])/runningCoverage);
					var coverage;
					var coverageElement = $('coverage_'+hcid+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode'));

					if (parseFloatEx(coverageElement.value)>0) {
						coverage = parseFloatEx(coverageElement.value);
						if (coverage > runningCost) coverage=runningCost;
					}
					else {
						coverage = cost-currentCoverage;
					}

					if (auto) coverage=cost*(parseFloat(limit[n.getAttribute('hcareId')])/runningCoverage);
					//
					if (n.checked || auto) {
						n.checked = true;
						if (limit[n.getAttribute('hcareId')] <= coverage) {
							coverage = limit[n.getAttribute('hcareId')];
						}
						limit[n.getAttribute('hcareId')] -= coverage;
						hcareCoverages[n.getAttribute('hcareId')] += coverage;
					}
					else {
						coverage=0;
					}

					if (coverage==0) $('apply_'+hcid+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')).checked=false;
					totalCoverage += coverage;
					runningCost -= coverage;
					coverageElement.value = formatNumber(coverage,2);
					//coverageElementLabel.update(formatNumber(coverage,2));
					//coverageElementLabel.setStyle({color:(coverage>0?'#00a000':'#c00000')});
				}
			);

			var excess,
					excessElement=$('excess_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode')),
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
	$('total_excess').update( formatNumber(totalExcess,2) );
}

function save() {

	var hCares = $$('[name="hcare"]');
	var items = $$('[name="items"]');
	var bulk = [];
	var ndx=0, priority=0, data='';

	var mode = $('mode').value;
	if (!mode) mode='M';

	xajax.call('clearExistingCoverage', { parameters : [$('refno').value, mode], mode:'synchronous' } );

	items.each (
		function(item) {
			priority++;
			hCares.each (
				function (h) {
					var coverage = $('coverage_'+h.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode'));
					var apply=$('apply_'+h.getAttribute('hcareId')+'_'+item.getAttribute('refSource')+'_'+item.getAttribute('itemCode'));

					if (apply.checked && parseFloatEx(coverage.value)>0) {
						//Added by Jarel to set values in multiple insert
						data += '("'+$('refno').value+'","'+item.getAttribute('refSource')+'","'+item.getAttribute('itemCode')+'","'+h.getAttribute('hcareId')+'","'+
								parseFloatEx( coverage.value )+'","'+priority+'"),';
						
						ndx++;
					}
				}
			);
		}
	)
	xajax.call('saveCoverage', { parameters : [data] } );
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

</script>
{{$sFormStart}}
<div style="width:95%; padding:4px;">
	<div style="float:left">
		<ul id="request-tabs" class="segTab" style="padding-left:10px;">
			<li id="tabm" class="{{$sTabMClass}}" onclick="tabClick(this)" segMode="M">
				<h2 class="segTabText">Drugs and Meds</h2>
			</li>
			<li id="tabo" class="{{$sTabOClass}}" onclick="tabClick(this)" segMode="O">
				<h2 class="segTabText">X-Ray, Lab and Other charges</h2>
			</li>
		</ul>
	</div>
	<div style="float:right">{{$sSaveButton}}</div>
	<br style="clear:left" />
	<div class="" style="width:100%; border-top:2px solid #4e8ccf;padding:3px 2px">
		<div align="right" style="padding-bottom:4px"></div>
		<div id="ajaxLoader" class="lgAjaxLoad" style="height:60px;background-color:white!important;border:0px"></div>
		<div id="contentArea" style="display:none;"></div>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}}
