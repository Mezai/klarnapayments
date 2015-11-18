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

{foreach from=$klarna_data item=value}
	{if $value.group.code == 'invoice'}

<h4>{$klarna_special_description|escape:'htmlall':'UTF-8'}</h4>
{if $klarna_locale == 'SE'}
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
{/if}
{if $klarna_locale == 'NO'}
<table class="table">
		<tbody>
      	<tr>
			<td>{$value.details.interest_rate.label}</td>
			<td>{$klarna_special_invoicefee|escape:'htmlall':'UTF-8'}{$value.details.interest_rate.symbol}</td>
		</tr>
		<tr>
			<td>{$value.details.start_fee.label}</td>
			<td>{$value.details.start_fee.value|escape:'htmlall':'UTF-8'}{$value.details.start_fee.symbol}</td>

		</tr>
      	<tr>
		    <td>{$value.details.monthly_invoice_fee.label}</td>
		    <td>{$value.details.monthly_invoice_fee.value|escape:'htmlall':'UTF-8'}{$value.details.monthly_invoice_fee.symbol}</td>
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
	{/if}
{/foreach}
