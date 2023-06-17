
//call autosave
/*setInterval("autosave()",3000);

function autosave(){
    alert('autosave');
}*/

function preset(){
  
    $J("#parameter_list").find("div").each(function(){
        //alert(this.id);
        $J("#"+this.id).hide();
        $J("#T"+this.id).hide();
    });

    $J('#Search').bind('keyup', function() {
      //if ((event.keyCode == 13)&&(isValidSearch($J('#Search').val()))) getReports();
      getReports();
    });
    
    $J('#datefrom').bind('blur', function() {
        IsValidDate(this,$J('#date_format').val());
    });
    
    $J('#dateto').bind('blur', function() {
        IsValidDate(this,$J('#date_format').val());
    });
    

}

function isValidSearch(key){
    if (key.length >= 2)
	    return true;
	else
	    return false;
}

//set other parameters, aside from date period
//edited by Jarel 05/03/2013
function setParameter(reportid){
    var list = $('parameter_list'+reportid);
    var dBody=list.getElementsByTagName("tbody")[0];
    var param = new Array();
    var paramlabel = 'param_';
    var paramvalue;
    var paramid;
    var params;
    var cont;
        
    if (dBody) { 
        var dParams = dBody.getElementsByTagName("span");
        if (dParams) {
            for (i=0;i<dParams.length;i++) {
                paramid = paramlabel+dParams[i].id;
                if (paramid.match('time')){
                    var start_time = $J('#'+paramid+'_from').val();
                    var end_time = $J('#'+paramid+'_to').val();
                    paramvalue = '';
                    // time period ==> from and to
                    if ((start_time == "") && (end_time == "")){
                        cont = true;
                    }else if ((start_time != "") || (end_time != "")){
                        if ((start_time != "") && (end_time != "")){
                             var start = start_time+" "+$J('#'+paramid+'_meridian_from').val();
                             var end = end_time+" "+$J('#'+paramid+'_meridian_to').val();
                             //set a temp date
                             var dtStart = new Date('1/1/2012 '+start);
                             var dtEnd = new Date('1/1/2012 '+end);
                             diff_in_ms = dtEnd - dtStart;
                            
                            if (diff_in_ms < 0){
                                 alert("Invalid Time Range!\n Start Time cannot be after End Time!")
                                 $(paramid+'_from').focus();
                                 cont = false;
                                 break;
                            }else{
                                //from
                                paramvalue_from = start
                                paramid_from = paramid+'_from';
                                
                                params = paramid_from+'--'+paramvalue_from;
                                param.push(params);
                                //to
                                paramvalue_to = end
                                paramid_to = paramid+'_to';
                                
                                params = paramid_to+'--'+paramvalue_to;
                                param.push(params);
                                
                                cont = true;
                               
                            }   
                        }else{
                            alert("Invalid Time Range!\n Start Time or End Time cannot be blank!")
                            if ($J('#'+paramid+'_from').val() == "")
                                $(paramid+'_from').focus();
                            if ($J('#'+paramid+'_to').val() == "")
                                $(paramid+'_to').focus();
                            cont = false;
                            break;
                        }
                    }     
                }else if (paramid.match('checkbox')){
                    paramvalue = $(paramid).checked;
                    if (paramvalue){
                        params = paramid+'--'+paramvalue;
                        param.push(params);
                    }
                    cont = true; 
                }else{
                    paramvalue = $J('#'+paramid).val();
                    if (paramvalue){
                        params = paramid+'--'+paramvalue;
                        param.push(params);
                    }
                    cont = true;
                }
                /*if (paramvalue){
                    params = paramid+'--'+paramvalue;
                    param.push(params);
                }*/ 
            }
        }
    }
    
    if (cont)
        return param;
    else
        return param;    
    
}
//added by daryl
function generate_report(reportid, with_template, query_in_jasper,repformat){


    var param;
    
    frmdte = $J('#datefrom').val();   // format is mm/dd/YYYY
    todte  = $J('#dateto').val();
    
        nleft = (screen.width - 800)/2;
        ntop = (screen.height - 640)/2;

        frmdte = dateFormat(frmdte,'isoDate');
        todte = dateFormat(todte,'isoDate');
        
        frmdte = frmdte + ' 00:00:00';
        todte  = todte + ' 00:00:00';

        fromdate = getDateFromFormat(frmdte, 'yyyy-MM-dd HH:mm:ss')/1000;
        todate =  getDateFromFormat(todte, 'yyyy-MM-dd HH:mm:ss')/1000;
        
        if (fromdate > todate) {
            alert("Invalid Date Range!\nStart Date cannot be after End Date!")
            return false;
        }else{
            param = setParameter(reportid);//edited by Jarel 05/03/2013
         
            if (param){
                if (with_template==1){ 
                    var openWin;
                    
                    //$J.blockUI({ message: "<h1>Generating the report is in progress...</h1>" })
                    if (query_in_jasper==1) 
                        openWin = window.open('../reports/show_report_jasper.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                    else
                        openWin = window.open('../reports/show_report.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);    
                    
                    //$J.unblockUI();
                    
                }else{
                    alert('The template of this report is not yet AVAILABLE! \n Please inform the system administrator...');
                } 
            }       
        }    
    }    
//ended by daryl

function __showReport(reportid, with_template, query_in_jasper,repformat) {


         frmdte = $J('#datefrom').val();   // format is mm/dd/YYYY
         todte  = $J('#dateto').val();

   if(reportid == "MMHR" || reportid=="mmhr")
    {
        generate_report(reportid, with_template, query_in_jasper,repformat);
    }else{
            if ((frmdte=='') || (todte=='')){
               alert('Please specify report period!');
               return false;
            }else{
                 generate_report(reportid, with_template, query_in_jasper,repformat);
            }
    }

}

function getReports() {
    searchSource();
}

function HidePane(){
    
    if ($J('#parameter_list').is(":visible")){
        $J('#parameter_list').hide();
        //$J('#collapse_trigger').removeClass('icon minus');
        //$J('#collapse_trigger').addClass('icon plus');    
        $J('#col').attr('class','icon plus');
    }else{
        $J('#parameter_list').show();     
        //$J('#collapse_trigger').removeClass('icon plus');    
        //$J('#collapse_trigger').addClass('icon minus');
        $J('#col').attr('class','icon minus');
    }
    
}

function setMuniCity(mun_nr, mun_name) {
    $J("#param_munnr").val(mun_nr);
    $J("#param1_munnr").val(mun_name);
}

function setProvince(prov_nr, prov_name) {
    $J("#param_provnr").val(prov_nr);
    $J("#param1_provnr").val(prov_name);
}

function clearNr(id) {
    
    if ($J('#'+id).val() == '') {
        switch (id) {
            case "param1_brgynr":
                $J('#param_brgynr').val('');
                break;
                
            case "param1_munnr":
                $J('#param_munnr').val('');  
                
                if ($J('#param1_brgynr').val()!=''){
                    $J('#param_brgynr').val('');
                    $J('#param1_brgynr').val('');
                }    
                    
                break;     
                
            case "param1_provnr":
                $J('#param_provnr').val('');
                
                if ($J('#param1_brgynr').val()!=''){
                    $J('#param_brgynr').val('');
                    $J('#param1_brgynr').val('');
                }    
                    
                if ($J('#param1_munnr').val()!=''){
                    $J('#param_munnr').val('');    
                    $J('#param1_munnr').val('');    
                }    
                
                break;  
        }
    }
}

function clearNr2(id) {
    
    switch (id) {
        case "param1_brgynr":
            $J('#param_brgynr').val('');
            break;
            
        case "param1_munnr":
            $J('#param_munnr').val('');  
            
            if ($J('#param1_brgynr').val()!=''){
                $J('#param_brgynr').val('');
                $J('#param1_brgynr').val('');
            }    
                
            break;     
            
        case "param1_provnr":
            $J('#param_provnr').val('');
            
            if ($J('#param1_brgynr').val()!=''){
                $J('#param_brgynr').val('');
                $J('#param1_brgynr').val('');
            }    
                
            if ($J('#param1_munnr').val()!=''){
                $J('#param_munnr').val('');    
                $J('#param1_munnr').val('');    
            }    
            
            break;  
    }
}

function validate(id){
    switch (id) {
        case "param1_brgynr":
            
            if (($J('#param1_munnr').val()=='') && ($J('#param1_provnr').val() != '')){
                alert('Enter a Municipality or City first.');
                $J('#param1_munnr').focus();
                return false;
            }/*else if ($J('#param1_provnr').is(":empty")){
                alert('Enter a Province first.');
                $J('#param1_provnr').focus();
            }*/
            
            break;
            
        case "param1_munnr":
            if ($J('#param1_provnr').val()==''){
                alert('Enter a Province first.');
                $J('#param1_provnr').focus();
                return false;            
            }     
            
            break;     
    }
    return true;
}
//edited by daryl
function GenerateParams(reportid, with_template, query_in_jasper,repformat){
    
    frmdte = $J('#datefrom').val();   // format is mm/dd/YYYY
    todte  = $J('#dateto').val();
    
        frmdte = dateFormat(frmdte,'isoDate');
        todte = dateFormat(todte,'isoDate');
        
        frmdte = frmdte + ' 00:00:00';
        todte  = todte + ' 00:00:00';

        fromdate = getDateFromFormat(frmdte, 'yyyy-MM-dd HH:mm:ss')/1000;
        todate =  getDateFromFormat(todte, 'yyyy-MM-dd HH:mm:ss')/1000;
        
        if (fromdate > todate) {
            alert("Invalid Date Range!\nStart Date cannot be after End Date!")
            return false;
        }else{
              $J( "#addParameters"+reportid).dialog({
                autoOpen: true,
                modal:true,
                height: "auto",
                width: "auto",
                show: "blind",
                hide: "explode",
                title: "Additional Parameters",
                position: "top", 
                buttons: {
                        GENERATE: function() {
                            __showReport(reportid, with_template, query_in_jasper,repformat);
                            $J( this ).dialog( "close" );
                        },
                        CANCEL: function() {
                            $J( this ).dialog( "close" );
                        }
                },
                open: function(){
                    // for ICD
                    if ($J( "#param1_icd10" )){
                        $J( "#param1_icd10" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_ICD10.php?iscode="+$(reportid+'_paramCheck_icd10').checked+"", request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            //alert(ui.item.id);
                            $('param_icd10').value = ui.item.id;
                        }
                        });
                        
                        $J('#paramCheck_icd10').click(function(){
                            $('param1_icd10').value = '';
                            $('param1_icd10').focus();    
                        });
                    }   
                    //---------------
                    
                    
                    // for ICP
                    if ($J( "#param1_icpm" )){
                        $J( "#param1_icpm" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_ICPM.php?iscode="+$(reportid+'_paramCheck_icpm').checked+"", request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            //alert(ui.item.id);
                            $('param_icpm').value = ui.item.id;
                        }
                        });
                        
                        $J('#paramCheck_icpm').click(function(){
                            $('param1_icpm').value = '';
                            $('param1_icpm').focus();    
                        });    
                    }   
                    // ---------------
                    
                    //added by VAN 03-02-2013
                    //DEMOGRAPHICS
                    //for province
                    if ($J( "#param1_provnr" )){
                        $J( "#param1_provnr" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_Province.php", request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            $('param_provnr').value = ui.item.id;
                        }
                        });
                    }
                    
                    //for municipal and city
                    if ($J( "#param1_munnr" )){
                        $J( "#param1_munnr" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_Municipality.php?prov_nr="+$J('#param_provnr').val(), request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            $('param_munnr').value = ui.item.id;
                            xajax_getProvince(ui.item.id);
                        }
                        });
                    }
                    
                    //for Barangay
                    if ($J( "#param1_brgynr" )){
                        $J( "#param1_brgynr" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_Barangay.php?prov_nr="+$J('#param_provnr').val()+"&mun_nr="+$J('#param_munnr').val(), request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            $('param_brgynr').value = ui.item.id;
                            xajax_getMuniCityandProv(ui.item.id);
                        }
                        });
                    }
                },
                close:function(){
                    renameID2(reportid);
                } 
            });   
        }
          
    
    }
