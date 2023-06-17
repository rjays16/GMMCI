function validate_accommodation() {
  var is_error = false;
  if (J('#room_list').val() == 0) {
    alert('Invalid room');
    is_error = true;
  }
  if (isNaN(parseFloat(J('#room_rate').val())) || parseFloat(J('#room_rate').val())<0) {
    alert('Invalid room rate');
    is_error = true;
  }
  var days;
  var hours; 
  if (isNaN(parseFloat(J('#room_days').val())) || parseFloat(J('#room_days').val())<0) {
    days = 0;
  }
   if (isNaN(parseFloat(J('#room_hours').val())) || parseFloat(J('#room_hours').val())<0) {
    hours = 0;
  }
    
  if (parseFloat(days)<=0 && parseFloat(hours)<=0) {
    alert('The number of days and hours accommodated could not be both empty');
    is_error = true;
  }
  if (!is_error) {
    add_accommodation();
    J('#room_days').val('');  
    J('#room_hours').val('');
  }
}

function populate_accommodation(details) {
  var room_nr = details.room_nr;
  var table = 'accommodation_list';
  if ($('accommodation_row'+room_nr)) {
    alert('Existing');
  }
  else {
    var table1 = $(table).getElementsByTagName('tbody').item(0); 
    if ($('empty_accommodation_row')) {
      table1.removeChild($('empty_accommodation_row'));
    }
    var row = document.createElement('tr');
    /**var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                          {type: 'td_text', name: 'Room ' + details.room_number},
                          {type: 'td_text', name: details.room_type + ' (' +  details.ward_name + ')'},
                          {type: 'input', element_type: 'text', name: 'room_rate[]', text_value: details.room_rate, id: 'room_rate'+room_nr},  
                          {type: 'input', element_type: 'text', name: 'room_days[]', text_value: details.room_days, id: 'room_days'+room_nr},
                          {type: 'input', element_type: 'text', name: 'room_hours[]', text_value: details.room_hours, id: 'room_hours'+room_nr},
                          {type: 'td_text', name: details.total}
                          ];**/
    var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                          {type: 'td_text', name: 'Room ' + details.room_number},
                          {type: 'td_text', name: details.room_type + ' (' +  details.ward_name + ')'},
                          {type: 'td_text', name: details.room_rate, id: 'room_rate'+room_nr},  
                          {type: 'td_text', name: details.room_days, id: 'room_days'+room_nr},
                          {type: 'td_text', name: details.room_hours, id: 'room_hours'+room_nr},
                          {type: 'td_text', name: details.total, align: 'right'}
                          ];
    for (var i=0; i<array_elements.length; i++) {
      var cell = document.createElement("td");
      if (array_elements[i].type == 'td_text') {
        cell.appendChild(document.createTextNode(array_elements[i].name));
      }
      if(array_elements[i].type == 'input')  {
        element = document.createElement(array_elements[i].type) 
        cell.appendChild(element);
        element.name = array_elements[i].name;
        if (array_elements[i].element_type) {
          element.type = array_elements[i].element_type;
          
        }
        element.className = "short";
        
         //element.addEventListener("change", function() {update_acct_total(details.equipment_id)}, false);
        if (array_elements[i].text_value) {
          element.value = array_elements[i].text_value;
        }
        if (array_elements[i].id) {
          element.id = array_elements[i].id;
        }
      }
       if (array_elements[i].align) {
        cell.align = array_elements[i].align;
      }
      if (array_elements[i].type == 'img') {
        img = document.createElement("img");
        cell.appendChild(img);
        img.src = array_elements[i].src;
        img.style.cursor = "pointer";
        img.addEventListener("click", function() {remove_accommodation(room_nr)}, false);
      }
      row.appendChild(cell);
    }
    row.id = 'accommodation_row'+room_nr;
    $(table).getElementsByTagName('tbody').item(0).appendChild(row);
    /**                  
    var hidden_elements = [{name: 'room_nr[]', value: room_nr, id: 'room_nr'+room_nr},
                           {name: 'ward_nr[]', value: details.ward_nr, id: 'ward_nr'+room_nr}];
  **/                   
  var hidden_elements = [{name: 'total_accommodation[]', value: details.total_accommodation, id: 'total_accommodation'+room_nr},
                        {name: 'existing_room_nr[]', value: room_nr, id: 'existing_room_nr'+room_nr}];
for (var i=0; i<hidden_elements.length; i++) {
  var hidden_array = document.createElement('input');
  hidden_array.type = 'hidden';
  hidden_array.name = hidden_elements[i].name;
  hidden_array.value = hidden_elements[i].value;
  if (hidden_elements[i].id) {
    hidden_array.id = hidden_elements[i].id;
  }
  document.forms[0].appendChild(hidden_array);
}
  update_total_charge_accommodation();  
  }
  
}

