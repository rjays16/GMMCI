var ListGen = function() {
	var STATUS = { isInitializing: 0, isReady: 1, isLoading: 2 };
	var SORTING = { none:0, asc:1, desc:-1 };

	function _highlightColumn(e) {
		var column = Event.element(e);
		var idx = column.readAttribute('colIndex');
		var list = $A(arguments).shift();
	}

	function create(obj, options) {
		// obj is the id or the DOM object of the container div
		obj = $(obj)
		if (!obj || obj.list)	return false; // if the list is already initialized, exit

		options = Object.extend({

			// data fetch options
			url: false,
			method: 'get',  // POST or GET
			responseType: 'xml', // XML or JSON
			params: {}, // hash value that will be appended as a query string to URL
			data: [], // initial data to be loaded

			// appearance options
			layout: [
				['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
				['#thead'],
				['#tbody']
			],
			height: 200,  // height of the content area, set to 'auto' for dynamic sizing
			width: 'auto',	// width of the List, 'auto' will set width to that of the parent element
			rowHeight: 24, // height of the table rows in pixels


			minColumnWidth: 50, // for column resizing
			maxColumnWidth: 100,

			zebra: true,  // enable odd/even row striping
			showFooter: false, // show list headers on footer
			iconsOnly: false, // set to true if text inside controls should be hidden (minimizes list width)
			showRownum: false, // show row number
			effects: false, // superfluous effects if available

			// pagination options
			enablePagination: true,
			currentPage: 1,
			totalPages: 1,
			maxRows: 10, // max rows per page
			pageStat: 'Showing {from}-{to} of {total} items', // tokens: from, to, total, pagefrom, pageto, totalpages

			// behavioral options
			resizable: false,
			resiazbleColumns: false,
			autoLoad: true, // load remote data upon initialization?

			// messages
			initialMessage: 'No records found...', // message to display when the list is first rendered
			emptyMessage: 'No records found...', // message to display when no rows are returned

			// styles
			style: {
				list: 'lg-list',
				button: 'lg-button',
				toolbar: 'lg-toolbar',
				navigator: 'lg-nav',
					pageStat: 'lg-pageStat',
					next: 'lg-pageNext',
					prev: 'lg-pagePrev',
					first: 'lg-pageFirst',
					last: 'lg-pageLast',
					refresh: 'lg-pageRefresh',
				wrapper: 'lg-wrapper',
				content: 'lg-content',
					header: 'lg-header',
					footer: 'lg-footer',
					columnHeader: 'lg-columnHeader',
					spacer: 'lg-spacer',
					dataTable: 'lg-table',
					dataRow: 'lg-tr',
					altRow: 'lg-alt',
					dataItem: 'lg-td',
				sortable: 'lg-sortable',
				sortAsc: 'lg-asc',
				sortDesc: 'lg-desc',
				loader: 'lg-loader',
				activeCol: 'lg-activeColumn',
			},

			// callbacks
			onCreate: null, // fired when the list is initialized
			onChangeSort: null, // fired when column header is clicked to change sorting
			onRequest: null, // fired before AJAX request is sent
			onError: null, // fired on AJAX request error
			onSuccess: null, // fired if AJAX request is sccessful
			onComplete: null // fired after AJAX request is completed
		}, options);

		/* column Model specification */
		/*
		columnModel = [
			0: { // <-- column index
				name: '',
				label: 'Column label',
				width: 100,
				sort: SORTING.asc,
				isSortable: true,
				isVisible: true,

				//  the default renderer processes data as raw text, this can be overwritten
				// if complex data is passed, and needs to be processed first.
				render: function(data, rowIndex, columnName) {
					return data;
				}
			}
		];
		*/

		/* create the list object
			list.options -> options object for the list
		*/
		// copy option properties to list object
		var theList = Object.extend(options, theList);

		theList = Object.extend({
			_internals: {
				effectsFinished: true,
				queue:[]
			},
			version: '0.1b',
			element: obj,
			status: STATUS.isInitializing,

			enableControls : function(enable) {
				// disable nav buttons
				this.elements.container.select('.'+this.style.button).each(
					function(e) { e.writeAttribute('disabled', true) }
				);

				this.elements.container.select('.'+this.style.columnHeader).each(
					function(e) {
						var idx = parseInt(e.getAttribute('colIndex')),
							cm = this.columnModel[idx];
						if (isNaN(idx)) return false;
						if (cm.sortable) {
							e.writeAttribute({
								disabled: !enable,
								sortDir: SORTING.none
							});
							e.firstDescendant().removeClassName(this.style.sortAsc)
								.removeClassName(this.style.sortDesc);
							if (this.sortColumn==cm.name) {
								e.writeAttribute('sortDir',this.sortDirection)
									.firstDescendant().addClassName( this.sortDirection == SORTING.asc ? this.style.sortAsc : this.style.sortDesc );
							}
						}
					}.bind(this)
				);

				if ('undefined' !== typeof this.elements.refresh)
				{
					this.elements.refresh.writeAttribute('disabled', !enable);
				}

				if (enable && this.data.length && this.enablePagination) {
					if ('undefined' !== this.elements.first) this.elements.first.writeAttribute('disabled', this.currentPage<=1);
					if ('undefined' !== this.elements.prev) this.elements.prev.writeAttribute('disabled', this.currentPage<=1);
					if ('undefined' !== this.elements.next) this.elements.next.writeAttribute('disabled', this.currentPage>=this.totalPages);
					if ('undefined' !== this.elements.last) this.elements.last.writeAttribute('disabled', this.currentPage>=this.totalPages);
				}
			},

			firstPage: function() {
				this.refresh(1, this.sortColumn, this.sortDirection);
			},

			previousPage: function() {
				if (this.currentPage > 1) {
					this.refresh(this.currentPage-1, this.sortColumn, this.sortDirection);
				}
			},

			nextPage: function() {
				if (this.currentPage < this.totalPages) {
					this.refresh(this.currentPage+1, this.sortColumn, this.sortDirection);
				}
			},

			lastPage: function() {
				this.refresh(this.totalPages, this.sortColumn, this.sortDirection);
			},

			sort: function(col, dir) {
				this.refresh(this.currentPage, col, dir);
			},

			reload: function() {
				this.refresh(this.currentPage, this.sortColumn, this.sortDirection);
			},

			refresh: function(page, sort, dir) {

				if (typeof(page)!='undefined')
					this.currentPage=page;
				else
					this.currentPage=1;
				if (typeof(sort)!='undefined') {
					this.sortColumn=sort;
				}
				if (typeof(dir)!='undefined') {
					this.sortDirection=dir;
				}

				if (this.url) {
					this._startLoading();
					var queryObject = Object.extend({
						mr: this.enablePagination ? this.maxRows : 0,
						sort: this.sortColumn,
						dir: this.sortDirection,
						page: this.enablePagination ? this.currentPage : 1
					},this.params);
					if (this.onRequest) {
						this.onRequest.bind(this)();
					}
					new Ajax.Request( this.url, {
						method: this.method,
						parameters: queryObject,
						onError: function(transport){
							this.total = 0;
							this.totalPages = 1;
							this.data = [];
							this._updateData('Error occurred while fetching data...');

							// invoke onError callback
							if (this.onError) {
								this.onError.bind(this)();
							}
						}.bind(this),
						onSuccess: function(transport) {

							try {
								this.response = transport.responseText.evalJSON();
							}
							catch (e)
							{
								this.response = null;
							}

							if (this.response) {
								if (this.enablePagination) {
									if (this.response.currentPage)
										this.currentPage = parseInt(this.response.currentPage);

									this.total = parseInt(this.response.total);
									if (this.enablePagination) {
										if (isNaN(this.total)) {
											this.total = 0;
											this.totalPages = 1;
										}
										else {
											this.totalPages = Math.ceil(this.total/this.maxRows);
										}
									} else {
										this.totalPages = 1;
									}
								}

								this.data = this.response.data;
								this._updateData();
								this._stopLoading();

								// invoke onSuccess callback
								if (this.onSuccess) {
									this.onSuccess.bind(this)();
								}
							}
							else {
								this.total = 0;
								this.totalPages = 1;
								this.data = [];
								this._updateData(transport.responseText);

								this._stopLoading();
								// invoke onError callback
								if (this.onError) {
									this.onError.bind(this)();
								}
							}
						}.bind(this)

					});
				}
				else {
				}
			},

			// private methods

			_setSortColumn: function(idx, dir) {
				if (!this.columnModel[idx] || !this.columnModel[idx].sortable) {
					return false;
				}
				for (i=0;i<this.columnModel.length;i++) {
					if (i==idx) {
						this.columnModel[i].sorting = dir;
						this.sortColumn = this.columnModel[i].name;
						this.sortDirection = dir;
					}
					else {
						this.columnModel[i].sorting = SORTING.none;
					}
				}
			},

			_stopLoading: function() {
				if (!this._internals.effectsFinished) {
					this._internals.queue.push(this._stopLoading)
					return false;
				}
				if (this.status != STATUS.isLoading) {
					return false;
				}
				this._internals.effectsFinished = false;
				if (typeof(Scriptaculous)=='object' && this.effects) {
					this.elements.loader.setStyle({position:'absolute'});

					if ('undefined' !== this.elements.content)
					{
						this.elements.content.show();
					}

					this.elements.loader.clonePosition(this.elements.content);
					this.elements.loader.fade({
						duration:0.8,
						afterFinish: function() {
							this.enableControls(true);
							this.status = STATUS.isReady;
							this._internals.effectsFinished = true;
							if (this._internals.queue==true) {
								this._internals.queue.shift().bind(this)();
							}
						}.bind(this)
					});
				}
				else {
					this.elements.loader.hide();
					if ('undefined' !== this.elements.content)
					{
						this.elements.content.show();
					}
					this.enableControls(true);
					this.status = STATUS.isReady;
					this._internals.effectsFinished = true;
				}
			},

			_startLoading: function() {
				if (!this._internals.effectsFinished) {
					this._internals.queue.push(this._startLoading)
					return false;
				}
				if (this.status == STATUS.isLoading)
					return false;
				this._internals.effectsFinished = false;
				this.status = STATUS.isLoading;
				this.enableControls(false);
				this.elements.loader.clonePosition(this.elements.content);
				this.elements.loader.setStyle({position:'static'});

				if ('undefined' !== this.elements.content)
				{
					this.elements.content.hide();
				}

				this.elements.loader
					.setStyle({position:'static'})
					.show();
				this.elements.content.hide();
				this._internals.effectsFinished = true;
			},

			_updateData: function(msg) {
				if ('undefined' === typeof this.elements.content)
					return;

				this.elements.content.wrapper.update();
				var containerWidth = this.elements.container.getWidth();

				if (this.data.length) {
					var i, j;
					var tb = new Element('table', {id: this.id+'-dataTable'}).addClassName(this.style.dataTable);
					for (i=0;i<this.data.length;i++) {
//						var totalWidth = 0;
						var tr = new Element('tr', {id: this.id+'-dataRow'+i})
							.addClassName(i%2==0 ? this.style.dataRow : this.style.altRow)
							.setStyle({ height: this.rowHeight+'px' });
						for (j=0;j<this.columnModel.length;j++) {
							var cm = this.columnModel[j];
							var data = this.data[i][cm.name];
							var td = new Element('td', {id: this.id+'-dataItem'+i+'-'+cm.name}).addClassName(this.style.dataItem)
								.setStyle( cm.styles );

							if (!cm.visible) {
								td.hide();
							}

							if ('undefined' !== typeof(cm.width))
							{
								if ('string' != typeof(cm.width))
								{
									cm.width = cm.width.toString();
								}

								if (cm.width.substr(-1) == '%')
								{
									td.style.width = ( containerWidth*(cm.width.slice(0,-1)/100) - 2 )+'px';
								}
								else
								{
									td.style.width = cm.width + 'px';
								}
							}

							var span = new Element('span');
							if (cm.render) {
								span.update( cm.render(this.data, i, cm.name) )
							}
							else {
								span.update(data);
							}
							td.update( span );
							tr.insert( td );
						}
						tr.insert('<td class=" '+theList.style.spacer+'"></td>');
						tb.insert( tr );
					}
					this.elements.content.wrapper.insert( tb );

					// update page statistics
					if (this.enablePagination && 'undefined'!==this.elements.pageStat) {
						var from = (this.currentPage-1)*theList.maxRows+1;
						var to = from+this.data.length-1;
						this.elements.pageStat.update(
							this.pageStat.replace(/\{from\}/gi, from)
								.replace(/\{to\}/gi, to)
								.replace(/\{total\}/gi, this.total)
								.replace(/\{page\}/gi, this.currentPage)
								.replace(/\{pagetotal\}/gi, this.totalPages)
						);
					}
				}
				else {
					var tb = new Element('table', {id: this.id+'-dataTable-empty'}).addClassName(this.style.dataTable);
					var tr = new Element('tr', {id: this.id+'-dataRow-empty'}).addClassName(this.style.dataRow);
					var td =  new Element('td', {id: this.id+'-dataItem-empty'}).addClassName(this.style.dataItem)
						.update(this.status==STATUS.isInitializing ? this.initialMessage :
							(msg ? msg : this.emptyMessage));
					this.elements.content.wrapper.insert( tb.insert( tr.insert( td ) ) );
					if ('undefined'!==this.elements.pageStat)
					{
						this.elements.pageStat.update('');
					}
				}

				if (this.width !== 'auto')
				{
					this.elements.content.setStyle({
						width: this.width+'px',
						overflowX: 'auto'
					});
				}
				else
				{
					this.elements.content.setStyle({
						overflowX: 'auto'
					});
				}

				if (this.height !== 'auto')
				{
					this.elements.content.setStyle({
						height: this.height+'px',
						overflowY: 'auto'
					});
				}
				else
				{
					this.elements.content.setStyle({
						overflowY: 'auto'
					});
				}

			},

			_updateHeaders: function() {
				if ('undefined' === typeof this.elements.header && 'undefined' === typeof this.elements.footer)
				{
					return;
				}

				var containerWidth = this.elements.container.getWidth();

				if ('undefined' !== typeof this.columnModel) {
					var totalWidth = 0;
					var sorted=false;
					var hTb, fTb, hTr, fTr;

					if ('undefined' !== typeof this.elements.header)
					{
						hTb = new Element('table', {id: this.id+'-tableHeader'});
						hTr = new Element('tr', {id: this.id+'-trHeader'});
					}

					if ('undefined' !== typeof this.elements.footer)
					{
						fTb = new Element('table', {id: this.id+'-tableFooter'});
						fTr = new Element('tr', {id: this.id+'-trFooter'});
					}

					for (i=0;i<this.columnModel.length;i++) {

						var hTh, hSpan, fTh, fSpan;
						var cm = this.columnModel[i];


						hTh = new Element('td', {id: this.id+'-columnHeader-'+i})
							.addClassName(this.style.columnHeader)
							.writeAttribute({
								colIndex: i,
								colName: cm.name,
								sortDir: cm.sorting
							});

						if ('undefined' !== typeof(cm.width)) {
							if ('string' != typeof(cm.width))
							{
								cm.width = cm.width.toString();
							}
							if (cm.width.substr(-1) == '%')
							{
								w = containerWidth*(cm.width.slice(0,-1)/100);
								hTh.style.width = w+'px';
								totalWidth += w;
							}
							else
							{
								hTh.style.width = cm.width + 'px';
								totalWidth += (cm.width-0.0);
							}
						}

						if (!cm.visible) {
							hTh.hide();
						}

						if (cm.sortable) {
							hTh.addClassName(this.style.sortable);
						}

						// Header label
						hSpan = new Element('span').update(cm.label);
						if (cm.sorting !== SORTING.none) {
							if (cm.sorting === SORTING.asc) {
								hSpan.addClassName(this.style.sortAsc);
							}
							else if (cm.sorting === SORTING.desc) {
								hSpan.addClassName(this.style.sortDesc);
							}
						}

						if ('undefined' !== typeof this.elements.footer) {
							fTh = $(hTh.cloneNode(true))
								.writeAttribute({id:this.id+'-columnFooter-'+i});
							fSpan = $(hSpan.cloneNode(true));
						}

						// set event observers for column Headers/Footers
						if (cm.sortable) {
							if ('undefined' !== typeof this.elements.header)
							{
								hTh.observe(
									'click',function(event) {
										var element = Event.findElement(event, 'td');
										var col=element.readAttribute('colName'),
											dir=parseInt(element.readAttribute('sortDir')),
											newDir=SORTING.asc;
										if (dir == SORTING.asc) {newDir=SORTING.desc;}
										if (!element.readAttribute('disabled')) {
											this.sort(col, newDir);
										}
									}.bindAsEventListener(this)
								);
							}

							if ('undefined' !== typeof this.elements.footer)
							{
								fTh.observe(
									'click',function(event) {
										var element = Event.findElement(event, 'td');
										var col=element.readAttribute('colName'),
											dir=parseInt(element.readAttribute('sortDir')),
											newDir=SORTING.asc;
										if (dir == SORTING.asc) {newDir=SORTING.desc;}
										if (!element.readAttribute('disabled'))
											this.sort(col, newDir);
									}.bindAsEventListener(this)
								);
							}
						}

						if ('undefined' !== typeof this.elements.header)
						{
							hTh.update(hSpan);
							hTr.insert(hTh);
						}

						if ('undefined' !== typeof this.elements.footer)
						{
							fTh.update(fSpan);
							fTr.insert(fTh);
						}
					}


					//alert(totalWidth)
					var spacerWidth = '20px';
					if (totalWidth < containerWidth)
					{
						spacerWidth=(containerWidth-totalWidth)+'px';
					}
					if ('undefined' !== typeof this.elements.header)
					{
						//hTr.insert('<td class="'+theList.style.columnHeader+' '+theList.style.spacer+'" '+(this.width == 'auto' ? 'style="width:20px"' : '') + '></td>');
						hTr.insert('<td class="'+theList.style.columnHeader+' '+theList.style.spacer+'" style="width:'+spacerWidth+'"></td>');
						hTb.insert(hTr);
						this.elements.header.wrapper.insert( hTb );
					}

					if ('undefined' !== typeof this.elements.footer)
					{
						fTr.insert('<td class="'+theList.style.columnHeader+' '+theList.style.spacer+'" style="width:'+spacerWidth+'"></td>');
						fTb.insert(fTr);
						this.elements.footer.wrapper.insert( fTb );
					}
				}
			}
		}, theList);

		if ("string" !== typeof theList.width)
		{
			theList.width = theList.width.toString();
		}

		// compute Page stats
		if (theList.data) {
			theList.count = options.data.length;
		}

		// create divs
		theList.elements = {
			container: new Element('div', {id: theList.id+'-container' })
				.addClassName(theList.style.list)
		};

		var parentWidth = obj.getWidth();
		if (theList.width.substr(-1) == '%')
		{
			theList.elements.container.style.width = parentWidth*(theList.width.slice(0,-1)/100)+'px';
		}
		else
		{
			if (theList.width!='auto') {
				theList.elements.container.style.width = theList.width+'px';
			}
			else {
				//theList.elements.container.style.width = 'auto';
				totalWidth = 0;
				for (i=0;i<theList.columnModel.length;i++) {
					cm = theList.columnModel[i];

					if ("string" !== typeof cm.width)
					{
						cm.width = cm.width.toString();
					}
					if (cm.width.substr(-1) == '%')
					{
						w = parseFloat(parentWidth*(cm.width.slice(0,-1)/100));
					}
					else
					{
						w = parseFloat(cm.width);
					}
					totalWidth += w;
				}
				theList.elements.container.style.width = totalWidth+2+'px';
			}
		}

		// create toolbar
		theList.elements.toolbars = [];



		function __isArray(testObject )
		{
			return testObject && !(testObject.propertyIsEnumerable('length')) && typeof testObject === 'object' && typeof testObject.length === 'number';
		}

		function __createToolbar()
		{
			//return new Element('div', {id: theList.id+'-toolbar'+id}).addClassName(theList.style.toolbar);
			return new Element('div').addClassName(theList.style.toolbar);
		}

		function __layout(container, layout)
		{

			if ('undefined' === typeof container)
				return false;


			if (__isArray(layout))
			{
				for (var i=0; i<layout.length; i++)
				{
					switch (layout[i])
					{
						case 'align:center':
							container.setStyle({textAlign: 'center'});
						break;

						case 'align:right':
							container.setStyle({textAlign: 'right'});
						break;

						case '#pagestat':
							if ('undefined' === typeof theList.elements.pageStat)
							{
								theList.elements.pageStat = new Element('span',{ id: theList.id+'-pagestat'})
									.addClassName(theList.style.pageStat);
								container.insert(theList.elements.pageStat);
							}
						break;


						case '#first':
							if ('undefined' === typeof theList.elements.first)
							{
								theList.elements.first = new Element('a', { id: theList.id+'-first', disabled:true })
									.addClassName(theList.style.button+' '+theList.style.first)
									.update(theList.iconsOnly ? '<span/>' : '<span>First</span>')
									.observe('click', function() {
										if (!this.readAttribute('disabled')) {
											theList.firstPage();
										}
										return false;
									});
								container.insert(theList.elements.first);
							}
						break;
						case '#prev':
							if ('undefined' === typeof theList.elements.prev)
							{
								theList.elements.prev = new Element('a', { id: theList.id+'-prev', disabled:true })
									.addClassName(theList.style.button+' '+theList.style.prev)
									.update(theList.iconsOnly ? '<span/>' : '<span>Prev</span>')
									.observe('click', function() {
										if (!this.readAttribute('disabled')) {
											theList.previousPage();
										}
										return false;
									});
								container.insert(theList.elements.prev);
							}
						break;

						case '#next':
							if ('undefined' === typeof theList.elements.next)
							{
								theList.elements.next = new Element('a', { id: theList.id+'-next', disabled:true })
									.addClassName(theList.style.button+' '+theList.style.next)
									.update(theList.iconsOnly ? '<span/>' : '<span>Next</span>')
									.observe('click', function() {
										if (!this.readAttribute('disabled')) {
											theList.nextPage();
										}
										return false;
									});
								container.insert(theList.elements.next);
							}
						break;

						case '#last':
							if ('undefined' === typeof theList.elements.last)
							{
								theList.elements.last = new Element('a', { id: theList.id+'-last', disabled:true })
									.addClassName(theList.style.button+' '+theList.style.last)
									.update(theList.iconsOnly ? '<span/>' : '<span>Last</span>')
									.observe('click', function() {
										if (!this.readAttribute('disabled')) {
											theList.lastPage();
										}
										return false;
									});
								container.insert(theList.elements.last);
							}
						break;


						case '#refresh':
							if ('undefined' === typeof theList.elements.refresh)
							{
								theList.elements.refresh = new Element('a', {id: theList.id+'-refresh', disabled:false})
									.addClassName(theList.style.button+' '+theList.style.refresh)
									.update(theList.iconsOnly ? '<span/>' : '<span>Refresh</span>')
									.observe('click', function() {
										if (!this.readAttribute('disabled')) {
											theList.refresh();
										}
										return false;
									});
								container.insert(theList.elements.refresh);
							}
						break;

						case '#thead':
							if ('undefined' === typeof theList.elements.header)
							{
								theList.elements.header = new Element('div', {id: theList.id+'-header'}).addClassName(theList.style.header);
								theList.elements.header.wrapper = new Element('div', {id: theList.id+'-headerWrapper'}).addClassName(theList.style.wrapper);
								theList.elements.header.update( theList.elements.header.wrapper);

								container.insert(theList.elements.header);
							}
						break;


						case '#tfoot':
							if ('undefined' === typeof theList.elements.footer)
							{
								theList.elements.footer = new Element('div', {id: theList.id+'-footer'}).addClassName(theList.style.footer);
								theList.elements.footer.wrapper = new Element('div', {id: theList.id+'-footerWrapper'}).addClassName(theList.style.wrapper);
								theList.elements.footer.update( theList.elements.footer.wrapper);
								container.insert(theList.elements.footer);
							}
						break;


						case '#tbody':
							if ('undefined' === typeof theList.elements.content)
							{
								theList.elements.content = new Element('div', {id: theList.id+'-content'})
									.addClassName(theList.style.content)
									.observe('scroll', function(e) {
										if ('undefined' !== typeof theList.elements.header)
										{
											this.elements.header.scrollLeft = this.elements.content.scrollLeft;
										}
										if ('undefined' !== typeof theList.elements.footer)
										{
											this.elements.footer.scrollLeft = this.elements.content.scrollLeft;
										}
									}.bindAsEventListener(theList));

								theList.elements.content.wrapper = new Element('div', {id: theList.id+'-contentWrapper'})
									.addClassName(theList.style.wrapper);
								theList.elements.content.insert( theList.elements.content.wrapper );

								container.insert(theList.elements.content);

								theList.elements.loader = new Element('div', {id: theList.id+'-loader'})
									.addClassName(theList.style.loader)
									.setStyle({
										padding: 0,
										border: 0,
										width: '100%',
										zIndex: 999,
										position: 'absolute'
									})
									.hide();
								container.insert( theList.elements.loader );
							}
						break;


						default:
							container.insert(layout[i]);
						break;
					}
				} // for-do loop
			}
			else {
				// Not an array >:-O
				// alert(typeof layout)
			}

			return container;
		}



		for (var i=0; i<theList.layout.length; i++)
		{
			var wrapper = new Element('div').addClassName(theList.style.wrapper);
			theList.elements.container.insert( __createToolbar().update(__layout( wrapper, theList.layout[i])) );
		}


//		theList.elements.toolbars[0] = new Element('div', {id: theList.id+'-toolbar0'}).addClassName(theList.style.toolbar);
//		theList.elements.container.insert(theList.elements.toolbars[0]);



//		theList.elements.navigator = new Element('div', {id: theList.id+'-navigator'}).addClassName(theList.style.navigator);

//		theList.elements.pageStat = new Element('span',{ id: theList.id+'-pagestat'})
//			.addClassName(theList.style.pageStat);

//		theList.elements.first = new Element('div', { id: theList.id+'-first', disabled:(theList.currentPage<=1) })
//			.addClassName(theList.style.button+' '+theList.style.first)
//			.update(theList.iconsOnly ? '&nbsp;' : 'First')
//			.observe('click', function() {
//				if (!this.readAttribute('disabled')) {
//					theList.firstPage();
//				}
//			});

//		theList.elements.prev = new Element('div', { id: theList.id+'-prev', disabled:(theList.currentPage<=1) })
//			.addClassName(theList.style.button+' '+theList.style.prev)
//			.update(theList.iconsOnly ? '&nbsp;' : 'Prev')
//			.observe('click', function() {
//				if (!this.readAttribute('disabled')) {
//					theList.previousPage();
//				}
//			});

//		theList.elements.next = new Element('div', { id: theList.id+'-next', disabled:(theList.currentPage>=theList.totalPages) })
//			.addClassName(theList.style.button+' '+theList.style.next)
//			.update(theList.iconsOnly ? '&nbsp;' : 'Next')
//			.observe('click', function() {
//				if (!this.readAttribute('disabled')) {
//					theList.nextPage();
//				}
//			});

//		theList.elements.last = new Element('div', { id: theList.id+'-last', disabled:(theList.currentPage>=theList.totalPages) })
//			.addClassName(theList.style.button+' '+theList.style.last)
//			.update(theList.iconsOnly ? '&nbsp;' : 'Last')
//			.observe('click', function() {
//				if (!this.readAttribute('disabled')) {
//					theList.lastPage();
//				}
//			});

//		theList.elements.refresh = new Element('div', {id: theList.id+'-refresh', disabled:false})
//			.addClassName(theList.style.button+' '+theList.style.refresh)
//			.update(theList.iconsOnly ? '&nbsp;' : 'Refresh')
//			.observe('click', function() {
//				if (!this.readAttribute('disabled')) {
//					theList.refresh();
//				}
//			});

//		if (!theList.enablePagination) {
//			theList.elements.toolbars[0].hide();
//		}

//		theList.elements.toolbars[0].insert(
//			theList.elements.navigator.insert(theList.elements.first)
//				.insert(theList.elements.prev)
//				.insert(theList.elements.pageStat)
//				.insert(theList.elements.next)
//				.insert(theList.elements.last)
//				.insert(theList.elements.refresh)
//		);

		// render headers
//		theList.elements.header = new Element('div', {id: theList.id+'-header'}).addClassName(theList.style.header);
//		theList.elements.header.wrapper = new Element('div', {id: theList.id+'-headerWrapper'}).addClassName(theList.style.wrapper);
//		theList.elements.header.update( theList.elements.header.wrapper);

//		theList.elements.footer = new Element('div', {id: theList.id+'-footer'}).addClassName(theList.style.footer);
//		theList.elements.footer.wrapper = new Element('div', {id: theList.id+'-footerWrapper'}).addClassName(theList.style.wrapper);
//		theList.elements.footer.update( theList.elements.footer.wrapper);

//		if (!theList.showFooter) {
//			theList.elements.footer.hide();
//		}
//		theList.elements.container.insert(theList.elements.header);
		// normalize columnModel

		for (i=0;i<theList.columnModel.length;i++) {
			theList.columnModel[i] = Object.extend({
				name: i,
				width: 100,
				sorting: SORTING.none,
				sortable: false,
				visible: true,
				highlight: true,
				styles: {},
				css: {},
				events: {}
			}, theList.columnModel[i]);

			// load default events to column
			theList.columnModel[i].events = Object.extend(
				{
					click: Object.extend(
						{column:null, header:null
					}, theList.columnModel[i].events.click),

					mouseover: Object.extend({
						column:null, header:null
					},theList.columnModel[i].events.mouseover),

					mouseout: Object.extend({
						column:null, header:null}
					,theList.columnModel[i].events.mouseout)

				}, theList.columnModel[i].events);

			// also make sure that only only the first sort order specified is active
			var sorted = false;
			if (theList.columnModel[i].sorting != SORTING.none) {
				if (sorted) {
					theList.columnModel[i].sorting = SORTING.none;
				}
				else {
					// first sortable column is the default sorting
					theList.sortColumn = theList.columnModel[i].name;
					theList.sortDirection = theList.columnModel[i].sorting;
					sorted = true;
				}
			}
		}

		// render data content
//		theList.elements.content = new Element('div', {id: theList.id+'-content'})
//			.addClassName(theList.style.content)
//			.observe('scroll', function(e) {
//				this.elements.header.scrollLeft = this.elements.content.scrollLeft;
//				this.elements.footer.scrollLeft = this.elements.content.scrollLeft;
//			}.bindAsEventListener(theList));
//		theList.elements.content.wrapper = new Element('div', {id: theList.id+'-contentWrapper'})
//			.addClassName(theList.style.wrapper);
//		theList.elements.content.insert( theList.elements.content.wrapper );
//		theList.elements.container.insert( theList.elements.content );

		// create AJAX loader layer
//		theList.elements.loader = new Element('div', {id: theList.id+'-loader'})
//			.addClassName(theList.style.loader)
//			.setStyle({
//				padding: 0,
//				border: 0,
//				width: '100%',
//				zIndex: 999,
//				position: 'absolute'
//			})
//			.hide();
//		theList.elements.container.insert( theList.elements.loader );
//		theList.elements.container.insert(theList.elements.footer);


		theList.element.insert(theList.elements.container);

		// insert container to list Object
		theList.element.insert(theList.elements.container);

		// fire onCreate hook
		if (theList.onCreate) {
			theList.onCreate.bind(this)();
		}

		theList._updateHeaders();

		if (theList.autoLoad) {
			theList._updateData();
			theList.refresh();
		}
		else {
			theList.status = STATUS.isReady;
		}

//		Sortable.create(theList.id+'-trHeader', {
//			elements: theList.elements.header.select('td'),
//			overlap: 'horizontal',
//			constraint: 'horizontal',
//			ghosting: true,
//			containment: [theList.elements.header.wrapper],
//			dragOnEmpty: true,
//		});
		obj.list = theList;
	};

	return {
		STATUS: STATUS,
		SORTING: SORTING,
		create: create,
	};

}();