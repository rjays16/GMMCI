<div class="drop-shadow rounded-borders-all">  
    <table>
        <tr>
            <td colspan="3" class="drop-shadow rounded-borders-all" style="vertical-align:top">
                <div class="form-header rounded-borders-top">
                    <div class="form-column" style="width: 100%" >
                        <strong>Patient's Name (Lastname, Firstname) or HRN : </strong> &nbsp;
                        <input type="text" id="Search" maxlength="255" size="50" name="Search" class="segInput" onBlur="DisabledSearch();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('Search').value))) getReports(); ">
                        <button title="Search" id="searchButton" name="searchButton" onClick="getReports();">
                            <span class="icon magnifier"></span>
                            Search
                        </button>
                        <img src="../../gui/img/common/default/redpfeil_l.gif">
                        <div id="loading_indicator" class="ajax-loading-bar" style="visibility:hidden"></div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="dashlet">
                    <div id="labresult-list" style="margin-top:10px"></div>
                </div>
            </td>    
        </tr>
    </table>    
</div>