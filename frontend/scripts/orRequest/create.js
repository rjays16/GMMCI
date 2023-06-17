$(document).ready(function(){
    var search_patients_form = $('#search-patients-form');
    var search_packages_form = $('#search-packages-form');

    var patient_search_result = $('#patient-search-result');
    var package_search_result = $('#package-search-result');

    var patient_search_text = $('#OrRequest_patient_search_text');
    var package_search_text = $('#OrRequest_package_search_text');

    var or_amount = $('#OrRequest_amount');

    var prenended_icon = $('.input-prepend .add-on .loader');

    var or_refno = $('#or_refno').val();

    //init
    loadPackages(package_search_result,package_search_text,prenended_icon);

    search_patients_form.on('submit', function(){
        prenended_icon.html('<img alt="loader" src="'+base_url+'/css/frontend/images/small_loader.gif" class="ajax-loader" />');
        $.ajax({
            url:base_url+'/index.php?r=or/patient/search/or/true/q/'+patient_search_text.val(),
            dataType:'json',
            success:function(data){
                if(data.length > 0){
                    patient_search_result.show();
                    var patient_search_block = patient_search_result.find('tbody');
                    patient_search_block.html('');
                    $.each(data, function(index, patient){
                        var patient_result = "<tr>"
                        +'<td><input type="hidden" class="patient-id" value="'+patient.id+'" />'+patient.fullName+'</td>'
                        +"<td>"+patient.sex+"</td>"
                        +"<td>"+patient.age+"</td>"
                        +"<td>"+patient.birthDate+"</td>"
                        +'<td><a class="btn btn-mini" onclick="javascript:getPID(this);" data-dismiss="modal">OK</a></td>'
                        +"</tr>";
                        patient_search_block.append(patient_result);
                    });
                }
                else{
                    patient_search_result.hide();
                }
                prenended_icon.html('<i class="icon-search"></i>');
            }
        });
        return false;
    });

    search_packages_form.on('submit', function(){
        loadPackages(package_search_result,package_search_text,prenended_icon);
        return false;
    });

    //check if there is existing package (for update)
    if(or_refno != '')
        $.ajax({
            url:base_url+'/index.php?r=or/orRequest/getExtPackage/or_refno/'+or_refno,
            dataType:'json',
            success:function(data){
                var request_package_list = $('#request-package-list');

                $.each(data, function(key, value){
                    getPackageData(value.package_id);
                });
            }
        });

    or_amount.on('change', function(){
        var $this = $(this);

        $this.val(parseFloat($this.val()).toFixed(2));
    });
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

function getPID(thisBtn){
    var pid = $(thisBtn).parent().parent().find('.patient-id').val();
    $.ajax({
        url:base_url+'/index.php?r=or/patient/latestEncounter/pid/'+pid,
        dataType:'json',
        success:function(data){
            $('#OrRequest_encounter_nr').val(data.encounter_nr);
            $('#OrRequest_patient_name').val(data.person_name);
            $('#OrRequest_patient_gender').val(data.gender);
            $('#OrRequest_patient_age').val(data.age);
            $('#OrRequest_patient_address').val(data.address);
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