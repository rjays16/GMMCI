<div style="text-align:center">
{if $value == 0}
{if $options.offImage}
<img src="{$options.imagesPath}{$options.offImage}" align="absmiddle" />
{else}
{$options.offText}
{/if}
{else}
{if $options.onImage}
<img src="{$options.imagesPath}{$options.onImage}" align="absmiddle" />
{else}
{$options.onText}
{/if}
{/if}
</div>