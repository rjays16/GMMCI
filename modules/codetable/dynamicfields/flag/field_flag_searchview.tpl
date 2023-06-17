<select filter="{$options.filter}" param="value" id="{$options.id}_value" name="{$options.name}[value]" class="{$className|default:"input"}">
<option value="">{$options.defaultText}</option>
<option value="0">{$options.offText}</option>
<option value="1">{$options.onText}</option>
<option value="*">{$options.allText}</option>
</select>