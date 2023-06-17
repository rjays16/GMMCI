$(document).ready(function(){
    var curr_selection = '';
    var personnel_type = '';

    var full_name = $('#Person_fullName');
    var search_personnel_form =  $('#search-personnel-form');
    var personnel_search_result_table = $('#personnel-search-result');
    var prenended_icon = $('.input-prepend .add-on .loader');

    search_personnel_form.on('submit', function(){
        prenended_icon.html('<img alt="loader" src="'+base_url+'/css/frontend/images/small_loader.gif" class="ajax-loader" />');
        $.ajax({
            url:base_url+'/index.php?r=or/personnel/listPersonnel&term='+full_name.val(),
            dataType:'json',
            success:function(data){
                if(!$.isEmptyObject(data)){
                    personnel_search_result_table.show();
                    var personnel_search_block = personnel_search_result_table.find('tbody');
                    personnel_search_block.html('');
                    $.each(data, function(nr, person_name){
                        var patient_result = "<tr>"
                            +'<td><input type="hidden" class="person-id" value="'+nr+'" />'+nr+'</td>'
                            +"<td>"+person_name+"</td>"
                            +'<td><input type="hidden" class="person-name" value="'+person_name+'" /><a class="btn btn-mini" onclick="javascript:addPersonnel(this,\''+curr_selection+'\',\''+personnel_type+'\');" data-dismiss="modal">OK</a></td>'
                            +"</tr>";
                        personnel_search_block.append(patient_result);
                    });
                }
                else{
                    personnel_search_result_table.hide();
                }
                prenended_icon.html('<i class="icon-search"></i>');
            }
        });
        return false;
    });

    $('[data-target="#populate-personnel"]').on('click',function(){
        full_name.val('');
        personnel_search_result_table.find('tbody').html('');
        personnel_search_result_table.hide();
        curr_selection = $(this).attr('data-purpose');
        personnel_type = $(this).attr('data-personnel-type');
    });
});

function deleteSched(thisBtn){
    $(thisBtn).parent().parent().remove();
    var purpose = $(thisBtn).data("purpose");
    var table = $('#'+purpose).find('tbody');
    if($.trim(table.html()) == ""){
        table.append('<tr id="default-entry"><td colspan="2"><i>No Scheduled</i></td></tr>');
    }
}

function addPersonnel(thisBtn,curr_selection, personnel_type){
    var person_id = $(thisBtn).parent().parent().find('.person-id').val();
    var person_name = $(thisBtn).parent().parent().find('.person-name').val();

    var field_block = '<tr>'
        +'<td>'
        +'<input type="hidden" name="OrPreOpDetails[orSurgicalTeams][personell_nr][]" value="'+person_id+'" /> '
        +'<input type="hidden" name="OrPreOpDetails[orSurgicalTeams][role_type][]" value="'+personnel_type+'" /> '
        +person_id
        +'</td><td>'+person_name+'<a class="pull-right btn btn-mini" onclick="javascript:deleteSched(this);" data-purpose="'+curr_selection+'"><i class="icon-remove"></i></a></td>'
        +'</tr>';

    var curr_table_block = $('#'+curr_selection).find('tbody');

    if(curr_table_block.find('#default-entry').length !== 0){
        curr_table_block.html('');
    }

    curr_table_block.append(field_block);
}   

