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

{if $showinvoice}
<img id="klarna_logo" class="klarna_logo" src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=130">
<div class="container-fluid">
			<div class="row">
				<div class="col-sm-12">
					<div class="page-header">
						<h4 class="klarna_payment_description">
						{if $klarna_country == 'SE'}Faktura: Betala om 14 dagar{/if}
						{if $klarna_country == 'DK'}Faktura: Betal om 14 dage{/if}
						{if $klarna_country == 'FI'}Lasku: Maksa 14 päivän kuluessa{/if}
						{if $klarna_country == 'NL'}Achteraf betalen: binnen 14 dagen{/if}
						{if $klarna_country == 'NO'}Faktura: Betal om 14 dager{/if}
						{if $klarna_country == 'AT'}Rechnung: In 14 Tagen bezahlen{/if}
						{if $klarna_country == 'DE'}Rechnung: In 14 Tagen bezahlen{/if}
						</h4>
					</div>
				</div>
			</div>
		<div class="row margin-b-2">
				<div class="col-sm-6">
						<h4>{if $klarna_country == 'SE'}Klarna Faktura{/if}
							{if $klarna_country == 'DK'}Klarna Faktura{/if}
							{if $klarna_country == 'FI'}Klarna Lasku{/if}
							{if $klarna_country == 'NL'}Klarna Achteraf betalen{/if}
							{if $klarna_country == 'NO'}Klarna Faktura{/if}
							{if $klarna_country == 'AT'}Klarna Rechnung{/if}
							{if $klarna_country == 'DE'}Klarna Rechnung{/if}
						</h4>
							<form action="{$link->getModuleLink('klarnapayments', 'paymentinvoice', [], true)|escape:'htmlall':'UTF-8'}" method="post" id="klarna_form">

								<div class="required form-group">
									<label for="select_klarna_method" class="required">{l s='Select a payment method' mod='klarnapayments'}</label>

  										<select class="form-control" id="select_klarna_method" name="select_klarna_method">
  											<option id="invoice_selected" class="klarna_invoice" value="-1">
  											{if $klarna_country == 'SE'}Faktura betala om 14 dagar{elseif $klarna_country == 'NO'}Betal om 14 dager{elseif $klarna_country == 'FI'}Maksa 14 päivän kuluessa{elseif $klarna_country == 'DK'}Betal om 14 dage{elseif $klarna_country == 'DE'}Rechnung: In 14 Tagen bezahlen{elseif $klarna_country == 'NL'}Achteraf betalen: binnen 14 dagen{elseif $klarna_country == 'AT'}Rechnung: In 14 Tagen bezahlen
  											{/if}
  											</option>
  											{if isset($klarna_special_id)}
  											<option id="special_selected" class="klarna_special" value="{$klarna_special_id|escape:'htmlall':'UTF-8'}">{$klarna_special_description|escape:'htmlall':'UTF-8'}</option>
  											{/if}
  										</select>
  									</div>
  									{if $klarna_country == 'NL'}
  									<div class="required form-group">
										   <label for="klarna_gender" class="required">{l s='Gender' mod='klarnapayments'}</label>
										   <div name="klarna_gender">
										    <div class="radio">
										     <label>
										      <input id="klarnafemale" name="klarnapaymentsgender" value="0" type="radio" checked>{l s='Female' mod='klarnapayments'}</label>
										    </div>
										    <div class="radio">
										     <label>
										      <input id="klarnamale" name="klarnapaymentsgender" value="1" type="radio">{l s='Male' mod='klarnapayments'}</label>
										   </div>
										</div>
  									</div>
  									{/if}
  									{if $klarna_country == 'DE' or $klarna_country == 'NL' or $klarna_country == 'AT'}
 									<div class="required form-group">
 										<label for="klarna_house_number" class="required">{l s='House number:' mod='klarnapayments'}</label>
 										<input type="text" id="klarna_house_number" class="validate form-control" name="klarna_house_num" required>
 									</div>
 									{/if}
 									{if $klarna_country == 'NL'}
 									<div class="required form-group">
 										<label for="klarna_house_extension" class="required">{l s='House extension:' mod='klarnapayments'}</label>
 										<input type="text" id="klarna_house_extension" class="validate form-control" name="klarna_house_ext" required>
 									</div>
 									{/if}
  									<div class="required form-group">
									<label for="klarna_pno" class="required">{l s='Social security number:' mod='klarnapayments'}</label>
										<input type="text" class="is_required validate form-control" autocomplete="off" name="klarna_pno" pattern="{$klarna_pno_pattern|escape:'htmlall':'UTF-8'}" placeholder="{$klarna_pno_placeholder|escape:'htmlall':'UTF-8'}" id="klarna_pno" maxlength="11" required/>
									</div>
									{if isset($klarna_error)}<div class="klarna_error">{l s='Please check PNO/social security number' mod='klarnapayments'}</div>{/if}

			<p class="cart_navigation" id="cart_navigation">
			<button type="submit" id="button_klarna" class="button_klarna" name="submitKlarnaPayment">{l s='Submit Payment' mod='klarnapayments'}</button>
			<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="button_large">{l s='Other payment methods' mod='klarnapayments'}</a>
			</p>

