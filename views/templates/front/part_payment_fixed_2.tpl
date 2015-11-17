<!--part pay fixed 1-->
{foreach from=$klarna_data item=value}
	{if $value.group.code == 'part_payment'}
<h4>{displayPrice price=$klarna_calc_monthly2}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[2]->getDescription()}</h4>
<table class="table">
<tbody>
<tr>
	<td>{$value.details.interest_rate.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[2]->getInterestRate()}{$value.details.interest_rate.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{l s='Effective interest rate' mod='klarnapayments'}</td>
	<td>{$klarna_calc_apr2|escape:'htmlall':'UTF-8'}{$value.details.interest_rate.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{$value.details.start_fee.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[2]->getStartFee()}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{$value.details.monthly_invoice_fee.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[2]->getInvoiceFee()}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}{l s='/month'
		mod='klarnapayments'}</td>

</tr>

<tr>
	<td>{$value.details.monthly_pay.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_calc_monthly2|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}{l s='/month'
		mod='klarnapayments'}</td>

</tr>

<tr>
	<td>{l s='Total cost' mod='klarnapayments'}</td>
	<td>{$klarna_calc_total_credit2|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>
</tbody>
</table>
<p><span id="accountxx"></span></p>
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
								
							