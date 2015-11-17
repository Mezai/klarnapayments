<h4>{l s='Invoice summary' mod='klarnapayments'}</h4>
	<table class="table">
		<tbody>
      	<tr>
      		<td>{l s='Shopping cart summary' mod='klarnapayments'}</td>	
      		<td>{displayPrice price=$total}</td>
      	</tr>
      	<tr>
		    <td>{l s='Invoice fee' mod='klarnapayments'}</td>
		    <td>{displayPrice price=$klarna_invoice_sum}</td>
		   </tr>
		<tr>
			<td>{l s='Order total' mod='klarnapayments'}</td>
			<td>{displayPrice price=$klarna_invoice_sum + $total} </td>
		</tr>
		</tbody>
</table>
<p><span id="invoicexx"></span></p>
<script type="text/javascript">
new Klarna.Terms.Invoice({
el: 'invoicexx',
eid: "{$merchant_id|escape:'htmlall':'UTF-8'}",
locale: "{$locale|escape:'htmlall':'UTF-8'}",
charge: "{$klarna_invoice_sum|escape:'htmlall':'UTF-8'}",
type: "{$type|escape:'htmlall':'UTF-8'}"
});
</script>