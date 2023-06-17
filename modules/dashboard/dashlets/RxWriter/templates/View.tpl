<div id="rx-list-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0">{{$encounterNr}}</div>
<script type="text/javascript">
ListGen.create("rx-list-{{$dashlet.id}}", {
    id:'rx-obj-{{$dashlet.id}}',
    width: "100%",
    height: "auto",
    url: "dashlets/RxWriter/listgen.php",
    showFooter: true,
    iconsOnly: true,
    effects: true,
    dataSet: [],
    autoLoad: true,
    maxRows: {{$settings.pageSize|default:"5"}},
    rowHeight: 32,
    layout: [
        //['<h1>Prescriptions</h1>'],
        ['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
        ['#thead'],
        ['#tbody'],
        ['align:right',
            '<div style="text-align:left; padding:2px">'+
                '<button class="lg-toolbar-button" '+
                    'onclick="openPrescription(); return false;" {{$disableRxWriter}}>'+
                '<img src="../../gui/img/common/default/pencil.png"/>Write prescription</button>'+
            '</div>'
        ]
    ],
    columnModel:[
        {
            name: "date",
            label: "Rx Date",
            width: "25%",
            styles: {
                color: "#0000c0",
                textAlign: "center"
            },
            sorting: ListGen.SORTING.desc,
            sortable: true,
            visible: true
        },
        {
            name: "name",
            label: "Patient name",
            width: "50%",
            sorting: ListGen.SORTING.none,
            sortable: true,
            visible: true,
            styles: {
                fontSize: "11px"
            }
        },
        {
            name: "options",
            label: '',
            width: "25%",
            sortable: false,
            visible: true,
            styles: {
                textAlign: "center",
                whiteSpace: "nowrap"
            },
            render: function(data, index)
            {
                var row = data[index];
                //edited by VAN 10-01-2012
                //var as_grp = 0; 
                /*return  '<img class="link" src="../../images/cashier_print.gif" style="margin:1px" onclick="if (confirm(\'Do you wish to print this prescription\')) { if (confirm(\'Print as a group?\')) as_grp=1; else as_grp=0; Dashboard.dashlets.sendAction(\'{{$dashlet.id}}\', \'printRx\', {id:\''+row.id+'\', encounter:\''+row.encounter+'\', as_grp:as_grp} ); return false;}" />'+
                    '<img class="link" src="../../images/cashier_delete.gif" style="margin:1px" onclick="if (confirm(\'Do you wish to delete this entry\')) Dashboard.dashlets.sendAction(\'{{$dashlet.id}}\', \'deleteRx\', {id:\''+row.id+'\'} ); return false;" />';*/
                
                //'<img class="link" src="../../images/cashier_print.gif" style="margin:1px" onclick="if (confirm(\'Do you wish to print this prescription\')) { printPrescription(\''+row.id+'\'); }" />'+  
                //edited by VAN 11-12-2012
                return  '<img class="link" src="../../images/cashier_print.gif" style="margin:1px" onclick="printPrescription(\''+row.id+'\');" />'+ 
                        '<img class="link" src="../../images/cashier_delete.gif" style="margin:1px" onclick="if (confirm(\'Do you wish to delete this entry\')) Dashboard.dashlets.sendAction(\'{{$dashlet.id}}\', \'deleteRx\', {id:\''+row.id+'\'} ); return false;" />';   
                
            }
        }
    ],
});

function printPrescription(id){
    /*
    $J('#printgrpDialog').dialog({
        title:'Print as a group?',
        width: 250,
        height: 100,
        buttons: {
            'Yes': function(){
                $J(this).dialog("close");
                Dashboard.dashlets.sendAction(dashlet_id, print_type, {id:id, encounter:encounter_nr, as_grp:1} );
            },
            'No': function(){
                $J(this).dialog("close");
                Dashboard.dashlets.sendAction(dashlet_id, print_type, {id:id, encounter:encounter_nr, as_grp:0} );
            }
        },
        close: function(){
            
        }
    })
    */
    
    $J('#printgrpDialog').dialog({
        title:'Select Yes or No?',
        width: 250,
        height: 100,
        modal: true,
        show: 'fade',
        hide: 'fade',
        resizable: false,
        closeOnEscape: true,
        open: function(event, ui){
            $J('#prescription_id').val(id);
            //$J(this).dialog('close');
        },
        close: function(){
           
        }
    })
    
}

function printAsGrp(as_grp){
  //alert(parameters.toSource());
  Dashboard.dashlets.sendAction('{{$dashlet.id}}', 'printRx', {id:$J('#prescription_id').val(), encounter:$('encounterNr').value, as_grp:as_grp} );
  $J('#printgrpDialog').dialog('close');
}

//------------------


function openPrescription()
{
        Dashboard.launcher.launch({
            title:'Write prescription',
            href:'../../modules/prescription/seg-clinic-new-prescription.php{{$URL_APPEND}}&checkintern=1&encounter_nr='+$('encounterNr').value,
            width: 820,
            height: 450
        })
}
</script>

<!-- added by VAN 11-12-2012-->
<div class="segPanel" id="printgrpDialog" style="display:none" align="left">
    <span>Print as a group?</span><br><br>
    <div align="center" style="overflow:hidden">
        <input type="hidden" name="prescription_id" id="prescription_id" value="">
        
        <button onclick="printAsGrp('1');">
        <img src="../../gui/img/common/default/accept.png">
        Yes
        </button> &nbsp;
        <button onclick="printAsGrp('0');">
        <img src="../../gui/img/common/default/stop.png">
        No
        </button>
    </div>
</div>