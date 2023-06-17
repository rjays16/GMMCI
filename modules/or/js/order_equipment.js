function retrieve_equipment(table, details) {
  var table1 = $(table).getElementsByTagName('tbody').item(0);
  if ($('empty_equipment_row')) {
  table1.removeChild($('empty_equipment_row'));   
} 
  var row = document.createElement("tr");

  var element_orig = Object();
  var element_adj = Object();
  if (parseInt(details.is_sc)) {
     element_orig.type = 'td_text';
     element_orig.name = formatNumber(details.original_price, 2);
     element_orig.align = 'right';                                                                                              
     element_orig.id = 'equipment_price_orig'+details.equipment_id;
    
    element_adj.type = 'input';
    element_adj.name = 'equipment_price_adj[]';
    element_adj.text_value = formatNumber(details.adjusted_price, 2);
    element_adj.align = 'right';
    element_adj.id = 'equipment_price_adj'+details.equipment_id;
  }
  else {
    element_orig.type = 'td_text';
    element_orig.name = formatNumber(details.original_price, 2);
    element_orig.align = 'right';
    element_adj.type = 'td_text',
    element_adj.name = formatNumber(details.adjusted_price, 2);
    element_adj.align = 'right';
    element_orig.id = 'equipment_price_orig'+details.equipment_id;
    element_adj.id = 'equipment_price_adj'+details.equipment_id;
  }

  var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                      {type: 'td_text', name: details.equipment_name},
                      {type: 'td_text', name: details.equipment_description},
                      {type: 'input', name: 'number_of_usage[]', text_value: details.number_of_usage, id: 'number_of_usage'+details.equipment_id},
                      element_orig,
                      element_adj,
                      {type: 'td_text', name: formatNumber(details.account_total, 2), align: 'right', id: 'equip_acct'+details.equipment_id},
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
      element.addEventListener("change", function() {update_acct_total(details.equipment_id)}, false);
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
    img.addEventListener("click", function() {remove_equipment(table, details.equipment_id)}, false);
  }
                                               
  if (array_elements[i].align) {
    cell.align = array_elements[i].align;
  }
  row.appendChild(cell);
}
row.id = 'equipment_row'+details.equipment_id;
$(table).getElementsByTagName('tbody').item(0).appendChild(row);
      
var hidden_elements = [{name: 'equipments[]', value: details.equipment_id, id: 'equipments'+details.equipment_id},
                       {name: 'original_price[]', value: parseFloat(details.original_price), id: 'original_price'+details.equipment_id},
                       {name: 'adjusted_price[]', value: parseFloat(details.adjusted_price), id: 'adjusted_price'+details.equipment_id},
                       {name: 'account_total[]', value: parseFloat(details.account_total), id: 'account_total'+details.equipment_id},
                       {name: 'equipment_serial[]', value: 0, id: 'equipment_serial'+details.equipment_id} 
                      ];
 
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

update_total();
}  



function compute_details(details) {
  var computed_details = new Object();
  var discount = parseFloatEx($("discount").value);          
  var is_cash = ($('iscash1') == null) ? parseInt($('transaction_type').value) : $('iscash1').checked;            
  var is_senior_citizen = parseInt($("issc").value);
  var is_socialized = (details.equipment_is_socialized==0) ? false : true;
  if (is_cash) {
     computed_details.original_price = details.equipment_cash;
	 if (discount > 0 && is_socialized)
	   computed_details.adjusted_price = details.equipment_cash - (details.equipment_cash * discount);
	 else
	   computed_details.adjusted_price = details.equipment_cash;
  }
  else {
	 computed_details.original_price = details.equipment_charge;
	 computed_details.adjusted_price = details.equipment_charge;
  }
  computed_details.account_total = details.number_of_usage * computed_details.adjusted_price;
  return computed_details;
}


