var trayItems = 0;

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

/*
	This will trim the string i.e. no whitespaces in the
	beginning and end of a string AND only a single
	whitespace appears in between tokens/words
	input: object
	output: object (string) value is trimmed
*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}/* end of function trimString */

function preset(){
    var quantity = $('quantity').value;
    var service_code = $('service_code').value;
    
    for(i=1;i<=quantity;i++){
        id = service_code+i
        setEnable(id);
        done_setEnable(id);
        issuance_setEnable(id);
//added by:borj 2013/25/11
        returned_setEnable(id);
        reissue_setEnable(id);
        consumed_setEnable(id);


//end borj
    }    

}


function done_setEnable(id){
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('date_done_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_done'+id).readOnly = '';    
        $('time_done'+id).readOnly = '';
        $('done_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_done'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_done'+id).mask('99:99');
        });
        
        $J('#date_done'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_done'+id).bind('change', function() {
            setFormatTime(this,id,'done_');
        });
        
        $J('#date_done_save'+id).bind('click', function() {
            validateDate(id,'done');
        });
        
        $J('#date_done_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Date Done to be cancelled...');
            }else{    
                if (confirm('Remove the Done Date entered?')){
                    $J('#date_done'+id).val('');
                    $J('#time_done'+id).val('');
                    $J('#done_meridian'+id).val('');
                    
                    deleteDate(id,'done');
                }    
            }    
        });
        
        
        if ($J('#date_done'+id).val().length==0){
            $J('#date_done'+id).val('');
            $J('#time_done'+id).val('');
            $J('#done_meridian'+id).val('');
        }
        
        $J('#date_done_trigger'+id).css("cursor", "pointer");
        $J('#date_done_save'+id).css("cursor", "pointer");
        $J('#date_done_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('date_done_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_done'+id).readOnly = 'readonly';
        $('date_done'+id).value='';
        
        $('time_done'+id).readOnly = 'readonly';
        $('time_done'+id).value = '';
        $('done_meridian'+id).disabled = true;
        $('done_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_done'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_done'+id).unmask('99:99');
        });
        
        $J('#date_done'+id).val('');
        $J('#time_done'+id).val('');
        $J('#done_meridian'+id).val('');
        
        $J('#date_done_trigger'+id).css("cursor", "default");
        $J('#date_done_save'+id).css("cursor", "default");
        $J('#date_done_cancel'+id).css("cursor", "default");
    }    
}

function issuance_setEnable(id){
    
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_issuance_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_issuance'+id).readOnly = '';
        $('time_issuance'+id).readOnly = true;
        $('issuance_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_issuance'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_issuance'+id).mask('99:99');
        });
        
        $J('#date_issuance'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_issuance'+id).bind('change', function() {
            setFormatTime(this,id,'issuance_');
        });
        
        $J('#date_issuance_save'+id).bind('click', function() {
            validateDate(id,'issuance');
        });
        
        $J('#date_issuance_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Issuance Date to be cancelled...');
            }else{    
                if (confirm('Remove the Issuance Date entered?')){
                    $J('#date_issuance'+id).val('');
                    $J('#time_issuance'+id).val('');
                    $J('#issuance_meridian'+id).val('');
                    
                    deleteDate(id,'issuance');
                }    
            }    
        });
        
        if ($J('#date_issuance'+id).val().length==0){
            $J('#date_issuance'+id).val('');
            $J('#time_issuance'+id).val('');
            $J('#issuance_meridian'+id).val('');
        }
        
        $J('#date_issuance_trigger'+id).css("cursor", "pointer");
        $J('#date_issuance_save'+id).css("cursor", "pointer");
        $J('#date_issuance_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_issuance_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_issuance'+id).readOnly = 'readonly';
        $('date_issuance'+id).value='';
        
        $('time_issuance'+id).readOnly = 'readonly';
        $('time_issuance'+id).value = '';
        $('issuance_meridian'+id).disabled = true;
        $('issuance_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_issuance'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_issuance'+id).unmask('99:99');
        });
        
        $J('#date_issuance'+id).val('');
        $J('#time_issuance'+id).val('');
        $J('#issuance_meridian'+id).val('');
        
        $J('#date_issuance_trigger'+id).css("cursor", "default");
        $J('#date_issuance_save'+id).css("cursor", "default");
        $J('#date_issuance_cancel'+id).css("cursor", "default");
    }    
}