function add_accommodation() {
  var room_nr = J('#room_list').val();
  var table = 'accommodation_list';
  if ($('accommodation_row'+room_nr)) {
    alert('Existing');
  }
  else {
    var table1 = $(table).getElementsByTagName('tbody').item(0);
    if ($('empty_accommodation_row')) {
      table1.removeChild($('empty_accommodation_row'));
    }
    var row = document.createElement('tr');
    
    var room_hours = parseInt(J('#room_hours').val()/24);
    var final_room_hours = room_hours == 0 ? J('#room_hours').val() : ((J('#room_hours').val()) - (room_hours * 24));
    var final_room_days = parseInt(J('#room_days').val()) + room_hours;
    var computed_days = final_room_hours > 5 ? final_room_days + 1 : final_room_days;
    var total = computed_days + 'day' + ((computed_days > 1) ? 's' : ' ') + '= ' + formatNumber(computed_days * J('#room_rate').val(), 2); 
         
    //$details->room_hours = $room_hours == 0 ? $row['room_hours'] : (($row['room_hours']) - ($room_hours * 24));
    //$details->room_days = $row['room_days'] + $room_hours;
    //$computed_days = $details->room_hours > 5 ? $details->room_days + 1 : $details->room_days; 
      //    $details->total = ($computed_days) . 'day'.(($computed_days > 1) ? 's ' : ' ') . '= '.number_format($computed_days * $details->room_rate, 2, '.', ''); 
    var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                          {type: 'td_text', name: J('#room_list :selected').text()},
                          {type: 'td_text', name: J('#room_type').val() + ' (' +  J('#ward_list :selected').text() + ')'},
                          {type: 'input', element_type: 'text', name: 'room_rate[]', text_value: J('#room_rate').val(), id: 'room_rate'+room_nr},  
                          {type: 'input', element_type: 'text', name: 'room_days[]', text_value:final_room_days, id: 'room_days'+room_nr},
                          {type: 'input', element_type: 'text', name: 'room_hours[]', text_value: final_room_hours, id: 'room_hours'+room_nr},
                          {type: 'td_text', name: total, id:'total_accommodation_td'+room_nr, align:'right'}  
                          ];                     
    for (var i=0; i<array_elements.length; i++) {
      var cell = document.createElement("td");
      if (array_elements[i].type == 'td_text') {
        cell.appendChild(document.createTextNode(array_elements[i].name));
        if (array_elements[i].id) {
          cell.id = array_elements[i].id;  
        }
      }
      if(array_elements[i].type == 'input')  {
        element = document.createElement(array_elements[i].type) 
        cell.appendChild(element);
        element.name = array_elements[i].name;
        if (array_elements[i].element_type) {
          element.type = array_elements[i].element_type;
        }
         element.className = "short"; 
         element.addEventListener("change", function() {update_accommodation_total(room_nr)}, false);
        if (array_elements[i].text_value) {
          element.value = array_elements[i].text_value;
        }
        if (array_elements[i].id) {
          element.id = array_elements[i].id;
        }
      }
      if (array_elements[i].type == 'img') {
        img = document.createElement("img");
        cell.appendChild(img);
        img.src = array_elements[i].src;
        img.style.cursor = "pointer";
        img.addEventListener("click", function() {remove_accommodation2(room_nr)}, false);
      }
       if (array_elements[i].align) {
         cell.align = array_elements[i].align;
       }
      row.appendChild(cell);
    }
    row.id = 'accommodation_row'+room_nr;
    $(table).getElementsByTagName('tbody').item(0).appendChild(row);
    
           /**{type: 'input', element_type: 'hidden', name: 'room_nr[]', text_value: room_nr, id: 'room_nr'+room_nr},
                          {type: 'input', element_type: 'hidden', name: 'ward_nr[]', text_value: J('#ward_list').val(), id: 'ward_nr'+room_nr},**/                    
    var hidden_elements = [{name: 'room_nr[]', value: room_nr, id: 'room_nr'+room_nr},
                           {name: 'ward_nr[]', value: J('#ward_list').val(), id: 'ward_nr'+room_nr},
                           {name: 'total_accommodation[]', value: computed_days * J('#room_rate').val(), id: 'total_accommodation'+room_nr}];
 
for (var i=0; i<hidden_elements.length; i++) {
  var hidden_array = document.createElement('input');
  hidden_array.type = 'hidden';
  hidden_array.name = hidden_elements[i].name;
  hidden_array.value = hidden_elements[i].value;
  if (hidden_elements[i].id) {
    hidden_array.id = hidden_elements[i].id;
  }
  document.forms[0].appendChild(hidden_array);
}
  update_total_charge_accommodation();  
  }
  
}