function append_equipment(table, details) {

if ($('equipments'+details.equipment_id)) {
  alert('Existing');
}
else {
 
var number_of_usage = 0;
while (isNaN(parseFloat(number_of_usage)) || parseFloat(number_of_usage)<=0) {
  number_of_usage = prompt("Enter quantity:")
  if (number_of_usage === null) return false;
}
details.number_of_usage = number_of_usage; 
var computed_details = compute_details(details);

var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_equipment_row')) {
  table1.removeChild($('empty_equipment_row'));   
} 
var row = document.createElement("tr");

var element_orig = Object();
var element_adj = Object();
if (parseInt($("issc").value)) {
    element_orig.type = 'td_text';
    element_orig.name = formatNumber(computed_details.original_price, 2);
    element_orig.align = 'right';
    element_orig.id = 'equipment_price_orig'+details.equipment_id;
  
  element_adj.type = 'input';
  element_adj.name = 'equipment_price_adj[]';
  element_adj.text_value = formatNumber(computed_details.adjusted_price, 2);
    element_adj.align = 'right';
    element_adj.id = 'equipment_price_adj'+details.equipment_id;
}
else {
  element_orig.type = 'td_text';
  element_orig.name = formatNumber(computed_details.original_price, 2);
  element_orig.align = 'right';
  element_adj.type = 'td_text',
  element_adj.name = formatNumber(computed_details.adjusted_price, 2);
  element_adj.align = 'right';
    element_orig.id = 'equipment_price_orig'+details.equipment_id;
    element_adj.id = 'equipment_price_adj'+details.equipment_id;
}

var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                      {type: 'td_text', name: details.equipment_name},
                      {type: 'td_text', name: details.equipment_description},
                      {type: 'input', name: 'number_of_usage[]', text_value: details.number_of_usage, id: 'number_of_usage'+details.equipment_id},
                      element_orig,
                      element_adj,
                      {type: 'td_text', name: formatNumber(computed_details.account_total, 2), align: 'right', id: 'equip_acct'+details.equipment_id},
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
    element.addEventListener("change", function() {update_acct_total(details.equipment_id)}, false);
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
    img.addEventListener("click", function() {remove_equipment(table, details.equipment_id)}, false);
  }
                                               
  if (array_elements[i].align) {
    cell.align = array_elements[i].align;
  }
  row.appendChild(cell);
}

row.id = 'equipment_row'+details.equipment_id;


$(table).getElementsByTagName('tbody').item(0).appendChild(row);
 
      
var hidden_elements = [{name: 'equipments[]', value: details.equipment_id, id: 'equipments'+details.equipment_id},
                       {name: 'original_price[]', value: parseFloat(computed_details.original_price), id: 'original_price'+details.equipment_id},
                       {name: 'adjusted_price[]', value: parseFloat(computed_details.adjusted_price), id: 'adjusted_price'+details.equipment_id},
                       {name: 'account_total[]', value: parseFloat(computed_details.account_total), id: 'account_total'+details.equipment_id},
                       {name: 'equipment_serial[]', value: 0, id: 'equipment_serial'+details.equipment_id}
                      ];

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
 
update_total();
}

}

function update_total() {
  var original_price = document.getElementsByName('original_price[]');
  var adjusted_price = document.getElementsByName('adjusted_price[]');
  var account_total = document.getElementsByName('account_total[]');
  var number_of_usage = document.getElementsByName('number_of_usage[]');
  var equipments = document.getElementsByName('equipments[]');
  
  var sub_total = 0;
  var discount_total = 0;
  var net_total = 0;
  
  
  for (var i=0; i<equipments.length; i++) {
    sub_total += parseFloat(number_of_usage[i].value * original_price[i].value);
    discount_total += parseFloat(number_of_usage[i].value * adjusted_price[i].value);
    net_total += parseFloat(account_total[i].value);
  }
  
  J('#equipment_subtotal').html(formatNumber(sub_total, 2));
  J('#equipment_discount_total').html('('+formatNumber(sub_total - discount_total, 2)+')');
  J('#equipment_net_total').html(formatNumber(net_total, 2));
  
     
}

function update_acct_total(id) {
  var is_senior_citizen = parseInt($("issc").value);
  var original_price = 0;
  var adjusted_price = 0;
  var number_of_usage = J('#number_of_usage'+id).val();
  original_price = J('#original_price'+id).val();
  if (is_senior_citizen) {
    adjusted_price = J('#equipment_price_adj'+id).val()
  }
  else {
    adjusted_price = J('#adjusted_price'+id).val();
  }
  
  
  var account_total = number_of_usage * adjusted_price;
  J('#original_price'+id).val(original_price); 
  J('#adjusted_price'+id).val(adjusted_price); 
  J('#account_total'+id).val(account_total); 
  J('#equip_acct'+id).html(formatNumber(account_total, 2));
  update_total();
}

