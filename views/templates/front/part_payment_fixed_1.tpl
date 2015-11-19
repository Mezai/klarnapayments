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

{if $klarna_locale == 'SE'}
<h4>{displayPrice price=$klarna_calc_monthly1}{l s=' i ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()|strip_tags:'UTF-8'}</h4>
<table class="table">
<tbody>
<tr>
	<td>{$partpayment_interest_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[1]->getInterestRate()|escape:'htmlall':'UTF-8'}{$partpayment_interest_symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{l s='Effective interest rate' mod='klarnapayments'}</td>
	<td>{$klarna_calc_apr1|escape:'htmlall':'UTF-8'}{$partpayment_interest_symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{$partpayment_startfee_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[1]->getStartFee()|escape:'htmlall':'UTF-8'}{$partpayment_invoicefee_symbol|escape:'htmlall':'UTF-8'}</td>

</tr>

<tr>
	<td>{$partpayment_invoicefee_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$KlarnaPClass[1]->getInvoiceFee()}{$partpayment_invoicefee_symbol|escape:'htmlall':'UTF-8'}{l s='/month'
		mod='klarnapayments'}</td>

</tr>

<tr>
	<td>{$partpayment_monthlypay_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_calc_monthly1|escape:'htmlall':'UTF-8'}{$partpayment_invoicefee_symbol|escape:'htmlall':'UTF-8'}{l s='/month'
		mod='klarnapayments'}</td>

</tr>

<tr>
	<td>{l s='Total cost' mod='klarnapayments'}</td>
	<td>{$klarna_calc_total_credit1|escape:'htmlall':'UTF-8'}{$partpayment_invoicefee_symbol|escape:'htmlall':'UTF-8'}</td>

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
	<td>{$partpayment_interest_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$partpayment_interest_value|escape:'htmlall':'UTF-8'}{$partpayment_interest_symbol|escape:'htmlall':'UTF-8'}</td>

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
<p><span>{$partpayment_use_case|escape:'htmlall':'UTF-8'}</span></p>
{/if}
{if $klarna_locale == 'DK'}
<h4>{displayPrice price=$klarna_calc_monthly1}{l s=' i ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()|strip_tags:'UTF-8'}</h4>
<table class="table">
<tbody>
<tr>
	<td>Årlig rente</td>
	<td>{$KlarnaPClass[1]->getInterestRate()|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
</tr>
<tr>
	<td>Oprettelsesgebyr</td>
	<td>{$KlarnaPClass[1]->getStartFee()|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
</tr>
<tr>
	<td>Administrationsgebyr</td>
	<td>{$KlarnaPClass[1]->getInvoiceFee()|escape:'htmlall':'UTF-8'}{l s='kr/.md' mod='klarnapayments'}</td>
</tr>
<tr>
	<td>Månedlig omkostning</td>
	<td>{$klarna_calc_monthly1|escape:'htmlall':'UTF-8'}{l s='kr/.md' mod='klarnapayments'}</td>
</tr>
<tr>
	<td>Effektiv rente</td>
	<td>{$klarna_calc_apr1|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
</tr>
<tr>
	<td>Total omkostning</td>
	<td>{displayPrice price=$klarna_calc_total_credit1 - $total}</td>
</tr>
<tr>
	<td>Kreditkøbspris</td>
	<td>{displayPrice price=$klarna_calc_total_credit1}</td>
</tr>
</tbody>	
{/if}

{if $klarna_locale == 'FI'}
<h4>{displayPrice price=$klarna_calc_monthly1}{l s='/kk' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()|escape:'htmlall':'UTF-8'}</h4>
<table class="table">
<tbody>
	<tr>
		<td>Vousikorko</td>
		<td>{$KlarnaPClass[1]->getInterestRate()|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
	</tr>
	<tr>
		<td>Alouitusmaksu</td>
		<td>{displayPrice price=$KlarnaPClass[1]->getStartFee()}</td>
	</tr>
	<tr>
		<td>Hallinnointimaksu</td>
		<td>{$KlarnaPClass[1]->getInvoiceFee()|escape:'htmlall':'UTF-8'}{l s='€/kk' mod='klarnapayments'}</td>
	</tr>
	<tr>
		<td>Kuukausikustannus</td>
		<td>{$klarna_calc_monthly1|escape:'htmlall':'UTF-8'}{l s='€/kk' mod='klarnapayments'}</td>
	</tr>
	<tr>
		<td>Todellinen vousikorko</td>
		<td>{$klarna_calc_apr1|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
	</tr>
	<tr>
		<td>Loppusumma</td>
		<td>{displayPrice price=$klarna_calc_total_credit1}</td>
	</tr>			
</tbody>
</table>
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
					
							