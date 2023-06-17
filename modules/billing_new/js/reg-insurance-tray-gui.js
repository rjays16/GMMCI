var member, pageParams = null;

function showVerificationResult(data) {
    // if( window.parent.$('frombilling'))
    //    var bill = '1';
    // else
    //    var bill = '0';
    
    if (window.parent.$('encounter_nr')){
        var enc = window.parent.$('encounter_nr').value;
    } else {
        var pid = $('pid').value;
        var enc ='';
    }
      
    
	var urlholder = '../../modules/eclaims/reports/cewsreport.php?data='+encodeURIComponent(data)+'&frombilling='+getPageParam('frombilling')+'&enc='+enc+'&pid='+pid;

	nleft = (screen.width - 680)/2;
	ntop = (screen.height - 520)/2;
	printwin = window.open(urlholder, "Verification Result", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
	return true;
}

function prepareAdd(id, bjustadded, data) {

	var details = new Object();
	details.id = $('id'+id).value;
	details.name = $('name'+id).innerHTML;

	if (data) {
		details.principal_id = data.pid;
		details.last_name = data.last_name;
		details.first_name = data.first_name;
		details.middle_name = data.middle_name;
		details.street = data.street;
		details.infosrc = data.infosrc;
		details.is_updated = 1;
		details.nr = data.insurance_nr;
        details.trackingno = '';
//		details.barangay = data.barangay;
//		details.municipality = data.municipality;
	}
	else {
		details.principal_id = '';
		details.last_name = '';
		details.first_name = '';
		details.middle_name = '';
		details.street = '';
		details.infosrc = 2;
		details.is_updated = 0;
		details.nr= $('nr'+id).value;
        details.trackingno = '';
//		details.barangay = '';
//		details.municipality = '';
	}

	//alert($('isPrincipal'+id).checked);
	if ($('isPrincipal'+id).checked){
		details.isPrincipal2 = "YES";
		details.isPrincipal = 1;
	}else{
		details.isPrincipal2 = "NO";
		details.isPrincipal = 0;
	}
	//alert("details.isPrincipal2 = "+details.isPrincipal2);
	var list = window.parent.document.getElementById('order-list');
	result = window.parent.appendOrder(list,details, bjustadded);
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

//function enableButtonAdd(id){
//	document.getElementById('add_insurance'+id).disabled=false;
//}

//function disableButtonAdd(id){
//	document.getElementById('add_insurance'+id).disabled=true;
//}

//function enableGet(id){
//    document.getElementById('get'+id).disabled=false;
//}

//function disableGet(id){
//    document.getElementById('get'+id).disabled=true;
//}

function getPageParam(param)
{
    if (pageParams === null) {
        var hash,
            hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

        pageParams = [];

        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            pageParams.push(hash[0]);
            pageParams[hash[0]] = hash[1];
        }
    }
    if ('undefined' !== typeof pageParams[param]) {
        return pageParams[param];
    } else {
        return null;
    }
}

function disableButtonAdd(id){
	//alert("enableButtonAdd = "+id);
	document.getElementById('add_insurance'+id).disabled=true;
	document.getElementById('isPrincipal'+id).disabled=true;
}


function addProductToList(listID, id, firm_id, name,insurance_nr, is_principal) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {

				alt = (dRows.length%2)+1;
				var check, disable;

				if (insurance_nr)
					disable = "";
				else
					disable = "disabled";

				if (is_principal==1)
					check = "checked";
				else
					check = "";


                disableCheck = (firm_id !== 'PhilHealth' || is_principal == 0);
				rowSrc = "<tr>"+
                            '<td>'+
                            '<input type="hidden" name="member-lname'+id+'" id="member-lname'+id+'" value="">'+
                            '<input type="hidden" name="member-fname'+id+'" id="member-fname'+id+'" value="">'+
                            '<input type="hidden" name="member-mname'+id+'" id="member-mname'+id+'" value="">'+
                            '<input type="hidden" name="member-suffx'+id+'" id="member-suffx'+id+'" value="">'+
                            '<input type="hidden" name="member-bdate'+id+'" id="member-bdate'+id+'" value="">'+
                            '<input type="hidden" name="prelation'+id+'" id="prelation'+id+'" value="">'+
                            '<input type="hidden" name="member-type'+id+'" id="member-type'+id+'" value="">'+
                            '<input type="hidden" name="memployerno'+id+'" id="memployerno'+id+'" value="">'+
                            '<input type="hidden" name="memployernm'+id+'" id="memployernm'+id+'" value="">'+
                                '<div id="name'+id+'" style="font:bold 12px Arial;color:#660000">'+firm_id+'</div>'+
                                '<div id="desc'+id+'" style="font:normal 11px Arial; color:#003366">'+name+'</div>'+
									'</td>'+
                            '<td align="center" style="white-space:nowrap">'+
                                '<input type="hidden" id="id'+id+'" name="id'+id+'" value="'+id+'">'+
                                '<input class="segInput insurance-no" id="nr'+id+'" align="right" type="text" style="width:90%" value="'+insurance_nr+'" offKeyUp="if (this.value.length >= 1) enableButtonAdd(\''+id+'\'); else disableButtonAdd(\''+id+'\');" style="text-align:right"/>'+
									'</td>'+
                            '<td align="center"><input class="is-principal" id="isPrincipal'+id+'" align="right" type="checkbox" '+check+' /></td>'+
                            '<td style="white-space:nowrap">'+
                                '<button class="segButton" name="add_insurance'+id+'" id="add_insurance'+id+'" '+
                                    'data-provider="' + id + '" '+
                                    'onclick="prepareHolderData(this); return false;" '+
                                '><img src="../../gui/img/common/default/text_list_bullets.png" />Details</button>'+
									'</td>'+
								'</tr>';

								//'<td width="25%" align="center"><input id="nr'+id+'" align="right" type="text" style="width:90%" value="" style="text-align:right" onblur="this.value = isNaN(parseFloat(this.value))?\'\':parseFloat(this.value)"/></td>'+
		}
		else {
			rowSrc = '<tr><td colspan="9" style="">No such insurance firm exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}
function prepareHolderData(obj) {
    var $this = $J(obj),
        providerId = $this.data('provider'),
        root = $this.parent().parent(),
        insuranceNo = root.find('.insurance-no').first(),
        isPrincipal = root.find('.is-principal').first();

    var holderId = insuranceNo.val(),
        isPrincipal = isPrincipal.is(':checked') ? 1 : 0;

    promptMemberInfo(providerId, holderId, isPrincipal);
}

function promptMemberInfo(providerId, holderId, isPrincipal){
	// load the member form using ajax
	jQuery.get("../../modules/billing_new/seg-memberinfo.php",
        {
            id: holderId,
            provider: providerId,
            principal: isPrincipal,
            pid: getPageParam('pid'),
            encounter_nr: getPageParam('encounter_nr')
        },
		function(data){
			// create a modal dialog with the data
			jQuery(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
                modal: true,
				overlayId: 'modal-overlay',
				containerId: 'member-container',
                position: ['5%'],
				onOpen: member.open,
				onShow: member.show,
				onClose: member.close
			});
		}
	);
}

jQuery(function($) {
	member = {
		messages: [],
		open: function (dialog) {
			dialog.overlay.fadeIn(200, function () {
				dialog.container.fadeIn(200, function () {
					dialog.data.fadeIn(200, function () {});
				});
			});
		},
        isFormComplete: function(scenario) {
            var checkClass = 'required-' + scenario,
                emptyInputs = $J('#member-container .'+checkClass+':not([disabled])').filter(function() { return !$J(this).val() });
            if (emptyInputs.size()) {
                emptyInputs.each(function(i, item) {
                    member.addMessage($J(item).prev().text() + ' cannot be blank');
                });
                //alert(member.messages.join('\n'));
                return false;
            } else {
                return true;
            }
        },
        getPIN: function(){
            member.clearMessages();
            if (this.isFormComplete('get')) {
                // Send request
                $J.get("ajax/check_in.php", {
                    provider: $J('#provider').val(),
                    lastname: $J('#member-lastname').val(),
                    firstname: $J('#member-firstname').val(),
                    middlename: $J('#member-middlename').val(),
                    suffix: $J('#member-suffix').val(),
                    birthdate: $J('#member-birthdate').val(),
                }, function(data){
                    if (data) {        
                        if (data.match && data.match(/[0-9\-]+/)) {
                            $('#member-id').val(data);
                        } else {                                           ;
                            //alert('Error: ' + data);
                            member.clearMessages();
                            member.addMessage(data);
                            member.showMessages();
                        }
                    } else {
                        alert('Error: No data available');
                        return false;
                    }
                }).error(function(problem) {
                    member.clearMessages();
                    if (problem.responseText) {
                        //alert('Error: ' + problem.responseText);
                        member.addMessage(problem.responseText);
                    } else {
                        //alert('Error: ' + problem.statusText);
                        member.addMessage(problem.statusText);
                    }
                    member.showMessages();
                }).always(function() {

                });
            } else {
                member.showMessages();
            }
        },
        verify: function() {
            if (this.isFormComplete('verify')) {
                var params = {};
                $J.each($J('#member-form').serializeArray(), function(index,value) {
                    params[value.name] = value.value;
                });
                params['pIsFinal'] = getPageParam('frombilling');  
              
                $J.get("ajax/verify_eligibility.php", params,
                    function (data){
                        //alert(data);
                        showVerificationResult(data);
                    }
                ).error(function(problem) {
                    member.clearMessages();
                    if (problem.responseText) {
                        //alert('Error: ' + problem.responseText);
                        member.addMessage(problem.responseText);
                    } else {
                        //alert('Error: ' + problem.statusText);
                        member.addMessage(problem.statusText);
                    }
                    member.showMessages();
                });
            } else {
                member.showMessages();
            }
        },
//        checkSPC: function(){
//            member.clearMessages();
//            if (this.isFormComplete('check')) {
//                 Send request
//                $J.get("ajax/check_single_period.php", {
                
//                }, function(data){
//                    if (data) {
//                        if (data.match && data.match(/[0-9\-]+/)) {
//                            $('#member-id').val(data);
//                        } else {
//                            alert('Error: ' + data);
//                            member.clearMessages();
//                            member.addMessage(data);
//                            member.showMessages();
//                        }
//                    } else {
//                        alert('Error: No data available');
//                        return false;
//                    }
//                }).error(function(problem) {
//                    member.clearMessages();
//                    if (problem.responseText) {
//                        alert('Error: ' + problem.responseText);
//                        member.addMessage(problem.responseText);
//                    } else {
//                        alert('Error: ' + problem.statusText);
//                        member.addMessage(problem.statusText);
//                    }
//                    member.showMessages();
//                }).always(function() {

//                });
//            } else {
//                member.showMessages();
//            }
//        },
        save: function() {
            if (this.isFormComplete('save')) {
                var params = {};
                $J.each($J('#member-form').serializeArray(), function(index,value) {
                    params[value.name] = value.value;
                });
                // CHEATER!!!!
                params['class_nr'] = 2;//window.parent.jQuery('[name=insurance_class_nr]:checked').val();
                //alert(params['class_nr'])
                $J.get("../../modules/registration_admission/ajax/save_membership_info.php", params,
                    function (data) {
                            var provId = $J('#provider').val();
                            window.parent.addInsuranceRow({
                                providerID: provId,
                                providerName: $J('#name'+provId).text(),
                                insuranceNo: $J('#member-id').val(),
                                isPrincipal: $J('#isPrincipal'+provId).is(':checked') ? true : false
                            }, 1);
                            member.clearMessages();
                            window.parent.location.reload();   
                    }
                ).error(function(problem) {
                    member.clearMessages();
                    if (problem.responseText) {
                        //alert('Error: ' + problem.responseText);
                        member.addMessage(problem.responseText);
                    } else {
                        //alert('Error: ' + problem.statusText);
                        member.addMessage(problem.statusText);
                    }
                    member.showMessages();
                });
            } else {
                member.showMessages();
            }
        },
		show: function(dialog) {
            $('#member-get').click(function(e) {
                e.preventDefault();
                member.getPIN(dialog);
            });
            $('#member-verify').click(function(e) {
                e.preventDefault();
                member.verify(dialog);
            });
            $('#member-save').click(function(e) {
                e.preventDefault();
                member.save(dialog);
//                // validate form
//                var msg = '';
//                if (member.validate()) {
//                    msg = $('#modal-container .modal-message');
//                    msg.fadeOut(function () {
//                        msg.removeClass('message-error').empty();
//                    });
//                    $('#member-container .member-title').html('Verifying PHIC eligibility ...');
//                    $('#member-container form').fadeOut(200);
//                    $('#member-container .member-content').animate({
//                        height: '80px'
//                    }, function () {
//                        var id = $('#member-container #rowid').val();
//                        $('#member-lname'+id).val($('#member-container #member-lastname').val());
//                        $('#member-fname'+id).val($('#member-container #member-firstname').val());
//                        $('#member-mname'+id).val($('#member-container #member-middlename').val());
//                        $('#member-suffx'+id).val($('#member-container #member-suffix').val());
//                        $('#member-bdate'+id).val($('#member-container #member-birthdate').val());
//                        $('#prelation'+id).val($('#member-container #patient-relation').val());
//                        $('#member-type'+id).val($('#member-container #membership-type').val());
//                        $('#memployerno'+id).val($('#member-container #member-pempno').val());
//                        $('#memployernm'+id).val($('#member-container #member-pempnm').val());
//                        verifyEligibility(id, false);
//                        member.close(dialog);
//                    });
//                }
//                else {
//                    if ($('#member-container .modal-message:visible').length > 0) {
//                        msg = $('#member-container .modal-message div');
//                        msg.fadeOut(200, function () {
//                            msg.empty();
//                            member.showError();
//                            msg.fadeIn(200);
//                        });
//                    }
//                    else {
//                        member.showError();
//                    }

//                }
            });

            $('#member-cancel').click(function (e) {
                e.preventDefault();
                member.close(dialog);
            });
		},
		close: function (dialog) {
            $('.simplemodal-data button').attr('disabled', 'disabled');
			$('#member-container .modal-message').fadeOut();
			$('#member-container form').fadeOut(200);
			$('#member-container .modal-content').animate({
				height: 40
			}, function () {
				dialog.data.fadeOut(200, function () {
//					dialog.container.fadeOut(200, function () {
                    dialog.overlay.fadeOut(200, function () {
                        $.modal.close();
                    });
//					});
				});
			});
		},
		error: function (xhr) {
			alert(xhr.statusText);
		},

        addMessage: function (msg) {
            member.messages.push(msg);
        },

        clearMessages: function () {
            member.messages = [];
            $('#member-container .modal-message').text('');
        },

		showMessages: function () {
            var msg = $('<div class="message-error"></div>');
            msg.append(member.messages.join('<br/>'));
            msg.click(function(){
                var $this=$(this);
                $this.fadeOut(200);
            });
			$('#member-container .modal-message')
                .hide()
			    .append(msg)
			    .fadeIn(200);
		}
	};
});