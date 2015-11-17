<!--account-->
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
	</tbody>
</table>


<!--part pay fixed 1-->
<h4>{displayPrice price=$klarna_calc_monthly1}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()}</h4>
<table class="table">
<tbody>
<tr>
	<td></td>
	<td></td>

</tr>

<tr>
	<td></td>
	<td></td>

</tr>

<tr>
	<td></td>
	<td></td>

</tr>

<tr>
	<td></td>
	<td></td>

</tr>

<tr>
	<td></td>
	<td></td>

</tr>

<tr>
	<td></td>
	<td></td>

</tr>
</tbody>
</table>
