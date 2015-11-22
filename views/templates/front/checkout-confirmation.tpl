{if isset($klarna_error)}
<div class="warning">{$klarna_error}</div>
{else}
{$klarna_html}
{/if}