function setEnable(id){
    if ($J('#is_received'+id).is(":checked")){
			$('date_received_trigger'+id).disabled = false;
	        //$('date_received'+id).readOnly = '';
            $('time_received'+id).readOnly = true;
            $('meridian'+id).disabled = false;
            
            $('serial'+id).readOnly = '';
            $('component'+id).disabled = false;
            $('result'+id).disabled = false;
            
            jQuery(function($){
                $J('#date_received'+id).mask('99/99/9999');
            });
            
            jQuery(function($){
                $J('#time_received'+id).mask('99:99');
            });
            
            $J('#date_received'+id).bind('blur', function() {
                IsValidDate(this,'MM/dd/yyyy');
            });
            
            $J('#time_received'+id).bind('change', function() {
                setFormatTime(this,id,'');
            });
            
            
            if ($('date_received'+id).value==''){
                $('date_received'+id).value=$('current_date').value;
                $('time_received'+id).value = $('current_time').value;
                $('meridian'+id).value=$('current_meridian').value;
            }
            
            $J('#date_received_trigger'+id).css("cursor", "pointer");
            
    }else{
			$('date_received_trigger'+id).disabled = true;
	        //$('date_received'+id).readOnly = 'readonly';
            $('date_received'+id).value='';
            
            $('serial'+id).readOnly = 'readonly';
            $('serial'+id).value = '';
            $('component'+id).disabled = true;
            $('component'+id).value='';
            $('result'+id).disabled = true;
            $('result'+id).value='noresult';
            
            $('time_received'+id).readOnly = 'readonly';
            $('time_received'+id).value = '';
            $('meridian'+id).disabled = true;
            $('meridian'+id).value='AM';
            
            jQuery(function($){
                $J('#date_received'+id).unmask('99/99/9999');
            });
            
            jQuery(function($){
                $J('#time_received'+id).unmask('99:99');
            });
            
            
            $J('#date_received_trigger'+id).css("cursor", "default");
            
    }
    
    /*$J('#date_done'+id).val('');
    $J('#time_done'+id).val('');
    $J('#done_meridian'+id).val('');
    
    $J('#date_issuance'+id).val('');
    $J('#time_issuance'+id).val('');
    $J('#issuance_meridian'+id).val('');*/
            
    
    $('counter').innerHTML = $J(".tdrec input:checked").length;//edited by nick, class tdrec, 2/5/14
    $('received_qty').value = $('counter').innerHTML;
}
//added by:borj 2013/23/11
function returned_setEnable(id){
    
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_returned_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_returned'+id).readOnly = '';
        $('time_returned'+id).readOnly = true;
        $('returned_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_returned'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_returned'+id).mask('99:99');
        });
        
        $J('#date_returned'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_returned'+id).bind('change', function() {
            setFormatTime(this,id,'returned_');
        });
        
        $J('#date_returned_save'+id).bind('click', function() {
            validateDate(id,'returned');
        });
        
        $J('#date_returned_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Returned Date to be cancelled...');
            }else{    
                if (confirm('Remove the Returned Date entered?')){
                    $J('#date_returned'+id).val('');
                    $J('#time_returned'+id).val('');
                    $J('#returned_meridian'+id).val('');
                    
                    deleteDate(id,'returned');
                }    
            }    
        });
        
        if ($J('#date_returned'+id).val().length==0){
            $J('#date_returned'+id).val('');
            $J('#time_returned'+id).val('');
            $J('#returned_meridian'+id).val('');
        }
        
        $J('#date_returned_trigger'+id).css("cursor", "pointer");
        $J('#date_returned_save'+id).css("cursor", "pointer");
        $J('#date_returned_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_returned_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_returned'+id).readOnly = 'readonly';
        $('date_returned'+id).value='';
        
        $('time_returned'+id).readOnly = 'readonly';
        $('time_returned'+id).value = '';
        $('returned_meridian'+id).disabled = true;
        $('returned_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_returned'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_returned'+id).unmask('99:99');
        });
        
        $J('#date_returned'+id).val('');
        $J('#time_returned'+id).val('');
        $J('#returned_meridian'+id).val('');
        
        $J('#date_returned_trigger'+id).css("cursor", "default");
        $J('#date_returned_save'+id).css("cursor", "default");
        $J('#date_returned_cancel'+id).css("cursor", "default");
    }  
}

