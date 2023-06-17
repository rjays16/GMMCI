var ISFINALBILL = false;
var FLAG = '';

$(document).ready(function(){
    if($('#isFinalBill').val() == 1)
        ISFINALBILL = true;
    FLAG = $('#request_flag').val();

    var sa_form = $('#search-anesthesia-form');
    var at_table = $('#anesthesia-search-result');
    var a_input = $('#OrAnesthesia_anest_name');
    var icpm_form = $('#search-icpm-form');
    var med_form = $('#search-med-form');
    var misc_form = $('#search-misc-form');

    var package_search_result = $('#package-search-result');
    var package_search_text = $('#OrRequest_package_search_text');
    var prenended_icon = $('.input-prepend .add-on .loader');
    var search_packages_form = $('#search-packages-form');

    var orTechniqueDD = $('#OrPostOpDetails_technique_id');
    var orTechniqueTA = $('#renderTechDesc');

    load_anesthesia(a_input,at_table);
    loadPackages(package_search_result,package_search_text,prenended_icon);

    orTechniqueDD.on('change', function(){
        var $this = $(this),
            val = $this.val();

        if(val == '')
            CKEDITOR.instances['OrPostOpDetails_technique_desc'].setData('');
        else
            $.ajax({
                url: base_url+'/index.php?r=or/orTechnique/getDesc/id/'+val,
                success: function(data){
                    CKEDITOR.instances['OrPostOpDetails_technique_desc'].setData(data);
                }
            })
    });

    sa_form.on('submit',function(){
        load_anesthesia(a_input,at_table);

        return false;
    });

    icpm_form.on('submit', function(){
        load_icpm();

        return false;
    });

    med_form.on('submit', function(){
        load_med();

        return false;
    });

    search_packages_form.on('submit', function(){
        loadPackages(package_search_result,package_search_text,prenended_icon);
        return false;
    });

    misc_form.on('submit', function(){
        load_misc();
        return false;
    });

    $.ajax({
        url: base_url+'/index.php?r=or/orRequest/listPackageMed/id/'+$('#OrPostOpDetails_or_refno').val(),
        dataType: 'json',
        success: function(data){
            var medsup_out_table = $('#medsup-table');
            var medsup_out_table_block = medsup_out_table.find('tbody');
            var read_only;

            if(!$.isEmptyObject(data)){
                if(medsup_out_table_block.find('#default-entry').length !== 0){
                    medsup_out_table_block.html('');
                }

                if(ISFINALBILL)
                    read_only = 'readonly="readonly"';
                else
                    read_only = '';

                $.each(data,function(index, val){
                    var field_block = '<tr>'
                        +'<td>'
                        +'<input type="hidden" name="OrPostOpDetails[ormedsup][id][]" value="'+val.id+'" /> '
                        +val.id+'</td>'
                        +'<td>'+val.name+'</td>'
                        +'<td><input class="span12" name="OrPostOpDetails[ormedsup][qty][]" type="text" '+read_only+' onchange="javascript:calculateTotal(this)" value="'+val.qty+'"></td>'
                        +'<td class="price"><input class="span12" name="OrPostOpDetails[ormedsup][price][]" type="text" readonly="readonly" value="'+val.price+'"></td>'
                        +'<td class="total"><input class="span12" name="OrPostOpDetails[ormedsup][total][]" type="text" readonly="readonly" value="'+(val.price*val.qty)+'"></td>';

                    if(!ISFINALBILL)
                        field_block += '<td><a class="pull-right btn btn-mini" onclick="javascript:deleteMeds(this);"><i class="icon-remove"></i></a></td>';
                        
                    field_block += '</tr>';

                    medsup_out_table_block.append(field_block);
                });
            }
        }
    });

    $.ajax({
        url: base_url+'/index.php?r=or/orRequest/listExtMisc/id/'+$('#OrPostOpDetails_or_refno').val(),
        dataType: 'json',
        success: function(data){
            var misc_out_table = $('#misc-table');
            var misc_out_table_block = misc_out_table.find('tbody');
            var read_only;

            if(!$.isEmptyObject(data)){
                console.log(data);
                if(misc_out_table_block.find('#default-entry').length !== 0){
                    misc_out_table_block.html('');
                }

                $.each(data,function(index, val){

                    if(ISFINALBILL)
                        read_only = 'readonly="readonly"';
                    else
                        read_only = '';

                    var field_block = '<tr>'
                        +'<td>'
                        +'<input type="hidden" name="OrPostOpDetails[orMisc][id][]" value="'+val.id+'" /> '
                        +val.id+'</td>'
                        +'<td>'+val.name+'</td>'
                        +'<td><input class="span12" name="OrPostOpDetails[orMisc][qty][]" '+read_only+' type="text" onchange="javascript:calculateTotal(this)" value="'+val.qty+'"></td>'
                        +'<td class="price"><input class="span12" name="OrPostOpDetails[orMisc][price][]" type="text" readonly="readonly" value="'+val.price+'"></td>'
                        +'<td class="total"><input class="span12" name="OrPostOpDetails[orMisc][total][]" type="text" readonly="readonly" value="'+(val.qty * val.price)+'"></td>';

                    if(!ISFINALBILL)
                        field_block += '<td><a class="pull-right btn btn-mini" onclick="javascript:deleteMisc(this);"><i class="icon-remove"></i></a></td>';
                        
                    field_block += '</tr>';

                    misc_out_table_block.append(field_block);
                });
            }
        }
    });

    $.ajax({
        url:base_url+'/index.php?r=or/orRequest/getExtPackage/or_refno/'+$('#OrPostOpDetails_or_refno').val(),
        dataType:'json',
        success:function(data){
            var request_package_list = $('#request-package-list');

            $.each(data, function(key, value){
                getPackageData(value.package_id);
            });
        }
    });

    if(FLAG == 'done'){
        $.ajax({
            url: base_url+'/index.php?r=or/orRequest/listExtIcpm/id/'+$('#OrPostOpDetails_or_refno').val(),
            dataType: 'json',
            success: function(data){
                var icpm_out_table = $('#icpm-table');
                var icpm_out_table_block = icpm_out_table.find('tbody');

                if(!$.isEmptyObject(data)){
                    if(icpm_out_table_block.find('#default-entry').length !== 0){
                        icpm_out_table_block.html('');
                    }

                    var display = 'style="display:none;"';
                    var field_block = '';
                    var isLateral = false;
                    var ic_laterality = 0;
                    $.each(data,function(index, val){
                        if(val.laterality == ''){
                            isLateral = false;
                            ic_laterality = 0;
                        }
                        else{
                            isLateral = true;
                            ic_laterality = 1;
                        }
                            
                        display = 'style="display:none;"';
                        if(isLateral)
                            display = '';

                        field_block = '<tr>'
                            +'<td>'
                            +'<input type="hidden" name="OrPostOpDetails[orIcpm][code][]" value="'+val.code+'" /> '
                            +'<input type="hidden" name="OrPostOpDetails[orIcpm][for_laterality][]" value="'+ic_laterality+'" /> '
                            +val.code+'</td>'
                            +'<td>'+val.name+'</td>'
                            + '<td><select class="icpm-laterality" name="OrPostOpDetails[orIcpm][laterality][]" '+display+'>'
                            + '<option value="L">Left</option>'
                            + '<option value="R">Right</option>'
                            + '<option value="B">Both</option>'
                            + '</select></td>';

                        if(!ISFINALBILL)
                            field_block += '<td><a class="pull-right btn btn-mini" onclick="javascript:deleteIcpm(this);"><i class="icon-remove"></i></a></td>';
                            
                        field_block += '</tr>';
                        icpm_out_table_block.append(field_block);

                        if(isLateral)
                            icpm_out_table_block.last().find('.icpm-laterality').val(val.laterality);
                    });
                }
            }
        });

        $.ajax({
            url: base_url+'/index.php?r=or/orRequest/listExtAnesthesia/id/'+$('#OrPostOpDetails_or_refno').val(),
            dataType: 'json',
            success: function(data){
                var ap_table = $('#anesthesia-procedures');
                var ap_table_block = ap_table.find('tbody');

                if(!$.isEmptyObject(data)){
                    if(ap_table_block.find('#default-entry').length !== 0){
                        ap_table_block.html('');
                    }

                    $.each(data,function(index, val){
                        var field_block = '<tr>'
                            +'<td>'
                            +'<input type="hidden" name="OrPostOpDetails[orAnesthesiaUses][anesth_id][]" value="'+val.id+'" /> '
                            +val.name+'</td>'
                            +'<td>'+val.cat+'</td>'
                            +'<td><div class="bootstrap-timepicker input-append"><input hint="Start Time" class="no-user-select input-small operation-time" name="OrPostOpDetails[orAnesthesiaUses][time_begun][]" type="text" value="'+val.tStart+'"><span class="add-on"><i class="icon-time"></i></span></div></td>'
                            +'<td><div class="bootstrap-timepicker input-append"><input hint="Start Time" class="no-user-select input-small operation-time" name="OrPostOpDetails[orAnesthesiaUses][time_end][]" type="text" value="'+val.tEnd+'"><span class="add-on"><i class="icon-time"></i></span></div></td>'
                            +'<td><a class="pull-right btn btn-mini" onclick="javascript:deleteAnes(this);"><i class="icon-remove"></i></a></td>'
                            +'</tr>';

                        ap_table_block.append(field_block);
                    });

                    $('.operation-time').timepicker();
                }
            }
        });
    }
});

