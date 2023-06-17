<input id="{$options.id}" name="{$options.name}" type="hidden" value="{$value}" />
<input id="{$options.id|cat:"_view"}" class="{$options.className|default:"input"}" type="text" 
value="{$value|string_format:"%.`$options.decimal`f"}" 
style="width:100px; text-align:right" 
{if $options.required}required="required"{/if} 
onfocus="var x=$J('#{$options.id}').val();var i=parseFloat(x);if(isNaN(i))i=0;$J(this).val(i);this.select();"
onblur="var i=parseFloat(this.value);if(isNaN(i))i=0;$J('#{$options.id}').val(i);this.value=$J().number_format(i,{ldelim}numberOfDecimals:{$options.decimal|default:2}{rdelim})"
/>
<script type="text/javascript">
$('{$options.id}').validator=function(){ldelim}
o = $J(this);
if (o.is('[required]')){ldelim}
if (o.val() === '') return false;
{rdelim}
var i=parseFloat(o.val());
if (isNaN(i)) return false;
else {ldelim}
return true;
{rdelim}
{rdelim};
</script>