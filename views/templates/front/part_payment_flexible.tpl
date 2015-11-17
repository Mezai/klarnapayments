<!--account-->
{foreach from=$klarna_data item=value}
	{if $value.group.code == 'part_payment'}
<h4>{$value.title}</h4>						
<table class="table">
<tbody>

<tr>
	<td>{$value.details.interest_rate.label}</td>
	<td>{$value.details.interest_rate.value}{$value.details.interest_rate.symbol}</td>
</tr>
<tr>
	<td>{$value.details.start_fee.label}</td>
	<td>{$value.details.start_fee.value}{$value.details.start_fee.symbol}</td>
</tr>
<tr>
	<td>{$value.details.monthly_invoice_fee.label}</td>
	<td>{$value.details.monthly_invoice_fee.value}{$value.details.monthly_invoice_fee.symbol}</td>
</tr>
<tr>
	<td>{$value.details.monthly_invoice_fee.label}</td>
	<td>{$value.details.monthly_invoice_fee.value}{$value.details.monthly_invoice_fee.symbol}</td>
</tr>

	</tbody>
</table>
<p>{$value.use_case}</p><p><span id="accountxx"></span></p>
<script type="text/javascript">
new Klarna.Terms.Account({
    el: 'accountxx',
    eid: "{$merchant_id|escape:'htmlall':'UTF-8'}",
    locale: "{$locale|escape:'htmlall':'UTF-8'}",
    type: "{$type|escape:'htmlall':'UTF-8'}"
});

</script>
{/if}
{/foreach}
