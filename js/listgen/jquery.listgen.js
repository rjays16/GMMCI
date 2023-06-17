/**
 * [ description]
 *
 *
 */
(function($){

	var STATUS = {
		isInitializing: 0,
		isReady: 1,
		isLoading: 2
	};

	var listgenVersion = '1.0';
	var listgenDefaultOptions = {

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
		paginate: true,
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
		cssClasses: {
			list: 'lg-list',
			button: 'lg-button',
			toolbar: 'lg-toolbar',
			'navigator': 'lg-nav',
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
		create: null, // fired when the list is initialized
		sort: null, // fired when column header is clicked to change sorting
		before: null, // fired before AJAX request is sent
		error: null, // fired on AJAX request error
		success: null, // fired if AJAX request is sccessful
		complete: null // fired after AJAX request is completed
	};


	/* column Model specification */
	/*
	columns = [
		0: { // <-- column index
			name: '', default = ''
			label: 'Column label', // default = ''
			width: 100, // default=auto
			sort: 'asc'|'desc'|null, // defaults=null
			isSortable: true, // default=false
			isVisible: true, // default=true

			//  the default renderer processes data as raw text, this can be overwritten
			// if complex data is passed, and needs to be processed first.
			render: function(data, rowIndex, columnName) {
				return data;
			}
		}
	];
	*/

	$.fn.listGen = function(options) {
		var $this = $(this).first(),
			data = $this.data('listgen');
		if (data instanceof ListGen) {
			return data;
		} else {
			return new ListGen(this, options);
		}
	};

	function ListGen(obj, options) {
		var that = this;

		// obj is the id or the DOM object of the container div
		var target = $(obj).first();
		if (!target.size() || target.data('listgen')) {
			throw "Cannot reinitialize an active ListGen instance";
		}

		target.data('listgen', this);

		options = $.extend(listgenDefaultOptions, options);

		this._internals = {
			effectsFinished: true,
			queue:[]
		};
		this.version = listgenVersion;
		this.target = target;
		this.status = STATUS.isInitializing;
		this._options = options;
		this.params = this._options.params;

		if ('string' !== typeof this._options.width) {
			this.width = this._options.width.toString();
		}

		// compute Page stats
		if (this._options.data) {
			this.rows = this._options.data.length;
		} else {
			this.rows = 0;
		}

		this.components = {};
		this.components.wrapper = $('<div/>', {
			id: this.id + '-container',
		}).addClass(this._options.cssClasses.list);

		var parentWidth = this.target.width();
		if (this._options.width.substr(-1) == '%') {
			var pct = this._options.width.slice(0,-1)/100;
			this.components.wrapper.width(parentWidth * pct);
		} else {
			if (this._options.width!='auto') {
				this.components.wrapper.width (this._options.width);
			} else {
				var totalWidth = 0;
				for (i=0;i<this._options.columns.length;i++) {
					var cm = this._options.columns[i];
					if ("string" !== typeof cm.width) {
						cm.width = cm.width.toString();
					}

					if (cm.width.substr(-1) == '%') {
						w = parseFloat(parentWidth*(cm.width.slice(0,-1)/100));
					} else {
						w = parseFloat(cm.width);
					}
					totalWidth += w;
				}
				this.components.wrapper.width(totalWidth+2);
			}
		}

		this.components.toolbars = [];

		if (this._options.layout.isArray()) {
			for (var j=0; j<this._options.layout.length; j++) {

				// create layout
				var toolbar = $('<div/>')
					.addClass(this._options.cssClasses.toolbar);

				var wrapper = $('</div>')
					.addClass(this._options.cssClasses.wrapper);

				for (var i=0; i<layout.length; i++) {
					switch (layout[i]) {
						case 'align:center':
							wrapper.css({'text-align':'center'});
							break;

						case 'align:right':
							wrapper.css({'text-align':'right'});
							break;

						case '#pagestat':
							if ('undefined' === typeof this.components.pageStat) {
								this.components.pageStat = $('<span/>',{ id: this.id+'-pagestat'})
									.addClass(this._options.cssClasses.pageStat);
								wrapper.append(this.components.pageStat);
							}
							break;

						case '#first':
							if ('undefined' === typeof this.components.first) {
								this.components.first = $('<button/>', { id: this.id+'-first' })
									.prop('disabled', true)
									.addClass(this._options.cssClasses.button + ' ' + this._options.cssClasses.first)
									.html(this.iconsOnly ? '<span/>' : '<span>First</span>')
									.click(function(e) {
										e.preventDefault();
										if (!this.prop('disabled')) {
											this.firstPage();
										}
									});
								wrapper.append(this.components.first);
							}
							break;
						case '#prev':
							if ('undefined' === typeof this.components.prev) {
								this.components.prev = $('<button/>', { id: this.id+'-prev' })
									.prop('disabled')
									.addClass(this._options.cssClasses.button + ' ' + this._options.cssClasses.first)
									.html(this.iconsOnly ? '<span/>' : '<span>Prev</span>')
									.click(function(e) {
										e.preventDefault();
										if (!this.prop('disabled')) {
											this.previousPage();
										}
									});
								wrapper.append(this.components.prev);
							}
							break;

						case '#next':
							if ('undefined' === typeof this.components.next) {
								this.components.next = $('<button/>', { id: this.id+'-next', disabled: 'disabled' })
									.addClass(this._options.cssClasses.button + ' ' + this._options.cssClasses.next)
									.html(this.iconsOnly ? '<span/>' : '<span>Next</span>')
									.click(function(e) {
										e.preventDefault();
										if (!this.attr('disabled')) {
											this.nextPage();
										}
									});
								wrapper.append(this.components.next);
							}
							break;

						case '#last':
							if ('undefined' === typeof this.components.last) {
								this.components.last = $('<button/>', { id: this.id+'-last'})
									.prop('disabled')
									.addClass(this._options.cssClasses.button + ' ' + this._options.cssClasses.last)
									.html(this.iconsOnly ? '<span/>' : '<span>Last</span>')
									.click(function(e) {
										e.preventDefault();
										if (!this.prop('disabled')) {
											this.lastPage();
										}
									});
								wrapper.append(this.components.last);
							}
							break;


						case '#refresh':
							if ('undefined' === typeof this.components.refresh) {
								this.components.refresh = $('<button/>', { id: this.id+'-refresh' })
									.prop('disabled')
									.addClass(this._options.cssClasses.button + ' ' + this._options.cssClasses.refresh)
									.html(this.iconsOnly ? '<span/>' : '<span>Refresh</span>')
									.click(function(e) {
										e.preventDefault();
										if (!this.prop('disabled')) {
											this.refresh();
										}
									});
								wrapper.append(this.components.refresh);
							}
							break;

						case '#thead':
							if ('undefined' === typeof this.components.header) {
								this.components.header = $('<div/>', {id: this.id+'-header'}).addClass(this._options.cssClasses.header);
								this.components.header.wrapper = $('<div/>', {id: this.id+'-headerWrapper'}).addClass(this._options.cssClasses.wrapper);
								this.components.header.insert( this.components.header.wrapper);
								wrapper.insert(this.components.header);
							}
							break;


						case '#tfoot':
							if ('undefined' === typeof this.components.footer) {
								this.components.footer = $('<div/>', {id: this.id+'-footer'}).addClass(this._options.cssClasses.footer);
								this.components.footer.wrapper = $('<div/>', {id: this.id+'-footerWrapper'}).addClass(this._options.cssClasses.wrapper);
								this.components.footer.insert( this.components.footer.wrapper);
								wrapper.insert(this.components.footer);
							}
							break;


						case '#tbody':
							if ('undefined' === typeof this.components.content) {
								this.components.content = $('<div/>', {id: this.id+'-content'})
									.addClass(this._options.cssClasses.content)
									.scroll(function(e) {
										if ('undefined' !== typeof that.components.header) {
											that.components.header.scrollLeft = that.components.content.scrollLeft;
										}
										if ('undefined' !== typeof that.components.footer) {
											that.components.footer.scrollLeft = that.components.content.scrollLeft;
										}
									});

								this.components.content.wrapper = $('<div/>', {id: this.id+'-contentWrapper'})
									.addClass(this._options.cssClasses.wrapper);
								this.components.content.append( this.components.content.wrapper );

								wrapper.append(this.components.content);

								this.components.loader = $('div', {id: this.id+'-loader'})
									.addClass(this._options.cssClasses.loader)
									.setStyle({
										padding: 0,
										border: 0,
										width: '100%',
										zIndex: 999,
										position: 'absolute'
									})
									.hide();
								wrapper.append( this.components.loader );
							}
							break;

						default:
							wrapper.append($(layout[i]));
							break;
					}
				} // for-do loop
				toolbar.append(wrapper);
				this.components.wrapper.insert(toolbar);
			}
		}

		// normalize columns
		this.columns = this._options.columns;
		for (i=0; i<this.columns.length; i++) {
			this.columns[i] = $.extend({
				name: i,
				width: 100,
				sort: null,
				sortable: false,
				visible: true,
				highlight: true,
				styles: {},
				css: {},
				events: {}
			}, this.columns[i]);

			// load default events to column
			this.columns[i].events = $.extend({
				click: $.extend({
					column: null,
					header: null
				}, this.columns[i].events.click),

				mouseover: $.extend({
					column:null,
					header:null
				}, this.columns[i].events.mouseover),

				mouseout: $.extend({
					column:null,
					header:null
				}, this.columns[i].events.mouseout)
			}, this.columns[i].events);

			// also make sure that only only the first sort order specified is active
			var sorted = false;
			if (this.columns[i].sort !== null) {
				if (sorted) {
					this.columns[i].sort = null;
				} else {
					// first sortable column is the default sorting
					this.sortColumn = this.columns[i].name;
					this.sortDirection = this.columns[i].sort;
					sorted = true;
				}
			}
		}

		this.target.append(this.components.wrapper);

		this._updateHeaders();

		if (this.autoLoad) {
			this._updateData();
			this.refresh();
		} else {
			this.status = STATUS.isReady;
		}

		// fire onCreate hook
		if ('function'===typeof this.create) {
			this.create();
		}
	}

	/**
	 * [enableControls description]
	 * @param  {[type]} enable [description]
	 * @return {[type]}        [description]
	 */
	this.enableControls = function(enable) {
		// disable nav buttons
		this.components.wrapper.find('.'+this._options.cssClasses.button).each(
			function() { $(this).prop('disabled', true) }
		);

		this.components.wrapper.find('.'+this._options.cssClasses.columnHeader).each(function() {
			var $this = $(this);
			var idx = parseInt($this.data('lg-column-index')),
				cm = that.columns[idx];
			if (isNaN(idx)) return false;
			if (cm.sortable) {
				$this.prop('disabled', !enable)
					.data('lg-sort-dir', null);
				$this.children('span:first')
					.removeClass(this._options.cssClasses.sortAsc)
					.removeClass(this._options.cssClasses.sortDesc);
				if (that.sortColumn==cm.name) {
					$this.data('lg-sort-dir', that.sortDirection)
						.children('span:first').addClass(
							that.sortDirection == 'asc' ?
								this._options.cssClasses.sortAsc :
								this._options.cssClasses.sortDesc
						);
				}
			}
		});

		if ('undefined' !== typeof that.components.refresh) {
			that.components.refresh.prop('disabled', !enable);
		}

		if (enable && that.data.length && that.paginate) {
			if ('undefined' !== that.components.first) {
				that.components.first.prop('disabled', that.currentPage<=1);
			}
			if ('undefined' !== that.components.prev) {
				that.components.prev.prop('disabled', that.currentPage<=1);
			}
			if ('undefined' !== that.components.next) {
				that.components.next.prop('disabled', that.currentPage>=that.totalPages);
			}
			if ('undefined' !== that.components.last) {
				that.components.last.prop('disabled', that.currentPage>=this.totalPages);
			}
		}
	};

	/**
	 * [firstPage description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype.firstPage = function() {
		this.refresh(1, this.sortColumn, this.sortDirection);
	};

	/**
	 * [previousPage description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype.previousPage = function() {
		if (this.currentPage > 1) {
			this.refresh(this.currentPage-1, this.sortColumn, this.sortDirection);
		}
	},

	/**
	 * [nextPage description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype.nextPage = function() {
		if (this.currentPage < this.totalPages) {
			this.refresh(this.currentPage+1, this.sortColumn, this.sortDirection);
		}
	};

	/**
	 * [lastPage description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype.lastPage = function() {
		this.refresh(this.totalPages, this.sortColumn, this.sortDirection);
	};

	/**
	 * [sort description]
	 * @param  {[type]} col [description]
	 * @param  {[type]} dir [description]
	 * @return {[type]}     [description]
	 */
	ListGen.prototype.sort = function(col, dir) {
		this.refresh(this.currentPage, col, dir);
	};

	/**
	 * [reload description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype.reload = function() {
		this.refresh(this.currentPage, this.sortColumn, this.sortDirection);
	};

	/**
	 * [refresh description]
	 * @param  {[type]} page [description]
	 * @param  {[type]} sort [description]
	 * @param  {[type]} dir  [description]
	 * @return {[type]}      [description]
	 */
	ListGen.prototype.refresh = function(page, sort, dir) {

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
			var queryObject = $.extend({
				mr: this._options.paginate ? this.maxRows : 0,
				sort: this.sortColumn,
				dir: this.sortDirection,
				page: this._options.paginate ? this.currentPage : 1
			}, this.params);

			$.ajax( this.url, {
				method: this._options.method,
				data: queryObject,
				beforeSend: function() {
					$.proxy(that._options.beforeSend, that)();
				}
			}).fail(function(){
				that.total = 0;
				that.totalPages = 1;
				that.data = [];
				that._updateData('Error occurred while fetching data...');

				// invoke onError callback
				if (that.error) {
					$.proxy(that.error, that)();
				}
			}).success(function(data) {

				try {
					that.response = $.parseJSON(data);
				} catch (e) {
					that.response = null;
				}

				if (that.response) {
					if (that.paginate) {
						if (that.response.currentPage)
							that.currentPage = parseInt(that.response.currentPage);

						that.total = parseInt(that.response.total);
						if (that.paginate) {
							if (isNaN(that.total)) {
								that.total = 0;
								that.totalPages = 1;
							}
							else {
								that.totalPages = Math.ceil(that.total/that.maxRows);
							}
						} else {
							that.totalPages = 1;
						}
					}

					that.data = that.response.data;
					that._updateData();
					that._stopLoading();

					// invoke onSuccess callback
					if (that.success) {
						$.proxy(that.success,that)();
					}
				} else {
					that.total = 0;
					that.totalPages = 1;
					that.data = [];
					that._updateData(data);

					that._stopLoading();
					// invoke onError callback
					if (that.error) {
						$.proxy(that.error,that)();
					}
				}
			});
		} else {
			// no url
		}
	};


	/**
	 * [_setSortColumn description]
	 * @param {[type]} idx [description]
	 * @param {[type]} dir [description]
	 * @scope private
	 */
	ListGen.prototype._setSortColumn = function(idx, dir) {
		if (!this.columns[idx] || !this.columns[idx].sortable) {
			return false;
		}
		for (i=0;i<this.columns.length;i++) {
			if (i==idx) {
				this.columns[i].sort = dir;
				this.sortColumn = this.columns[i].name;
				this.sortDirection = dir;
			}
			else {
				this.columns[i].sort = SORTING.none;
			}
		}
	};


	/**
	 * [_stopLoading description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype._stopLoading = function() {
		if (!this._internals.effectsFinished) {
			this._internals.queue.push(this._stopLoading)
			return false;
		}
		if (this.status != STATUS.isLoading) {
			return false;
		}
		this._internals.effectsFinished = false;

		if (this._options.showEffects) {
			if ('undefined' !== this.components.content) {
				this.components.content.show();
			}
			this.components.loader.clonePosition(this.components.content);
			this.components.loader.effect({
				effect: this._options.effect ? this._options.effect : 'fade',
				duration:'fast',
				complete: function() {
					that.enableControls(true);
					that.status = STATUS.isReady;
					that._internals.effectsFinished = true;
					if (that._internals.queue) {
						var processQueue = that._internals.queue.shift();
						if ('function' === typeof processQueue) {
							$.proxy(processQueue, that)();
						}
					}
				}
			});
		} else {
			this.components.loader.hide();
			if ('undefined' !== this.components.content) {
				this.components.content.show();
			}
			this.enableControls(true);
			this.status = STATUS.isReady;
			this._internals.effectsFinished = true;
		}
	};

	/**
	 * [_startLoading description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype._startLoading = function() {
		if (!this._internals.effectsFinished) {
			this._internals.queue.push($.proxy(this._startLoading, this));
			return false;
		}
		if (this.status == STATUS.isLoading)
			return false;
		this._internals.effectsFinished = false;
		this.status = STATUS.isLoading;
		this.enableControls(false);
		this.components.loader.clonePosition(this.components.content);
		// this.components.loader.setStyle({position:'static'});

		if ('undefined' !== this.components.content) {
			this.components.content.hide();
		}

		this.components.loader
			//.setStyle({position:'static'})
			.show();
		this.components.content.hide();
		this._internals.effectsFinished = true;
	};

	/**
	 * [_updateData description]
	 * @param  {[type]} msg [description]
	 * @return {[type]}     [description]
	 */
	ListGen.prototype._updateData = function(msg) {
		if ('undefined' === typeof this.components.content)
			return;

		this.components.content.wrapper.update();
		var containerWidth = this.components.wrapper.width();

		if (this.data.length) {
			var i, j;
			var tb = $('<table/>', {id: this.id+'-dataTable'}).addClass(this._options.cssClasses.dataTable);
			for (i=0;i<this.data.length;i++) {
				var tr = $('<tr/>', {id: this.id+'-dataRow'+i})
					.addClass(i%2==0 ? this._options.cssClasses.dataRow : this._options.cssClasses.altRow)
					.css({ height: this._options.rowHeight+'px' });
				for (j=0;j<this.columns.length;j++) {
					var cm = this.columns[j];
					var data = this.data[i][cm.name];
					var td = $('<td/>', {id: this.id+'-dataItem'+i+'-'+cm.name})
						.addClass(this._options.cssClasses.dataItem)
						.css( cm.style );

					if (!cm.visible) {
						td.hide();
					}

					if ('undefined' !== typeof(cm.width)) {
						if ('string' != typeof(cm.width)) {
							cm.width = cm.width.toString();
						}

						if (cm.width.substr(-1) == '%') {
							td.width( containerWidth*(cm.width.slice(0,-1)/100) - 2 );
						} else {
							td.width(cm.width);
						}
					}

					var span = $('<span/>');
					if (cm.render) {
						span.html( cm.render(this.data, i, cm.name) )
					} else {
						span.html(data);
					}
					td.append( span );
					tr.append( td );
				}
				tr.append($('<td class=" '+this._options.cssClasses.spacer+'"></td>'));
				tb.insert( tr );
			}
			this.components.content.wrapper.append( tb );

			// update page statistics
			if (this._options.paginate && 'undefined'!==this.components.pageStat) {
				var from = (this.currentPage-1)*this.maxRows+1;
				var to = from+this.data.length-1;
				this.components.pageStat.html(
					this._options.pageStat.replace(/\{from\}/gi, from)
						.replace(/\{to\}/gi, to)
						.replace(/\{total\}/gi, this.total)
						.replace(/\{page\}/gi, this.currentPage)
						.replace(/\{pagetotal\}/gi, this.totalPages)
				);
			}
		}
		else {
			var tb = $('<table/>', {id: this.id+'-dataTable-empty'}).addClass(this._options.cssClasses.dataTable);
			var tr = $('<tr/>', {id: this.id+'-dataRow-empty'}).addClass(this._options.cssClasses.dataRow);
			var td = $('<td/>', {id: this.id+'-dataItem-empty'}).addClass(this._options.cssClasses.dataItem)
				.html(this.status==STATUS.isInitializing ? this._options.initialMessage :
					(msg ? msg : this._options.emptyMessage));
			this.components.content.wrapper.append( tb.append( tr.appened( td ) ) );
			if ('undefined'!==this.components.pageStat)
			{
				this.components.pageStat.update('');
			}
		}

		if (this._options.width !== 'auto') {
			this.components.content.css({
				width: this._options.width+'px',
				overflowX: 'auto'
			});
		} else {
			this.components.content.css({
				overflowX: 'auto'
			});
		}

		if (this._options.height !== 'auto') {
			this.components.content.css({
				height: this._options.height+'px',
				overflowY: 'auto'
			});
		} else {
			this.components.content.css({
				overflowY: 'auto'
			});
		}

	};

	function _handleColumnHeaderSorting(e) {
		var $this = $(this);
		var col=$this.data('lg-column-name'),
			dir=parseInt($this.data('lg-sort-dir')),
			newDir='asc';
		if (dir =='asc') {
			newDir='desc';
		}
		if (!$this.data('lg-sortable')) {
			that.sort(col, newDir);
		}
	}

	/**
	 * [_updateHeaders description]
	 * @return {[type]} [description]
	 */
	ListGen.prototype._updateHeaders = function() {
		if ('undefined' === typeof this.components.header &&
			'undefined' === typeof this.components.footer) {
			return;
		}

		var containerWidth = this.components.wrapper.width();

		if ('undefined' !== typeof this.columns) {
			var totalWidth = 0;
			var sorted=false;
			var hTb, fTb, hTr, fTr;

			if ('undefined' !== typeof this.components.header) {
				hTb = $('<table/>', {id: this.id+'-tableHeader'});
				hTr = $('<tr/>', {id: this.id+'-trHeader'});
			}

			if ('undefined' !== typeof this.components.footer) {
				fTb = $('<table/>', {id: this.id+'-tableFooter'});
				fTr = $('<tr/>', {id: this.id+'-trFooter'});
			}

			for (i=0;i<this.columns.length;i++) {

				var hTh, hSpan, fTh, fSpan;
				var cm = this.columns[i];

				hTh = $('<td/>', {id: this.id+'-columnHeader-'+i})
					.addClass(this._options.cssClasses.columnHeader)
					.data({
						'lg-column-index': i,
						'lg-column-name': cm.name,
						'lg-sort-dir': cm.sort
					});

				if ('undefined' !== typeof(cm.width)) {
					if ('string' != typeof(cm.width)) {
						cm.width = cm.width.toString();
					}
					if (cm.width.substr(-1) == '%') {
						w = containerWidth*(cm.width.slice(0,-1)/100);
						hTh.width(w);
						totalWidth += w;
					} else {
						hTh.width(cm.width);
						totalWidth += (cm.width-0.0);
					}
				}

				if (!cm.visible) {
					hTh.hide();
				}

				if (cm.sortable) {
					hTh.data('lg-sortable', true)
						.addClass(this._options.cssClasses.sortable);
				}

				// Header label
				hSpan = $('<span/>').html(cm.label);
				if (cm.sort !== null) {
					if (cm.sort === 'asc') {
						hSpan.addClass(this._options.cssClasses.sortAsc);
					} else if (cm.sort === 'desc') {
						hSpan.addClass(this._options.cssClasses.sortDesc);
					}
				}

				if ('undefined' !== typeof this.components.footer) {
					fTh = hTh.clone(true)
						.attr({id:this.id+'-columnFooter-'+i});
					fSpan = hSpan.clone(true);
				}

				// set event observers for column Headers/Footers
				if (cm.sortable) {


					if ('undefined' !== typeof this.components.header) {
						hTh.click(_handleColumnHeaderSorting);
					}

					if ('undefined' !== typeof this.components.footer) {
						fTh.click(_handleColumnHeaderSorting);
					}
				}

				if ('undefined' !== typeof this.components.header) {
					hTh.append(hSpan);
					hTr.append(hTh);
				}

				if ('undefined' !== typeof this.components.footer) {
					fTh.append(fSpan);
					fTr.append(fTh);
				}
			}

			var spacerWidth = 20;
			if (totalWidth < containerWidth) {
				spacerWidth=(containerWidth-totalWidth);
			}
			if ('undefined' !== typeof this.components.header)
			{
				//hTr.insert('<td class="'+this._options.cssClasses.columnHeader+' '+this._options.cssClasses.spacer+'" '+(this.width == 'auto' ? 'style="width:20px"' : '') + '></td>');
				hTr.append($('<td class="'+this._options.cssClasses.columnHeader+' '+this._options.cssClasses.spacer+'"></td>').width(spacerWidth));
				hTb.append(hTr);
				this.components.header.wrapper.append( hTb );
			}

			if ('undefined' !== typeof this.components.footer)
			{
				fTr.append($('<td class="'+this._options.cssClasses.columnHeader+' '+this._options.cssClasses.spacer+'" style="width:'+spacerWidth+'"></td>').width(spacerWidth));
				fTb.append(fTr);
				this.components.footer.wrapper.append( fTb );
			}
		}
	};

})(jQuery);