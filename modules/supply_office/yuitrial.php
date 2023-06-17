<?php
?>

<html>
<head>
<title> YUI trial </title>

<script type="text/javascript" src="yuiloadtry.js"></script>
<script>
YAHOO.namespace("example.container");

function init() {
    
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };
    var handleSuccess = function(o) {
        var response = o.responseText;
        response = response.split("<!")[0];
        document.getElementById("resp").innerHTML = response;
    };
    var handleFailure = function(o) {
        alert("Submission failed: " + o.status);
    };

    // Instantiate the Dialog
    YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1", 
                            { width : "30em",
                              fixedcenter : true,
                              visible : false, 
                              constraintoviewport : true,
                              buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
                                      { text:"Cancel", handler:handleCancel } ]
                            });

    // Validate the entries in the form to require that both first and last name are entered
    YAHOO.example.container.dialog1.validate = function() {
        var data = this.getData();
        if (data.firstname == "" || data.lastname == "") {
            alert("Please enter your first and last names.");
            return false;
        } else {
            return true;
        }
    };

    // Wire up the success and failure handlers
    YAHOO.example.container.dialog1.callback = { success: handleSuccess,
                             failure: handleFailure };
    
    // Render the Dialog
    YAHOO.example.container.dialog1.render();

    YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.dialog1.show, YAHOO.example.container.dialog1, true);
    YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.dialog1.hide, YAHOO.example.container.dialog1, true);
}

YAHOO.util.Event.onDOMReady(init);
</script>

</head>
<body>

<div>
<button id="show">Show dialog1</button> 
<button id="hide">Hide dialog1</button>
</div>

<div style="visibility: inherit; width: 30em;" class="yui-module yui-overlay yui-panel" id="dialog1">
<div id="dialog1_h" style="cursor: move;" class="hd">Please enter your information</div>
<div class="bd">
<form method="post" action="assets/post.php">
    <label for="firstname">First Name:</label><input name="firstname" type="textbox">
    <label for="lastname">Last Name:</label><input name="lastname" type="textbox">
    <label for="email">E-mail:</label><input name="email" type="textbox"> 

    <label for="state[]">State:</label>
    <select multiple="multiple" name="state[]">
        <option value="California">California</option>
        <option value="New Jersey">New Jersey</option>
        <option value="New York">New York</option>
    </select> 

    <div class="clear"></div>

    <label for="radiobuttons">Radio buttons:</label>
    <input name="radiobuttons[]" value="1" checked="checked" type="radio"> 1
    <input name="radiobuttons[]" value="2" type="radio"> 2
    
    <div class="clear"></div>

    <label for="check">Single checkbox:</label><input name="check" value="1" type="checkbox"> 1
    
    <div class="clear"></div>
        
    <label for="textarea">Text area:</label><textarea name="textarea"></textarea>

    <div class="clear"></div>

    <label for="cbarray">Multi checkbox:</label>
    <input name="cbarray[]" value="1" type="checkbox"> 1
    <input name="cbarray[]" value="2" type="checkbox"> 2
</form>
</div>
</div>

<div>
pugee
</div>

    
    <!--END SOURCE CODE FOR EXAMPLE =============================== -->
    
        
        </div>

</body>
</html>