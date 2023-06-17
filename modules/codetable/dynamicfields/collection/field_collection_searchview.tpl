{if $options.modes}
<select filter="{$options.filter}" param="mode" id="{$options.id}_mode" name="{$options.name}[mode]" class="{$className|default:"input"}">
<option value="startswith">Starts with</option>
{if $options.modes|contains:"endswith"}<option value="endswith">Ends with</option>{/if}
{if $options.modes|contains:"contains"}<option value="contains">Contains</option>{/if}
{if $options.modes|contains:"doesnotcontain"}<option value="doesnotcontain">Does not contain</option>{/if}
{if $options.modes|contains:"exactly"}<option value="exactly">Exactly</option>{/if}
</select>
{/if}
<input filter="{$options.filter}" param="value" id="{$options.id}_value" name="{$options.name}[value]" class="{$className|default:"input"}" type="text" value="{$value|escape:"html"}" style="width:{$width}" />