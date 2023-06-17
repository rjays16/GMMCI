/*
	FOR Draft Printing Data in Applet
	Added code by angelo m. 08.04.2010
*/
//-- start --

var PrintObject=new PrintAttrib();
var jsonPrintAttrib;
PrintObject.arrData=new Array();

//attributes of the PrinterJob and Properties
function PrintAttrib(){
	var printerType;
	var printerPort;
	var rows;
	var cols;
	var fontName;
	var condensed;
	var bold;
	var arrData=new Array();
}

//invoke the function for adding the data to print and its location X,Y
function addDataPrint(stringData,setX,setY){
	var i=PrintObject.arrData.length;
	PrintObject.arrData[i]=new DataPrint();
	PrintObject.arrData[i].stringData=stringData;
	PrintObject.arrData[i].setX=setX;
	PrintObject.arrData[i].setY=setY;
}

//attributes of data print
function DataPrint(){
	var stringData;
	var setX;
	var setY;
}

//invoke to convert the JSON object to JSONString pass to applet
function toJsonStringify(){
	jsonPrintAttrib=JSON.stringify(PrintObject);
	return jsonPrintAttrib;
}

//-- end --