function reissue_setEnable(id){
    
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_reissue_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_reissue'+id).readOnly = '';
        $('time_reissue'+id).readOnly = true;
        $('reissue_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_reissue'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_reissue'+id).mask('99:99');
        });
        
        $J('#date_reissue'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_reissue'+id).bind('change', function() {
            setFormatTime(this,id,'reissue_');
        });
        
        $J('#date_reissue_save'+id).bind('click', function() {
            validateDate(id,'reissue');
        });
        
        $J('#date_reissue_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Reissue Date to be cancelled...');
            }else{    
                if (confirm('Remove the Reissue Date entered?')){
                    $J('#date_reissue'+id).val('');
                    $J('#time_reissue'+id).val('');
                    $J('#reissue_meridian'+id).val('');
                    
                    deleteDate(id,'reissue');
                }    
            }    
        });
        
        if ($J('#date_reissue'+id).val().length==0){
            $J('#date_reissue'+id).val('');
            $J('#time_reissue'+id).val('');
            $J('#reissue_meridian'+id).val('');
        }
        
        $J('#date_reissue_trigger'+id).css("cursor", "pointer");
        $J('#date_reissue_save'+id).css("cursor", "pointer");
        $J('#date_reissue_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_reissue_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_reissue'+id).readOnly = 'readonly';
        $('date_reissue'+id).value='';
        
        $('time_reissue'+id).readOnly = 'readonly';
        $('time_reissue'+id).value = '';
        $('reissue_meridian'+id).disabled = true;
        $('reissue_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_reissue'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_reissue'+id).unmask('99:99');
        });
        
        $J('#date_reissue'+id).val('');
        $J('#time_reissue'+id).val('');
        $J('#reissue_meridian'+id).val('');
        
        $J('#date_reissue_trigger'+id).css("cursor", "default");
        $J('#date_reissue_save'+id).css("cursor", "default");
        $J('#date_reissue_cancel'+id).css("cursor", "default");
    }    
}

function consumed_setEnable(id){
    
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_consumed_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_consumed'+id).readOnly = '';
        $('time_consumed'+id).readOnly = true;
        $('consumed_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_consumed'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_consumed'+id).mask('99:99');
        });
        
        $J('#date_consumed'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_consumed'+id).bind('change', function() {
            setFormatTime(this,id,'consumed_');
        });
        
        $J('#date_consumed_save'+id).bind('click', function() {
            validateDate(id,'consumed');
        });
        
        $J('#date_consumed_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Consumed Date to be cancelled...');
            }else{    
                if (confirm('Remove the Consumed Date entered?')){
                    $J('#date_consumed'+id).val('');
                    $J('#time_consumed'+id).val('');
                    $J('#consumed_meridian'+id).val('');
                    
                    deleteDate(id,'consumed');
                }    
            }    
        });
        
        if ($J('#date_consumed'+id).val().length==0){
            $J('#date_consumed'+id).val('');
            $J('#time_consumed'+id).val('');
            $J('#consumed_meridian'+id).val('');
        }
        
        $J('#date_consumed_trigger'+id).css("cursor", "pointer");
        $J('#date_consumed_save'+id).css("cursor", "pointer");
        $J('#date_consumed_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_consumed_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_consumed'+id).readOnly = 'readonly';
        $('date_consumed'+id).value='';
        
        $('time_consumed'+id).readOnly = 'readonly';
        $('time_consumed'+id).value = '';
        $('consumed_meridian'+id).disabled = true;
        $('consumed_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_consumed'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_consumed'+id).unmask('99:99');
        });
        
        $J('#date_consumed'+id).val('');
        $J('#time_consumed'+id).val('');
        $J('#consumed_meridian'+id).val('');
        
        $J('#date_consumed_trigger'+id).css("cursor", "default");
        $J('#date_consumed_save'+id).css("cursor", "default");
        $J('#date_consumed_cancel'+id).css("cursor", "default");
    }    
}

//end borj

function submitRequest(refno, id){
    var details = new Object();
    $('submitted').value = 0;
    //at least one checkbox is checked
    /*if ($J("input:checked").length<=0){
        alert('Please check at least one sample received.');
        //$('is_received'+id+'1').focus();
        return false;
    }else{*/
        //xajax_updateBloodReceived(refno, id, details);
        $('submitted').value = 1;    
        $('inputform').submit();
        //return true;
    //}
}

/*function repeatRequest(id, service_code){
	 //alert('repeat = '+id);
	 var refno = $('refno').innerHTML;

	 if ($('date_received'+id).value==''){
			alert('Please indicate the date of repeat service.');
			$('date_received'+id).focus();
			//$('date_received_trigger'+id).focus();
			return false;
	 }

	 //alert(refno+", "+$('date_received'+id).value+", "+service_code);
}*/

function reset(id){
    
    //var n = $J("input:checked").length;
    var n = $J("input:checkbox").length;
    
    for (var i=1;i<=n;i++){
      $J('#date_received'+id+i).unmask('99/99/9999');
      $J('#time_received'+id+i).unmask('99:99');
      
      $J('#date_done'+id+i).unmask('99/99/9999');
      $J('#time_done'+id+i).unmask('99:99');   
      
      $J('#date_issuance'+id+i).unmask('99/99/9999');
      $J('#time_issuance'+id+i).unmask('99:99');      
      //added by:borj
      //2013/23/11
      $J('#date_returned'+id+i).unmask('99/99/9999');
      $J('#time_returned'+id+i).unmask('99:99');

      $J('#date_reissue'+id+i).unmask('99/99/9999');
      $J('#time_reissue'+id+i).unmask('99:99');

      $J('#date_consumed'+id+i).unmask('99/99/9999');
      $J('#time_consumed'+id+i).unmask('99:99');            
      //end borj
    }
    
    $J('input:text').val('');
    $J('input:text').attr('readOnly', 'readonly');
    $J('select').val('');
    $J('select').attr('disabled', true);
    $J('input:checkbox').attr('checked', false);
    $J('button').attr('disabled', true);
    $J('button[name=submit_btn]').attr('disabled', false);
    $J('button[name=close_btn]').attr('disabled', false);

}


