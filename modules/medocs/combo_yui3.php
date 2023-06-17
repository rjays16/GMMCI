<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Use AutoComplete to access flat-file data from a web service</title>

<style type="text/css">
body {
	margin:0;
	padding:0;
}
</style>
<!--
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/animation/animation-min.js"></script>

<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/connection/connection-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/autocomplete/autocomplete-min.js"></script>
-->
<link rel="stylesheet" type="text/css" href="../../../js/yui-2.3/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="../../../js/yui-2.3/build/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="../../../js/yui-2.3/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../../../js/yui-2.3/build/animation/animation-min.js"></script>

<script type="text/javascript" src="../../../js/yui-2.3/build/connection/connection-min.js"></script>
<script type="text/javascript" src="../../../js/yui-2.3/build/autocomplete/autocomplete-min.js"></script>


<!--begin custom header content for this example-->
<style type="text/css">
/* custom styles for multiple stacked instances with custom formatting */
#example0 { z-index:9001; } /* z-index needed on top instances for ie & sf absolute inside relative issue */
#example1 { z-index:9000; } /* z-index needed on top instances for ie & sf absolute inside relative issue */
.autocomplete { padding-bottom:2em;width:40%; }/* set width of widget here*/
.autocomplete .yui-ac-highlight .sample-quantity,
.autocomplete .yui-ac-highlight .sample-result,
.autocomplete .yui-ac-highlight .sample-query { color:#FFF; }
.autocomplete .sample-quantity { float:right; } /* push right */
.autocomplete .sample-result { color:#A4A4A4; }
.autocomplete .sample-query { color:#000; }
</style>


<!--end custom header content for this example-->

</head>

<body class=" yui-skin-sam">

<div id="autocomplete_examples">
   <h2>Sample Search (1 sec query delay):</h2>
	<div id="example0" class="autocomplete">
		<input id="ysearchinput0" type="text">
		<div id="ysearchcontainer0"></div>

	</div>
	<h2>Sample Search (0.2 sec query delay):</h2>
	<div id="example1" class="autocomplete">
		<input id="ysearchinput1" type="text">
		<div id="ysearchcontainer1"></div>
	</div>
	<h2>Sample Search (0 sec query delay):</h2>
	<div id="example2" class="autocomplete">

		<input id="ysearchinput2" type="text">
		<div id="ysearchcontainer2"></div>
	</div>
</div>
	
<script type="text/javascript">
YAHOO.example.ACFlatData = new function(){
    // Define a custom formatter function
    this.fnCustomFormatter = function(oResultItem, sQuery) {
        var sKey = oResultItem[0];
        var nQuantity = oResultItem[1];
        var sKeyQuery = sKey.substr(0, sQuery.length);
        var sKeyRemainder = sKey.substr(sQuery.length);
        var aMarkup = ["<div class='sample-result'><div class='sample-quantity'>",
            nQuantity,
            "</div><span class='sample-query'>",
            sKeyQuery,
            "</span>",
            sKeyRemainder,
            "</div>"];
        return (aMarkup.join(""));
    };

    // Instantiate one XHR DataSource and define schema as an array:
    //     ["Record Delimiter",
    //     "Field Delimiter"]
    this.oACDS = new YAHOO.widget.DS_XHR("assets/php/ysearch_flat.php", ["\n", "\t"]);
    this.oACDS.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
    this.oACDS.maxCacheEntries = 60;
    this.oACDS.queryMatchSubset = true;

    // Instantiate first AutoComplete
    var myInput = document.getElementById('ysearchinput0');
    var myContainer = document.getElementById('ysearchcontainer0');
    this.oAutoComp0 = new YAHOO.widget.AutoComplete(myInput,myContainer,this.oACDS);
    this.oAutoComp0.delimChar = ";";
    this.oAutoComp0.queryDelay = 1;
    this.oAutoComp0.formatResult = this.fnCustomFormatter;

    // Instantiate second AutoComplete
    this.oAutoComp1 = new YAHOO.widget.AutoComplete('ysearchinput1','ysearchcontainer1', this.oACDS);
    this.oAutoComp1.delimChar = ";";
    this.oAutoComp1.formatResult = this.fnCustomFormatter;

    // Instantiate third AutoComplete
    this.oAutoComp2 = new YAHOO.widget.AutoComplete('ysearchinput2','ysearchcontainer2', this.oACDS);
    this.oAutoComp2.queryDelay = 0;
    this.oAutoComp2.delimChar = ";";
    this.oAutoComp2.prehighlightClassName = "yui-ac-prehighlight";
    this.oAutoComp2.formatResult = this.fnCustomFormatter;
};
</script>

</body>
</html>
