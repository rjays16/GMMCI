
var ViewMode = false;

function resetRefNo(newRefNo,error) {
    $("refno").style.color = error ? "#ff0000" : "";
    $("refno").value=newRefNo;
}

function jsFromAreaOptionChng(obj, value){
    if(obj.id == 'from_area') {           
        js_ClearOptions('to_area');
        xajax_getToAreas(value);
    }
}

function showToAreas(options) {
    $('returned_to').innerHTML = options;
}