function retrieve_misc(table, details) {
  
if ($('misc'+details.code)) {
  alert('Existing');
}
else {
var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_misc_row')) {
  table1.removeChild($('empty_misc_row'));   
} 
var row = document.createElement("tr");

var total_charge =  details.quantity * details.price;

var array_elements = new Array();
if (details.is_removable == 1) {
  array_elements.push({type: 'img', src: '../../../images/btn_delitem.gif'});
}
array_elements.push({type: 'td_text', name: details.code},
                      {type: 'td_text', name: details.name},
                      {type: 'td_text', name: details.description},
                      {type: (details.is_removable==1) ? 'input' : 'td_text', name: (details.is_removable==1) ? 'quantity[]' : details.quantity, text_value: details.quantity, id: 'quantity'+details.code},
                      {type: 'td_text', name: details.price},
                      {type: 'td_text', name: total_charge.toFixed(2), id:'total_misc_td'+details.code, align:'right'});


for (var i=0; i<array_elements.length; i++) {
  var cell = document.createElement("td");
  if (array_elements[i].type == 'td_text') {
    cell.appendChild(document.createTextNode(array_elements[i].name));
    if (array_elements[i].id) {
      cell.id = array_elements[i].id;  
    }
  }
  if(array_elements[i].type == 'input')  {
    element = document.createElement(array_elements[i].type) 
    cell.appendChild(element);
    element.name = array_elements[i].name;
    element.type = "text";
    element.addEventListener("change", function() {update_misc_total(details.code)}, false);
    if (array_elements[i].text_value) {
      element.value = array_elements[i].text_value;
    }
    if (array_elements[i].id) {
      element.id = array_elements[i].id;
    }
    
  }

  if (array_elements[i].type == 'img') {
    img = document.createElement("img");
    cell.appendChild(img);
    img.src = array_elements[i].src;
    img.style.cursor = "pointer";
    img.addEventListener("click", function() {remove_misc_charge(details.code)}, false);
  }
                                               
  if (array_elements[i].align) {
    cell.align = array_elements[i].align;
  }
  row.appendChild(cell);
}
row.id = 'misc_row'+details.code;


$(table).getElementsByTagName('tbody').item(0).appendChild(row);

if (details.is_removable == 1) {      
var hidden_elements = [{name: 'misc[]', value: details.code, id: 'misc'+details.code},
                      {name: 'original_misc_price[]', value: details.price, id: 'original_misc_price'+details.code},
                      {name: 'account_type[]', value: details.account_type, id: 'account_type'+details.code}];

for (var i=0; i<hidden_elements.length; i++) {
  var hidden_array = document.createElement('input');
  hidden_array.type = 'hidden';
  hidden_array.name = hidden_elements[i].name;
  hidden_array.value = hidden_elements[i].value;
  if (hidden_elements[i].id) {
    hidden_array.id = hidden_elements[i].id;
  }
  document.forms[0].appendChild(hidden_array);
}
}

update_total_misc();
}

}
function append_misc(table, details) {
  
if ($('misc'+details.code)) {
  alert('Existing');
}
else {

var quantity = 0;
while (isNaN(parseFloat(quantity)) || parseFloat(quantity)<=0) {
  quantity = prompt("Enter quantity:")
  if (quantity === null) return false;
}
details.quantity = quantity; 
//var computed_details = compute_details(details);

var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_misc_row')) {
  table1.removeChild($('empty_misc_row'));   
} 
var row = document.createElement("tr");

var total_charge =  details.quantity * details.price;

var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                      {type: 'td_text', name: details.code},
                      {type: 'td_text', name: details.name},
                      {type: 'td_text', name: details.description},
                      {type: 'input', name: 'quantity[]', text_value: details.quantity, id: 'quantity'+details.code},
                      {type: 'td_text', name: details.price},
                      {type: 'td_text', name: total_charge.toFixed(2), align:'right'},
                      ];

