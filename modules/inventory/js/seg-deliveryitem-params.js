function closeRelInfoPrompt() {
    window.parent.cClick();
}

function addSelectedItem() {

    var id = $('item_code').value;
    var unit = $('unit').value;
    var unit_name = $('unit_name').value;
    var isperpc = $('isperpc').value;
    var qty = $('item_qty').value;
    qty = qty.replace(",","");
    var expiry_dt = $('expiry_date').value;
    var serial_no = '';
    var lot_no = $('lot_no').value;
    var manufacturer = $('manufacturer').value;
    var perpack = $('perpack').value;

    if(lot_no == ''){
        alert('Lot No. is required');
        $('lot_no').focus();
        return false;
    }
    if(manufacturer == ''){
        alert('Manufacturer is required');
        $('manufacturer').focus();
        return false;
    }

    //added by bryan 102709
    var price_add = $('price_add').value;
    //alert(price_add);
    // if ($('chk_expiry').checked) expiry_dt = $('expiry_date').value;
    if ($('chk_serial').checked) serial_no = $('serial_no').value;    
    // if ($('chk_lot').checked) lot_no = $('lot_no').value;
    // if ($('chk_manufacturer').checked) manufacturer = $('manufacturer').value;
    window.parent.prepareAdd(id, unit, unit_name, isperpc, qty, expiry_dt, serial_no, lot_no, manufacturer, price_add, perpack);
    window.parent.cClick();  
}


function formatDate(exdate){
    var mon = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return mon[exdate.getMonth()] + " " + exdate.getDate() + ", " + exdate.getFullYear();
}

//added by ken: for updating the entry items in delivery tray
function updateSelectedItem() {
    var id = $('item_code').value;
    var qty = $('item_qty').value;
    var srow = $('srow').value;
    qty = qty.replace(",","");
    var expiry_dt = $('expiry_date').value;
    var serial_no = '';
    var price_add = $('price_add').value;
    var lot_no = $('lot_no').value;
    var manufacturer = $('manufacturer').value;

    var r = new Date(expiry_dt);
    expiry_dt = formatDate(r);

    if(lot_no == ''){
        alert('Lot No. is required');
        return false;
    }
    if(manufacturer == ''){
        alert('Manufacturer is required');
        return false;
    }

    // if ($('chk_expiry').checked) expiry_dt = $('expiry_date').value;
    if ($('chk_serial').checked) serial_no = $('serial_no').value; 
    // if ($('chk_lot').checked) lot_no = $('lot_no').value;
    // if ($('chk_manufacturer').checked) manufacturer = $('manufacturer').value;
    window.parent.updateItem(id, qty, expiry_dt, serial_no, price_add, srow, lot_no,manufacturer);  
    alert('Successfully updated.')
}

function checkQty(order, old_qty){
    var qty = $('item_qty').value;
    if(order){
        if(qty > old_qty){
            alert('Quantity exceeded in ordered quantity');
            $('item_qty').value = old_qty; 
        }
    }
}

//ended by ken
