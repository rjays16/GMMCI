$(document).ready(function(){
    var search_items_form = $('#search-items-form');
    var items_search_result_table = $('#items-search-result');
    var item_name_input = $('#PackageDetails_item_name');
    var package_details_select = $('#PackageDetails_item_purpose');
    var dismiss_button = $('[data-dismiss="modal"]');
    var prepended_icon = $('.input-prepend .add-on .loader');

    dismiss_button.on('click',function(){
        items_search_result_table.find('tbody').html('');
        item_name_input.val('');
        package_details_select.val('PH');
    });

    search_items_form.on('submit', function(){
        prepended_icon.html('<img alt="loader" src="'+base_url+'/css/frontend/images/small_loader.gif" class="ajax-loader" />');
        $.ajax({
            url:base_url+'/index.php?r=or/packageDetails/jsonItemSearch',
            type:'get',
            data:{term:item_name_input.val(),purpose:package_details_select.val()},
            dataType:'json',
            success:function(data){
                var item_block = items_search_result_table.find('tbody');
                item_block.html('');
                if(!$.isEmptyObject(data)){
                    $.each(data, function(index, item){
                        var item_block_string = '<tr>' +
                            '<td>' +
                            '<input type="hidden" class="i-id" value="'+item.id+'" />' +
                            '<input type="hidden" class="i-name" value="'+item.name+'" />' +
                            '<input type="hidden" class="i-price" value="'+item.price+'" />' +
                            '<input type="hidden" class="i-purpose" value="'+item.purpose+'" />' +
                            '<input type="hidden" class="i-purpose-value" value="'+item.purposeValue+'" />' +
                            item.id + '</td>' +
                            '<td>' + item.name + '</td>' +
                            '<td><input type="text" class="i-quantity" value="0" style="width: 50px; margin: 0px;" /></td>' +
                            '<td><a class="btn btn-mini" onclick="javascript:addItem(this);" data-dismiss="modal">OK</a></td>' +
                            '</tr>';
                        item_block.append(item_block_string);
                    })
                }
                else{
                    item_block.append('<tr><td colspan="100"><i>Please enter item name on search box</i></td></tr>');
                }
                prepended_icon.html('<i class="icon-search"></i>');
            }
        });
        return false;
    });

    package_details_select.on('change', function(){
        search_items_form.submit();
    });
});

function addItem(thisButton){
    var this_button = $(thisButton);
    var i_id = this_button.parent().parent().find('.i-id');
    var i_name = this_button.parent().parent().find('.i-name');
    var i_price = this_button.parent().parent().find('.i-price');
    var i_purpose = this_button.parent().parent().find('.i-purpose');
    var i_purpose_value = this_button.parent().parent().find('.i-purpose-value');
    var i_quantity = this_button.parent().parent().find('.i-quantity');

    var package_item_string = "<tr>" +
        "<td>" +
        '<input type="hidden" name="Packages[items][item_code][]" value="' + i_id.val() + '" />' +
        '<input type="hidden" name="Packages[items][item_name][]" value="' + i_name.val() + '" />' +
        '<input type="hidden" name="Packages[items][item_purpose][]" value="' + i_purpose_value.val() + '" />' +
        '<input type="hidden" name="Packages[items][quantity][]" value="' + i_quantity.val() + '" />' +
        i_id.val() +
        "</td>" +
        "<td>" + i_name.val() + "</td>" +
        "<td>" + i_purpose.val() + "</td>" +
        "<td>" + 
        '<input class="span12" onchange="javascript:calculateTotal(this)" type="text" name="Packages[items][price][]" value="' + i_price.val() + '" />' +
        "</td>" +
        "<td>" + i_quantity.val() + "</td>" +
        "<td class = 'item_total'>" + parseFloat(i_quantity.val() * i_price.val()).toFixed(2) + "</td>" +
        '<td><a rel="tooltip" data-toggle="tooltip" title="" href="javascript:;" onclick="javascript:removeItem(this);" data-original-title="Remove"><i class="icon-trash"></i></a></td>'
        + "</tr>";

    var package_items_block = $('#package-items-block');

    if(package_items_block.find('#no-item').length !== 0){
        package_items_block.html('');
    }

    package_items_block.append(package_item_string);
}

function removeItem(thisButton){
    var this_button = $(thisButton);
    if(confirm("Are you sure you want to remove " + this_button.parent().parent().find('td input[name="Packages[items][item_name][]"]').val()))
        this_button.parent().parent().remove();
}

function calculateTotal(thisBtn){
    var price = $(thisBtn);
    price.val(parseFloat(price.val()).toFixed(2));
    var qty = $(thisBtn).parent().parent().find('td input[name="Packages[items][quantity][]"]').val();
    $(thisBtn).parent().parent().find('td.item_total').html(parseFloat(qty*price.val()).toFixed(2));
}