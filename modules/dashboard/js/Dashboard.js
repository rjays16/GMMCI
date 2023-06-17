/**
 Dashboard.js

 The file contains definitions for the Dashboard javascript client-side backend.

*/


/**
 * The global Dashboard object provides the interface which a Dashboard class instance
 * uses to interact with the client components. All calls to and from the Dashboard and its
 * Dashlets and UI components relegate to this global object.
 */
var Dashboard = function($J)
{

/**
 * The id of the Dashboard instance
 */
var id = '';

/**
 * The title for the Dashboard (as shown in the Dashboard tab)
 */
var title='';

/**
 * The number of column placeholders for the Dashlet components
 */
var columns;
/**
 * Array to hold the value of the relative widths of each column placeholder
 */
var columnWidths;

/**
 *
 */
var locked = false;

/**
 * Initializes the Dashboard object. This method should be called upon DOM loading. Initialization
 * requires the id of the Dashboard instance.
 *
 */
function initialize(id)
{
	Dashboard.id = id;

	Dashboard.dialog.init();
	Dashboard.launcher.init();
	Dashboard.dashlets.init();
	Dashboard.sortables.init();
}


//function loadExtension(obj)
//{
//	if (typeof obj == 'object')
//	{
//		if (obj.init) obj.init();
//		if (obj.run) obj.run();
//	}
//}

function openWindow(options)
{
	options = $J.extend({
		url: '',
		data: {},
		toolbar: null,
		status: null,
		location: null,
		menubar: null,
		directories: null,
		resizable: null,
		scrollbars: null,
		height: null,
		width: null
	}, options);
	window.open(options.url+'?'+$J.param(options.data, true), 'dashboardWindow', "menubar=no,directories=no");
}



/**
*
*/
function scrap()
{
// create parameter object
	var data = {
		dashboard: Dashboard.id,
	};

	// send AJAX request
	$J.ajax({
		url: 'ajax/deleteDashboard.ajax.php',
		async: false,
		data: data,
		type: 'POST',

		beforeSend: function() {
			// disable user interaction while saving...
			Dashboard.blockUI('Scrapping dashboard...');
		},

		error: function(xhr, textStatus) {
			// lock Dashboard on error
			Dashboard.lock('An error occurred while attempting to delete the dashboard. Try reloading the page...');
		},

		success: function(data, textStatus, xhr) {
			if (data.success)
			{
				window.location.href = window.location.href;
				return;
			}
			else
				// lock Dashboard on error
				Dashboard.lock('An error occurred while attempting to delete the dashboard layout. Try reloading the page...');
		}
	});
}



/**
*
*/
function save()
{
	// create parameter object
	var data = {
		dashboard: Dashboard.id,
		title: Dashboard.title,
		columns: Dashboard.columns,
		columnWidths: Dashboard.columnWidths,
		dashlets: []
	};

	// cycle through each Dashlet item in the Dashboard and determine proper ordering of
	// the Dashlets
	$J(".dashletItem").each(function(index, element) {
		element = $J(element);

		// get column index of the Dashlet list item's (li) parent element (ul)
		columnIndex = parseInt(element.parent(':last').attr('columnIndex'));
		if ('undefined' == typeof(data.dashlets[columnIndex]))
		{
			data.dashlets[columnIndex] = [];
		}

		// store the Dashlet Id into the list
		data.dashlets[columnIndex].push(element.attr('id'));
	});


	// send AJAX request
	$J.ajax({
		url: 'ajax/saveLayout.ajax.php',
		async: false,
		data: data,
		type: 'POST',

		beforeSend: function() {
			// disable user interaction while saving...
			Dashboard.blockUI('Saving layout...');
		},

		error: function(xhr, textStatus) {
			// lock Dashboard on error
			Dashboard.lock('An error occurred while attempting to save the dashboard layout. Try reloading the page...');
		},

		success: function(data, textStatus, xhr) {
			if (data.success)
			{
			}
			else
				// lock Dashboard on error
				Dashboard.lock('An error occurred while attempting to save the dashboard layout. Try reloading the page...');
		},

		complete: function(xhr, textStatus)
		{
			Dashboard.unblockUI();
		}
	});
}


function addDashboard(data)
{
	// create parameter object
	var data = $J.extend({
		title: ''
//		columns: Dashboard.columns,
//		columnWidths: Dashboard.columnWidths,
//		dashlets: []
	}, data);

	// send AJAX request
	$J.ajax({
		url: 'ajax/loadDashboard.ajax.php',
		async: false,
		data: data,
		type: 'POST',

		beforeSend: function() {
			// disable user interaction while saving...
			Dashboard.blockUI('Adding new dashboard...');
		},

		error: function(xhr, textStatus) {
			// lock Dashboard on error
			Dashboard.lock('An error occurred while attempting to save the dashboard layout. Try reloading the page...');
		},

		success: function(data, textStatus, xhr) {
			if (data.success)
			{
				$J('#dashboard-create').parent('li:first').before('<li class="">'+
					'<a href="dashboard.php?tab='+data.id+'">'+
						'<span class="ui-icon ui-icon-'+data.icon+'"></span>'+
						'<span id="title-'+data.id+'">'+data.title+'</span>'+
					'</a>'+
				'</li>');
				Dashboard.unblockUI();
			}
			else
				// lock Dashboard on error
				Dashboard.lock('An error occurred while attempting to save the dashboard layout. Try reloading the page...');
		},
	});
}


/**
 * Updates the current Dashboard's layout based on the settings specified in the <code>options</code> argument
 */
function layout(options)
{

	// default options
	options = $J.extend({
		title: 'No title',
		columns: 3,
		columnWidths: [33,34,33]
	}, options || {});

	// disable all page interactions first...
	Dashboard.blockUI('Applying new settings...');

	// reduce the number of columns if needed, and move the contents of the deleted columns to the first column ...
	columns = $J(".dashlet-column");
	while (columns.length > options.columns)
	{
		columnItems = $J(".dashlet-column:last .dashletItem").detach();
		$J(".dashlet-column:first .dashletList").append(columnItems);
		$J(".dashlet-column:last").remove();
		columns = $J(".dashlet-column");
	}

	// ...or increase the number of columns if needed
	while (columns.length < options.columns)
	{
		$J('#dashboard-column-container').append(
			'<td class="dashlet-column flow-height" style="vertical-align:top; width:{{$column.width}}">'+
				'<ul class="dashletList" columnIndex="'+columns.length+'"></ul>'+
			'</td>');
		columns = $J('.dashlet-column');
	}

	// adjust the widths of the columns
	$J('.dashlet-column').each( function(index, Element) {
		$J(Element).css({width:options.columnWidths[index]+'%'})
	});

	// re-initialize the columns as Sortables
	Dashboard.sortables.init();

	$J('#title-'+Dashboard.id).html(options.title);

	// update Dashboard settings, and then save the new layout
	Dashboard.title = options.title
	Dashboard.columns = options.columns;
	Dashboard.columnWidths = options.columnWidths;

	Dashboard.dashlets.refreshAll();

	// finally, enable user interaction again
	Dashboard.unblockUI();

	// try to save the new settings
	Dashboard.save();
}


/**
 * Page-wide locking of the Dashboard. Typically called when the Dashboard scipt encounters
 * an irrecoverable error
 *
 * @todo External CSS for blockUI styling
 */
function lock(message)
{
	$J.blockUI({
		message: message,
		fadeIn: 0,
		css: {
			font: 'bold 16px Arial',
			backgroundColor: '#fff',
			padding: '10px',
			border: 'none',
			'-webkit-border-radius': '4px',
			'-moz-border-radius': '4px',
			'-moz-box-shadow': '0 1px 8px #2d2d2d',
			opacity: 0.9
		},
		overlayCSS: {
			opacity: 0.3
		}
	});
	// Ensure that the lock cannot removed by <code>Dashboard.unblockUI</code>
	Dashboard.locked = true;
}



/**
 *
 */
function blockUI(message)
{
	if (!message)
	{
		message = 'Loading...';
	}

	$J.blockUI({
		message: '<div style="padding:10px"><span style="font:bold 12px Arial">'+message+'</span><span class="ui-ajax-loading" style="display:inline-block; width:60px; height:14px;"></span></div>',
		width: '25%',
		left: '37.5%',
		fadeIn:  0,
		fadeOut:  0,
		css: {
			top: '120px',
			backgroundColor: '#fff',
			padding: '0',
			border: 'none',
			'-webkit-border-radius': '4px',
			'-moz-border-radius': '4px',
			'-moz-box-shadow': '0 1px 8px #2d2d2d',
			opacity: 0.9
		},

		overlayCSS: {
			opacity: 0.3
		}
	});
}


function unblockUI()
{
	if (!Dashboard.locked) $J.unblockUI();
}




/**
 *  Dashboard.sortables
 * Helper utilities for setting up and manipulation of Sortable elements within the Dashboard
 */

sortables = {};

sortables.init = function()
{
	$J(".dashletList").sortable("destroy");
	$J(".dashletList").sortable({
		connectWith: ".dashletList",
		handle: ".dashletTitle",
		placeholder: "dashletDroppable",
		forceHelperSize: true,
		forcePlaceholderSize: true,
		revert: 150,
		opacity: 0.8,
		zIndex: 1300,
		start: function(event, ui) {
			ui.helper.addClass('dashletDraggable');
		},
		stop: function(event, ui) {
			ui.item.removeClass('dashletDraggable');
		},
		update: function(event, ui) {
			Dashboard.save();
			Dashboard.dashlets.refresh(ui.item.attr("id"));
		}
	});
	//.addTouch();
}


/**
 * Dashboard.dialog
 *
 * Contains utility functions for invoking the generic modal dialog component for the Dashboard.
 * Generally used for loading UI components and for launching dashlet applications.
 */
dialog = {};

/**
 * Initialization routine for the Dashboard dialog
 */
dialog.init = function()
{
	Dashboard.dialog.ui = $J('#dashboard-ui-dialog:first');
	Dashboard.dialog.panel = $J('#dashboard-ui-dialog-contents:first');
	Dashboard.dialog.ui.dialog({
		width: 520,
		height: 'auto',
		autoOpen: false,
		modal: true,
		show: 'fade',
//		hide: 'fade',
		resizable: false,
		close: function() {
			Dashboard.dialog.panel.empty();
		}
	});
}

/**
 *	Opens the Dashboard dialog.
 */
dialog.open = function( options )
{
	Dashboard.blockUI('Loading UI...');

	options = $J.extend({
		title: "",
		width: 520,
		height: "auto",
		url: "ajax/loadUI.ajax.php",
		position: "center",
		parameters: {}
	}, options);

	options.parameters.dashboard = Dashboard.id;

	$J.ajax({
		url: options.url,
		data: options.parameters,
		beforeSend: function() {
		},

		error: function(xHR, errorMessage) {
			Dashboard.dialog.panel
				.html("Unable to load contents: " + errorMessage)
				.dialog("open");
		},

		success: function(data, successCode) {
			Dashboard.dialog.panel.html(data)
				.removeClass("ui-hide");

			Dashboard.dialog.ui.dialog("option", "title", options.title)
				.dialog("option", "width", 		options.width)
				.dialog("option", "position", options.position)
				.dialog("option", "height", 	options.height)
				.dialog("open");

			Dashboard.dialog.panel.find("input:visible, textarea:visible, select:visible").first().focus();
		},

		complete: function()
		{
			Dashboard.unblockUI();
		}

	});
}

dialog.close = function()
{
	dialog.ui.dialog("close");
}




/**
 * Dashboard.launcher
 *
 * Contains utility functions for launching module pages
 */
launcher = {};

/**
 * Initialization routine for the Dashboard launcher
 */
launcher.init = function()
{
	Dashboard.launcher.ui = $J('#dashboard-ui-launcher:first');
	Dashboard.launcher.iframe = $J('#dashboard-ui-launcher-iframe:first');
	Dashboard.launcher.ui.dialog({
		autoOpen: false,
		modal: true,
		show: 'fade',
		hide: 'fade',
		width: 900,
		height: 520,
		resizable: false,
		autoResize: false,
		close: function() {
			Dashboard.launcher.iframe.attr("src", "");
		}
	});
}

/**
 *	Opens the Dashboard dialog.
 */
launcher.launch = function( options )
{
	var horizontalPadding = 10;
	var verticalPadding = 30;

	options = $J.extend({
		title: "",
		href : "",
		width: 870,
		height: 520,
		position: "center",
		parameters: {}
	}, options);

	options.parameters.dashboard = Dashboard.id;
	Dashboard.launcher.iframe
		.attr("src", options.href)
		.width(options.width-horizontalPadding)
		.height(options.height-verticalPadding)
		.removeClass("ui-hide");

	Dashboard.launcher.ui.dialog("option", "title", options.title)
		.dialog("option", "position", options.position)
		.dialog("option", "width", options.width)
		.dialog("option", "height", options.height)
		.dialog("open");
	//window.open (options.href,"dashboard-launcher","location=1,status=1,scrollbars=1,width="+options.width+",height="+options.height);
}

launcher.close = function()
{
	Dashboard.launcher.ui.dialog("close");
}



/**
 * Dashboard.dashlets
 *
 * Contains utility functions for handling Dashlets.
 */
dashlets = {};

dashlets.init = function()
{
	Dashboard.dashlets.dialog = {
		ui: $J('#config-dialog'),
		panel: $J('#config-dialog-contents')
	};

	// Initialize the UI dialog used for Dashlet configurations
	Dashboard.dashlets.dialog.ui.dialog({
		width: 520,
		height: 'auto',
		autoOpen: false,
		modal: true,
		show: 'fade',
		hide: 'fade',
		resizable: false,
		close: function() {
			Dashboard.dashlets.dialog.panel.empty();
		}
	});
}




/**
 * Calls the configuration dialog for the Dashlet
 */
dashlets.edit = function(id)
{
	options = {
		title: "Edit dashlet",
		width: 520,
		height: "auto",
		url: "ajax/loadDashletConfig.ajax.php",
		position: "center",
		parameters: {}
	};

	options.parameters.dashboard = this.id;
	options.parameters.id = id;

	$J.ajax({
		url: options.url,
		data: options.parameters,
		beforeSend: function() {
		},

		error: function(xHR, errorMessage) {
			Dashboard.dashlets.dialog.panel
				.html("Unable to load contents: " + errorMessage)
				.dialog("open");
		},

		success: function(data, successCode) {
			Dashboard.dashlets.dialog.panel
				.html(data)
				.removeClass("ui-hide");

			Dashboard.dashlets.dialog.ui
				.dialog("option", "title", 		options.title)
				.dialog("option", "width", 		options.width)
				.dialog("option", "height", 	options.height)
				.dialog("option", "position", options.position)
				.dialog("open");

			Dashboard.dashlets.dialog.panel.find("input:visible, textarea:visible, select:visible").first().focus();
		}
	});
}

//dashlets.doneEdit = function()
//{
//	Dashboard.dashlets.dialog.ui.dialog("close");
//}


/**
 *  Adds a Dashlet interface to the Dashboard
 *
 * The Dashlet to be inserted is specified in the parameters object which could contain
 * the id of the Dashlet  or, in case of a new Dashlet, the fully qualified class name
 * of the Dashlet to be added.
 *
 */
dashlets.add = function( parameters )
{
	var url = "ajax/loadDashlet.ajax.php";

	parameters = parameters || {};
	parameters = $J.extend({
		dashlet: '',
		name: '',
		column: 0,
		saveOnAdd: true,
	}, parameters);

	parameters.dashboard = Dashboard.id;

	$J.ajax({
		url: url,
		async: false,
		data: parameters,
		dataType: 'json',
		beforeSend: function() {
		},

		error: function(xHR, errorMessage) {
			unblockUI();
		},

		success: function(data, successCode) {
			dashlet = $J('<li/>', {
				id: data.id,
				className: 'dashletItem '
			});
			if (data.append)
			{
				$J('.dashletList:eq('+parameters.column+')').append(dashlet);
			}
			else
			{
				$J('.dashletList:eq('+parameters.column+')').prepend(dashlet);
			}
			Dashboard.dashlets.refresh(data.id);
			if (parameters.saveOnAdd)
			{
				Dashboard.save();
			}
		}
	});
}



/**
 *  Adds a Dashlet interface to the Dashboard
 *
 * The Dashlet to be inserted is specified in the parameters object which could contain
 * the id of the Dashlet  or, in case of a new Dashlet, the fully qualified class name
 * of the Dashlet to be added.
 *
 */
dashlets.remove  = function( parameters )
{
	var url = "ajax/deleteDashlet.ajax.php";

	parameters = parameters || {};
	parameters = $J.extend({
		dashlet: ''
	}, parameters);

	parameters.dashboard = Dashboard.id;

	$J.ajax({
		url: url,
		async: false,
		data: parameters,
		dataType: 'json',
		beforeSend: function() {
		},

		error: function(xHR, errorMessage) {
			Dashboard.lock('An error occurred while attempting operation. Try reloading the page...');
		},

		success: function(result, successCode) {

			if (result.status === 1)
			{
				$J("#"+parameters.dashlet).remove();
				Dashboard.save();
			}
			else
			{
				Dashboard.lock('An error occurred while attempting operation. Try reloading the page...');
			}
		}

	});
}


/**
 *
 */
dashlets.sendAction = function( dashletId, action, parameters )
{

	if (!action)
		return false;

	$J.ajax({
		url: "ajax/processAction.ajax.php",
		dataType: 'json',
		data: {
			dashboard: Dashboard.id,
			dashlet: dashletId,
			action: action,
			parameters: parameters||null
		},
		type: 'POST',

		beforeSend: function()
		{
		},

		error: function(xHR, errorMessage)
		{
			alert('Action could not be processed. Please try again!');
		},

		success: function(responses, successCode)
		{

			// Check if response is an array or not
			if (!responses || (responses.propertyIsEnumerable('length')) || typeof responses !== 'object' || typeof responses.length !== 'number')
			{
				// not a JS array object
				alert('Invalid response received, action could not be processed. Please try again!');
			}
			else
			{
				if (responses.length)
				{
					// iterate over each Response
					$J.each(responses, function(index, response){
						var responseType = response.rsp;
						var data = response.data;
						if (Dashboard.processors[responseType])
						{
							Dashboard.processors[responseType](data);
						}
					});
				}
			}
		}
	});

}


dashlets.refreshAll = function()
{
	$J('.dashletItem').each( function(index, element) {
		var id = element.id;
		if (id)
		{
			Dashboard.dashlets.refresh(id);
		}
	});
}

/**
 *
 */
dashlets.refresh = function(dashlet)
{
	if (!dashlet)
	{
		return false;
	}

	parameters = {
		dashlet: dashlet,
		dashboard: Dashboard.id
	};


	Dashboard.dashlets.block(dashlet);


	/*
	* Loads the Dashlet container and Dashlet contents fragments
	*/
	$J.ajax({
		url: "ajax/render.ajax.php",
		dataType: 'json',
		data: parameters,
		beforeSend: function()
		{

		},

		error: function(xHR, errorMessage)
		{
			alert("Error:"+errorMessage)
		},

		success: function(response, successCode)
		{
			$J('#'+dashlet).empty().html(response.render);
			$J('#'+dashlet+" .dashletTitle span").first().ellipsis(true);
			// Add the Dashlet's group tags as HTML attributes
			if (response.group && response.group.join)
			{
				$J('#'+dashlet).attr('dashletGroup', response.group.join(',')).attr('dashletClass', response.className);
			}
		}
	});

	Dashboard.dashlets.unblock(dashlet);
}



dashlets.block = function(dashlet)
{
	$J('#'+dashlet).block({
		message: '',
		fadeIn:  0,
		fadeOut:  0,
		css: {
			backgroundColor: '#fff',
			padding: '0',
			border: 'none',
			'-webkit-border-radius': '4px',
			'-moz-border-radius': '4px',
			opacity: 0.9
		},

		overlayCSS: {
			backgroundColor: "#fff",
			opacity: 0.5
		}
	});
}


dashlets.unblock = function(dashlet)
{
	$J('#'+dashlet).unblock();
}


dashlets.setTitle = function(dashlet, title)
{
	$J('#title_'+dashlet).html(title);
}


dashlets.minimize = function(dashlet)
{
	$J('#'+dashlet+' .dashletBody').slideUp(400, function() {
		Dashboard.dashlets.sendAction(dashlet, 'setState', { state:'minimized' });
	});
}

dashlets.restore = function(dashlet)
{
	$J('#'+dashlet+' .dashletBody').slideUp(400, function() {
		Dashboard.dashlets.sendAction(dashlet, 'setState', { state:'normal' });
	});
}


/**
 * Dashboard.processors
 *
 * Contains methods for processing DashletResponse objects received from the server after issuing
 * a dashlets.sendAction request.
 *
 */
processors = {};


// GroupSend
processors['gs'] = function(args)
{
	var groupName = args.n,
		action = args.a,
		params = args.p;

	var elements = $J('[dashletGroup~='+groupName+']');
	elements.each( function(index, element) {
		Dashboard.dashlets.sendAction( element.id, action, params )
	});
	return true;
}

// GroupRefresh
processors['gref'] = function(args)
{
	var groupName = args.n;
	var elements = $J('[dashletGroup~='+groupName+']');
	elements.each( function(index, element) {
		Dashboard.dashlets.refresh( element.id )
	});
	return true;
}


// ClassSend
processors['cs'] = function(args)
{
	var className = args.n,
		action = args.a,
		params = args.p;

	var elements = $J('[dashletClass~='+className+']');
	elements.each( function(index, element) {
		Dashboard.dashlets.sendAction( element.id, action, params )
	});
	return true;
}

// ClassRefresh
processors['cref'] = function(args)
{
	var className = args.n;
	var elements = $J('[dashletClass~='+className+']');
	elements.each( function(index, element) {
		Dashboard.dashlets.refresh( element.id )
	});
	return true;
}



// Alert
processors['alert'] = function(args)
{
	alert(args);
	return true;
}

// LoadScript
processors['ldjs'] 	= function(args)
{
	$J.getScript(args, function() {
	});
	return true;
}

// Call
processors['call'] 	= function(args){
	var parameters = args.args;
	var scr = new Array();
	scr.push(args.fn);
	scr.push('(');
	if ('undefined' != typeof parameters)
	{
		if ('object' == typeof (parameters))
		{
			var iLen = parameters.length;
			if (0 < iLen) {
				scr.push('parameters[0]');
				for (var i = 1; i < iLen; ++i)
					scr.push(', parameters[' + i + ']');
			}
		}
	}
	scr.push(');');

	args.context = {};
	args.context.delegateCall = function() {
		eval(scr.join(''));
	}
	args.context.delegateCall();
}


// Execute
processors['exec'] = function(args) {
	var returnValue = true;
	var context = {};
	context.delegateCall = function() {
		eval(args);
	}
	context.delegateCall();
	return returnValue;
}



/**
 * Dashboard.utilities
 */

utilities = {};


/**
 * Private utility function that updates the Sliders of Column width settings
 */
utilities._updateCwSliders = function(suffix, columns, widths)
{

	// reset Counters
	$J("#slider-counter-"+suffix+" input").each(function(index, element) {
		$J(element).val(widths[index]+'%');
	});

	// show only relevant Counters
	$J("#slider-counter-"+suffix+" td").hide().attr("width", "0.1%");
	$J("#slider-counter-"+suffix+" td.slider-"+columns).show().each(function(index, element) {
		$J(element).attr("width", widths[index]+'%');
	});

	if (columns == 1)
	{
		$J("#layout-widths-"+suffix).slider( "option", "disabled", true);
	}
	else
	{
		$J("#layout-widths-"+suffix).slider( "destroy");
		$J("#layout-widths-"+suffix).slider({
			animate: 'fast',
			range: columns==3,
			min: 0,
			max: 100,
			value: widths[0],
			stop: function(event, ui) {
				ui.values = ui.values || [0,100];
				var i = (columns > 2) ? ui.values[0] : ui.value,
					j = ui.values[1];

				if (columns == 2) {
					if (i < 20)
					{
						i=20;
					}
					if (i > 80)
					{
						i=80;
					}
					$J("#layout-widths-"+suffix).slider("option", "value", i);
				}

				if (columns == 3)
				{
					if (i < 20)
					{
						i=20;
					}
					if (j > 80)
					{
						j = 80;
						if (i > j) i=j;
					}
					if ((j-i) < 20)
					{
						d = 20-(j-i);
						di = i-20;
						dj = 80-j;
						i = i - d*(di/(di+dj));
						j = j + d*(dj/(di+dj));
					}

					i = parseInt(i);
					j = parseInt(j);
					$J("#layout-widths-"+suffix+"").slider("option", "values", [i,j]);
				}

				$J("#widths-0-"+suffix+"").val( i+'%' );
				$J("#widths-1-"+suffix+"").val( (j-i)+'%' );
				$J("#widths-2-"+suffix+"").val( (100-j)+'%' );

				$J("#slider-counter-"+suffix+" td.slider-"+columns)
					.each(function(index, element) {
						$J(element).attr( "width", $J("#widths-"+index+"-"+suffix).val() )
					});
			}
		}).slider("option", "disabled", false);

		if (columns==3)
		{
			$J("#layout-widths-"+suffix+"").slider("option", "values", [ widths[0], widths[0]+widths[1] ]);
		}

	}
}


return {
	id : id,
	title: title,
	columns: columns,
	columnWidths: columnWidths,
	locked: locked,

	openWindow: openWindow,
	addDashboard: addDashboard,
	lock: lock,
	blockUI: blockUI,
	unblockUI: unblockUI,

	getId: function() { return Dashboard.id},
	initialize: initialize,
	save: save,
	layout: layout,
	scrap: scrap,

	sortables: sortables,
	dialog: dialog,
	launcher: launcher,
	dashlets: dashlets,
	processors: processors,
	utilities: utilities

}

}(jQuery);

//alert(Dashboard.dashlets.setTitle)