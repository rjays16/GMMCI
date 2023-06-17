<html>
<head>
<title>Simple ComboBox</title>
</head>
<link rel="stylesheet" type="text/css" media="all" href="../../../js/dojo-0.9.1/dijit/themes/tundra/tundra.css">
<link rel="stylesheet" type="text/css" media="all" href="../../../js/dojo-0.9.1/dojo/resources/dojo.css">
<script type="text/javascript" src="../../../js/dojo-0.9.1/dojo/dojo.js"
        djConfig="parseOnLoad: true"></script>
<!--		  
<script type="text/javascript">
       dojo.require("dojo.parser");
       dojo.require("dijit.form.ComboBox");
		 function setVal1(value) {
           console.debug("Selected "+value);
       }
</script>

<body class="tundra">
        <select name="state1"
                dojoType="dijit.form.ComboBox"
                autocomplete="false"
                value="California"
                onChange="setVal1">
                <option selected="selected">California</option>
                <option >Illinois</option>
                <option >New York</option>
                <option >Texas</option>
					 <option >Illinois1</option>
                <option >New York1</option>
                <option >Texas1</option>
					 <option >Illinois2</option>
                <option >New York2</option>
                <option >Texas2</option>
					 <option >Illinois3</option>
                <option >New York3</option>
                <option >Texas3</option>
					 <option >Illinois4</option>
                <option >New York4</option>
                <option >Texas4</option>
					 <option >Illinois5</option>
                <option >New York5</option>
                <option >Texas5</option>
        </select>
</body>
-->
<!--
      <script type="text/javascript">
        dojo.require("dojo.parser");
        dojo.require("dijit.form.FilteringSelect");
      </script>
  </head>

  <body class="tundra">
    <h2>Auto Completer Combo box</h2>
    <select dojoType="dijit.form.FilteringSelect" name="sname" 
    autocomplete="false" value="Vinod">
      <option value="Vinod">Vinod</option>
      <option value="Vikash" >Vikash</option>
      <option value="Deepak" >Deepak</option>
      <option value="DeepakSir" >Deepak Sir</option>
      <option value="Arun" >Arun</option>
      <option value="Amar" >Amar</option>
      <option value="Aman" >Aman</option>
    </select>
  </body>
 -->
 <script type="text/javascript">
    dojo.require("dojo.data.ItemFileReadStore");
    dojo.require("dijit.form.FilteringSelect");
    dojo.require("dojo.parser");
  </script>

  <script type="text/javascript">
    var majorList = {identifier:"major",items:[{major:"Atl",label:"Atl"},{major:"NYC",label:"Atl"}]};
    var majorStore = new dojo.data.ItemFileReadStore({data: majorList});
    var secondList = {identifier:"name",items:[{major:"Atl",name:"Atlanta"},{major:"Atl",name:"Atlanta2"},{major:"NYC",name:"New York City"}]};
    var secondStore = new dojo.data.ItemFileReadStore({data: secondList});
    function getData(selectedValue){
      secondStore = new dojo.data.ItemFileReadStore({data: secondList});
      secondStore.fetch({
        query: { major: selectedValue },
          onComplete: getOptions,
          onError: errorHandler
        });     
    }

    var getOptions = function( items, request )
      {
        var tmpStore = new dojo.data.ItemFileReadStore({data:{identifier:'name',items:items}});
        dijit.byId("secondBox").store = tmpStore;
        dijit.byId("secondBox").setDisplayedValue('');
      }

    var errorHandler = function(error, request){
      console.debug(error);
    }       
  </script>
               
</head>
<body class="tundra">

                     
     <input dojoType="dijit.form.FilteringSelect"
                store="majorStore"s
                searchAttr="major"
                name="majorsBox"
                onChange="getData"
                id="majorsBox"
                />
               
     <input dojoType="dijit.form.FilteringSelect"
              store="secondStore"
                searchAttr="name"
                name="secondBox"
                id="secondBox"
                />

</body> 
</html>
