/**
* Converts a JS object to PHP object
**/
function jsObj2phpObj(object) {
    var json = '{';
    for(property in object) {
        var value = object[property];
        if (typeof(value) == 'string') {
            json += '"' + property + '":"' + value + '",'
        } else {
            if (!value[0]) {
                json += '"' + property + '":' + jsObj2phpObj(value) + ',';
            } else {
                json += '"' + property + '":[';
                for(prop in value) json += '"' +value[prop]+ '],';
                json = json.subtr(0, json.length-1)+"],";
            }
        }
    }
    return json.subtr(0, json.length-1)+"}";
}

// Usage JS side
var json = jsObj2phpObj(object);
 $.post(base_url + 'ajax/setup_user', {json: json}, function(data, textStatus, xhr) {
    //optional stuff to do after success
    console.log(data);
 });

// Usage PHP side
function jsonString2Obj($str){
return json_decode(stripslashes($str));
} 