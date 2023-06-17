<!-- notification ENTRY BLOCK -->
<!--begin custom header content for this example-->
<style type="text/css">
#notificationAutoComplete {
        width:5em; /* set width here or else widget will expand to fit its container */
        padding-bottom:1.75em;
}

#notificationDescAutoComplete {
        width:33em; /* set width here or else widget will expand to fit its container */
        padding-bottom:1.75em;
}  <!---->

</style>

<div id="notificationSearchTab" style="border:0px solid black; padding:2px; background-color:#FFFFFF; width:100%; position:relative; display:block" align="center">
    <table width="100%" border="0" cellpadding="0" style="width:100%">
        <tr>
            <td width="100%" valign="top">
                <div style="width:99%;height:139px;overflow:hidden;border:1px solid black; margin-left:-4px;">
                <table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:10px">
                        <thead>
                            <tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader" width="50%">
                                <th width="13%" align="center">
                                  <strong>Notification</strong>&nbsp;</th>
                                    <th width="50%" nowrap="nowrap" align="left">
                                         <table width="100%" border=0>

                                            <td width="20%" nowrap="nowrap">Request Date :</td>
                                            <td width="15%" nowrap="nowrap" align="left">
                                                 <div>
                                                    <input type="text" maxlength="10" size="8" id="request_date" value="" name="request_date" onblur="IsValidDate(this,'MM/dd/yyyy');" />
                                                    <img id="request_date_trigger" name="request_date_trigger" height="22" width="26" border="0" align="absmiddle" style="cursor:pointer" src="../../gui/img/common/default/show-calendar.gif">
                                                 </div>
                                            </td>
                                            
                                            <td width="10%" valign="middle">Notification:</td>
                                            <td width="10%" nowrap="nowrap" align="left">
                                                 <div id="notificationAutoComplete">
                                                    <input type="hidden" size="15" value="" id="notificationCode" name="notificationCode" onkeyup="if (event.keyCode == 13) { addNotification(); }" onblur="trimString(this);" />
                                                    <div id="notificationContainer" style="width:40em"></div>
                                                 </div>
                                            </td>
                                           
                                            <td width="*" nowrap="nowrap" align="left">
                                                 <div id="notificationDescAutoComplete">
                                                    <input type="text" size="67" value="" id="notificationDesc" name="notificationDesc" onkeyup="if (event.keyCode == 13) { addNotification(); }" />
                                                    <div id="notificationDescContainer" style="height:85px; max-width: 400px; overflow-x:hidden"></div>
                                                 </div>
                                            </td>
                                            <input id="hnotificationCode" type="hidden" value="">
                                         </table>
                                    </th>
                                    
                                <th width="10%">
                                    <input id="btnAddnotificationCode" height="10" type="button" value="Add" onclick="addNotification();" style="width:80%">
                                </th>
                            </tr>
                        </thead>
                    </table>
                   
                    
                <div style="width:100%;height:107px;overflow:scroll;border:1px solid black">
                <table id="notificationCodeTable" name="notificationCodeTable" width="100%" border="0" cellpadding="0" cellspacing="1">
                        <thead></thead>
                        <tbody>
                             
                        </tbody>
                    </table>
                </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>
<script type="text/javascript">

now = new Date();
Calendar.setup ({
        inputField: "request_date",
        dateFormat: "%m/%d/%Y",
        trigger: "request_date_trigger",
        showTime: false,
        fdow: 0,
        max : Calendar.dateToInt(now),
        onSelect: function() { this.hide() }
});
                                        
YAHOO.example.BasicRemote = function() {
        
        // Use an XHRDataSource
        var notificationDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/medocs/ajax/notification-query.php");
        // Set the responseType
        notificationDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
        // Define the schema of the delimited results
        notificationDS.responseSchema = {
                recordDelim: "\n",
                fieldDelim: "\t"
        };
        // Enable caching
        notificationDS.maxCacheEntries = 5;

        // Instantiate the AutoComplete
        var notificationAC = new YAHOO.widget.AutoComplete("notificationCode", "notificationContainer", notificationDS);
        notificationAC.formatResult = function(oResultData, sQuery, sResultMatch) {
                return "<span style=\"float:left;width:15%\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
        };
        
        // Define an event handler to populate a hidden form field
        // when an item gets selected
        var mynotificationDesc = YAHOO.util.Dom.get("notificationDesc");
        var notificationHandler = function(sType, aArgs) {
                var myAC1 = aArgs[0]; // reference back to the AC instance
                var elLI1 = aArgs[1]; // reference to the selected LI element
                var oData1 = aArgs[2]; // object literal of selected item's result data

                // update text input control ...
                mynotificationDesc.value = oData1[1];
        };
        notificationAC.itemSelectEvent.subscribe(notificationHandler);

        // Use an XHRDataSource
        var notificationDescDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/medocs/ajax/notificationdesc-query.php");
        // Set the responseType
        notificationDescDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
        // Define the schema of the delimited results
        notificationDescDS.responseSchema = {
                recordDelim: "\n",
                fieldDelim: "\t"
        };
        // Enable caching
        notificationDescDS.maxCacheEntries = 5;

        // Instantiate the AutoComplete
        var notificationDescAC = new YAHOO.widget.AutoComplete("notificationDesc", "notificationDescContainer", notificationDescDS);
        notificationDescAC.formatResult = function(oResultData, sQuery, sResultMatch) {
                //return "<span style=\"float:left;width:*\">"+oResultData[0]+"</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style\"float:right;width:10%\">"+oResultData[1]+"</span>";
                return "<span style=\"float:left;width:*\">"+oResultData[0]+"</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
        };

        // Define an event handler to populate a hidden form field
        // when an item gets selected
        var mynotification = YAHOO.util.Dom.get("notificationCode");
        var notificationDescHandler = function(sType, aArgs) {
                var myAC2 = aArgs[0]; // reference back to the AC instance
                var elLI2 = aArgs[1]; // reference to the selected LI element
                var oData2 = aArgs[2]; // object literal of selected item's result data

                // update text input control ...
                mynotification.value = oData2[1];
        };
        notificationDescAC.itemSelectEvent.subscribe(notificationDescHandler);
        
        
        return {
                notificationDS: notificationDS,
                notificationAC: notificationAC
        };
}();
</script>

<?php
$smarty->assign('class',"class=\"yui-skin-sam\"");
?>

<!-- END: notification BLOCK -->