function loadPackages(package_search_result,package_search_text,prenended_icon)
{
    prenended_icon.html('<img alt="loader" src="'+base_url+'/css/frontend/images/small_loader.gif" class="ajax-loader" />');
    $.ajax({
        url:base_url+'/index.php?r=or/packages/jsonList/name/'+package_search_text.val(),
        dataType:'json',
        success:function(data){
            if(data.length > 0){
                package_search_result.show();
                var package_search_block = package_search_result.find('tbody');
                package_search_block.html('');
                $.each(data, function(index, package){
                    var package_result = "<tr>"
                        +'<td><input type="hidden" class="package-id" value="'+package.package_id+'" />'+package.package_id+'</td>'
                        +"<td>"+package.package_name+"</td>"
                        +"<td>"+package.package_price+"</td>"
                        +'<td><a class="btn btn-mini" onclick="javascript:getPackage(this);" data-dismiss="modal">OK</a></td>'
                        +"</tr>";
                    package_search_block.append(package_result);
                });
            }
            else{
                package_search_result.hide();
            }
            prenended_icon.html('<i class="icon-search"></i>');
        }
    });
}

function getPackage(thisBtn){
    var package_id = $(thisBtn).parent().parent().find('.package-id').val();
    getPackageData(package_id);
}

function getPackageData(package_id){
    var request_package_list = $('#request-package-list');
    var package_counter_field = $('#package-counter');

    if(request_package_list.css('height')==='0px'){
        request_package_list.css('height', "");
    }

    $.ajax({
        url:base_url+'/index.php?r=or/packages/requestPackage/package_id/'+package_id,
        success:function(package){
            request_package_list.append(package);
        }
    });
}

