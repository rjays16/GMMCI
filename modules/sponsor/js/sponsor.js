function parseFloatEx(x) {
  var str = x.toString().replace(/\,|\s/,'')
  return parseFloat(str)
}

function formatNumber(num,dec) {
  var nf = new NumberFormat(num);
  if (isNaN(dec)) dec = nf.NO_ROUNDING;
  nf.setPlaces(dec);
  return nf.toFormatted();
}

function resetControls() {
  $('patientname').value="";
  $('pid').value="";
  $('encounter_nr').value="";
  $('clear-enc').disabled = false;
  $('sw-class').innerHTML = 'None';
  $('encounter_type_show').innerHTML = 'WALK-IN';
  $('encounter_type').value = '';
  $('select-enc').className = 'segSimulatedLink';
  
  //alert('msg:'+rlst.initialMessage)
  if (typeof(rlst) == 'object') {
    rlst.clear({message:rlst.initialMessage});
  }
  
  $('content').hide();
  new Effect.Appear($('rqsearch'),{ duration:0.5 });
}
  
function reclassRows(list,startIndex) {
  if (list) {
    var dBody=list.getElementsByTagName("tbody")[0];
    if (dBody) {
      var dRows = dBody.getElementsByTagName("tr");
      if (dRows) {
        for (i=startIndex;i<dRows.length;i++) {
          dRows[i].className = "wardlistrow"+(i%2+1);
        }
      }
    }
  }
}

function addPatientRequest(details) {
  list = $('rlst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        var source=details["source"],
          nr=details["refno"],
          item=details["itemno"],
          id=source+nr+item,
          date=details["date"],
          name=details["name"],
          qty=details["qty"],
          total=details["total"],
          discounted=details["discounted"],
          status=details["status"],
          disabled=(details["disabled"]=='1');

        var dRows = dBody.select("tr");
        var alt = (dRows.length%2>0) ? 'alt':'';
        var disabledAttrib = disabled ? 'disabled="disabled"' : "";
        
        var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'ri_date_'+id}).update(date)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'ri_source_'+id }).update(source)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'ri_nr_'+id, style:'color:#660000'}).update(nr)
          )
        ).insert(
          new Element('td', { class:'leftAlign' } ).update(
            new Element('span', { id: 'ri_name_'+id }).update(name).setStyle( { font:'bold 11px Tahoma' } )
          ).insert(
            new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'ri_qty_'+id}).update(qty+' items')
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'ri_total_'+id}).update( formatNumber(total,2) )
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'ri_discounted_'+id}).update( formatNumber(discounted,2) )
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'ri_status_'+id}).update(status)
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('img', 
              { id:'ri_view_'+id, title:'Edit grant', class:'link', src:'../../images/cashier_edit.gif', 'source':source, 'nr':nr, 'item':item } 
            ).setStyle( { margin:'1px' }
            ).observe('click', 
              function(event) {
                //showDetails(source, nr);
                grantItem( this.getAttribute('source'), this.getAttribute('nr'), this.getAttribute('item') )
              }
            )
          ).insert(
            new Element('img', 
              { id:'ri_del_'+id, title:'Remove grants', class:'link', src:'../../images/cashier_cancel.gif', 'source':source, 'nr':nr, 'item':item }
            ).setStyle( { margin:'1px' }
            ).observe('click', 
              function(event) {
                //showDetails(source, nr);
                alert(this);
              }
            )
          )
        );
        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

function addPatientBillAccount(details) {
  list = $('hlst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        var nr=details["nr"],
          id=nr,
          date=details["date"],
          status=details["status"],
          disabled=(details["disabled"]=='1');
        var dRows = dBody.select("tr");
        var alt = (dRows.length%2>0) ? 'alt':'';
        var disabledAttrib = disabled ? 'disabled="disabled"' : "";

        var row = new Element('tr', { class: alt, id:'requestRow'+id , style:'height:26px' } ).update(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'requestNr'+id, style:'color:#660000'}).update(nr)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'requestDate'+id}).update(date)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'requestStatus'+id}).update(status)
          )
        ).insert(
          new Element('td', { class:'rightAlign' }).update(
            new Element('img', 
              { id:'requestView'+id, title:'View details', class:'segSimulatedLink', src:'../../gui/img/common/default/show_details.gif', 'nr':nr } 
            ).observe('click', 
              function(event) {
                //showDetails(source, nr);
                iNr = nr;
                
                $('rqsearch').hide();
                new Effect.Appear($('content'),{ duration:0.5 });
                
                blst.fetcherParams = { source: 'FB', nr: nr};
                blst.reload();
                
                balst.fetcherParams = { nr: nr};
                balst.reload();
              }
            )
          )
        );
        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

function showDetails(src, nr) {
  var o = new Object();
  o['source'] = src;
  o['nr'] = nr;
  dlst.fetcherParams = o;
  dlst.reload();
}

