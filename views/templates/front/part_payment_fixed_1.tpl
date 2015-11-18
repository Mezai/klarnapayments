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

<!--part pay fixed 1-->
{foreach from=$klarna_data item=value}
	{if $value.group.code == 'part_payment'}
<h4>{displayPrice price=$klarna_calc_monthly1}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()|escape:'htmlall':'UTF-8'}</h4>
{if $klarna_locale == 'SE'}
<table class="table">
<tbody>
<tr>
	<td>{$value.details.interest_rate.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[1]->getInterestRate()|escape:'htmlall':'UTF-8'}{$value.details.interest_rate.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{l s='Effective interest rate' mod='klarnapayments'}</td>
	<td>{$klarna_calc_apr1|escape:'htmlall':'UTF-8'}{$value.details.interest_rate.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{$value.details.start_fee.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[1]->getStartFee()|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{$value.details.monthly_invoice_fee.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[1]->getInvoiceFee()}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}{l s='/month'
		mod='klarnapayments'}</td>

</tr>

<tr>
	<td>{$value.details.monthly_pay.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_calc_monthly1|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}{l s='/month'
		mod='klarnapayments'}</td>

</tr>

<tr>
	<td>{l s='Total cost' mod='klarnapayments'}</td>
	<td>{$klarna_calc_total_credit1|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>
</tbody>
</table>
{/if}
{if $klarna_locale == 'NO'}
<table class="table">
<tbody>
<tr>
	<td>{l s='Bel&oslash;p per m&aring;ned' mod='klarnapayments'}</td>
	<td>{displayPrice price=$klarna_calc_monthly1}</td>
</tr>
<tr>
	<td>{$value.details.interest_rate.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$value.details.interest_rate.value|escape:'htmlall':'UTF-8'}{$value.details.interest_rate.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>
<tr>
	<td>{$value.details.start_fee.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$value.details.start_fee.value|escape:'htmlall':'UTF-8'}{$value.details.start_fee.symbol|escape:'htmlall':'UTF-8'}</td>

</tr>
<tr>
	<td>{$value.details.monthly_invoice_fee.label|escape:'htmlall':'UTF-8'}</td>
	<td>{$value.details.monthly_invoice_fee.value|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol|escape:'htmlall':'UTF-8'}</td>
</tr>

</tbody>
</table>
<p><span>{$value.use_case|escape:'htmlall':'UTF-8'}</span></p>
{/if}

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
								
							