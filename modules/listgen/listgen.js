function _lgsleep(millisecondi) {
	var now = new Date();
	var exitTime = now.getTime() + millisecondi;
	while(true)	{
		now = new Date();
		if(now.getTime() > exitTime) return;
	}
}

function _lgformatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function LGList(id, params) {

	this.SET_PAGE			=	0;
	this.FIRST_PAGE		=	1;
	this.PREV_PAGE		=	2;
	this.NEXT_PAGE		=	3;
	this.LAST_PAGE		=	4;

	this.id 					= id;
	this.currentPage 	= 0;
	this.lastPage 		= 0;
	this.maxRows 			= 0;
	this.listSize 		= 0;
	this.rowCount			= 0;

	this.params				= params;
	this.isLoading		= false;
}

LGList.prototype.disableNav = function() {

	with ($('page-first-'+this.id)) {
		className = 'lgInactive'
		setAttribute('onclick','')
	}
	with ($('page-prev-'+this.id)) {
		className = 'lgInactive'
		setAttribute('onclick','')
	}
	with ($('page-next-'+this.id)) {
		className = 'lgInactive'
		setAttribute('onclick','')
	}
	with ($('page-last-'+this.id)) {
		className = 'lgInactive'
		setAttribute('onclick','')
	}

}

LGList.prototype.setPagination = function () {
	var current_page, last_page, first, last, total;
	current_page=parseInt(this.currentPage);
	last_page=parseInt(this.lastPage);
	first = (parseInt(this.currentPage)*parseInt(this.maxRows))+1;
	total = parseInt(this.listSize);
	if (current_page==last_page)
		last = total;
	else
		last = (parseInt(this.currentPage)+1)*parseInt(this.maxRows);
	if (parseInt(total)) {
		$("page-message-"+this.id).innerHTML = '<span>Showing '+(_lgformatNumber(first))+'-'+(_lgformatNumber(last))+' of '+(_lgformatNumber(parseInt(total)))+' item(s)</span>'
		$("page-message-"+this.id).removeClassName('lgInactive');
	}
	else
		$("page-message-"+this.id).innerHTML = ''
	$("page-first-"+this.id).className = (current_page>0&&last_page>0) ? "lgActive" : "lgInactive";
	$("page-prev-"+this.id).className = (current_page>0&&last_page>0) ? "lgActive" : "lgInactive";
	$("page-next-"+this.id).className = (current_page<last_page) ? "lgActive" : "lgInactive";
	$("page-last-"+this.id).className = (current_page<last_page) ? "lgActive" : "lgInactive";
	$("page-refresh-"+this.id).className = "lgActive";
}

LGList.prototype.disablePagination = function () {
	$("page-message-"+this.id).addClassName('lgInactive');
	$("page-first-"+this.id).className = "lgInactive";
	$("page-prev-"+this.id).className = "lgInactive";
	$("page-next-"+this.id).className = "lgInactive";
	$("page-last-"+this.id).className = "lgInactive";
	$("page-refresh-"+this.id).className = "lgInactive";
}

LGList.prototype.jump = function(jumptype, page) {
	switch(jumptype) {
		case this.FIRST_PAGE:
			this.currentPage = 0;
		break;
		case this.PREV_PAGE:
			if (this.currentPage>0) this.currentPage--;
		break;
		case this.NEXT_PAGE:
			if (parseInt(this.currentPage)<parseInt(this.lastPage)) this.currentPage++;
		break;
		case this.LAST_PAGE:
			if (parseInt(this.currentPage)<parseInt(this.lastPage)) this.currentPage = this.lastPage;
		break;
	}
	this.reload();
}

LGList.prototype.enableControls = function(enable) {}

LGList.prototype.showLoading = function(show) {
	if (show) {
		$('list-loader-'+this.id).setStyle(
			{
				height:($('list-body-'+this.id).getHeight()+'px'),
				display:''
			}
		);
		$('list-body-'+this.id).hide();
		$('list-loader-div-'+this.id).clonePosition($('list-loader-'+this.id));
		$('list-loader-div-'+this.id).show();
	}
	else {
		$('list-loader-'+this.id).hide();
		$('list-body-'+this.id).show();
		$('list-loader-div-'+this.id).clonePosition($('list-body-'+this.id));
		$('list-loader-div-'+this.id).show().setOpacity(1);
		if (typeof(Scriptaculous)=='object') {
			$('list-loader-div-'+this.id).fade( {duration:0.8} );
		}
		else {
			$('list-loader-div-'+this.id).hide();
		}
	}
	this.isLoading = show;
}