/*function updateFields(cal) {
	var date = cal.selection.get();
	if (date) {
		date = Calendar.intToDate(date);
		document.getElementById("f_date").value = Calendar.printDate(date, "%Y-%m-%d");
	}
	document.getElementById("f_hour").value = cal.getHours();
	document.getElementById("f_minute").value = cal.getMinutes();
};*/

function set_calendar(id){
	var datenow = $('datenow').value;

	// disable from day after current day and onward
	Calendar.setup ({
		inputField: 'date_received'+id,
		//dateFormat: '%B %e, %Y',
		dateFormat: '%m/%d/%Y %I:%M%P',
		trigger: 'date_received_trigger'+id,
		showTime: true,
		onSelect: function() { this.hide() },
		/*disabled: function(date) {
				if (date.getDay() == 5) {
						return true;
				} else {
						return false;
				}
		} */
		max: eval(datenow)
	});
    
    Calendar.setup ({
        inputField: 'date_done'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_done_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    
    Calendar.setup ({
        inputField: 'date_issuance'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_issuance_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    //added by:borj
    //2013/23/11
    Calendar.setup ({
        inputField: 'date_returned'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_returned_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });

    Calendar.setup ({
        inputField: 'date_reissue'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_reissue_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });

    Calendar.setup ({
        inputField: 'date_consumed'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_consumed_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    //end borj

	// onSelect     : updateFields,
	// onTimeChange : updateFields
}

var seg_validTime=false;

function setFormatTime(thisTime, id, name){
//    var time = $('time_text_d');
    var stime = thisTime.value;
    var hour, minute;
    var ftime ="";
    var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
    var f2 = /^[0-9]\:[0-5][0-9]$/;

    trimString(thisTime);

    if (thisTime.value==''){
        seg_validTime=false;
        return;
    }

    stime = stime.replace(':', '');

    if (stime.length == 3){
        hour = stime.substring(0,1);
        minute = stime.substring(1,3);
    } else if (stime.length == 4){
        hour = stime.substring(0,2);
        minute = stime.substring(2,4);
    }else{
        alert("Invalid time format.");
        thisTime.value = "";
        seg_validTime=false;
        thisTime.focus();
        return;
    }

    if (hour==0){
         hour = 12;
         $(name+'meridian'+id).value = "AM";
    }else    if((hour > 12)&&(hour < 24)){
         hour -= 12;
         $(name+'meridian'+id).value = "PM";
    }
    
    if ((typeof hour)=='number'){
        if (hour < 10)
            hour = '0'.concat(hour);
    } 

    ftime =  hour + ":" + minute;

    if(!ftime.match(f1) && !ftime.match(f2)){
        thisTime.value = "";
        alert("Invalid time format.");
        seg_validTime=false;
        thisTime.focus();
    }else{
        thisTime.value = ftime;
        seg_validTime=true;
    }
}// end of function setFormatTime

function deleteDate(id, mode){
     var refno = $('refno').innerHTML;
     var service_code = $('test_code').innerHTML;
     var index = id.replace(service_code,'');
     
     xajax_save_dateinfo(refno, service_code, index, mode, '', ''); 

 } 
//added by: borj 2013/2/12
function validateDate(id, str){
    var date = new Date();
    var received_date = Date.parse($J("#date_received"+id).val());
    var received_date2 = $J("#date_received"+id).val();
    var received_hour = $J('#time_received'+id).val().substring(0,2);
    var received_minute = $J('#time_received'+id).val().substring(3,5);

    var issuance_date = Date.parse($J("#date_issuance"+id).val());
    var issuance_date2 = $J("#date_issuance"+id).val();
    var issuance_hour = $J('#time_issuance'+id).val().substring(0,2);
    var issuance_minute = $J('#time_issuance'+id).val().substring(3,5);

    var returned_date = Date.parse($J("#date_returned"+id).val());
    var returned_date2 = $J("#date_returned"+id).val();
    var returned_hour = $J('#time_returned'+id).val().substring(0,2);
    var returned_minute = $J('#time_returned'+id).val().substring(3,5);

    var done_date = Date.parse($J("#date_done"+id).val());
    var done_date2 = $J("#date_done"+id).val();
    var done_hour = $J('#time_done'+id).val().substring(0,2);
    var done_minute = $J('#time_done'+id).val().substring(3,5);

    var reissue_date = Date.parse($J("#date_reissue"+id).val());
    var reissue_date2 = $J("#date_reissue"+id).val();
    var reissue_hour = $J('#time_reissue'+id).val().substring(0,2);
    var reissue_minute = $J('#time_reissue'+id).val().substring(3,5);

    var regExp = /(\d{1,2})\:(\d{1,2})\:(\d{1,2})/;
    
    if ($J('#meridian'+id).val()=='PM'){
       if (parseFloat(received_hour) < 12)            
            received_hour = parseFloat(received_hour) + 12;
       received_time = received_hour+":"+received_minute+":00";
    }else{
       if (parseFloat(received_hour) == 12)            
            received_hour = '00';
       received_time = received_hour+":"+received_minute+":00";
    }
//added by: borj 2013/1/12
    if ($J('#done_meridian'+id).val()=='PM'){
       if (parseFloat(done_hour) < 12)            
            done_hour = parseFloat(done_hour) + 12;
       done_time = done_hour+":"+done_minute+":00";
    }else{  
       if (parseFloat(done_hour) == 12)            
            done_hour = '00';
       done_time = done_hour+":"+done_minute+":00";
    }

    if ($J('#issuance_meridian'+id).val()=='PM'){
       if (parseFloat(issuance_hour) < 12)            
            issuance_hour = parseFloat(issuance_hour) + 12;
       issuance_time = issuance_hour+":"+issuance_minute+":00";
    }else{
       if (parseFloat(issuance_hour) == 12)            
            issuance_hour = '00';
        issuance_time = issuance_hour+":"+issuance_minute+":00";
    }
             
    if ($J('#returned_meridian'+id).val()=='PM'){
       if (parseFloat(returned_hour) < 12)            
            returned_hour = parseFloat(returned_hour) + 12;
       returned_time = returned_hour+":"+returned_minute+":00";
    }else{
       if (parseFloat(returned_hour) == 12)            
            returned_hour = '00';
       returned_time = returned_hour+":"+returned_minute+":00";
    }

     if ($J('#reissue_meridian'+id).val()=='PM'){
       if (parseFloat(reissue_hour) < 12)            
            reissue_hour = parseFloat(reissue_hour) + 12;
       reissue_time = reissue_hour+":"+reissue_minute+":00";
    }else{
       if (parseFloat(reissue_hour) == 12)            
            reissue_hour = '00';
       reissue_time = reissue_hour+":"+reissue_minute+":00";
    }
    

//end borj

    
    if (str=='done'){
        if ($J('#date_done'+id).val().length==0){
            alert('Please enter the date done.');   
            $J('#date_done'+id).focus();
            return false;
        }else if ($J('#time_done'+id).val().length==0){
            alert('Please enter the time done.');   
            $J('#time_done'+id).focus();
            return false
        }else{    
            var done_date = Date.parse($J("#date_done"+id).val());
            var done_date2 = $J("#date_done"+id).val();        
            var diff_rec_done = (done_date - received_date);
            
            var done_time;
                
            done_hour = $J('#time_done'+id).val().substring(0,2);
            done_minute = $J('#time_done'+id).val().substring(3,5); 
            
            if ($J('#done_meridian'+id).val()=='PM'){
               if (parseFloat(done_hour) < 12)
                    done_hour = parseFloat(done_hour) + 12;
                    
               done_time = done_hour+":"+done_minute+":00";
            }else{
                if (parseFloat(done_hour) == 12)            
                    done_hour = '00';
            
                done_time = done_hour+":"+done_minute+":00";
            }
                
            if (diff_rec_done < 0){
                alert('Date Done of the blood units must be later than the Received Date of the blood units.');
                $J('#date_done'+id).focus();
                return false;
            }else if (diff_rec_done == 0){
                if(parseFloat(done_time.replace(regExp, "$1$2$3")) < parseFloat(received_time.replace(regExp, "$1$2$3"))){
                    alert("Time Done must be later than the Time Received");
                    return false;
                }else{
                    save_ajax_dateinfo('done', done_date2, done_time, id);
                }
                
            }else{
                save_ajax_dateinfo('done', done_date2, done_time, id);
            }
        }    
    }else if (str=='issuance'){
        if ($J('#date_issuance'+id).val().length==0){
            alert('Please enter the issuance date.');   
            $J('#date_issuance'+id).focus();
            return false;
        }else if ($J('#time_issuance'+id).val().length==0){
            alert('Please enter the issuance time.');   
            $J('#time_issuance'+id).focus();
            return false
        }else{    
            var issuance_date = Date.parse($J("#date_issuance"+id).val());   
            var issuance_date2 = $J("#date_issuance"+id).val();     
            var diff_rec_issuance = (issuance_date - done_date);
            
            var issuance_time;
                
            issuance_hour = $J('#time_issuance'+id).val().substring(0,2);
            issuance_minute = $J('#time_issuance'+id).val().substring(3,5); 
            
            if ($J('#issuance_meridian'+id).val()=='PM'){
               if (parseFloat(issuance_hour) < 12) 
                    issuance_hour = parseFloat(issuance_hour) + 12;
               
               issuance_time = issuance_hour+":"+issuance_minute+":00";
            }else{
               if (parseFloat(issuance_hour) == 12)            
                    issuance_hour = '00';
                     
               issuance_time = issuance_hour+":"+issuance_minute+":00";
            }
            
            if (diff_rec_issuance < 0){
                alert('Issuance Date of the blood units must be later than the Done Date of the blood units.');
                $J('#date_issuance'+id).focus();
                return false;
            }else if (diff_rec_issuance == 0){
                if(parseFloat(issuance_time.replace(regExp, "$1$2$3")) < parseFloat(done_time.replace(regExp, "$1$2$3"))){
                    alert("Issuance Time must be later than the Time Done");
                    return false;
                }else{
                    save_ajax_dateinfo('issuance', issuance_date2, issuance_time, id);
                }
                
            }else{
                save_ajax_dateinfo('issuance', issuance_date2, issuance_time, id);
            }
        }    
        //added by:borj 2013/24/2013
    }    

    else if (str=='returned'){
        if ($J('#date_returned'+id).val().length==0){
            alert('Please enter the returned date.');   
            $J('#date_returned'+id).focus();
            return false;
        }else if ($J('#time_returned'+id).val().length==0){
            alert('Please enter the returned time.');   
            $J('#time_returned'+id).focus();
            return false
        }else{    
            var returned_date = Date.parse($J("#date_returned"+id).val());   
            var returned_date2 = $J("#date_returned"+id).val();     
            var diff_rec_returned = (returned_date - issuance_date);
            
            var returned_time;
                
            returned_hour = $J('#time_returned'+id).val().substring(0,2);
            returned_minute = $J('#time_returned'+id).val().substring(3,5); 
            
            if ($J('#returned_meridian'+id).val()=='PM'){
               if (parseFloat(returned_hour) < 12) 
                    returned_hour = parseFloat(returned_hour) + 12;
               
               returned_time = returned_hour+":"+returned_minute+":00";
            }else{
               if (parseFloat(returned_hour) == 12)            
                    returned_hour = '00';
                     
               returned_time = returned_hour+":"+returned_minute+":00";
}

            if (diff_rec_returned < 0){
                alert('Returned Date of the blood units must be later than the Issuance Date of the blood units.');
                $J('#date_returned'+id).focus();
                return false;
            }else if (diff_rec_returned == 0){
                if(parseFloat(returned_time.replace(regExp, "$1$2$3")) < parseFloat(issuance_time.replace(regExp, "$1$2$3"))){
                    alert("Returned Time must be later than the Time Issuance");
                    return false;
                }else{
                    save_ajax_dateinfo('returned', returned_date2, returned_time, id);
                }
                
            }else{
                save_ajax_dateinfo('returned', returned_date2, returned_time, id);
            }
        }
    }
    else if (str=='reissue'){
        if ($J('#date_reissue'+id).val().length==0){
            alert('Please enter the reissue date.');   
            $J('#date_reissue'+id).focus();
            return false;
        }else if ($J('#time_reissue'+id).val().length==0){
            alert('Please enter the reissue time.');   
            $J('#time_reissue'+id).focus();
            return false
        }else{    
            var reissue_date = Date.parse($J("#date_reissue"+id).val());   
            var reissue_date2 = $J("#date_reissue"+id).val();     
            var diff_rec_reissue = (reissue_date - returned_date);
            
            var reissue_time;
     
            reissue_hour = $J('#time_reissue'+id).val().substring(0,2);
            reissue_minute = $J('#time_reissue'+id).val().substring(3,5); 
     
            if ($J('#reissue_meridian'+id).val()=='PM'){
               if (parseFloat(reissue_hour) < 12) 
                    reissue_hour = parseFloat(reissue_hour) + 12;
               
               reissue_time = reissue_hour+":"+reissue_minute+":00";
            }else{
               if (parseFloat(reissue_hour) == 12)            
                    reissue_hour = '00';
                     
               reissue_time = reissue_hour+":"+reissue_minute+":00";
            }
            
            if (diff_rec_reissue < 0){
                alert('Reissue Date of the blood units must be later than the Returned Date of the blood units.');
                $J('#date_reissue'+id).focus();
                return false;
            }else if (diff_rec_reissue == 0){
                if(parseFloat(reissue_time.replace(regExp, "$1$2$3")) < parseFloat(returned_time.replace(regExp, "$1$2$3"))){
                    alert("Reissue Time must be later than the Time Returned");
                    return false;
                }else{
                    save_ajax_dateinfo('reissue', reissue_date2, reissue_time, id);
                }
                
            }else{
                save_ajax_dateinfo('reissue', reissue_date2, reissue_time, id);
            }
        }
 }
  else  if ((str=='consumed')&&($('date_issuance'+id).value!='')&&($('date_reissue'+id).value=='')){
        if ($J('#date_consumed'+id).val().length==0){
            alert('Please enter the consumed date.');   
            $J('#date_consumed'+id).focus();
            return false;
        }else if ($J('#time_consumed'+id).val().length==0){
            alert('Please enter the consumed time.');   
            $J('#time_consumed'+id).focus();
            return false
        }else{    
            var consumed_date = Date.parse($J("#date_consumed"+id).val());   
            var consumed_date2 = $J("#date_consumed"+id).val();     
            var diff_rec_consumed = (consumed_date - issuance_date);
           
            var consumed_time;
                
            consumed_hour = $J('#time_consumed'+id).val().substring(0,2);
            consumed_minute = $J('#time_consumed'+id).val().substring(3,5); 
            
            if ($J('#consumed_meridian'+id).val()=='PM'){
               if (parseFloat(consumed_hour) < 12) 
                    consumed_hour = parseFloat(consumed_hour) + 12;
               
                consumed_time = consumed_hour+":"+consumed_minute+":00";
            }else{
               if (parseFloat(consumed_hour) == 12)            
                    consumed_hour = '00';
                     
               consumed_time = consumed_hour+":"+consumed_minute+":00";
    }    
            
            if (diff_rec_consumed < 0){
                alert('Consumed Date of the blood units must be later than the Issuance Date of the blood units.');
                $J('#date_consumed'+id).focus();
                return false;
            }else if (diff_rec_consumed == 0){
                if(parseFloat(consumed_time.replace(regExp, "$1$2$3")) < parseFloat(issuance_time.replace(regExp, "$1$2$3"))){
                    alert("Consumed Time must be later than the Time Issuance");
                    return false;
                }else{
                    save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
                }
                
            }else{
                save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
            }
        } 
    //end borj            
}
   else if ((str=='consumed')&&($('date_issuance'+id).value!='')&&($('date_reissue'+id).value!='')){
        
        if ($J('#date_consumed'+id).val().length==0){
            alert('Please enter the consumed date.');   
            $J('#date_consumed'+id).focus();
            return false;
        }else if ($J('#time_consumed'+id).val().length==0){
            alert('Please enter the consumed time.');   
            $J('#time_consumed'+id).focus();
            return false
        }else{    
            var consumed_date = Date.parse($J("#date_consumed"+id).val());   
            var consumed_date2 = $J("#date_consumed"+id).val();     
            var diff_rec_consumed = (consumed_date - reissue_date);

            var consumed_time;

            consumed_hour = $J('#time_consumed'+id).val().substring(0,2);
            consumed_minute = $J('#time_consumed'+id).val().substring(3,5); 
            
            if ($J('#consumed_meridian'+id).val()=='PM'){
               if (parseFloat(consumed_hour) < 12) 
                    consumed_hour = parseFloat(consumed_hour) + 12;
               
                consumed_time = consumed_hour+":"+consumed_minute+":00";
            }else{
               if (parseFloat(consumed_hour) == 12)            
                    consumed_hour = '00';
                     
               consumed_time = consumed_hour+":"+consumed_minute+":00";
            }
     
            if (diff_rec_consumed < 0){
                alert('Consumed Date of the blood units must be later than the Reissue Date of the blood units.');
                $J('#date_consumed'+id).focus();
                return false;
            }else if (diff_rec_consumed == 0){
                if(parseFloat(consumed_time.replace(regExp, "$1$2$3")) < parseFloat(reissue_time.replace(regExp, "$1$2$3"))){
                    alert("Consumed Time must be later than the Time Reissue");
                    return false;
                }else{
                    save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
                }
     
            }else{
                save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
            }
 }
    }    
}

 
function refreshFrame(outputResponse){
    alert(""+outputResponse);
    window.location.reload();
}


function getCurrentDate(id){
    if ($J('#is_issued'+id).is(":checked")){
                

        if ($('date_consumed'+id).value==''){
            $('date_consumed'+id).value=$('current_date').value;
            $('time_consumed'+id).value = $('current_time').value;
            $('consumed_meridian'+id).value=$('current_meridian').value;

        if ($('date_issuance'+id).value==''){
             $date_issuance = date("m/d/Y",strtotime($row_status['issuance_date']));
             $time_issuance = date("h:i",strtotime($row_status['issuance_date']));
             $issuance_meridian = date("A",strtotime($row_status['issuance_date']));
        }
    }else{
       $date_issuance = '';
       $time_issuance = '';
       $issuance_meridian = 'AM';
             
    }        
}
}

//Added by Nick, 11/23/2013
function openPrintStubDialog(){
    $J("#printClaimStubDialog").dialog("open");    
}

//for claim stub print with dialog
function printClaimStub(refno,cmCheck,coombsCheck,compCheck,duCheck,cmVal,coombsVal,compVal,duVal,others){
    var url = "seg-print-claim-stub.php?refno="+refno+"&cmCheck="+cmCheck+
              "&coombsCheck="+coombsCheck+"&compCheck="+compCheck+
              "&duCheck="+duCheck+"&cmVal="+cmVal+"&coombsVal="+coombsVal+
              "&compVal="+compVal+"&duVal="+duVal+"&others="+others;
    window.open(url,'Rep_Gen','menubar=no,directories=no');
}

function printClaimStub2(refno){
    var url = "seg-print-claim-stub2.php?refno="+refno;
    window.open(url,'Rep_Gen','menubar=no,directories=no');
}
//block letters in a textbox
function key_check(e, value){
    if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
        return true;
    }else 
        return false;
}

//added by: borj
//2013/23/11
function getReissueCurrentDate(id){
    if ($J('#is_reissue'+id).is(":checked")){
        if ($('date_reissue'+id).value==''){
            $('date_reissue'+id).value=$('current_date').value;
            $('time_reissue'+id).value = $('current_time').value;
            $('reissue_meridian'+id).value=$('current_meridian').value;
        }
    }else{
        $('date_reissue'+id).value= '';
        $('time_reissue'+id).value = '';
        $('reissue_meridian'+id).value= 'AM';
    }        
}

function getConsumedCurrentDate(id){
    if ($J('#is_consumed'+id).is(":checked")){
        if ($('date_consumed'+id).value==''){
            $('date_consumed'+id).value=$('current_date').value;
            $('time_consumed'+id).value = $('current_time').value;
            $('consumed_meridian'+id).value=$('current_meridian').value;
        }
    }else{
        $('date_consumed'+id).value= '';
        $('time_consumed'+id).value = '';
        $('consumed_meridian'+id).value= 'AM';
    }        
}

function getReturnReason(mode, dateinfo, timeinfo, id){
         var reasonBloodSample
         returnReason = prompt("Please put the reason.");

        if (returnReason)
            save_ajax_dateinfo(mode, dateinfo, timeinfo, id, returnReason);
           
        else
            save_ajax_dateinfo(mode, dateinfo, timeinfo, id, returnReason);

    if ($J('#is_returnedreason'+id).is(":checked")){
         $('date_returned_show'+id).style.display = "block";
        if($('date_returned'+id).value==''){
           $('date_returned'+id).value=$('current_date').value;
           $('time_returned'+id).value=$('current_time').value;
           $('returned_meridian'+id).value=$('current_meridian').value;
        
           $J('#is_returnedreason'+id).attr('title',returnReason);
        }
    }else{
        $('date_returned'+id).value= '';
        $('time_returned'+id).value = '';
        $('returned_meridian'+id).value= 'AM';
        $('date_returned_show'+id).style.display = "none";
    }
}

function save_ajax_dateinfo(mode, dateinfo, timeinfo, id, return_reason){
     var refno = $('refno').innerHTML;
     var service_code = $('test_code').innerHTML;
     var index = id.replace(service_code,'');
        
     xajax_save_dateinfo(refno, service_code, index, mode, dateinfo, timeinfo, return_reason); 
 }
 //added by: borj 2013/29/11
// function showConsumedUI(id){
//     $J('#date_consumed_show'+id).css({
//         'display':'block'
//     });
// }

// function showReissuedUI(id){
//     $J('#date_reissue_show'+id).css({
//         'display':'block'
//     });
// }

function getDoneCurrentDate(id){
    if ($J('#is_done'+id).is(":checked")){
        if ($('date_done'+id).value==''){
            $('date_done'+id).value=$('current_date').value;
            $('time_done'+id).value = $('current_time').value;
            $('done_meridian'+id).value=$('current_meridian').value;
        }
    }else{
        $('date_done'+id).value= '';
        $('time_done'+id).value = '';
        $('done_meridian'+id).value= 'AM';
    }        
}

function getIssuanCurrentDate(id){
    if ($J('#is_issued'+id).is(":checked")){
        if ($('date_issuance'+id).value==''){
            $('date_issuance'+id).value=$('current_date').value;
            $('time_issuance'+id).value = $('current_time').value;
            $('issuance_meridian'+id).value=$('current_meridian').value;
        }
    }else{
        $('date_issuance'+id).value= '';
        $('time_issuance'+id).value = '';
        $('issuance_meridian'+id).value= 'AM';
    }        
}

//end borj