for (var i=0; i<array_elements.length; i++) {
  var cell = document.createElement("td");
  if (array_elements[i].type == 'td_text') {
    cell.appendChild(document.createTextNode(array_elements[i].name));
    if (array_elements[i].id) {
      cell.id = array_elements[i].id;  
    }
  }
  if(array_elements[i].type == 'input')  {
    element = document.createElement(array_elements[i].type) 
    cell.appendChild(element);
    element.name = array_elements[i].name;
    element.type = "text";
    element.addEventListener("change", function() {update_misc_total(details.code)}, false);  
    if (array_elements[i].text_value) {
      element.value = array_elements[i].text_value;
    }
    if (array_elements[i].id) {
      element.id = array_elements[i].id;
    }
    
  }

  if (array_elements[i].type == 'img') {
    img = document.createElement("img");
    cell.appendChild(img);
    img.src = array_elements[i].src;
    img.style.cursor = "pointer";
    img.addEventListener("click", function() {remove_misc_charge(details.code)}, false);
  }
                                               
  if (array_elements[i].align) {
    cell.align = array_elements[i].align;
  }
  row.appendChild(cell);
}
row.id = 'misc_row'+details.code;


$(table).getElementsByTagName('tbody').item(0).appendChild(row);

      
var hidden_elements = [{name: 'misc[]', value: details.code, id: 'misc'+details.code},
                      {name: 'original_misc_price[]', value: details.price, id: 'original_misc_price'+details.code},
                      {name: 'account_type[]', value: details.account_type, id: 'account_type'+details.code}];

for (var i=0; i<hidden_elements.length; i++) {
  var hidden_array = document.createElement('input');
  hidden_array.type = 'hidden';
  hidden_array.name = hidden_elements[i].name;
  hidden_array.value = hidden_elements[i].value;
  if (hidden_elements[i].id) {
    hidden_array.id = hidden_elements[i].id;
  }
  document.forms[0].appendChild(hidden_array);
}

update_total_misc();
}

}

function update_total_misc() {
  //to do: discounts
  
  var misc = document.getElementsByName('misc[]');
  var quantity = document.getElementsByName('quantity[]');   
  var original_misc_price = document.getElementsByName('original_misc_price[]');   
  
  var sub_total = 0;
  var discount_total = 0;
  var net_total = 0;
 
  for (var i=0; i<misc.length; i++) {
    sub_total += parseFloat(quantity[i].value * original_misc_price[i].value);
    //discount_total += parseFloat(number_of_usage[i].value * adjusted_price[i].value);
    //net_total += parseFloat(account_total[i].value);
  }
  discount = 0;

  J('#misc_subtotal').html(formatNumber(sub_total, 2));
  J('#misc_discount_total').html('('+formatNumber(0, 2)+')');
  J('#misc_net_total').html(formatNumber(sub_total, 2));
  
     
}

