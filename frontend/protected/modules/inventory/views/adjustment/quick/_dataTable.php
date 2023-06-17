<?php

$assetsUrl = Bootstrap::getBooster()->getAssetsUrl();
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

$cs->registerCssFile($assetsUrl . '/bootstrap-datepicker/css/bootstrap-datepicker.css');
$cs->registerScriptFile($assetsUrl . '/bootstrap-datepicker/js/bootstrap-datepicker.min.js');

$cs->registerCssFile($assetsUrl . '/bootstrap-editable/css/bootstrap-editable.css');
$cs->registerScriptFile($assetsUrl . '/bootstrap-editable/js/bootstrap-editable.min.js');

$readyJs = <<<'JS'
accounting.settings.currency.symbol = '';
accounting.settings.currency.precision = 2;

$.extend( $.fn.dataTableExt.oStdClasses, {
  "sWrapper": "dataTables_wrapper form-inline"
});

var xhr;
$('#adjustment-items').DataTable({
  columnDefs: [
    {
      orderable: false,
      className: 'select-checkbox',
      contentPadding: 'mm',
      targets:   0
    },
    {
      // ID
      render: function ( data, type, row ) {
        return row['id'];
      },
      targets: 'col-id'
    },
    {
      // ITEM NAME
      render: function ( data, type, row ) {
        var name = row['name'];
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
      // UNIT
      render: function ( data, type, row ) {
        var value = row['unit'];
        if (!value) {
          value = row['small_unit'];
        }
        var lookup = $('#adjustment-form').data('unit_lookup');
        return lookup[value] || '-';
      },
      targets: 'col-unit'
    },
    {
      // EXPIRY
      render: function ( data, type, row ) {
        var value = row['expiry'];
        if (value == '00/00/0000' || value === '01/01/1970') {
          value = '';
        }
        return $('<div/>').append($('<input/>', {
          'class': 'input input-block-level',
          'type': 'text',
          'placeholder' : 'No expiration',
          'value': value
        })).html();
      },
      createdCell: function(td) {
        $(td).find('.input:first').datepicker();
      },
      targets: 'col-expiry'
    },
    {
      // UNIT COST
      render: function ( data, type, row ) {
        var value = row['unit_cost'];
        if (!value) {
          value = '0.00';
        }

        return $('<div/>').append($('<input/>', {
          'class': 'unit-cost-editable input-block-level',
          'type': 'text',
          'value': value
        })).html();
      },
//      createdCell: function(td) {
//        $(td).css('text-align', 'right');
//          .find('a.unit-cost-editable').editable({
//            display: function(value) {
//              $(this).text(accounting.formatMoney(value));
//            }
//          });
//      },
      targets: 'col-unit_cost'
    },
    {
      // QUANTITY/STOCK LEVEL
      render: function ( data, type, row ) {
        var value = row['quantity'];
        if (!value) {
          value = 0;
        }

        return $('<div/>').append($('<input/>', {
          'class': 'input input-block-level',
          'type': 'number',
          'value': value
        })).html();
      },
      targets: 'col-quantity'
    }
  ],
  ajax: function ( data, callback, settings ) {
    xhr = $.ajax({
      dataType: 'json',
      data: {
        area: $('#Adjustment_area_code').val(),
        name: $('#search-name').val(),
        start: data.start,
        length: data.length
      },
      url: '{url}'
    }).done(function(jsonData) {
      var rows = [];
      $.each(jsonData.data, function() {
        rows.push({
          // Dummy data
          '0': '',
          '1': '',
          '2': '',
          '3': '',
          '4': '',
          '5': '',
          '6': '',
          '7': '',

          // Actual data
          id: this.id || '',
          name: this.name || '',
          generic: this.generic || '',
          batch: this.batch || '',
          unit: this.unit || '',
          small_unit: this.small_unit || '',
          big_unit: this.big_unit || '',
          packing: this.packing || '',
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
  sDom: "t<'row-fluid'<'span6'i><'span6'p>>",
  serverSide: true,
  ordering: false,
  lengthChange: false,
  searching: false,
  deferRender: true,
  scrollY: 350,
  scroller: {
    displayBuffer: 5,
    serverWait: 250,
    loadingIndicator: true
  },
  select: {
    style: 'multi+shift',
    selector: 'td:nth-child(1),td:nth-child(2),td:nth-child(3)'
  }
});


$('#search-name + button').off('click').on('click', function(e) {
  e.preventDefault();
  var api = $('#adjustment-items').DataTable();
  api.ajax.reload();
});

$('#search-name + button + button').off('click').on('click', function(e) {
  e.preventDefault();
  var api = $('#adjustment-items').dataTable().api();
  $('#search-name').val('')
  api.ajax.reload();
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

div.DTS div.dataTables_scrollBody {
  background: repeating-linear-gradient(45deg, #fafafa, #fafafa 10px, #fff 10px, #fff 20px);
}

div.DTS div.DTS_Loading {
  z-index: 10001;
}

CSS;

$readyJs = strtr($readyJs, array(
    '{url}' => $this->createUrl('sku/search')
));
$cs->registerScript('adjustment._grid #ready', $readyJs, CClientScript::POS_READY);
$cs->registerCss('adjustment._grid', $css, 'screen');

?>
    <div class="row-fluid">
        <div class="span6"></div>
        <div class="span6">
          <div class="input-append pull-right">
              <input id="search-name" class="input-large" type="text" placeholder="Enter item name / generic name">
              <button class="btn" type="button">Search</button>
              <button class="btn" type="button">Clear</button>
            </div>
        </div>
    </div>
    <div style="margin-top:1em">
        <table id="adjustment-items" class="display table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th width="1"></th>
                    <th class="col-id" width="10%">ID</th>
                    <th class="col-item_name" width="*">Item Name</th>
                    <th class="col-unit" width="10%">Unit</th>
                    <th class="col-batch" width="10%">Batch</th>
                    <th class="col-expiry" width="10%">Expiry</th>
                    <th class="col-unit_cost" width="10%">Unit Cost</th>
                    <th class="col-quantity" width="10%">Stock Level</th>
                </tr>
            </thead>
        </table>
    </div>