function remove_equipment(table, id) {
  var table1 = $(table).getElementsByTagName('tbody').item(0);
  table1.removeChild($('equipment_row'+id));
  document.forms[0].removeChild($('original_price'+id));
  document.forms[0].removeChild($('adjusted_price'+id));
  document.forms[0].removeChild($('account_total'+id));
  document.forms[0].removeChild($('equipments'+id));
  document.forms[0].removeChild($('equipment_serial'+id));
  
  if (!document.getElementsByName('equipments[]') || document.getElementsByName('equipments[]').length <= 0) {
    append_empty('equipment_list');
  }
  update_total();
}



function append_empty(table) {
  var table1 = $(table).getElementsByTagName('tbody').item(0);   
  
  if (table1.getElementsByTagName('tr').length <= 0) {

  var row = document.createElement("tr");
  var cell = document.createElement("td");
  row.id = "empty_equipment_row";
  cell.appendChild(document.createTextNode('Equipment order items is currently empty..'));
       
  cell.colSpan = "7"; 
  row.appendChild(cell);
  $(table).getElementsByTagName('tbody').item(0).appendChild(row);
  }
}

/** Oxygen **/
function append_oxygen(table, details) {
 
if ($('equipments'+details.equipment_id+details.serial_no)) {
  alert('Existing');
}
else {

var number_of_usage = 0;
while (isNaN(parseFloat(number_of_usage)) || parseFloat(number_of_usage)<=0 || parseFloat(number_of_usage) > details.remaining_quantity) {
  number_of_usage = prompt("Enter quantity:")
  if (number_of_usage === null) return false;
}
details.number_of_usage = number_of_usage; 
var computed_details = compute_details(details);

var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_equipment_row')) {
  table1.removeChild($('empty_equipment_row'));   
} 
var row = document.createElement("tr");
   
var element_orig = Object();
var element_adj = Object();
if (parseInt($("issc").value)) {
    element_orig.type = 'td_text';
    element_orig.name = formatNumber(computed_details.original_price, 2);
    element_orig.align = 'right';
    element_orig.id = 'equipment_price_orig'+details.equipment_id+details.serial_no;
  
  element_adj.type = 'input';
  element_adj.name = 'equipment_price_adj[]';
  element_adj.text_value = formatNumber(computed_details.adjusted_price, 2);
    element_adj.align = 'right';
    element_adj.id = 'equipment_price_adj'+details.equipment_id+details.serial_no;
                                                                       
}
else {

  element_orig.type = 'td_text';
  element_orig.name = formatNumber(computed_details.original_price, 2);
  element_orig.align = 'right';
  element_adj.type = 'td_text',
  element_adj.name = formatNumber(computed_details.adjusted_price, 2);
  element_adj.align = 'right';
    element_orig.id = 'equipment_price_orig'+details.equipment_id+details.serial_no;
    element_adj.id = 'equipment_price_adj'+details.equipment_id+details.serial_no;
}  

var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                      {type: 'td_text', name: details.equipment_name + ' - ' + details.serial_no},
                      {type: 'td_text', name: details.equipment_description},
                      {type: 'input', name: 'number_of_usage[]', text_value: details.number_of_usage, id: 'number_of_usage'+details.equipment_id+details.serial_no},
                      element_orig,
                      element_adj,
                      {type: 'td_text', name: formatNumber(computed_details.account_total, 2), align: 'right', id: 'equip_acct'+details.equipment_id+details.serial_no},
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
    element.addEventListener("change", function() {update_acct_total(details.equipment_id+details.serial_no)}, false);
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
    img.addEventListener("click", function() {remove_equipment(table, details.equipment_id+details.serial_no)}, false);
  }
                                               
  if (array_elements[i].align) {
    cell.align = array_elements[i].align;
  }
  row.appendChild(cell);
}
row.id = 'equipment_row'+details.equipment_id+details.serial_no;


