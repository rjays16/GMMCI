<?php

$baseUrl = Yii::app()->request->baseUrl;
/** @var CClientScript $cs */
$cs = Yii::app()->getClientScript();
$cs->registerCssFile($baseUrl . '/js/datatables/css/dataTables.bootstrap2.css');
$cs->registerCssFile($baseUrl . '/js/datatables/extensions/Select/css/select.dataTables.min.css');
$cs->registerCssFile($baseUrl . '/js/datatables/extensions/Scroller/css/scroller.bootstrap.min.css');

$cs->registerScriptFile($baseUrl . '/js/datatables/js/jquery.dataTables.min.js');
$cs->registerScriptFile($baseUrl . '/js/datatables/js/dataTables.bootstrap.min.js');
$cs->registerScriptFile($baseUrl . '/js/datatables/js/dataTables.bootstrap2.js');
$cs->registerScriptFile($baseUrl . '/js/datatables/extensions/Select/js/dataTables.select.min.js');
$cs->registerScriptFile($baseUrl . '/js/datatables/extensions/Scroller/js/dataTables.scroller.min.js');
$cs->registerScriptFile($baseUrl . '/js/accounting.min.js');

$readyJs = <<<'JS'
accounting.settings.currency.symbol = '';
accounting.settings.currency.precision = 2;

$.extend( $.fn.dataTableExt.oStdClasses, {
  "sWrapper": "dataTables_wrapper form-inline"
});

var xhr;
$('#adjustment-items').DataTable({
  sDom: "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
  columnDefs: [
    {
      orderable: false,
      className: 'select-checkbox',
      contentPadding: 'mm',
      targets:   0
    },
    {
      // ITEM NAME
      render: function ( data, type, row ) {
            console.log(row);
        var name = row['name'] + ' ' + $('<div/>').append($('<strong/>').text('(#' + row['id'] + ')')).html();
        if (row['generic']) {
          name +=  $('<div/>').append('<br/>').append($('<small/>', { 'class':'light-blue-800' }).text(row['generic'])).html();
        }
        return name;
      },
      targets: 'col-item_name'
    },
    {
      // BATCH
      render: function ( data, type, row ) {
        if (row['batch']) {
          return row['batch'];
        }
        return '<em>-No stock-</em>'
      },
      targets: 'col-batch'
    },
    {
      // EXPIRY
      render: function ( data, type, row ) {
        return row['expiry'];
      },
      targets: 'col-expiry'
    },
    {
      // UNIT COST
      render: function ( data, type, row ) {
        return accounting.formatMoney(row['unit_cost']);
      },
      createdCell: function(td) {
        $(td).css('text-align', 'right')
      },
      targets: 'col-unit_cost'
    },
    {
      // QUANTITY/STOCK LEVEL
      render: function ( data, type, row ) {
        return row['quantity'] || '-';
      },
      targets: 'col-quantity'
    },
    {
      // REASON
      render: function ( data, type, row ) {
        return '';
      },
      targets: 'col-reason'
    }
  ],
  ajax: function ( data, callback, settings ) {
    if (xhr) {
      return;
    }
    xhr = $.ajax({
      dataType: 'json',
      data: {
        area: $('#Adjustment_area_code').val(),
        name: data.search.value,
        start: data.start,
        len: data.length
      },
      url: '{url}'
    }).done(function(jsonData) {
      var rows = [];
      $.each(jsonData.data, function() {
//        rows.push([
//          this.name || '',
//          this.batch || '',
//          this.expiry || '',
//          this.unit_cost || '0.00',
//          this.quantity || '-',
//          ''
//        ]);
        rows.push({
          // Dummy data
          '0': '',
          '1': '',
          '2': '',
          '3': '',
          '4': '',
          '5': '',
          '6': '',

          // Actual data
          id: this.id || '',
          name: this.name || '',
          generic: this.generic || '',
          batch: this.batch || '',
          expiry: this.expiry || '',
          unit_cost: this.unit_cost || '',
          quantity: this.quantity || ''
        });
      });
      callback( {
        draw: data.draw,
        data: rows,
        recordsTotal: jsonData.recordsTotal,
        recordsFiltered: jsonData.recordsFiltered
      });
    }).always(function() {
      // Set current running request to null
      xhr = null;
    });
  },
  serverSide: true,
  ordering: false,
  deferRender: true,
  scrollY: 350,
  scroller: {
    displayBuffer: 5,
    serverWait: 250,
    loadingIndicator: true
  },
  select: {
    style: 'multi+shift',
    selector: 'td'
  }
});

JS;

$css = <<<'CSS'
tr {
  cursor: default;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

table.dataTable td.focus {
  outline: 1px solid #8fb1dd;
  outline-offset: -4px;
  background-color: #f1f6fc !important;
}

table.dataTable tbody > tr.selected > td {
  background-color: #e6edfb;
}

table.dataTable.hover tbody>tr.selected:hover>td,
table.dataTable.hover tbody>tr>.selected:hover,
table.dataTable.display tbody>tr.selected:hover>td,
table.dataTable.display tbody>tr>.selected:hover {
  background-color: #d8e4fb;
}
CSS;

$readyJs = strtr($readyJs, array(
    '{url}' => $this->createUrl('sku/search')
));
$cs->registerScript('adjustment._grid #ready', $readyJs, CClientScript::POS_READY);
$cs->registerCss('adjustment._grid', $css, 'screen');

?>

    <table id="adjustment-items" class="display table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th width="1"></th>
                <th class="col-item_name" width="*">Item Name</th>
                <th class="col-batch" width="12.5%">Batch</th>
                <th class="col-expiry" width="12.5%">Expiry</th>
                <th class="col-unit_cost" width="12.5%">Unit Cost</th>
                <th class="col-quantity" width="12.5%">Stock Level</th>
                <th class="col-reason" width="12.5%">Reason</th>
            </tr>
        </thead>
    </table>