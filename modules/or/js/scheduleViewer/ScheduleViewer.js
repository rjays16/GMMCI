/**
 *  Schedule Viewer for OR module
 *
 * @author Alvin Quinones
 */
var ScheduleViewer = function() {


	/**
	 * Internal utility function for extracting the time portion of a Date object
	 */
	function __utils_extractTime(date) {

		var curhour = date.getHours();
		var curmin = date.getMinutes();
		var cursec = date.getSeconds();
		var time = "";
		if(curhour == 0) curhour = 12;
		time = (curhour > 12 ? curhour - 12 : curhour) + ":" +
				 (curmin < 10 ? "0" : "") + curmin+
				 // ":" + (cursec < 10 ? "0" : "") + cursec + " " +
				 (curhour > 12 ? "pm" : "am");
		return time;
	}


	function create(object, options) {

		var MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
											 'September', 'October', 'November', 'December'];
		var DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		var dt = new Date();

		object = $(object);
		if (!object || object.scheduleViewer)	return false; // if the ScheduleViewer object is already initialized, exit


		options = Object.extend({

			id: '',
			title: '',

			url: false,											// URL of the data source

			maxDecking: 6,
			cutOffTime: 14,
			serverTime: null,

			month: dt.getMonth(),						// the month to be viewed
			day: dt.getDate(),
			year: dt.getFullYear(),					// the year to be viewed

			format: 'json',									// format of the data returned
			parameters: {},									// adittional parameters to be passed to AJAX

			method: 'post',


			viewType: 'month',
			width: 'auto',
			height: 'auto',


			/* STYLE NAMES */
			css: {
				ScheduleViewer: 'scheduleViewer',
				navigator: 'nav',
				navigatorDate: 'nav-date',
				navigatorPreviousMonth: 'nav-prev-mo',
				navigatorNextMonth: 'nav-next-mo',

				viewer: 'viewer',
				view: 'view',

				header: 'header',
				headerItem: 'header-item',

				footer: 'footer',


				calendar: 'Calendar',
				calendarWeek: 'cal-week',
				calendarDay: 'cal-day',
				calendarDayInactive: 'cal-day-inactive',
				calendarDayActive: 'cal-day-active',
				calendarDayHeader: 'cal-day-header',
				calendarDayContents: 'cal-day-contents',

				entry: 'cal-entry',
				entryTime: 'cal-entry-time',
				entryName: 'cal-entry-name'


			},

			/* PLACEHOLDER FOR CALLBACKS */
			callbacks: {}
		}, options);


		var Viewer=Object.extend(options, Viewer || {});
		Viewer=Object.extend({
			//
			options: options,

			// version of this script
			version: '1.0b',
			object: object,
			Schedule: [],

			// magic numbers
			firstDate: null,
			lastDate: null,


			/* initialization/redraw routine for ScheduleViewer components */
			__initializeUI : function() {

				if (this.serverTime)
				{
					var serverDate = new Date(
						this.serverTime.year,
						this.serverTime.month,
						this.serverTime.day,
						this.serverTime.hours,
						this.serverTime.minutes,
						this.serverTime.seconds,
						this.serverTime.msecs);

					var now = new Date();
					this.serverTimeOffset = now.getTime() - serverDate.getTime();
				}
				else
					this.serverTimeOffset = 0;

				var clientWidth = this.width;
				if (this.width == 'auto') {
					clientWidth = $(this.object).getWidth();
					this.width = clientWidth+'px';
				}

				this.components = {};
				this.components.window = new Element('div', {id: 'window-'+this.id })
					.addClassName(this.css.ScheduleViewer)
					.setStyle({width:this.width});

				/**
				 * Render the Navigator area; this contains the navigational elements for the ScheduleViewer
				 */
				this.components.navigator = {};
				this.components.navigator.container = new Element('div', {id: 'navigator-'+this.id}).addClassName(this.css.navigator);

				this.components.title = new Element('div', { className: 'title' }).update(""+this.title);
				var navigatorWrapper 	= new Element('div');

				this.components.navigator.date		= new Element( 'span', { id:'nav-date-'+this.id, className: this.css.navigatorDate} );
				this.components.navigator.dayView = new Element( 'button', { className: 'button'})
					.insert('<img src="../../../gui/img/common/default/calendar_view_day.png" />')
					.insert('Day View')
					.observe('click', function(e) {
						// No transitional effects DUH!
						$('monthView-'+this.id).hide();
						$('dayView-'+this.id).show();
						this.viewType = 'day';
						this.__go({ year: this.year, month: this.month, day: 1});
					}.bind(this)
				);

				this.components.navigator.monthView = new Element( 'button', { className: 'button'})
					.insert('<img src="../../../gui/img/common/default/calendar_view_month.png" />')
					.insert('Month View')
					.observe('click', function(e) {
						// No transitional effects DUH!
						$('monthView-'+this.id).show();
						$('dayView-'+this.id).hide();
						this.viewType = 'month';
						this.__go();
					}.bind(this)
				);

				var navigatorPrevious = new Element( 'button', { className: 'button'})
					.insert('<img src="../../../gui/img/common/default/control_rewind_blue.png" />')
					.insert('Previous')
					.observe('click', function(e) {
						this.__go(-1);
						e.stop();
					}.bind(this)
				);

				var navigatorNext = new Element( 'button', { className: 'button'})
					.insert('<img src="../../../gui/img/common/default/control_fastforward_blue.png" />')
					.insert('Next')
					.observe('click', function(e) {
						this.__go(0);
						e.stop();
					}.bind(this)
				);


				navigatorWrapper
					.insert(this.components.title)
					.insert(this.components.navigator.dayView)
					.insert(this.components.navigator.monthView)
					.insert(navigatorPrevious)
					.insert(this.components.navigator.date)
					.insert(navigatorNext)
				this.components.navigator.container.insert(navigatorWrapper);


				/**
				 * Render MonthView components
				 */
				this.components.monthView = new Element('div', {id: 'monthView-'+this.id })
					.addClassName(this.css.view)
					.setStyle({width:this.width});

				// header
				this.components.monthViewHeader = new Element('div', {id: 'monthViewHeader-'+this.id, className: this.css.header});
				// wrapper element for header items
				var headerWrapper = new Element('div').setStyle({width:'100%', overflow:'hidden'});
				// header items (Name of days)
				for (var i=0; i<DAY_NAMES.length; i++) {
					headerWrapper.insert(
						new Element('div',{ className: this.css.headerItem })
							.update('<span>'+DAY_NAMES[i]+'</span>')
							.setStyle({width:(100.0/7)+'%'})
					);
				}
				this.components.monthViewHeader.update(headerWrapper);
				this.components.monthly = new Element('div', {id: 'calendar-'+this.id+'', className: this.css.calendar});
				this.components.monthView.insert(this.components.monthViewHeader)
					.insert(this.components.monthly);


				/**
				 * Render DayView components
				 */
				this.components.dayView = new Element('div', {id: 'dayView-'+this.id })
					.addClassName(this.css.view)
					.setStyle({width:this.width})
					.hide();
				this.components.daily = new Element('div', {id: 'daily-'+this.id, className: ''});

				this.components.dayViewHeader = new Element('div', {id: 'dayViewHeader-'+this.id, className: this.css.header})
					.insert(
						new Element('div',{ className: this.css.headerItem, style:'width:100%' })
							.insert('<span>Day View</span>')
					);

				// wrapper element for header items
				//var height = Math.floor(450 / this.maxDecking);
				var height = this.height / this.maxDecking;
				for (var i=0; i<this.maxDecking; i++) {

					var dailyWrapper = new Element('div').setStyle({
						display: 'block',
						width:'100%',
						overflow:'hidden',
						height: height+'px',
						background: '#ccc'
					});



					var entryHeader = new Element('div',{ className: this.css.header })
						.update('<span style="font: normal 18px Arial; color:#fff">'+(i+1)+'</span>')
						.setStyle({
							display: 'inline-block',
							width:'40px',
							height: height+'px',
							paddingTop: (height/2-12)+'px',
							textAlign: 'center',
							textShadow: '0 -1px 0 #000'
						});


					var entry = new Element('div', { className: 'day-view-entry'} )
						.setStyle({
							height: height+'px'
						});


//					var entryColor = new Element('div', { className: 'day-view-entry-color'})
//						.setStyle({
							//display: 'inline-block',
//							display: 'none',
//							width: '6px',
//							backgroundColor: 'blue',
//							MozBoxShadow: 'inset 0 20px 0 rgba(255, 255, 255, 0.4)',
//							height: '100%'
//						});

//					var entryDetails = new Element('div', { className: 'day-view-entry-decked-panel'})
//						.setStyle({
//							display: 'inline-block',
//							backgroundColor: '#fdfdfd',
//							background: '-moz-linear-gradient(top, #fdfdfd, #e8e8e8)',
//							MozBoxShadow: 'inset 0 20px rgba(255, 255, 255, 0.4)',
//							width: '100%',
//							height: height+'px',
//							valign: 'top'
//						}).update(
//							'<table width="100%" cellspacing="0" cellpadding="0" border="0">'+//
//								'<tr>'+
//									'<td width="25%" valign="middle">'+
//										'<span class="dataLabel">Name:</span>'+'<span class="dataItem">Lastname, Firstname</span>'+
//										'<span class="dataLabel">Operation:</span>'+'<span class="dataItem">Herniorrhapy - G.A. (Adult)</span>'+
//										'<span class="dataLabel">Surgeon:</span>'+'<span class="dataItem">Segworks Doctor</span>'+
//									'</td>'+
//								'</tr>'+
//								'<tr>'+
//									'<td nowrap="nowrap">'+
//										'<span style="font:normal 11px Arial">PID:######## Case: ##########</span>'+
//									'</td>'+
//								'</tr>'+
//							'</table>'
//						);


					dailyWrapper.insert(entryHeader).insert(entry);
					this.components.daily.insert(dailyWrapper);
				}

				this.components.dayView
					.insert( this.components.dayViewHeader )
					.insert( this.components.daily);


				/**
				 * Render Viewer component which will act as the primary container element for the different Views (Day and MonthViews)
				 */
				this.components.viewer = new Element('div', {id: 'Viewer-'+this.id })
					.addClassName(this.css.viewer);

				// We need a wrapper around the views in case we need to add transition effects
				// As of now, adding transitions slow down the UI :(
				this.components.viewer.insert(
					new Element('div', { class: 'viewWrapper'} )
						.insert(this.components.monthView)
						.insert(this.components.dayView)
				);
				/**
				 * Insert the all the primary containers into the main window
				 */
				this.components.window
					.insert(this.components.navigator.container)
					.insert(this.components.viewer);

				/**
				 * Insert the Main window into the main object, rendering it visible in the DOM
				 */
				this.object.insert(this.components.window);
			},



			/**
			 * Renderer for updating navigational elements
			 */
			__drawNav: function() {

				switch (this.viewType)
				{
					case 'day':
						if (this.components.navigator.date) {
							this.components.navigator.date.update( MONTH_NAMES[this.month]+' '+this.day+', '+this.year );
						}
						break;
					default: case 'month':
						if (this.components.navigator.date) {
							this.components.navigator.date.update( MONTH_NAMES[this.month]+' '+this.year );
						}
						break;
				}


			},


			__drawCalendar: function() {
				var dt = new Date();
				var dayOne = new Date(this.year, this.month, 1, 0, 0, 0, 0);

				var clientWidth = this.width;
				if (this.width == 'auto') {
					clientWidth = $(this.object).getWidth();
				}


				var dayOfTheWeek = dayOne.getDay();
				var referenceDate = new Date(dayOne.getTime() - 24*60*60*1000*dayOfTheWeek);
				//var rowHeight = clientWidth/7;
				var rowHeight = 120;

				this.firstDate = new Date(referenceDate);

				this.components.monthly.update();

				while ( referenceDate.getMonth()/12+referenceDate.getFullYear() <= this.month/12+this.year) {

					var week =  new Element('div', {className: this.css.calendarWeek})
						.setStyle({height:(rowHeight)+'px'});
					for (var i=0;i<7;i++) {
						// render day box

						var cn=this.css.calendarDay;
						if (this.month != referenceDate.getMonth()) {
							cn=this.css.calendarDayInactive;
						}
						if (dt.getMonth() == referenceDate.getMonth() &&
								dt.getDate() == referenceDate.getDate() &&
								dt.getFullYear() == referenceDate.getFullYear()) {
							cn=this.css.calendarDayActive;
						}

						var curyear = referenceDate.getFullYear();
						var curmon = referenceDate.getMonth();
						var curday = referenceDate.getDate();
						var date = {
							fullDate: curyear + "-" + (curmon < 10 ? "0" : "") + (curmon+1) + "-" + (curday < 10 ? "0" : "") + curday,
							month: curmon,
							year: curyear,
							day: curday
						};

						var day = new Element('div', {className: cn}).setStyle({width:(100.0/7)+'%'})
							.observe('click', function() {
								args = $A(arguments)
								this.components.navigator.dayView.click();
								nd();
								this.__go({
									day: args[1].day,
									month: args[1].month,
									year: args[1].year
								})
							}.bindAsEventListener( this, date));


						if (this.callbacks.day) {
							if (this.callbacks.day.mouseover) {
								day.observe('mouseover', this.callbacks.day.mouseover.bindAsEventListener( day ));
							}

							if (this.callbacks.day.mouseout) {
								day.observe('mouseout', this.callbacks.day.mouseout.bindAsEventListener( day ));
							}

							if (this.callbacks.day.click) {
								//day.observe('click', this.callbacks.day.click.bindAsEventListener( day ));
							}
						}

						var dayHeader = new Element('div', { className: this.css.calendarDayHeader })
							.update(MONTH_NAMES[referenceDate.getMonth()].substring(0,3)+' '+referenceDate.getDate());


						var id=referenceDate.getTime()+'-'+this.id;

						var dayContents = new Element('div', {id:id, className: this.css.calendarDayContents})
							.setStyle({height:(rowHeight-19)+'px'});

						day.insert(dayHeader).insert(dayContents);
						week.insert(day);
						referenceDate.setTime(referenceDate.getTime() + 24*60*60*1000);
					}
					this.components.monthly.insert(week);
				}

				this.lastDate = new Date(referenceDate);
			},


			__fetch: function() {
				this.Schedule = [];

				if (this.url) {
					if (this.beforeRequest) {
						this.beforeRequest.bind(this)();
					}

					var parameters = this.parameters || {};

					if (this.viewType == 'day')
					{
						parameters.d1 = this.year+'-'+(this.month+1)+'-'+(this.day);
						parameters.d2 = this.year+'-'+(this.month+1)+'-'+(this.day);
					}
					else
					{
						if (this.firstDate) {
							parameters.d1 = this.firstDate.getFullYear()+'-'+(this.firstDate.getMonth()+1)+'-'+(this.firstDate.getDate());
						}
						if (this.lastDate) {
							parameters.d2 = this.lastDate.getFullYear()+'-'+(this.lastDate.getMonth()+1)+'-'+(this.lastDate.getDate());
						}
					}


					new Ajax.Request( this.url, {
						method: this.method,
						asynchronous: false,
						parameters: parameters,
						onSuccess: function(transport){
							this.Schedule = transport.responseText.evalJSON();;
							if (this.Schedule) {

								// invoke onSuccess callback
								if (this.onSuccess) {
									this.onSuccess.bind(this)();
								}

								this.__processSchedule();
							}
							else {
								// invoke onError callback
								// error in parsing JSON response
								if (this.onError) {
									this.onError.bind(this)();
								}
							}
						}.bind(this),
						onError: function(transport){
							 // invoke onError callback
							if (this.onError) {
								this.onError.bind(this)();
							}
						}
					});
				}
			},


			__processSchedule: function() {
				if (this.Schedule) {

					if (this.viewType == 'day')
					{
						this.__clearDayView();
						this.__populateDayView(this.Schedule);
					}
					else
					{
						for (var i=0; i<this.Schedule.length; i++) {
							this.__drawDoodad(this.Schedule[i]);
						}
					}
				}
			},


			__clearDayView: function()
			{
				// clear the current View first
				$$('.day-view-entry').each(function(element) {
					element = $(element);
					element.childElements().invoke('remove');
				})
			},

			__populateDayView: function(list)
			{
				var count = list.length;
				var entries = $$('.day-view-entry');
				for (var i=0; i<this.maxDecking; i++)
				{
					if (this.Schedule[i])
					{
						var sked = this.Schedule[i];
						var height = $(entries[i]).getHeight();
						var entryColor = new Element('div', { className: 'day-view-entry-color'})
							.setStyle({
								backgroundColor: sked.Color,
								MozBoxShadow: 'inset 0 '+(height/2)+'px 0 rgba(255, 255, 255, 0.4)'
							});
							var entryDetails = new Element('div', { className: 'day-view-entry-decked-panel'})
								.setStyle({
									height: '100%',
									MozBoxShadow: 'inset 0 '+(height/2)+'px rgba(255, 255, 255, 0.4)'
								}).update(
									'<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">'+
										'<tr>'+
											'<td width="25%" valign="middle">'+
												'<span class="dataItem" style="font:bold 14px Verdana">'+sked.Name+'</span>'+
												'<span class="dataLabel" style="font:bold 11px Arial">PID:</span>'+'<span class="dataItem" style="bold:normal 11px Arial">'+sked.Pid+'</span>'+
												'<span class="dataLabel" style="font:bold 11px Arial">Case:</span>'+'<span class="dataItem" style="bold:normal 11px Arial">'+sked.Case+'</span>'+

												'<br/>'+

												'<span class="dataLabel">Operation:</span>'+'<span class="dataItem">'+sked.Procedure+'</span>'+
												'<span class="dataLabel">Req. doctor:</span>'+'<span class="dataItem">'+sked.Surgeon+'</span>'+

												'<br/>'+

												'<span class="dataLabel">OR number:</span>'+'<span class="dataItem">'+sked.OrNo+'</span>'+
												'<span class="dataLabel">Date of payment:</span>'+'<span class="dataItem">'+sked.OrDate+'</span>'+


												'<br/>'+

												'<span class="dataLabel">Status:</span>'+'<span class="dataItem" style="color:'+sked.Color+'">'+sked.Status+'</span>'+

												'<br/>'+
												'<span class="dataLabel">Priority:</span>'+sked.Priority+

											'</td>'+
										'</tr>'+
									'</table>'
								);
						entries[i].insert(entryColor).insert(entryDetails);
					}
					else
					{
						var height = $(entries[i]).getHeight();
						var entryDetails = new Element('div')
							.setStyle({
								height: '100%',
								MozBoxShadow: 'inset 0 '+(height/2)+'px 0 rgba(255, 255, 255, 0.1)'
							});

						$(entries[i]).insert(entryDetails);

						var date = new Date(this.year, this.month, this.day);
						var now = new Date( (new Date()).getTime() - this.serverTimeOffset );
						//var todate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
						var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
						var cutoffDate = new Date( now.getFullYear(), now.getMonth(), now.getDate(), this.cutOffTime );

						12 - 13

						if ((date < now) ||
							(this.cutOffTime  && (now.getTime() > date.getTime()-(24-this.cutOffTime)*60*60*1000)))
						{
							entryDetails.addClassName('invalid');
						}
						else
						{
							entryDetails.addClassName('day-view-entry-empty-panel').update(
								'<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">'+
									'<tr>'+
										'<td width="25%" align="left" valign="middle" class="">'+
											'<span style="font:normal 18px Arial; color:green;">&nbsp;Available slot..</span>'+
										'</td>'+
									'</tr>'+
								'</table>'
							).observe('click', function() {
								if (window.parent.$('or_operation_date')) {
									window.parent.$('or_operation_date').value = this.year + '-' +
										((this.month+1) < 10 ? '0' : '') +
										(this.month+1) +
										'-' +
										((this.day+1) < 10 ? '0' : '') +
										this.day;
									window.parent.checkDate();
									window.parent.cClick();
								}
							}.bindAsEventListener(this));
							break;
						}
					}
				}
			},


			__go: function(where) {
				if ('object' == typeof where)
				{
					this.day = where.day;
					this.month = where.month;
					this.year = where.year;
				}

				else if ('number' == typeof where)
				{
					if ('day' == this.viewType)
					{
						var date = new Date(this.year, this.month, this.day);

						if (where < 0) {
							date = new Date(date.valueOf() - 24*60*60*1000);
						}
						else
						{
							date = new Date(date.valueOf() + 24*60*60*1000);
						}
						this.year = date.getFullYear();
						this.month = date.getMonth();
						this.day = date.getDate();
					}
					else if ('month' == this.viewType)
					{
						if (where < 0) {
							this.month -= 1;
							if (this.month<0) {
								this.year -= 1;
								this.month = 11;
							}
						}
						else {
							this.month += 1;
							if (this.month>11) {
								this.year += 1;
								this.month = 0;
							}
						}
					}
				}

				this.__drawNav();

				if (this.viewType == 'month')
					this.__drawCalendar();

				this.__fetch();
			},


			__drawDoodad: function(doodad) {
				doodad = Object.extend({
					Y:0, M:0, D:0,
					h:0, m:0, s:0,
					Name: '',
					Procedures: []
				}, doodad);

				if (this.viewType == 'day')
				{
				}

				else if (this.viewType == 'month') {
					var dt = new Date(doodad.Y, doodad.M, doodad.D);
					//var tm = new Date(doodad.Y, doodad.M, doodad.D, doodad.h, doodad.m, doodad.s, 0);

					if ($(dt.getTime()+'-'+this.id)) {
						var contentBox = $(dt.getTime()+'-'+this.id);

						var wrapper = $(doodad.Id);
//						var time = new Element('span', { className: this.css.entryTime })
//							.update(__utils_extractTime(tm));
						var name = new Element('span', { className: this.css.entryName });
							//.update(doodad.Name);

						if (!wrapper) {
							var wrapper = new Element('div',{id:doodad.Id}).addClassName( this.css.entry ).setStyle({ background: '-moz-linear-gradient(left,'+(doodad.Color||'grey')+',#fff)'});
							wrapper.callbacks = {};
						}
						else {
							if (wrapper.callbacks.click) wrapper.stopObserving('click', wrapper.callbacks.click);
							if (wrapper.callbacks.mouseover) wrapper.stopObserving('click', wrapper.callbacks.mouseover);
							if (wrapper.callbacks.mouseout) wrapper.stopObserving('click', wrapper.callbacks.mouseout);
						}
						if (this.callbacks.entry) {
							if (this.callbacks.entry.mouseover) {
								wrapper.callbacks.mouseover = this.callbacks.entry.mouseover.bindAsEventListener(doodad);
								wrapper.observe('mouseover', wrapper.callbacks.mouseover );
							}
							if (this.callbacks.entry.mouseout) {
								wrapper.callbacks.mouseout = this.callbacks.entry.mouseout.bindAsEventListener(doodad);
								wrapper.observe('mouseout', wrapper.callbacks.mouseout );
							}
							if (this.callbacks.entry.click) {
								wrapper.callbacks.click = this.callbacks.entry.click.bindAsEventListener(doodad);
								wrapper.observe('click', wrapper.callbacks.click );
							}
						}

						wrapper.update(name);
						contentBox.insert(wrapper);
					}
					else {
						// Date contents not in this View
					}
				}
			}

		}, Viewer);


		Viewer.__initializeUI();
		Viewer.__drawNav();
		Viewer.__drawCalendar();
		Viewer.__fetch();
		if (Viewer.callbacks.create) {
			Viewer.callbacks.create();
		}

		return {
			me: Viewer,
			fetch: Viewer.__go.bind(Viewer)
		};
	}



	return {
		create: create
	};
}();