var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
    if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')
    return parseFloat(str)    
}

function setPagination(pageno, lastpage, pagen, total) {
    currentPage=parseInt(pageno);
    lastPage=parseInt(lastpage);    
    firstRec = (parseInt(pageno)*pagen)+1;
    if (currentPage==lastPage)
        lastRec = total;
    else
        lastRec = (parseInt(pageno)+1)*pagen;
    if (parseInt(total))
        $("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>';
    else
        $("pageShow").innerHTML = '';
    $("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
    $("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
    $("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
    $("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
    if (el.className=="segDisabledLink") return false;
    if (lastPage==0) return false;
    switch(jumpType) {
        case FIRST_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',0);
        break;
        case PREV_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',currentPage-1);
        break;
        case NEXT_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(currentPage)+1);
        break;
        case LAST_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',lastPage);
        break;
    }
}

function prepareAddAdjustment(id, qty, unit, unit_name, isperpc) {
    var details = new Object();         

    details.id = $('id'+id).innerHTML;
    details.name = $('name'+id).innerHTML;
    details.desc = $('desc'+id).innerHTML;
    //details.pending = $('pending'+id).value;
    details.athand = Number(qty);
    details.reasons = $('reasons'+id).value;

    // added by monmon
    details.unit_cost = $('unit_cost'+id).value;
    
    details.expiry = $('expiry'+id).value;
    details.serial = $('serial'+id).value;
    
    if(details.expiry == '') details.expiry='-';
    
    details.unitid = unit;
    details.unitdesc = unit_name;
    details.is_perpc = Number(isperpc);

    var list = window.parent.document.getElementById('order-list');
    result = window.parent.appendOrder(list,details);
    if (result) 
        alert('Item added to order list...');
    else
        alert('Failed to add item...');
    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();      
}

function clearList(listID) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            dBody.innerHTML = "";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
}

function clearExp(listID) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("select")[0];
        if (dBody) {
            dBody.innerHTML = "<option value=''>Select Expiry</option>";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
    
}

function addToAdjustmentList(listID, details) {
    var list=$(listID), dRows, dBody, rowSrc;

//    var i,val;    
//    val = $('type_nr').value;    
    
    if (list) {        
        dBody=list.getElementsByTagName("tbody")[0];
//        dRows=dBody.getElementsByTagName("tr");        

        // get the last row id and extract the current row no.            
        if (typeof(details)=='object') {
            var id = details.id,
                name = details.name,
                desc = details.desc,
                reasons = details.reasons,
                expiry = details.expiry,
                serial = details.serial,
                pack = (details.pck_unitid == '' ? '1' : details.pck_unitid),
                piece = (details.pc_unitid == '' ? '2' : details.pc_unitid),
                packname = (details.pck_unitname == '') ? 'pack(s)' : details.pck_unitname,
                piecename = (details.pc_unitname == '') ? 'piece(s)' : details.pc_unitname,
                // added unit cost by monmon
                unit_cost = details.unit_cost;                
                
            rowSrc = '<tr>'+
                        '<td>'+
                            '<input id="idpo" type="hidden" value="'+id+'"/>'+
                            '<input id="unit_cost'+id+'" type="hidden" value="'+unit_cost+'"/>"'+
                            '<input id="reasons'+id+'" type="hidden" value="'+reasons+'"/>'+
                            '<input id="expiry'+id+'" type="hidden" value="'+expiry+'"/>'+
                            '<input id="serial'+id+'" type="hidden" value="'+serial+'"/>'+                                    
                            '<span id="name'+id+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
                            '<div style=""><div id="desc'+id+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div>'+
                        '</td>'+
                        '<td align="center">'+
                            '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span>'+
                        '</td>'+                                
                        /* '<td align="center">'+
                            '<input type="button" id="pcadd_pack_'+id+'" value="'+packname+'" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                'onclick="prepareAddAdjustment(\''+id+'\', '+details.pksathand+', \''+pack+'\',\''+packname+'\', 0)" '+
                            '/>'+
                        '</td>'+*/                                         
                        '<td  align="center">'+
                            '<input type="button" id="pcadd_pc_'+id+'" value="'+piecename+'" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                'onclick="prepareAddAdjustment(\''+id+'\', '+details.pcsathand+', \''+piece+'\',\''+piecename+'\', 1)" '+
                            '/>'+
                        '</td>'+
                    '</tr>';                    
		}
        else {
            rowSrc = '<tr><td colspan="4" style="">No such product exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
   }
  
}

//----------

function init(e){
    
    YAHOO.equipprompt.container.bBody = new YAHOO.widget.Module("bBody", {visible:true});
    YAHOO.equipprompt.container.bBody.render();
    
     shortcut.add('ESC', closeMe,
        {
            'type':'keydown',
            'propagate':false
        }
    );

    setTimeout("$('search').focus()",100);
    
    //xajax_populateTypesComboIss(); 
    
    
}//end function init

function initEquipmentPrompt(){

    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("equipmentBox",
                                                                                { width : "560px",
                                                                                 fixedcenter : true,
                                                                                 visible : false, 
                                                                                 constraintoviewport : true,
                                                                                 buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                             { text:"Cancel", handler:handleCancel } ]
                                                                                } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showEquipPrompt() {    

  // Define various event handlers for Dialog
    var handleSubmit = function() {
        
        var details = new Object();
        var qty;
        
        //id = $('temporaryid').value;
        //qty = prompt("Enter the number of pieces to be issued:")
        //    if (qty === null) return false;
        
        qty = $('eunit_qty').value;
        
        details.id = $('id'+id).innerHTML;
        details.name = $('name'+id).innerHTML;
        details.desc = $('desc'+id).innerHTML;
        //details.pending = $('pending'+id).value;
        details.pending = qty;
        details.unitid = 2;
        details.unitdesc = "piece";
        details.perpc = 1;

        var list = window.parent.document.getElementById('order-list');
        result = window.parent.appendOrder(list,details);
        if (result) 
            alert('Item added to order list...');
        else
            alert('Failed to add item...');
        if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
        
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("equipmentBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );


    YAHOO.equipprompt.container.edialog.render();
    YAHOO.equipprompt.container.edialog.show();    

        
}
*/

function initEquipmentPcPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("equipmentBoxPc", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showEquipPcPrompt() {    

    YAHOO.equipprompt.container.edialogPc.render();
    YAHOO.equipprompt.container.edialogPc.show();    

        
}
*/
function initMedicinePrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.mdialog = new YAHOO.widget.Dialog("medicineBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showMedicinePrompt() {    

    YAHOO.equipprompt.container.mdialog.render();
    YAHOO.equipprompt.container.mdialog.show();    

        
}
*/
function initMedicinePcPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.mdialogPc = new YAHOO.widget.Dialog("medicineBoxPc", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showMedicinePcPrompt() {  
  

    YAHOO.equipprompt.container.mdialogPc.render();
    YAHOO.equipprompt.container.mdialogPc.show();    

        
}
*/

function initOtherPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("otherBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
}

function initOtherPcPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("otherBoxPc", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );    
}