function append_empty_accommodation() {
  var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
  var row = document.createElement("tr");
  var cell = document.createElement("td");
  row.id = "empty_accommodation_row";
  cell.appendChild(document.createTextNode('Additional accommodation empty...'));
       
  cell.colSpan = "7"; 
  row.appendChild(cell);
  $('accommodation_list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function remove_accommodation(id) {
  var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
  table1.removeChild($('accommodation_row'+id));
   document.forms[0].removeChild($('total_accommodation'+id));     
  var hidden_array = document.createElement('input');
  hidden_array.type = 'hidden';
  hidden_array.name = 'removed_room_nr[]';
  hidden_array.value = id;
  hidden_array.id = 'removed_room_nr'+id;
  update_total_charge_accommodation();
  document.forms[0].appendChild(hidden_array);
  if (table1.getElementsByTagName('tr').length <= 0) {
    append_empty_accommodation();
  }
}

function remove_accommodation2(id) {
  var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
  table1.removeChild($('accommodation_row'+id));
  document.forms[0].removeChild($('room_nr'+id));
  document.forms[0].removeChild($('ward_nr'+id));
  document.forms[0].removeChild($('total_accommodation'+id));
  update_total_charge_accommodation();
  if (table1.getElementsByTagName('tr').length <= 0) {
    append_empty_accommodation();
  }
}

function update_accommodation_total(id) {
   var room_hours = parseInt(J('#room_hours'+id).val()/24);
   var final_room_hours = room_hours == 0 ? J('#room_hours'+id).val() : ((J('#room_hours'+id).val()) - (room_hours * 24));
   var final_room_days = parseInt(J('#room_days'+id).val()) + room_hours;
   var computed_days = final_room_hours > 5 ? final_room_days + 1 : final_room_days;
   var total = computed_days + 'day' + ((computed_days > 1) ? 's' : ' ') + '= ' + formatNumber(computed_days * J('#room_rate'+id).val(), 2); 
   J('#total_accommodation_td'+id).html(total); 
   J('#total_accommodation'+id).val(computed_days * J('#room_rate'+id).val());
   update_total_charge_accommodation(); 

}

function update_total_charge_accommodation() {
   var total_accommodation = document.getElementsByName('total_accommodation[]');  
   var sub_total = 0;
   var discount_total = 0;
   var net_total = 0;
   
   for (var i=0; i<total_accommodation.length; i++) {
    //sub_total += parseFloat(number_of_usage[i].value * original_price[i].value);
    //discount_total += parseFloat(number_of_usage[i].value * adjusted_price[i].value);
    net_total += parseFloat(total_accommodation[i].value);
   }
  
   J('#accommodation_subtotal').html(formatNumber(net_total, 2));
   J('#accommodation_discount_total').html('(0.00)');
   J('#accommodation_net_total').html(formatNumber(net_total, 2));
}

function remove_misc_charge(id) {
  var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
  table1.removeChild($('misc_row'+id));
  document.forms[0].removeChild($('misc'+id));
  document.forms[0].removeChild($('original_misc_price'+id));
  document.forms[0].removeChild($('account_type'+id));

  if (!document.getElementsByName('misc[]') || document.getElementsByName('misc[]').length <= 0) {
    append_empty_misc();
  }
  update_total_misc();
}

function append_empty_misc() {
  var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
  var row = document.createElement("tr");
  var cell = document.createElement("td");
  row.id = "empty_misc_row";
  cell.appendChild(document.createTextNode('Miscellaneous charges empty...'));
       
  cell.colSpan = "7"; 
  row.appendChild(cell);
  $('misc_list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function update_misc_total(id) {
    
   J('#total_misc_td'+id).html((J('#quantity'+id).val() * J('#original_misc_price'+id).val()).toFixed(2));
   update_total_misc();
}