//ended by daryl

//Added by Jarel 05/03/2013
function showAddParams(reportid, with_template, query_in_jasper,repformat){
    
         frmdte = $J('#datefrom').val();   // format is mm/dd/YYYY
         todte  = $J('#dateto').val();

   if(reportid == "MMHR" || reportid=="mmhr")
    {
        GenerateParams(reportid, with_template, query_in_jasper,repformat);
    }else{
            if ((frmdte=='') || (todte=='')){
               alert('Please specify report period!');
               return false;
            }else{
                 GenerateParams(reportid, with_template, query_in_jasper,repformat);
            }
    }



}

function renameID(reportid){
    var id;
    var elems = $J("#addParameters"+reportid+" :input").serializeArray(); 
    $J.each(elems, function(i, field){ 
       id = field.name.replace(reportid+"_","");
       $J("#"+field.name).attr('id',id);        
    });
}

function renameID2(reportid){
    var id;
    var elems = $J("#addParameters"+reportid+" :input").serializeArray(); 
    $J.each(elems, function(i, field){
     if(field.name.indexOf("Check") > 0){
        $J("#"+field.name).attr('checked','');
     }else{
        id = field.name.replace(reportid+"_","");
        $J("#"+id).val(''); 
        $J("#"+id).attr('id',field.name);  
     }
       
    });
}

function genReport(reportid, with_template, query_in_jasper,repformat,param){
    if(param=='0'){
        __showReport(reportid, with_template, query_in_jasper,repformat);    
    }else{
        renameID(reportid);
        showAddParams(reportid, with_template, query_in_jasper,repformat);
    }
}