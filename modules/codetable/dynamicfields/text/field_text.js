if (!typeof('CodeTable')=='undefined') CodeTable = {};
CodeTable.validators.text =
function(elem, required) {
if (required) {
if (!elem.value)
return false;
}
return true;
};