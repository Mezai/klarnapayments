{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}




{if $klarna_locale == 'SE'}
<h4>{$klarna_special_description|escape:'htmlall':'UTF-8'}</h4>
	<table class="table">
		<tbody>
      	<tr>
			<td>{$partpayment_interest_label|escape:'htmlall':'UTF-8'}</td>
			<td>{$klarna_special_invoicefee|escape:'htmlall':'UTF-8'}{$partpayment_interest_symbol|escape:'htmlall':'UTF-8'}</td>
		</tr>
      	<tr>
		    <td>{$partpayment_invoicefee_label|escape:'htmlall':'UTF-8'}</td>
		    <td>{$klarna_special_interest|escape:'htmlall':'UTF-8'}{$partpayment_invoicefee_symbol|escape:'htmlall':'UTF-8'}</td>
		  </tr>
		</tbody>
</table>
{/if}
{if $klarna_locale == 'NO'}
<table class="table">
		<tbody>
      	<tr>
			<td>{$partpayment_interest_label|escape:'htmlall':'UTF-8'}</td>
			<td>{$klarna_special_invoicefee|escape:'htmlall':'UTF-8'}{$partpayment_interest_symbol|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{$partpayment_startfee_label|escape:'htmlall':'UTF-8'}</td>
			<td>{$partpayment_startfee_value|escape:'htmlall':'UTF-8'}{$partpayment_startfee_symbol|escape:'htmlall':'UTF-8'}</td>

		</tr>
      	<tr>
		    <td>{$partpayment_invoicefee_label|escape:'htmlall':'UTF-8'}</td>
		    <td>{$partpayment_invoicefee_value|escape:'htmlall':'UTF-8'}{$partpayment_invoicefee_symbol|escape:'htmlall':'UTF-8'}</td>
		</tr>

		</tbody>
</table>
{/if}
{if $klarna_locale == 'DK'}
<h4>Betal i {$klarna_special_description|escape:'htmlall':'UTF-8'}</h4>
<table class="table">
<tbody>
	<tr>
		<td>Købesum</td>
		<td>{displayPrice price=$total}</td>
	</tr>
	<tr>
		<td>Årlig rente</td>
		<td>{$klarna_special_interest|escape:'htmlall':'UTF-8'}{l s='%' mod=klarnapayments}</td>
	</tr>
	<tr>
		<td>Oprettelsesgebyr</td>
		<td>{displayPrice price=$klarna_special_start_fee}</td>
	</tr>
	<tr>
		<td>Effektiv rente</td>
		<td>{$klarna_special_apr|escape:'htmlall':'UTF-8'}{l s='%' mod=klarnapayments}</td>
	</tr>
	<tr>
		<td>Total omkostning</td>
		<td>{displayPrice price=$klarna_special_credit - $total}</td>
	</tr>
	<tr>
		<td>Kreditkøbspris</td>
		<td>{displayPrice price=$klarna_special_credit}</td>
	</tr>
</tbody>
</table>

{/if}
<script type="text/javascript">
new Klarna.Terms.Special({
    el: 'specialxx',
    eid: "{$merchant_id|escape:'htmlall':'UTF-8'}",
    locale: "{$locale|escape:'htmlall':'UTF-8'}",
    type: "{$type|escape:'htmlall':'UTF-8'}"
});
</script>