</form>
</div>
<div id="invoice_summary" class="col-sm-6 info invoice">
					<div class="caption">
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
					<span id="invoicexx">
						<script>
							new Klarna.Terms.Invoice({
								el: 'invoicexx',
								eid: "{$klarna_merchant_eid|escape:'htmlall':'UTF-8'}",
								locale: "{$klarna_language|escape:'htmlall':'UTF-8'}",
								charge: "{$klarna_invoice_sum|escape:'htmlall':'UTF-8'}",
								type: "{$klarna_device|escape:'htmlall':'UTF-8'}"
								});
								</script>
					</span>
				</div>
		</div>
		{if isset($klarna_special_id)}
		<div id="special_selected" class="col-sm-6 info special">
					<div class="caption">
						<h4>{$klarna_special_description|escape:'htmlall':'UTF-8'}</h4>
						<table class="table">
							<tbody>
								{if $klarna_country == 'DK' || $klarna_country == 'FI'}
								<tr>
									<td>{if $klarna_country == 'DK'}Købesum{elseif $klarna_country == 'FI'}Ostosumma{/if}</td>
									<td>{displayPrice price=$total}</td>
								</tr>
								{/if}
      							<tr>
      								<td>{if $klarna_country == 'SE'}Årsränta{elseif $klarna_country == 'NO'}Årsrente{elseif $klarna_country == 'DK'}Årlig rente{elseif $klarna_country == 'FI'}Vuosikorko{/if}</td>
      								<td>{$klarna_special_interest|escape:'htmlall':'UTF-8'} %</td>

      							</tr>
      							<tr>
							        <td>{if $klarna_country == 'SE'}Administrationsavgift{elseif $klarna_country == 'NO'}Etableringsgebyr{elseif $klarna_country == 'DK'}Oprettelsesgebyr{elseif $klarna_country == 'FI'}Aloitusmaksu{/if}</td>
							        <td>{displayPrice price=$klarna_special_startfee}</td>
							    </tr>
							    {if $klarna_country == 'NO'}
							    <tr>
							    	<td>Fakturagebyr</td>
							    	<td>{displayPrice price=$klarna_special_invfee}</td>
							    </tr>
							    {/if}
							    {if $klarna_country == 'DK'}
							    <tr>
							    	<td>Total omkostning</td>
							    	<td>{displayPrice price=$klarna_special_total_credit - $total}</td>
							    </tr>
							    {/if}
							    {if $klarna_country == 'DK' || $klarna_country == 'FI'}
							    <tr>
							    	<td>{if $klarna_country == 'DK'}Kreditøbspris{elseif $klarna_country == 'FI'}Kokonaiskustannukset{/if}</td>
							    	<td>{displayPrice price=$klarna_special_total_credit}</td>
							    </tr>
							    {/if}
							</tbody>

					</table>
					<span id="specialxx"></span>
			</div>

		</div>
		<script>
		new Klarna.Terms.Special({
			el: 'specialxx',
			eid: "{$klarna_merchant_eid|escape:'htmlall':'UTF-8'}",
			locale: "{$klarna_language|escape:'htmlall':'UTF-8'}",
			type: "{$klarna_device|escape:'htmlall':'UTF-8'}"
			});
		</script>
</div>
{/if}
{/if}

{if $showpart}

<p class="payment_module">
	<a href="{$link->getModuleLink('klarnapayments', 'paymentpart')|escape:'htmlall':'UTF-8'}" title="{l s='Pay with Klarna part payments' mod='klarnapayments'}">
		<img src="https://cdn.klarna.com/1.0/shared/image/generic/logo/{$lang_code|escape:'htmlall':'UTF-8'}/basic/blue-black.png?width=200" alt="{l s='Pay with Klarna part payments' mod='klarnapayments'}"/>
		{l s='Pay with Klarna part payments' mod='klarnapayments'}

	</a>
</p>
{/if}
