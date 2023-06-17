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
  if (typeof(flst) == 'object') {
    rlst.clear({message:flst.initialMessage});
  }
  if (typeof(rlst) == 'object') {
    rlst.clear({message:rlst.initialMessage});
  }
  if (typeof(alst) == 'object') {
    rlst.clear({message:alst.initialMessage});
  }
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

function addAdjustment(details) {
  list = $('alst');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      if (typeof(details)=='object') {
        var nr=details["nr"],
          date=details["date"],
          //account_id=details["account_id"],
          //account_name=details["account_name"],
          amount=details["amount"],
          encoder=details["encoder"],
          remarks=details["remarks"],
          id=nr,
          status=details["status"];

        var dRows = dBody.select("tr");
        var alt = (dRows.length%2>0) ? 'alt':'';
        
        var row = new Element('tr', { class: alt, id:'fi_'+id , style:'height:26px' } ).update(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'fi_date_'+id}).update(date)
          )
        ).insert(
          new Element('td', { class:'rightAlign' } ).update(
            new Element('span', { id: 'fi_amount_'+id, style:'color:#660000'}).update(formatNumber(amount,2))
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'fi_encoder_'+id, style:'color:#660000'}).update(encoder)
          )
        ).insert(
          new Element('td', { class:'leftAlign' } ).update(
            new Element('span', { id: 'fi_remarks_'+id}).update(remarks)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('span', { id: 'fi_status_'+id}).update(status)
          )
        ).insert(
          new Element('td', { class:'centerAlign' } ).update(
            new Element('img',{ id:'fi_edit_'+id, class:'segSimulatedLink', src:'../../images/cashier_edit.gif' }
            ).setStyle( { margin:'1px' }
            ).observe( 'click',
              function(event) {
              }
            )
          ).insert(
            new Element('img',{ id:'fi_delete_'+id, class:'segSimulatedLink', src:'../../images/cashier_delete.gif' }
            ).setStyle( { margin:'1px' }
            ).observe( 'click',
              function(event) {
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