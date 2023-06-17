function appendGrant(details) {
  list = $('grants_list');
  if (list) {    
    var dBody=list.select("tbody")[0];
    if (dBody) {
      if (!details) details = { FLAG: false};
      if (details['FLAG']) {
        var source=details["source"],
          nr=details["refno"],
          item=details["itemno"],
          account=details["account"],
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