function addBillingBreakdownItem(details) {
  list = $('blst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      if (typeof(details) != 'object') details = { FLAG: false};
      if (details['FLAG']) {
        var 
          nr=details["nr"],
          id=details["code"],          
          area=details["area"],
          total=details["total"],
          imgStatus=details["status"]=='1' ? 'tick.png' : 'cross.png',
          disabled=(details["disabled"]=='1');
        var dRows = dBody.select("tr");
        var alt = (dRows.length%2>0) ? 'alt':'';
        var disabledAttrib = disabled ? 'disabled="disabled"' : "";

        var row = new Element('tr', { class: alt, id:'bRow'+id , style:'height:26px' } ).update(
          new Element('td', { class:'leftAlign' } ).update(
            new Element('span', { id: 'bArea'+id, style: 'color:#000066' }).update(area)
          ).insert(
            new Element('input', { id: 'bId'+id, type: 'hidden', value: id })
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'bTotal'+id, style:'font:bold 11px Tahoma; color:#006600'}).update( formatNumber(parseFloatEx(total),2) )
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'bStatus'+id}).update(
              new Element('img', { src: '../../images/'+imgStatus, border:0} )
            )
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('input', 
              { id:'bViewAction'+id, title:'View details', class:'segButton', src:'../../gui/img/common/default/show_details.gif', 'nr':nr, type:'button', value:'', style:'font:bold 12px Arial', value:'>' } 
            ).observe('click', 
              function(event) {
                // Load area details
                //startLoading();
                dlst.fetcherParams = { nr:nr,area:id };
                dlst.reload();
              }
            )
          )
        );
        
        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

function addItem(details) {
  list = $('dlst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      var lastRowNum = null,
          dRows = dBody.select("tr");
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        //alt = (dRows.length%2>0) ? ' class="alt"':'';
        var source=details['source'],
          refno=details['nr'],
          desc=details["description"],
          code=details["code"],
          id=code,
          quantity=details["quantity"],
          price=details["price"],
          discount=details["discount"],
          status=details["status"],
          disabled=(details["disabled"]==1);
        alt = (dRows.length%2>0) ? 'alt' : '';
        var disabledAttrib = disabled ? 'disabled="disabled"' : '';
/*
        src = '<tr'+alt+' id="row'+id+'" style="height:28px">';

        src+=
          '<td class="centerAlign">'+
            '<span id="itemDesc'+id+'">'+desc+'</span>'+
            '<input type="hidden" id="itemSource'+id+'" value="'+source+'"/>'+
          '</td>'+
          '<td class="centerAlign"><span id="requestNr'+id+'" style="color:#660000">'+nr+'</span></td>'+
          '<td class="centerAlign"><span id="requestDate'+id+'">'+date+'</span></td>';
        src+= 
          '<td class="rightAlign">'+
            '<input id="requestAdd'+id+'" class="segButton" type="button" value="+" '+disabledAttrib+' style="font:normal 16px Impact; padding:0px 2px; color:#000080; cursor:pointer" />'+
            '<input id="requestView'+id+'" class="segButton" type="button" value=">" '+disabledAttrib+' style="font:normal 16px Impact; padding:0px 2px; cursor:pointer"/>'+
          '</td>';
        src+='</tr>';
*/
        var row = new Element('tr', { class: alt, id:'itemRow'+id , style:'height:26px' } ).update(
          new Element('td', { class:'leftAlign', style:'padding-left:4px' }).update(
            new Element('span', { id: 'itemDesc'+id, style:'color:#000080' }).update(desc)
          ).insert(
            new Element('input', { id: 'itemSource'+id, type:'hidden', value:source })
          ).insert(
            new Element('input', { id: 'itemRefNo'+id, type:'hidden', value:refno })
          ).insert(
            new Element('input', { id: 'itemCode'+id, type:'hidden', value:code })
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'itemQuantity'+id }).update(quantity)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'itemPrice'+id} ).update(formatNumber(price,2))
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'itemDiscount'+id} ).update(formatNumber(discount,2))
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'itemStatus'+id} ).update(status)
          )
        ).insert(
          new Element('td', { class:'rightAlign' }).update(
            new Element('img', { id:'requestAdd'+id, class:'segSimulatedLink', src:'../../images/note_add.png' } 
            ).observe(
              'click', function(event) {
                openGrant(source, refno, code);
              }
            )
          )
        );

        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="6">List is currently empty...</td></tr>');
      }
      return true;
    }
  }
  return false;
}


