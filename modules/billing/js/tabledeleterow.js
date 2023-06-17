// tabledeleterow.js version 1.2 2006-02-21
// mredkj.com

// CONFIG notes. Below are some comments that point to where this script can be customized.
// Note: Make sure to include a <tbody></tbody> in your table's HTML

var INPUT_NAME_PREFIX = 'inputName'; // this is being set via script
var RADIO_NAME = 'totallyrad'; // this is being set via script
var TABLE_NAME = 'tblSample'; // this should be named in the HTML
var ROW_BASE = 1; // first number (for display)
var hasLoaded = false;

window.onload=fillInRows;

function fillInRows()
{
	hasLoaded = true;
	addRowToTable();
	addRowToTable();
}

// CONFIG:
// myRowObject is an object for storing information about the table rows
function myRowObject(one, two, three, four)
{
	this.one = one; // text object
	this.two = two; // input text object
	this.three = three; // input checkbox object
	this.four = four; // input radio object
}

/*
 * insertRowToTable
 * Insert and reorder
 */
function insertRowToTable()
{
	if (hasLoaded) {
		var tbl = document.getElementById(TABLE_NAME);
		var rowToInsertAt = tbl.tBodies[0].rows.length;
		for (var i=0; i<tbl.tBodies[0].rows.length; i++) {
			if (tbl.tBodies[0].rows[i].myRow && tbl.tBodies[0].rows[i].myRow.four.getAttribute('type') == 'radio' && tbl.tBodies[0].rows[i].myRow.four.checked) {
				rowToInsertAt = i;
				break;
			}
		}
		addRowToTable(rowToInsertAt);
		reorderRows(tbl, rowToInsertAt);
	}
}

/*
 * addRowToTable
 * Inserts at row 'num', or appends to the end if no arguments are passed in. Don't pass in empty strings.
 */
function addRowToTable(num)
{
	if (hasLoaded) {
		var tbl = document.getElementById(TABLE_NAME);
		var nextRow = tbl.tBodies[0].rows.length;
		var iteration = nextRow + ROW_BASE;
		if (num == null) { 
			num = nextRow;
		} else {
			iteration = num + ROW_BASE;
		}
		
		// add the row
		var row = tbl.tBodies[0].insertRow(num);
		
		// CONFIG: requires classes named classy0 and classy1
		row.className = 'classy' + (iteration % 2);
	
		// CONFIG: This whole section can be configured
		
		// cell 0 - text
		/*var cell0 = row.insertCell(0);
		var textNode = document.createTextNode(iteration);
		cell0.appendChild(textNode);
		*/
		// cell 1 - input text
		
		var cell1 = row.insertCell(0);
		var txtInp = document.createElement('input');
		txtInp.setAttribute('type', 'text');
		cell1.appendChild(txtInp);
		
		
		var cell1 = row.insertCell(1);
		var txtInp = document.createElement('input');
		txtInp.setAttribute('type', 'text');
		cell1.appendChild(txtInp);
		
		
		var cell1 = row.insertCell(2);
		var txtInp = document.createElement('input');
		txtInp.setAttribute('type', 'text');
		cell1.appendChild(txtInp);
		
		var cell1 = row.insertCell(3);
		var txtInp = document.createElement('input');
		txtInp.setAttribute('type', 'text');
	/*	txtInp.setAttribute('name', INPUT_NAME_PREFIX + iteration);
		txtInp.setAttribute('size', '40');
		txtInp.setAttribute('value', iteration); // iteration included for debug purposes*/
		cell1.appendChild(txtInp);
		
			
		
	/*	// cell 2 - input button
		var cell2 = row.insertCell(2);
		var btnEl = document.createElement('input');
		btnEl.setAttribute('type', 'button');
		btnEl.setAttribute('value', 'Delete');
		btnEl.onclick = function () {deleteCurrentRow(this)};
		cell2.appendChild(btnEl);
		
		// cell 3 - input checkbox
		var cell3 = row.insertCell(3);
		var cbEl = document.createElement('input');
		cbEl.setAttribute('type', 'checkbox');
		cell3.appendChild(cbEl);
		
		
		
		// cell 4 - input radio
		var cell4 = row.insertCell(4);
		var raEl;
		try {
			raEl = document.createElement('<input type="radio" name="' + RADIO_NAME + '" value="' + iteration + '">');
			var failIfNotIE = raEl.name.length;
		} catch(ex) {
			raEl = document.createElement('input');
			raEl.setAttribute('type', 'radio');
			raEl.setAttribute('name', RADIO_NAME);
			raEl.setAttribute('value', iteration);
		}
		
		*/
		cell4.appendChild(raEl);
		
		// Pass in the elements you want to reference later
		// Store the myRow object in each row
		row.myRow = new myRowObject(textNode, txtInp, cbEl, raEl);
	}
}

// CONFIG: this entire function is affected by myRowObject settings
// If there isn't a checkbox in your row, then this functio