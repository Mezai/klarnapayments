{foreach from=$klarna_data item=value}
	{if $value.group.code == 'part_payment'}

<h4>{$klarna_special_description|escape:'htmlall':'UTF-8'}</h4>
	<table class="table">
		<tbody>
      	<tr>
			<td>{$value.details.interest_rate.label}</td>
			<td>{$klarna_special_invoicefee|escape:'htmlall':'UTF-8'}{$value.details.interest_rate.symbol}</td>
		</tr>
      	<tr>
		    <td>{$value.details.monthly_invoice_fee.label}</td>
		    <td>{$klarna_special_interest|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol}</td>
		  </tr>
		</tbody>
</table>

<script type="text/javascript">
new Klarna.Terms.Special({
    el: 'specialxx',
    eid: "{$merchant_id|escape:'htmlall':'UTF-8'}",
    locale: "{$locale|escape:'htmlall':'UTF-8'}",
    type: "{$type|escape:'htmlall':'UTF-8'}"
});
</script>
	{/if}
{/foreach}