function deletePackage(thisBtn){
    $(thisBtn).parent().remove();
}

function load_anesthesia(a_input,at_table)
{
    $.ajax({
        url:base_url+'/index.php?r=or/orAnesthesia/listAnesthesia&token='+a_input.val(),
        dataType:'json',
        success:function(data){
            at_table.show();
            var at_table_tobdy = at_table.find('tbody');
            at_table_tobdy.html('');
            if(!$.isEmptyObject(data)){
                $.each(data,function(index, anesthesia){
                    var a_block = "<tr>"
                        +"<td>"
                        +"<input type='hidden' class='a-id' value='"+anesthesia.anesth_id+"' />"
                        +"<input type='hidden' class='a-name' value='"+anesthesia.anest_name+"' />"
                        +"<input type='hidden' class='a-cat' value='"+anesthesia.anest_category+"' />"
                        +anesthesia.anest_name
                        +"</td>"
                        +"<td>"+anesthesia.anest_category+"</td>"
                        +'<td><a class="btn btn-mini" onclick="javascript:add_anesthesia(this);" data-dismiss="modal">OK</a></td>'
                        +"</tr>";
                    at_table_tobdy.append(a_block);
                });
            }
            else
            {
                at_table.hide();
            }
        }
    });
}

function load_icpm(){
    var icpm_desc = $('#OpsRvs_description');
    var icpm_code = $('#OpsRvs_code');
    var icpm_table = $('#icpm-search-result');
    var icpm_search_btn = $('#icpm-search-btn');
    var params = '';

    if(icpm_code.val() != '')
        params += '/code/'+icpm_code.val();
    if(icpm_desc.val() != '')
        params += '/desc/'+icpm_desc.val();

    var icon = icpm_search_btn.find('i');

    $.ajax({
        url: base_url+'/index.php?r=or/orRequest/listIcpm'+params,
        dataType: 'json',
        beforeSend: function(){
            icon.removeClass();
            icon.addClass('icon-refresh fa-spin');
        },
        success: function(data){
            icpm_table.show();
            var icpm_table_tobdy = icpm_table.find('tbody');
            icpm_table_tobdy.html('');
            if(!$.isEmptyObject(data)){
                $.each(data,function(key, value){
                    var a_block = "<tr>"
                        +"<td>"
                            +"<input type='hidden' class='ic-id' value='"+value.package_id+"' />"
                            +"<input type='hidden' class='ic-code' value='"+value.code+"' />"
                            +"<input type='hidden' class='ic-name' value='"+value.description+"' />"
                            +"<input type='hidden' class='ic-rvu' value='"+value.rvu+"' />"
                            +"<input type='hidden' class='ic-laterality' value='"+value.laterality+"' />"
                            + value.code
                        +"</td>"
                        +"<td>"
                            + value.description
                        +"</td>"
                        +'<td><a class="btn btn-mini" onclick="javascript:add_icpm(this);" data-dismiss="modal">OK</a></td>'
                        +"</tr>";
                    icpm_table_tobdy.append(a_block);
                });
            }
            else
            {
                icpm_table.hide();
            }

            icon.removeClass();
            icon.addClass('icon-search');
        }
    })
}