$(table).getElementsByTagName('tbody').item(0).appendChild(row);

      
var hidden_elements = [{name: 'equipments[]', value: details.equipment_id, id: 'equipments'+details.equipment_id+details.serial_no},
                       {name: 'original_price[]', value: parseFloat(computed_details.original_price), id: 'original_price'+details.equipment_id+details.serial_no},
                       {name: 'adjusted_price[]', value: parseFloat(computed_details.adjusted_price), id: 'adjusted_price'+details.equipment_id+details.serial_no},
                       {name: 'account_total[]', value: parseFloat(computed_details.account_total), id: 'account_total'+details.equipment_id+details.serial_no},
                       {name: 'equipment_serial[]', value: details.serial_no, id: 'equipment_serial'+details.equipment_id+details.serial_no}
                      ];

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
 
update_total();
}

}

function retrieve_oxygen(table, details) {
  var table1 = $(table).getElementsByTagName('tbody').item(0);
  var row = document.createElement("tr");

  var element_orig = Object();
  var element_adj = Object();
  if (parseInt(details.is_sc)) {
     element_orig.type = 'td_text';
     element_orig.name = formatNumber(details.original_price, 2);
     element_orig.align = 'right';
     element_orig.id = 'equipment_price_orig'+details.equipment_id+details.serial_no;
    
    element_adj.type = 'input';
    element_adj.name = 'equipment_price_adj[]';
    element_adj.text_value = formatNumber(details.adjusted_price, 2);
    element_adj.align = 'right';
    element_adj.id = 'equipment_price_adj'+details.equipment_id+details.serial_no;
  }
  else {
    element_orig.type = 'td_text';
    element_orig.name = formatNumber(details.original_price, 2);
    element_orig.align = 'right';
    element_adj.type = 'td_text',
    element_adj.name = formatNumber(details.adjusted_price, 2);
    element_adj.align = 'right';
    element_orig.id = 'equipment_price_orig'+details.equipment_id+details.serial_no;
    element_adj.id = 'equipment_price_adj'+details.equipment_id+details.serial_no;
  }

  var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                      {type: 'td_text', name: details.equipment_name + ' - ' + details.serial_no},
                      {type: 'td_text', name: details.equipment_description},
                      {type: 'input', name: 'number_of_usage[]', text_value: details.number_of_usage, id: 'number_of_usage'+details.equipment_id+details.serial_no},
                      element_orig,
                      element_adj,
                      {type: 'td_text', name: formatNumber(details.account_total, 2), align: 'right', id: 'equip_acct'+details.equipment_id+details.serial_no},
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
      element.addEventListener("change", function() {update_acct_total(details.equipment_id+details.serial_no)}, false);
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
    img.addEventListener("click", function() {remove_equipment(table, details.equipment_id+details.serial_no)}, false);
  }
                                               
  if (array_elements[i].align) {
    cell.align = array_elements[i].align;
  }
  row.appendChild(cell);
}
row.id = 'equipment_row'+details.equipment_id+details.serial_no;
$(table).getElementsByTagName('tbody').item(0).appendChild(row);
      
var hidden_elements = [{name: 'equipments[]', value: details.equipment_id, id: 'equipments'+details.equipment_id+details.serial_no},
                       {name: 'original_price[]', value: parseFloat(details.original_price), id: 'original_price'+details.equipment_id+details.serial_no},
                       {name: 'adjusted_price[]', value: parseFloat(details.adjusted_price), id: 'adjusted_price'+details.equipment_id+details.serial_no},
                       {name: 'account_total[]', value: parseFloat(details.account_total), id: 'account_total'+details.equipment_id+details.serial_no},
                       {name: 'equipment_serial[]', value: details.serial_no, id: 'equipment_serial'+details.equipment_id+details.serial_no}
                      ];
 
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

update_total();
}

function assign_oxygen(table, details) {
  show_oxygen();
}

function empty_equipment() {
  var table1 = $('equipment_list').getElementsByTagName('tbody').item(0);
  table1.innerHTML = '<tr id="empty_equipment_row"><td colspan="7">Equipment order items is currently empty..</td></tr>';
  J("input[@name='equipments[]']").remove();
  J("input[@name='original_price[]']").remove();
  J("input[@name='adjusted_price[]']").remove();
  J("input[@name='account_total[]']").remove();
  J("input[@name='equipment_serial[]']").remove();
  update_total(); 
}