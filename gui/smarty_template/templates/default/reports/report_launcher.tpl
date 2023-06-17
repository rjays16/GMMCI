<div>
<div id="tabs" style="width:99%;margin-top:5px" class="ui-tabs">
    <ul>
		<li><a href="#tab-report" onclick="refreshlist('report');">
		    <span class="icon">
			    <img alt="General Hospital Report" src="../../gui/img/common/default/icon-reports.png">
			</span>
			<span><strong>GENERAL HOSPITAL REPORT</strong></span></a>
        </li>
	</ul>

	<div id="tab-report" class="ui-tabs-hide">
		<div class="drop-shadow rounded-borders-all"> 
            <table class="data-grid rounded-borders-bottom">
                <thead>
                    <tr>
                        <td colspan="3" class="drop-shadow rounded-borders-all" style="vertical-align:top">
                            <div class="form-header rounded-borders-top">
                                <div class="form-column" style="width: 100%" >
                                    <h1>Department:</h1> &nbsp;
                                    {{$department_selection}}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="drop-shadow rounded-borders-all" style="vertical-align:top">
                            <div class="form-header rounded-borders-top">
                                <div class="form-column" style="width: 100%">
                                    <h1>Category:</h1>
                                </div>
                            </div>
                            <table class="data-grid rounded-borders-bottom">
                                <tbody>
                                    <tr height="50px">
                                        <td width="10%">&nbsp;</td>
                                        <td width="*">
                                            {{$category_selection}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>&nbsp;</td>
                        <td width="50%" class="drop-shadow rounded-borders-all">
                            <div class="form-header rounded-borders-top">
                                <div class="form-column" style="width: 100%">
                                    <h1>Period:</h1>
                                </div>
                            </div>
                            <table class="data-grid rounded-borders-bottom">
                                <tbody>
                                    <tr>
                                        <td class="sublabel">From:&nbsp;&nbsp;</td>
                                        <td width="40%"> 
                                            {{$datefrom_fld}}
                                        </td>
                                        <td class="sublabel">To:&nbsp;&nbsp;</td>
                                        <td width="40%"> 
                                            {{$dateto_fld}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
<!-- Commented By Jarel 05/03/2013 --> 
                  <!--   <tr>
                        <td colspan="3" class="drop-shadow rounded-borders-all" style="vertical-align:top">
                            <div class="form-header rounded-borders-top">
                                <div class="form-column" style="width: 100%">
                                    <h1>Additional Filters:</h1> 
                                    <button id="collapse_trigger" title="Hide" onclick="HidePane()" style="margin-left: 4px; cursor: pointer;">
                                        <span id="col" class="icon plus"></span>
                                    </button>
                                </div>
                            </div>
                            <table class="data-grid rounded-borders-bottom" id="parameter_list1">
                                <tbody>
                                    <tr height="100px" id="params">   
                                        <td width="33%">{{$paramRow1}}</td>
                                        <td width="33%">{{$paramRow2}}</td>
                                        <td width="*">{{$paramRow3}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>  -->
                </thead>
            </table>
            {{$jsCalendarSetup}}
            <br>
            <div class="drop-shadow rounded-borders-all">  
			    <table>
                    <tr>
                        <td colspan="3" class="drop-shadow rounded-borders-all" style="vertical-align:top">
                            <div class="form-header rounded-borders-top">
                                <div class="form-column" style="width: 100%" >
                                    <h1>Available Reports:</h1> &nbsp;
                                    {{$search_input}}
                                    <!--<button title="Search" onclick="" id="searchButton" name="searchButton">
                                        <span class="icon magnifier"></span>
                                        Search
                                    </button>-->
                                    <img src="../../gui/img/common/default/redpfeil_l.gif">
                                    <div id="loading_indicator" class="ajax-loading-bar" style="visibility:hidden"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="dashlet">
				                <div id="report-list" style="margin-top:10px"></div>
			                </div>
                        </td>    
                    </tr>
                </table>    
            </div>
		</div>
	</div>
    <div class="segPanel" id="addParameters" style="display:none" align="left" height="auto">
        <table class="data-grid rounded-borders-bottom" id="parameter_list">
            <tbody>
                <tr id="params">  
                    <td width="*">{{$paramRow1}}</td>
                </tr>
            </tbody>
        </table>
    </div> 
    