function load_med(){
    var med_gen = $('#PharmaProductsMain_artikelname');
    var med_table = $('#med-search-result');
    var params = '';

    if(med_gen.val() != '')
        params += '/name/'+med_gen.val();

    $.ajax({
        url: base_url+'/index.php?r=or/orRequest/listMed'+params,
        dataType: 'json',
        success: function(data){
            med_table.show();
            var med_table_tobdy = med_table.find('tbody');
            med_table_tobdy.html('');
            if(!$.isEmptyObject(data)){
                $.each(data,function(key, value){
                    var name = value.artikelname + " " + value.generic
                    var a_block = "<tr>"
                        +"<td>"
                            +"<input type='hidden' class='medsup-id' value='"+value.bestellnum+"' />"
                            +"<input type='hidden' class='medsup-name' value='"+ name +"' />"
                            +"<input type='hidden' class='medsup-price' value='"+value.price_cash+"' />"
                            + value.bestellnum
                        +"</td>"
                        +"<td>"
                            + name
                        +"</td>"
                        +'<td><a class="btn btn-mini" onclick="javascript:add_med(this);">OK</a></td>'
                        +"</tr>";
                    med_table_tobdy.append(a_block);
                });
            }
            else
            {
                med_table.hide();
            }
        }
    })
}

function load_misc(){
    var misc_name = $('#OtherServices_name');
    var misc_table = $('#misc-search-result');
    var params = '';

    if(misc_name.val() != '')
        params += '/name/'+misc_name.val();

    $.ajax({
        url: base_url+'/index.php?r=or/orRequest/listMisc'+params,
        dataType: 'json',
        success: function(data){
            misc_table.show();
            var misc_table_tobdy = misc_table.find('tbody');
            misc_table_tobdy.html('');
            if(!$.isEmptyObject(data)){
                $.each(data,function(key, value){
                    var a_block = "<tr>"
                        +"<td>"
                            +"<input type='hidden' class='misc-id' value='"+value.service_code+"' />"
                            +"<input type='hidden' class='misc-name' value='"+ value.name +"' />"
                            +"<input type='hidden' class='misc-price' value='"+value.price+"' />"
                            + value.service_code
                        +"</td>"
                        +"<td>"
                            + value.name
                        +"</td>"
                        +'<td><a class="btn btn-mini" onclick="javascript:add_misc(this);">OK</a></td>'
                        +"</tr>";
                    misc_table_tobdy.append(a_block);
                });
            }
            else
            {
                misc_table.hide();
            }
        }
    })
}


