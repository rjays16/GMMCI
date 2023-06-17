<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Query a JavaScript Array for In-memory Data</title>

</head>

<style type="text/css">
body {
	margin:0;
	padding:0;
}

/* custom styles for multiple stacked instances */
/* custom styles for scrolling container */
#statesautocomplete2 {
    width:15em; /* set width here */
    padding-bottom:2em;
    height:12em; /* define height for container to appear inline */
	 z-index:9000; /* z-index needed on top instance for ie & sf absolute inside relative issue */
}

#statesinput2 {
    _position:absolute; /* abs pos needed for ie quirks */
}

/* custom styles for scrolling container */
#statescontainer2 .yui-ac-content {
    max-height:11em;overflow:auto;overflow-x:hidden; /* scrolling */
    _height:11em;  /* ie6 */ 
	  position:absolute
}


</style>

<!--
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/animation/animation-min.js"></script>

<script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/autocomplete/autocomplete-min.js"></script>
-->
<link rel="stylesheet" type="text/css" href="../../../js/yui-2.3/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="../../../js/yui-2.3/build/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="../../../js/yui-2.3/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../../../js/yui-2.3/build/animation/animation-min.js"></script>

<script type="text/javascript" src="../../../js/yui-2.3/build/autocomplete/autocomplete-min.js"></script>

<body class=" yui-skin-sam">

<h1>Query a JavaScript Array for In-memory Data</h1>

<!--BEGIN SOURCE CODE FOR EXAMPLE =============================== -->
<!--
<h3>Find a state:</h3>
<div id="statesautocomplete">
	<input id="statesinput" type="text">

	<div id="statescontainer"></div>
</div>
-->
<h3>Find an area code</h3>
<div id="statesautocomplete2">
	<input id="statesinput2" type="text">
	<div id="statescontainer2"></div>
</div>
<script type="text/javascript">
function getStates(keyword) {
//alert('sQuery = '+sQuery);
    aResults = [];
    if (keyword && keyword.length > 0) {
        var charKey = keyword.substring(0, 1).toLowerCase();
        //dataset is jason format
		  var oResponse = dataset[charKey];
        if (oResponse) {
            for (var i = oResponse.length - 1; i >= 0; i--) {
                var sKey = oResponse[i].STATE;
                var sKeyIndex = encodeURI(sKey.toLowerCase()).indexOf(keyword.toLowerCase());
                if (sKeyIndex === 0) {
                    aResults.unshift([sKey, oResponse[i].ABBR]);
                }
            }
            return aResults;
        }
    } else {
        for (var letter in dataset) {
            var oResponse = dataset[letter];
            for (var i = oResponse.length - 1; i >= 0; i--) {
                aResults.push([oResponse[i].STATE, oResponse[i].ABBR]);
            }
        }
        return aResults;
    }
}

var dataset = {
    'a' : [{"STATE" : "Alabama", "ABBR" : "AL"},
		{"STATE" : "Alaska", "ABBR" : "AK"},
		{"STATE" : "Arizona", "ABBR" : "AZ"},
		{"STATE" : "Arkansas", "ABBR" : "AR"}],
	'b' : [ ],
	'c' : [{"STATE" : "California", "ABBR" : "CA"},
		{"STATE" : "Colorado", "ABBR" : "CO"},
		{"STATE" : "Colorado1", "ABBR" : "CO1"},
		{"STATE" : "Colorado2", "ABBR" : "CO2"},
		{"STATE" : "Colorado3", "ABBR" : "CO3"},
		{"STATE" : "Colorado4", "ABBR" : "CO4"},
		{"STATE" : "Colorado5", "ABBR" : "CO5"},
		{"STATE" : "Colorado6", "ABBR" : "CO6"},
		{"STATE" : "Colorado7", "ABBR" : "CO7"},
		{"STATE" : "Colorado8", "ABBR" : "CO8"},
		{"STATE" : "Colorado9", "ABBR" : "CO9"},
		{"STATE" : "Colorado10", "ABBR" : "CO10"},
		{"STATE" : "Connecticut", "ABBR" : "CT"}],
 
};


YAHOO.example.ACJSFunction = new function(){
    // Instantiate JS Function DataSource
    this.oACDS = new YAHOO.widget.DS_JSFunction(getStates);
    this.oACDS.maxCacheEntries = 0;

    // Instantiate AutoComplete
    this.oAutoComp = new YAHOO.widget.AutoComplete('statesinput2','statescontainer2', this.oACDS);
    this.oAutoComp.alwaysShowContainer = true;
    this.oAutoComp.minQueryLength = 0;
    this.oAutoComp.maxResultsDisplayed = 50;
    this.oAutoComp.formatResult = function(oResultItem, keyword) {
        var sMarkup = oResultItem[0] + " (" + oResultItem[1] + ")";
        return (sMarkup);
    };

    // Show custom message if no results found
    this.myOnDataReturn = function(sType, aArgs) {
        var oAutoComp = aArgs[0];
        var keyword = aArgs[1];
        var aResults = aArgs[2];

        if(aResults.length == 0) {
            oAutoComp.setBody("<div id=\"statescontainerdefault2\">No matching results</div>");
        }
    };
    this.oAutoComp.dataReturnEvent.subscribe(this.myOnDataReturn);

    // Preload content in the container
    this.oAutoComp.sendQuery("");
};
</script>
</body>
</html>