LGList.prototype.reload = function() {
	this.showLoading(true);
	this.disablePagination();
	this.clear();
	this.fetchData(true);
}

LGList.prototype.fetchData = function() {
	var args;
	xajax.call(this.ajaxFetcher,
		{
			parameters:[this.currentPage, this.maxRows, this.sortOrder, this.fetcherParams],
			context:this
		}
	);
}

LGList.prototype.fetchDone = function() {
	this.render();
	this.showLoading(false);
}

LGList.prototype.render = function() {
	if (parseInt(this.dataSize) > 0) {
		for ( var i=0; i<this.dataSize; i++ ) {
			this.add(this.listData[i]);
		}

		// Update sortable headers
		var obj;
		for (var j=0; j<this.sortOrder.length; j++) {
			$(this.id+'-sortdn-'+j).style.display = 'none';
			$(this.id+'-sortup-'+j).style.display = 'none';
			if (this.sortOrder[j] != null) {
				obj = $(this.id+'-list-header-'+j);
				if (obj) {
					obj.addClassName('clickable');
					obj.setAttribute('onclick', obj.getAttribute('onclickex'));
					obj.setAttribute('onmouseover', obj.getAttribute('onmouseoverex'));
					obj.setAttribute('onmouseout', obj.getAttribute('onmouseoutex'));
					if (this.sortOrder[j] > 0) {
						$(this.id+'-sortup-'+j).style.display = '';
					}
					else if (this.sortOrder[j] < 0) {
						$(this.id+'-sortdn-'+j).style.display = '';
					}
				}
			}
			else {
				obj = $(this.id+'-list-header-'+j);
				if (obj) {
					obj.removeClassName('clickable');
					obj.setAttribute('onclick', '');
					obj.setAttribute('onmouseover', '');
					obj.setAttribute('onmouseout', '');
				}
			}
		}
	}
	else {
		this.currentPage = 0;
		this.lastPage = 0;
		this.listSize = 0;
		this.clear({message:this.emptyMessage});
		// Disable sortable headers
		var obj;
		for (var j=0; j<this.sortOrder.length; j++) {
			$(this.id+'-sortdn-'+j).style.display = 'none';
			$(this.id+'-sortup-'+j).style.display = 'none';
			obj = $(this.id+'-list-header-'+j);
			if (obj) {
				obj.removeClassName('clickable');
				obj.setAttribute('onclick', '');
				obj.setAttribute('onmouseover', '');
				obj.setAttribute('onmouseout', '');
			}
		}
	}
	this.setPagination();
}

LGList.prototype.doneReload = function() {
	//window.setTimeout(this.showLoading.bind(this,false),10);
}

LGList.prototype.add = function(details) {
	alert('This is the default add');
}

LGList.prototype.sort = function(i) {
	for (var j=0; j<this.sortOrder.length; j++) {
		if (j==i) {
			if (this.sortOrder[j] != 0) {
				if (this.sortOrder[j] >= 0) this.sortOrder[j]=-1;
				else this.sortOrder[j]=1;
			}
			else this.sortOrder[j]=1;
		}
		else {
			if (this.sortOrder[j] !== null) this.sortOrder[j]=0;
		}
	}
	this.reload();
}

LGList.prototype.clear = function(options) {
	var list=$(this.id);
	if (list) {
		var dBody=list.select("tbody")[0]
		if (dBody) {
			this.rowCount = 0;
			if (typeof(options)=='object') {
				if (options['message']) {
					dBody.update('<tr><td colspan="'+this.columnCount+'">'+options['message']+'</td></tr>');
				}
				else
					dBody.update('<tr><td colspan="'+this.columnCount+'">'+options.toString()+'</td></tr>');
			}
			else dBody.update("");
			return true;
		}
	}
	return false;
}

LGList.prototype.zebra = function(startIndex) {
	var list=$(this.id);
	if (list) {
		var dBody=list.select("tbody").first();
		if (dBody) {
			var dRows = dBody.select("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "alt"+(i%2+1);
				}
				return true;
			}
		}
	}
	return false;
}

function lgSortMouseOver(obj) {
	if (!$(obj).hasClassName('clickable')) return false;
}

function lgSortMouseOut(obj) {
	if (!$(obj).hasClassName('clickable')) return false;
}