function add_anesthesia(thisBtn)
{
    var a_id = $(thisBtn).parent().parent().find('.a-id').val();
    var a_name = $(thisBtn).parent().parent().find('.a-name').val();
    var a_cat = $(thisBtn).parent().parent().find('.a-cat').val();

    var ap_table = $('#anesthesia-procedures');
    var ap_table_block = ap_table.find('tbody');

    if(ap_table_block.find('#default-entry').length !== 0){
        ap_table_block.html('');
    }

    var field_block = '<tr>'
        +'<td>'
        +'<input type="hidden" name="OrPostOpDetails[orAnesthesiaUses][anesth_id][]" value="'+a_id+'" /> '
        +a_name+'</td>'
        +'<td>'+a_cat+'</td>'
        +'<td><div class="bootstrap-timepicker input-append"><input hint="Start Time" class="no-user-select input-small operation-time" name="OrPostOpDetails[orAnesthesiaUses][time_begun][]" type="text"><span class="add-on"><i class="icon-time"></i></span></div></td>'
        +'<td><div class="bootstrap-timepicker input-append"><input hint="Start Time" class="no-user-select input-small operation-time" name="OrPostOpDetails[orAnesthesiaUses][time_end][]" type="text"><span class="add-on"><i class="icon-time"></i></span></div></td>'
        +'<td><a class="pull-right btn btn-mini" onclick="javascript:deleteAnes(this);"><i class="icon-remove"></i></a></td>'
        +'</tr>';

    ap_table_block.append(field_block);

    $('.operation-time').timepicker();
}

function add_icpm(thisBtn){
    var ic_id = $(thisBtn).parent().parent().find('.ic-id').val();
    var ic_code = $(thisBtn).parent().parent().find('.ic-code').val();
    var ic_name = $(thisBtn).parent().parent().find('.ic-name').val();
    var ic_rvu = $(thisBtn).parent().parent().find('.ic-rvu').val();
    var ic_laterality = $(thisBtn).parent().parent().find('.ic-laterality').val();

    var icpm_out_table = $('#icpm-table');
    var icpm_out_table_block = icpm_out_table.find('tbody');

    if(icpm_out_table_block.find('#default-entry').length !== 0){
        icpm_out_table_block.html('');
    }

    var display = 'style="display:none;"';
    if(ic_laterality == '1')
        display = '';

    var field_block = '<tr>'
        +'<td>'
        +'<input type="hidden" name="OrPostOpDetails[orIcpm][code][]" value="'+ic_code+'" /> '
        +'<input type="hidden" name="OrPostOpDetails[orIcpm][for_laterality][]" value="'+ic_laterality+'" /> '
        +ic_code+'</td>'
        +'<td>'+ic_name+'</td>'
        + '<td><select name="OrPostOpDetails[orIcpm][laterality][]" '+display+'>'
        + '<option value="L">Left</option>'
        + '<option value="R">Right</option>'
        + '<option value="B">Both</option>'
        + '</select></td>'
        +'<td><a class="pull-right btn btn-mini" onclick="javascript:deleteIcpm(this);"><i class="icon-remove"></i></a></td>'
        +'</tr>';
    icpm_out_table_block.append(field_block);
}

