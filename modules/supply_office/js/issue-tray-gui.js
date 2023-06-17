var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var auto_append=1;

function updateAthandIss(expiry,item){
    area = parent.$('area_issued').value;
    
    xajax_updateValueAtHandIss(expiry,area,item);
}

function changeValueAtHandIss(qty,id,expiry) {
    $("athand"+id).value = qty;
    $("rowathand"+id).innerHTML = qty; 
    $("expiries"+id).value = expiry;
}

function updateAthandIssSer(serial,item){
    area = parent.$('area_issued').value;
    
    xajax_updateValueAtHandIssSer(serial,area,item);
}

function changeValueAtHandIssSer(qty,id,serial) {
    $("athand"+id).value = qty;
    $("rowathand"+id).innerHTML = qty; 
    $("serials"+id).value = serial;
}

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
    return true;
}

function prepareAdd(id) {
    var details = new Object();
    
     var    qty=0;
    
    qty = prompt("Enter quantity of package to be issued:")
    if (qty === null) return false;
 
    
    details.id = $('id'+id).innerHTML;
    details.name = $('name'+id).innerHTML;
    details.desc = $('desc'+id).innerHTML;
    //details.pending = $('pending'+id).value;
    details.pending = qty;
    details.unitid = $('unitid'+id).value;
    details.unitdesc = $('unitdesc'+id).value;
    details.perpc = $('perpc'+id).value;

    var list = window.parent.document.getElementById('order-list');
    result = window.parent.appendOrder(list,details);
    if (result) 
        alert('Item added to order list...');
    else
        alert('Failed to add item...');
    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
    return true;    
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

function validateIssueQty(atHand, qty, allowedQty) {
    qty = parseFloatEx(qty);
    allowedQty = parseFloatEx(allowedQty);
    atHand = parseFloatEx(atHand);

    if(isNaN(qty) || qty <= 0) {
        return false;
    }

    if(qty > allowedQty || qty > atHand) {
        alert('Entered quantity exceeds quantity on hand');
        return false;
    }


    return true;
}

//Modified by EJ 11/04/2014
function pepareAddItems(req_refno, item_codes, particulars, area ,areadest) {
    // var details = new Object();
    
    // details.item_codes = item_codes.split(",");
    // details.area = area;
    // details.areadest = areadest;
    // details.req_refno = req_refno;


    // details.id = item_codes.split(",");
    // details.name = particulars.split(",");
   

    // details.athand = $('athand'+req_refno).value.split(",");
    // details.pending = $('pending'+req_refno).value.split(",");
    // details.avg = $('avg'+req_refno).value.split(",");

    // details.unitid = 2;
    // details.unitdesc = "piece(s)";
    // details.perpc = 1;
    // var list = window.parent.document.getElementById('order-list');
    result = window.parent.populateIssueProductList(req_refno,area,areadest);
    // if (result)  {
    //     alert('Item(s) added to order list...');
         window.parent.cClick();
    // }
    // else {
    //     alert('Failed to add item...');
    // }
        
    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
    return true;
}

//Modified by EJ 11/04/2014
function addProductToList(listID, details) {

    // ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
    var list = $(listID), dRows, dBody, rowSrc;
    var i, val;

    val = $('type_nr').value;

    if (list) {
        dBody = list.getElementsByTagName("tbody")[0];
        dRows = dBody.getElementsByTagName("tr");

        // get the last row id and extract the current row no.

        if (typeof(details) == "object") {
            var req_refno = details.req_refno,
                request_date = details.request_date,
                requestor = details.requestor,
                particulars = details.particulars,
                item_codes = details.item_codes,
                area = details.area,
                areadest = details.areadest,

                id = details.id,
                name = details.name,
                desc = details.desc,
                pending = details.pending,
                perpc = details.perpc,
                qtyperpack = details.qtyperpack,
                soc = details.soc,
                expiry = details.expiry,
                serial = details.serial,
                avg = details.avg,
                athand = details.athand,
                pck_unitid1 = details.pck_unitid1,
                pc_unitid1 = details.pc_unitid1,
                pck_unitname1 = details.pck_unitname1,
                pc_unitname1 = details.pc_unitname1,
                filter = details.prod_class;            // Added by LST --- 10.21.2009            

            var cashHTML, chargeHTML;
            var cashSeniorHTML, chargeSeniorHTML;
            var packelemid, pcelemid;


            var pcId = 'pcadd_' + id;
            var packId = 'packadd_' + id;
            pcelemid = pcId;
            packelemid = packId;
            var unitId = '';
            if (perpc > 0) {
                unitId = pc_unitid1;
            } else {
                unitId = pck_unitid1;
            }

            rowSrc = '<tr>'+
            '<input id="id' + req_refno + '" type="hidden" value="' + id + '"/>' +
            '<input id="name' + req_refno + '" type="hidden" value="' + name + '"/>' +
            '<input id="desc' + req_refno + '" type="hidden" value="' + desc + '"/>' +
            '<input id="athand'+req_refno+'" type="hidden" value="'+athand+'"/>'+
            '<input id="avg' + req_refno + '" type="hidden" value="' + avg + '1"/>' +
            '<input id="pending' + req_refno + '" type="hidden" value="' + pending + '"/>' +
            '</tr>';

               /* for (var i = 0; i < item_codes.length; i++) {
                rowSrc += 
                '<input id="item_'+item_codes[i]+'" type="hidden" value="' + item_codes[i] + '"/>' +
                '<input id="name_'+item_codes[i]+'" type="hidden" value="' + particulars[i] + '"/>';
                }; */

            //item_codes = "'"+item_codes+"'";
            //particulars = "'"+particulars+"'";
            //area = "'"+area+"'";
            //areadest = "'"+areadest+"'";

            rowSrc += '<tr>'+
            '<td><span id="req_refno' + req_refno + '">' + req_refno + '</span></td>'+
            '<td align="center"><span id="request_date' + req_refno + '">' + request_date + '</span></td>'+
            '<td align="center"><span id="requestor' + req_refno + '">' + requestor + '</span></td>'+
            '<td align="center"><span id="particulars' + req_refno + '">' + particulars + '</span></td>'+
            '<td><input class="segButton" onclick="pepareAddItems(\''+req_refno+'\',\''+item_codes+'\',\''+particulars+'\',\''+area+'\',\''+areadest+'\')" '+
                      ' type="button" value="Request" style="color:#000066; font-weight:bold;"/></td>'+
            '</tr>';

            if (filter == 'M' || filter == 'S' || filter == '') {

                //YAHOO.util.Event for per pack of medicine

                YAHOO.util.Event.addListener(packelemid, "click", function showMedicinePrompt(e, id) {

                    var sBody, sOption;

                    var qty, qtyPerPack;

                    qty = parseFloatEx($(id).innerHTML);
                    qtyPerPack = parseFloatEx($('qtyperpack' + id).value);
                    qty = Math.floor( qty / qtyPerPack);
                    var atHandQty = Math.floor(parseFloatEx($('rowathand' + id).innerHTML) / qtyPerPack);

                    var handleSubmit = function () {
                        var details = new Object();
                        var inputQty, extra, perpack;
                        inputQty = $('munit_qty').value;
                        if(validateIssueQty(atHandQty, inputQty, qty)) {
                            details.type = 'M';
                            //details.expdate = '1';
                            details.expdate = expiry;
                            details.serial = "-";
                            details.id = id;
                            details.name = $('name' + id).innerHTML;
                            details.desc = $('desc' + id).innerHTML;
                            details.pending = inputQty;

                            details.unitid = $('pckunitid' + id).value;;
                            details.unitdesc = $('pckunitdesc' + id).value;
                            details.perpc = 0;
                            details.avg = $('avg' + id).value;
                            details.qtyperpack = $('qtyperpack' + id).value;
                            details.req_refno = $('reqrefno' + id).value;

                            var list = window.parent.document.getElementById('order-list');

                            result = window.parent.appendOrder(list, details);
                            if (result)
                                alert('Item added to order list...');

                            if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();

                            this.submit();
                            return true;
                        }

                    };
                    var handleCancel = function () {
                        this.cancel();
                    };

                    // Instantiate the Dialog
                    YAHOO.equipprompt.container.mdialog = new YAHOO.widget.Dialog(
                        "medicineBox",
                        { width: "560px",
                            fixedcenter: true,
                            visible: false,
                            constraintoviewport: true,
                            buttons: [
                                { text: "Save", handler: handleSubmit, isDefault: true },
                                { text: "Cancel", handler: handleCancel }
                            ]
                        });

                    YAHOO.equipprompt.container.mdialog.render();
                    YAHOO.equipprompt.container.mdialog.show();


                }, id);
                //YAHOO.util.Event for per piece of medicine
                YAHOO.util.Event.addListener(pcelemid, "click", function showMedicinePcPrompt(e, id) {

                    var sBoy1, sOption1;
                    var qty;
                    qty = $(id).innerHTML;

                    // Define various event handlers for Dialog
                    var handleSubmit = function () {
                        var details = new Object();
                        var inputQty;
                        inputQty = $('mpc_qty').value;
                        var atHandQty = $('rowathand' + id).innerHTML;

                        if (validateIssueQty(atHandQty, inputQty, qty)) {
                            details.type = 'M';
                            details.expdate = expiry;
                            details.serial = "-";
                            details.id = id;
                            details.name = $('name' + id).innerHTML;
                            details.desc = $('desc' + id).innerHTML;
                            details.pending = inputQty;
                            details.unitid = $('unitid' + id).value;;
                            details.unitdesc = $('unitdesc' + id).value;
                            details.perpc = 1;
                            details.avg = $('avg' + id).value;
                            details.qtyperpack = $('qtyperpack' + id).value;
                            details.req_refno = $('reqrefno' + id).value;
                            
                            var list = window.parent.document.getElementById('order-list');

                            result = window.parent.appendOrder(list, details);
                            if (result)
                                alert('Item added to order list.');

                            if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();

                            this.submit();
                            return true;
                        }
                        ;
                    }


                    var handleCancel = function () {
                        this.cancel();
                    };

                    // Instantiate the Dialog
                    YAHOO.equipprompt.container.mdialogPc = new YAHOO.widget.Dialog("medicineBoxPc",
                        { width: "560px",
                            fixedcenter: true,
                            visible: false,
                            constraintoviewport: true,
                            buttons: [
                                { text: "Save", handler: handleSubmit, isDefault: true },
                                { text: "Cancel", handler: handleCancel }
                            ]
                        });


                    YAHOO.equipprompt.container.mdialogPc.render();
                    YAHOO.equipprompt.container.mdialogPc.show();


                }, id);
            }

//            if(filter=='E') {                   // Removed filter == '' by LST -- 10.21.2009
//
//            //YAHOO.util.Event for per pack of equipment
//
//            YAHOO.util.Event.addListener(packelemid, "click", function showEquipPrompt(e, id) {
//
//                // Define various event handlers for Dialog
//                var handleSubmit = function() {
//
//                    var details = new Object();
//                    var qty,extra;
//                    var max;
//
//                    qty = $('eunit_qty').value;
//                    perpack = $('qtyperpack'+id).value;
//
//                    //extra = $('munit_expdate_hidden').value;
//                    extra = $('serials'+id).value;
//                    /*
//                    qty = $('eunit_qty').value;
//                    perpack = $('qtyperpack'+id).value;
//                    extra = $('eunit_serial').value;
//                    */
//                    qty = qty*perpack;
//
//                    max = $('pending'+id).value;
//                    alert("qty:"+qty+" pending:"+max);
//                    while ((max - qty) < 0){
//                        qty = prompt("Enter a number not exceeding qty requested:");
//                            if (qty === null) return false;
//                    }
//
//                    var athand;
//
//                    athand = $('athand'+id).value;
//
//                    while ((athand - qty) < 0){
//                        qty = prompt("Entered qty exceeds inventory at hand; New Qty:");
//                            if (qty === null) return false;
//                    }
//
//                    qty = $('eunit_qty').value;
//
//                    details.type = 'E';
//                    details.serial = extra;
//                    //details.expdate = "-";
//                    details.expdate = expiry;
//
//                    details.id = $('id'+id).innerHTML;
//                    details.name = $('name'+id).innerHTML;
//                    details.desc = $('desc'+id).innerHTML;
//                    details.pending = qty;
//                    details.unitid = $('pckunitid'+id).value;
//                    details.unitdesc = $('pckunitdesc'+id).value;
//                    details.perpc = $('perpc'+id).value;
//                    details.avg = $('avg'+id).value;
//                    details.qtyperpack = $('qtyperpack'+id).value;
//
//                    //added for equipment details
//                    details.epropno = $('eunit_propno').value;
//                    details.eestlife = $('eunit_estlife').value;
//
//                    var list = window.parent.document.getElementById('order-list');
//
//                    result = window.parent.appendOrder(list,details);
//                    if (result)
//                        alert('Item added to order list...');
//                    else
//                        alert('Failed to add item...');
//                    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
//
//                    this.submit();
//                    return true;
//                };
//                var handleCancel = function() {
//                    this.cancel();
//                };
//
//                // Instantiate the Dialog
//                YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("equipmentBox",
//                                                                                         { width : "560px",
//                                                                                          fixedcenter : true,
//                                                                                          visible : false,
//                                                                                          constraintoviewport : true,
//                                                                                          buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
//                                                                                                      { text:"Cancel", handler:handleCancel } ]
//                                                                                         } );
//
//
//                YAHOO.equipprompt.container.edialog.render();
//                YAHOO.equipprompt.container.edialog.show();
//
//
//            }, id);
//
//            //YAHOO.util.Event for per piece of equipment
//            YAHOO.util.Event.addListener(pcelemid, "click", function showEquipPcPrompt(e, id) {
//
//            // Define various event handlers for Dialog
//            var handleSubmit = function() {
//                var details = new Object();
//                var qty,extra;
//
//                qty = $('epc_qty').value;
//
//                extra = $('serials'+id).value;
//                max = $('pending'+id).value;
//
//                max = $('pending'+id).value;
//                alert("qty:"+qty+" pending:"+max);
//                while ((max - qty) < 0){
//                    qty = prompt("Enter a number not exceeding qty requested:");
//                        if (qty === null) return false;
//                }
//
//                var athand;
//
//                athand = $('athand'+id).value;
//
//                while ((athand - qty) < 0){
//                    qty = prompt("Entered qty exceeds inventory at hand; New Qty:");
//                        if (qty === null) return false;
//                }
//
//                details.type = 'E';
//                details.serial = extra;
//                //details.expdate = "-";
//                details.expdate = expiry;
//
//                details.id = $('id'+id).innerHTML;
//                details.name = $('name'+id).innerHTML;
//                details.desc = $('desc'+id).innerHTML;
//                details.pending = qty;
//                details.unitid = $('unitid'+id).value;
//                details.unitdesc = $('unitdesc'+id).value;
//                details.perpc = 1;
//                details.avg = $('avg'+id).value;
//                details.qtyperpack = $('qtyperpack'+id).value;
//
//                //added for equipment details
//                details.epropno = $('epc_propno').value;
//                details.eestlife = $('epc_estlife').value;
//
//                var list = window.parent.document.getElementById('order-list');
//                result = window.parent.appendOrder(list,details);
//                if (result)
//                    alert('Item added to order list...');
//                else
//                    alert('Failed to add item...');
//                if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
//
//                this.submit();
//            };
//            var handleCancel = function() {
//                this.cancel();
//            };
//
//            // Instantiate the Dialog
//            YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("equipmentBoxPc",
//                                                                                     { width : "560px",
//                                                                                      fixedcenter : true,
//                                                                                      visible : false,
//                                                                                      constraintoviewport : true,
//                                                                                      buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
//                                                                                                  { text:"Cancel", handler:handleCancel } ]
//                                                                                     } );
//
//            YAHOO.equipprompt.container.edialogPc.render();
//            YAHOO.equipprompt.container.edialogPc.show();
//
//
//            }, id);
//        }
//
//        if(filter=='B' || filter=='NS' || filter=='S' || filter=='DS' || filter=='RS' || filter=='LS' || filter=='HS'){
//            packelemid = "packadd"+id;
//            pcelemid = "pcadd"+id;
//            //YAHOO.util.Event for per pack of equipment
//
//            YAHOO.util.Event.addListener(packelemid, "click", function showOtherPrompt(e, id) {
//
//            // Define various event handlers for Dialog
//            var handleSubmit = function() {
//
//                var details = new Object();
//                var qty,extra;
//                var max;
//
//                qty = $('other_qty').value;
//                perpack = $('qtyperpack'+id).value;
//
//                qty = qty*perpack;
//
//                max = $('pending'+id).value;
//
//                while ((max - qty) < 0){
//                    qty = prompt("Enter a number not exceeding qty requested:");
//                        if (qty === null) return false;
//                }
//
//                var athand;
//
//                athand = $('athand'+id).value;
//
//                while ((athand - qty) < 0){
//                    qty = prompt("Entered qty exceeds inventory at hand; New Qty:");
//                        if (qty === null) return false;
//                }
//
//                qty = $('other_qty').value;
//
//                details.type = filter;
//                details.serial = "-";
//                //details.expdate = "-";
//                details.expdate = expiry;
//
//                details.id = $('id'+id).innerHTML;
//                details.name = $('name'+id).innerHTML;
//                details.desc = $('desc'+id).innerHTML;
//                details.pending = qty;
//                details.unitid = $('pckunitid'+id).value;
//                details.unitdesc = $('pckunitdesc'+id).value;
//                details.perpc = $('perpc'+id).value;
//                details.avg = $('avg'+id).value;
//                details.qtyperpack = $('qtyperpack'+id).value;
//
//                var list = window.parent.document.getElementById('order-list');
//                result = window.parent.appendOrder(list,details);
//                if (result)
//                    alert('Item added to order list...');
//                else
//                    alert('Failed to add item...');
//                if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
//
//                this.submit();
//            };
//            var handleCancel = function() {
//                this.cancel();
//            };
//
//            // Instantiate the Dialog
//            YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("otherBox",
//                                                                                     { width : "560px",
//                                                                                      fixedcenter : true,
//                                                                                      visible : false,
//                                                                                      constraintoviewport : true,
//                                                                                      buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
//                                                                                                  { text:"Cancel", handler:handleCancel } ]
//                                                                                     } );
//
//
//            YAHOO.equipprompt.container.edialog.render();
//            YAHOO.equipprompt.container.edialog.show();
//
//
//            }, id);
//
//            //YAHOO.util.Event for per piece of equipment
//            YAHOO.util.Event.addListener(pcelemid, "click", function showOtherPcPrompt(e, id) {
//
//            // Define various event handlers for Dialog
//            var handleSubmit = function() {
//                var details = new Object();
//                var qty,extra;
//
//                qty = $('otherpc_qty').value;
//
//                max = $('pending'+id).value;
//
//                while ((max - qty) < 0){
//                    qty = prompt("Enter a number not exceeding qty requested:");
//                        if (qty === null) return false;
//                }
//
//                var athand;
//
//                athand = $('athand'+id).value;
//
//                while ((athand - qty) < 0){
//                    qty = prompt("Entered qty exceeds inventory at hand; New Qty:");
//                        if (qty === null) return false;
//                }
//
//                details.type = filter;
//                details.serial = "-";
//                //details.expdate = "-";
//                details.expdate = expiry;
//
//                details.id = $('id'+id).innerHTML;
//                details.name = $('name'+id).innerHTML;
//                details.desc = $('desc'+id).innerHTML;
//                details.pending = qty;
//                details.unitid = $('unitid'+id).value;
//                details.unitdesc = $('unitdesc'+id).value;
//                details.perpc = 1;
//                details.avg = $('avg'+id).value;
//                details.qtyperpack = $('qtyperpack'+id).value;
//
//                var list = window.parent.document.getElementById('order-list');
//                result = window.parent.appendOrder(list,details);
//                if (result)
//                    alert('Item added to order list...');
//                else
//                    alert('Failed to add item...');
//                if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
//
//                this.submit();
//            };
//            var handleCancel = function() {
//                this.cancel();
//            };
//
//            // Instantiate the Dialog
//            YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("otherBoxPc",
//                                                                                     { width : "560px",
//                                                                                      fixedcenter : true,
//                                                                                      visible : false,
//                                                                                      constraintoviewport : true,
//                                                                                      buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
//                                                                                                  { text:"Cancel", handler:handleCancel } ]
//                                                                                     } );
//
//            YAHOO.equipprompt.container.edialogPc.render();
//            YAHOO.equipprompt.container.edialogPc.show();
//
//
//            }, id);
//
//        }
           
              
        }
        else {
            rowSrc = '<tr><td colspan="10" style="">There are no open requests...</td></tr>';
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
    
    startAJAXSearch('search',0);    // added by LST -- 02.07.2012
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