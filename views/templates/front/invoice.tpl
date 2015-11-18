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