function add_med(thisBtn){
    var medsup_id = $(thisBtn).parent().parent().find('.medsup-id').val();
    var medsup_name = $(thisBtn).parent().parent().find('.medsup-name').val();
    var medsup_price = $(thisBtn).parent().parent().find('.medsup-price').val();

    var medsup_out_table = $('#medsup-table');
    var medsup_out_table_block = medsup_out_table.find('tbody');

    if(medsup_out_table_block.find('#default-entry').length !== 0){
        medsup_out_table_block.html('');
    }

    var field_block = '<tr>'
        +'<td>'
        +'<input type="hidden" name="OrPostOpDetails[ormedsup][id][]" value="'+medsup_id+'" /> '
        +medsup_id+'</td>'
        +'<td>'+medsup_name+'</td>'
        +'<td><input class="span12" name="OrPostOpDetails[ormedsup][qty][]" type="text" onchange="javascript:calculateTotal(this)"></td>'
        +'<td class="price"><input class="span12" name="OrPostOpDetails[ormedsup][price][]" type="text" readonly="readonly" value="'+medsup_price+'"></td>'
        +'<td class="total"><input class="span12" name="OrPostOpDetails[ormedsup][total][]" type="text" readonly="readonly"></td>';

        if(!ISFINALBILL)
            field_block += '<td><a class="pull-right btn btn-mini" onclick="javascript:deleteMeds(this);"><i class="icon-remove"></i></a></td>';
            
        field_block += '</tr>';

    medsup_out_table_block.append(field_block);
}

function add_misc(thisBtn){
    var misc_id = $(thisBtn).parent().parent().find('.misc-id').val();
    var misc_name = $(thisBtn).parent().parent().find('.misc-name').val();
    var misc_price = $(thisBtn).parent().parent().find('.misc-price').val();

    var misc_out_table = $('#misc-table');
    var misc_out_table_block = misc_out_table.find('tbody');

    if(misc_out_table_block.find('#default-entry').length !== 0){
        misc_out_table_block.html('');
    }

    var field_block = '<tr>'
        +'<td>'
        +'<input type="hidden" name="OrPostOpDetails[orMisc][id][]" value="'+misc_id+'" /> '
        +misc_id+'</td>'
        +'<td>'+misc_name+'</td>'
        +'<td><input class="span12" name="OrPostOpDetails[orMisc][qty][]" type="text" onchange="javascript:calculateTotal(this)"></td>'
        +'<td class="price"><input class="span12" name="OrPostOpDetails[orMisc][price][]" type="text" readonly="readonly" value="'+misc_price+'"></td>'
        +'<td class="total"><input class="span12" name="OrPostOpDetails[orMisc][total][]" type="text" readonly="readonly"></td>'
        +'<td><a class="pull-right btn btn-mini" onclick="javascript:deleteMisc(this);"><i class="icon-remove"></i></a></td>'
        +'</tr>';

    misc_out_table_block.append(field_block);
}

function deleteAnes(thisBtn){
    $(thisBtn).parent().parent().remove();
    var ap_table = $('#anesthesia-procedures');
    var ap_table_block = ap_table.find('tbody');
    if($.trim(ap_table_block.html().length) == 0){
        ap_table_block.append('<tr id="default-entry"><td colspan="5"><i>No Anesthesia</i></td></tr>');
    }
}

function deleteIcpm(thisBtn){
    $(thisBtn).parent().parent().remove();
    var icpm_out_table = $('#icpm-table');
    var icpm_out_table_block = icpm_out_table.find('tbody');
    if($.trim(icpm_out_table_block.html().length) == 0){
        icpm_out_table_block.append('<tr id="default-entry"><td colspan="5"><i>No ICPM</i></td></tr>');
    }
}

function deleteMeds(thisBtn){
    $(thisBtn).parent().parent().remove();
    var medsup_out_table = $('#medsup-table');
    var medsup_out_table_block = medsup_out_table.find('tbody');
    if($.trim(medsup_out_table_block.html().length) == 0){
        medsup_out_table_block.append('<tr id="default-entry"><td colspan="8"><i>No Medicine and Supplies</i></td></tr>');
    }
}

function deleteMisc(thisBtn){
    $(thisBtn).parent().parent().remove();
    var misc_out_table = $('#misc-table');
    var misc_out_table_block = misc_out_table.find('tbody');
    if($.trim(misc_out_table_block.html().length) == 0){
        misc_out_table_block.append('<tr id="default-entry"><td colspan="8"><i>No Miscellaneous</i></td></tr>');
    }
}

function calculateTotal(thisBtn){
    var qty = $(thisBtn).val();
    var price = $(thisBtn).parent().parent().find('.price input').val();
    $(thisBtn).parent().parent().find('.total input').val(qty*price);
}