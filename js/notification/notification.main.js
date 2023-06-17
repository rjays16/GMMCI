'use strict';

/**
 * Namespace
 * Please see http://autobahn.ws/js/reference_wampv1.html
 */

if ('object' !== typeof his) {
    var his = {};
    his.main = {
        notification: {}
    };
} else {
    if ('object' !== typeof his.main) {
        his.main = {
            notification: {}
        };
    } else {
        if ('object' !== typeof his.main.notification) {
            his.main.notification = {

            };
        }
    }
}


his.main.notification = function(uri, topics){
	var _connection = new his.websocket.HISRealtimeConnection(uri);
    var _topics = topics;
    var _connected = null;
    
    this.connect = function(){
    	_connection.connect();

    	for(var key in _topics) {
			var topic = _topics[key].trim();
			_connection.subscribe(topic, his.main.notification.functions);
		}
    }

    _connection.on('connect', function(){
        var temp_setting = $j.extend(true, {}, g_settings);;
        temp_setting.closeBtn = false;
        _connected = parent.$j.sticky("connected", temp_setting);
        console.log(_connected);
    });

    _connection.on('hangUp', function(){
        if(_connected != null){
            $j('#' + _connected.id).dequeue().fadeOut('fast');
            _connected = null
        }
    });
};

var g_contentFrame = new his.frames("contentFrame").getFrame();
var g_settings = {'speed' : 'fast', 'autoclose' : false, 'duplicates' : false};
var g_l = window.location;
var g_href = g_l.protocol + "//" + g_l.host + "/" + g_l.pathname.split('/')[1] + "/";

function rad_new_order(details){
    g_contentFrame.src = g_href + "modules/laboratory/labor_test_request_pass.php?"
        +"target=radiorequestlist&dept_nr=158&patient_type="+details.encounter_type+"&from_notif=1";
}

function lab_new_order(details){
    g_contentFrame.src = g_href + "modules/laboratory/labor_test_request_pass.php?"
        +"target=seglabservrequest_new&user_origin=lab&patient_type="+details.encounter_type+"&from_notif=1";
}

function lab_spl_new_order(details){
    g_contentFrame.src = g_href + "modules/laboratory/labor_test_request_pass.php?"
        +"target=specialLab_list&user_origin=splab&patient_type="+details.encounter_type+"&from_notif=1";
}

//add new topics @ global_conf/areas
var topicCallbacks = {

	// Department
	"LAB_DEPT": {
		// Event
		NEW_ORDER: function(data) {
            var enc_type = getEncounterTypeName(data.encounter_type);
            parent.$j.sticky(
                "New Laboratory Order. \n" + enc_type,
                g_settings, lab_new_order, data
            );
		}
	},
    "LAB_SPL": {
        // Event
        NEW_ORDER: function(data) {
            var enc_type = getEncounterTypeName(data.encounter_type);
            parent.$j.sticky(
                "New Special Lab Order. " + enc_type,
                g_settings, lab_spl_new_order, data
            );
        }
    },
    "RAD_DEPT": {
        // Event
        NEW_ORDER: function(data) {
            var enc_type = getEncounterTypeName(data.encounter_type);
            parent.$j.sticky(
                "New Radiology Order. " + enc_type,
                g_settings, rad_new_order, data
            );
        },
        DELETE_ORDER: function(data) {
            parent.$j.sticky(
                data.message, g_settings
            );
        },
    },
};

his.main.notification.functions = function(topic, data){
    console.log(data);
	var data = data.data;
	var _event = data.event.toUpperCase().replace(/\s+/g, '_');
	topicCallbacks[topic.toUpperCase()][_event](data);
};

var getEncounterTypeName = function(encounter_type){
    var name = "";
    switch(encounter_type){
        case '1': 
        case 1: 
            name = " (Emergency Room)";
            break;
        case '2': 
        case 2: 
            name = " (Outpatient)";
            break;
        case '3': case '4': 
        case 3: case 4: 
            name = " (Inpatient)";
            break;
    }
    return name;
}