/* Called by the blst ListGen object to populate the per-account-type 
breakdown of the billing statement by billing area */
function addBreakdownDetail(details) {
  list = $('dlst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      var lastRowNum = null,
          dRows = dBody.select("tr");
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        var id=details['account_id'],
          source=details['source'],
          refno=details['nr'],
          area=details['area'],
          account=details["account"],
          total=details["total"],
          grant=details["grant"],
          status=details["status"],
          disabled=(details["disabled"]==1);
        alt = (dRows.length%2>0) ? 'alt' : '';
        var disabledAttrib = disabled ? 'disabled="disabled"' : '';
        var row = new Element('tr', { class: alt, id:'itemRow'+id , style:'height:26px' } ).update(
          new Element('td', { class:'leftAlign', style:'padding-left:4px' }).update(
            new Element('span', { id: 'itemDesc'+id, style:'color:#000080' }).update(account)
          ).insert(
            new Element('input', { id: 'itemId'+id, type:'hidden', value:id })
          ).insert(
            new Element('input', { id: 'itemRefNo'+id, type:'hidden', value:refno })
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'itemTotal'+id, style:'font:bold 11px Tahoma' } ).update( formatNumber(parseFloatEx(total),2) )
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'itemGrant'+id, style:'font:bold 11px Tahoma'} ).update( formatNumber(parseFloatEx(grant),2) )
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'itemStatus'+id} ).update(status)
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('img', { id:'requestAdd'+id, class:'segSimulatedLink', src:'../../images/note_add.png' } 
            ).observe(
              'click', function(event) {
                openGrant(source, refno, id, area, total);
              }
            )
          )
        );

        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="6">List is currently empty...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

function addGrant(details) {
  list = $('glst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      var lastRowNum = null,
          dRows = dBody.select("tr");
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        //alt = (dRows.length%2>0) ? ' class="alt"':'';
        var id=details['id'],
          acct=details['acct_id'],
          name=details['acct_name'],
          date=details['date'],
          amount=details["amount"],
          encoder=details["encoder"],
          disabled=(details["disabled"]==1);
        alt = (dRows.length%2>0) ? 'alt' : '';
        var disabledAttrib = disabled ? 'disabled="disabled"' : '';
        var row = new Element('tr', { class: alt, id:'grantRow'+id , style:'height:26px' } ).update(
          new Element('td', { class:'leftAlign', style:'padding-left:4px' }).update(
            new Element('span', { id: 'grantDate'+id, style:'color:#0000f0' }).update(date)
          ).insert(
            new Element('input', { id: 'grantId'+id, type:'hidden', value:id })
          ).insert(
            new Element('input', { id: 'grantAccount'+id, type:'hidden', value:acct })
          )
        ).insert(
          new Element('td', { class:'leftAlign' } ).update(
            new Element('span', { id: 'grantName'+id, style:'color:#2d2d2d' }).update(name)
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'grantAmount'+id, name:'grantAmount', style:'color:#000080' }).update(formatNumber(amount,2))
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'grantEncoder'+id, style:'color:#2d2d2d' }).update(encoder)
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('img', { id:'grantDelete'+id, class:'link', src:'../../images/cashier_delete.gif' } 
            ).observe(
              'click', function(event) {
                xajax.call('deleteGrant', { parameters:[ id ] } );
              }
            )
          )
        );
        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="10">No grants found for this item...</td></tr>');
      }
      return true;
    }
  }
  return false;
}

function addBillGrantAccount(details) {
  list = $('balst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      var lastRowNum = null,
          dRows = dBody.select("tr");
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        //alt = (dRows.length%2>0) ? ' class="alt"':'';
        var id=details['id'],
          name=details['name'],
          amount=details["amount"],
          status=details["status"],
          disabled=(details["disabled"]==1);
        alt = (dRows.length%2>0) ? 'alt' : '';
        var disabledAttrib = disabled ? 'disabled="disabled"' : '';
        var row = new Element('tr', { class: alt, id:'bgaRow'+id , style:'height:26px' } ).update(
          new Element('td', { class:'leftAlign', style:'padding-left:4px' }).update(
            new Element('span', { id: 'bgaName'+id, style:'color:#006000' }).update(name)
          ).insert(
            new Element('input', { id: 'bgaId'+id, type:'hidden', value:id })
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'bgaAmount'+id, style:'color:#000066' }).update(formatNumber(amount,2))
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'bgaEncoder'+id, style:'color:#2d2d2d' }).update('')
          )
        ).insert(
          new Element('td', { class:'centerAlign' }).update(
            new Element('img', { id:'bgaInfo'+id, src:'../../images/information.png', style:'cursor:pointer' } 
            ).observe(
              'click', function(event) {
              }
            )
          )
        );
        dBody.insert(row);
      }
      else {
        dBody.update('<tr><td colspan="10">No billing payments assigned yet...</td></tr>');
      }
      return true;
    }
  }
  return false;
}


function removeItem(id) {
  var destTable, destRows;
  var table = $('order-list');
  var rmvRow=$('row'+id);
  if (table && rmvRow) {
    var rndx = rmvRow.rowIndex-1;    
    rmvRow.remove();    
    if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
      appendOrder(table, null);
    reclassRows(table,rndx);
  }
}

function changeTransactionType() {
  clearEncounter();
  refreshDiscount();
}

function clickGrant() {
  if (parseFloatEx($('grant-amount').value)==0) {
    alert('Please enter an amount...');
    return false;
  }
  xajax.call('addGrant', { parameters:[ iSrc, iNr, iCode, iArea, $('grant-account').value, parseFloatEx($('grant-amount').value) ] });
}