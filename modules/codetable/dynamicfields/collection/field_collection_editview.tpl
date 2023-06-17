{if $options.rows <= 1 }
<input id="{$options.id}" name="{$options.name}" class="{$options.className|default:"input"}" type="text" value="{$value|escape:"html"}" style="width:{$options.width}" {if $options.required}required="required"{/if} />
{else}
<textarea id="{$options.id}" name="{$options.name}" class="{$options.className|default:"input"}" rows="{$options.rows}" style="width:{$width}" {if $options.required}required="required"{/if} >{$value}</textarea>
{/if}
<script type="text/javascript">
$('{$options.id}').validator=function() {ldelim}
o = $J(this);
if (o.is('[required]')) {ldelim}
return o.val() !== '';
{rdelim}
else
return true;
{rdelim};
</script>
