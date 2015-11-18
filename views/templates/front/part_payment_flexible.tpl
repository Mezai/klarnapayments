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

<!--account-->


{if $klarna_locale == 'NO'}	
<h4>{$partpayment_title|escape:'htmlall':'UTF-8'}</h4>					
<table class="table">
<tbody>
<tr>
	<td>{$partpayment_monthlypay_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$partpayment_monthlypay_value|escape:'htmlall':'UTF-8'}{$partpayment_monthlypay_symbol|escape:'htmlall':'UTF-8'}</td>
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
<p>{$partpayment_use_case|escape:'htmlall':'UTF-8'}</p>
{/if}
{if $klarna_locale == 'SE'}
<h4>{$partpayment_title|escape:'htmlall':'UTF-8'}</h4>
<table class="table">
<tbody>
<tr>
	<td>{$partpayment_interest_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{$partpayment_interest_symbol|escape:'htmlall':'UTF-8'}</td>
</tr>
<tr>
	<td>{$partpayment_startfee_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_account_start_fee|escape:'htmlall':'UTF-8'}{$partpayment_startfee_symbol|escape:'htmlall':'UTF-8'}</td>
</tr>
<tr>
	<td>{$partpayment_invoicefee_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_account_invoicefee|escape:'htmlall':'UTF-8'}{l s='SEK/mån' mod='klarnapayments'}</td>
</tr>
<tr>
	<td>{$partpayment_invoicefee_label|escape:'htmlall':'UTF-8'}</td>
	<td>{$klarna_account_monthly|escape:'htmlall':'UTF-8'}{l s='SEK/mån' mod='klarnapayments'}</td>
</tr>

	</tbody>
</table>
<p>{$partpayment_use_case|escape:'htmlall':'UTF-8'}</p>
{/if}

{if $klarna_locale == 'DE'}
<table class="table">
	<tbody>
			<tr>
				<td>Sollzinssatz (variabel)</td>
				<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
			</tr>
			<tr>
				<td>Ratenkaufgebühr</td>
				<td>{displayPrice price=$klarna_account_invoicefee}{l s=' /Monat' mod='klarnapayments'}</td>
		</tr>
		<tr>
			<td>Moatliche Mindestrate für diesen Einkauf</td>
			<td>{displayPrice price=$klarna_account_monthly}{l s=' /Monat' mod='klarnapayments'}</td>
		</tr>
	</tbody>
</table>
<p>Verfügungsrahmen ab 199,99 € (abhängig von der Höhe Ihrer Einkäufe), effektiver Jahreszins 18,07%* und Gesamtbetrag 218, 57€* (*bei Ausnutzung des vollen Verfügungsrahmens und Rückzahlung in 12 monatlichen Raten je 18,21 €). Hier finden Sie <a href="https://cdn.klarna.com/1.0/shared/content/legal/terms/EID/de_de/account" target="_blank">weitere Informationen,</a><a href="https://cdn.klarna.com/1.0/shared/content/legal/de_de/account/terms.pdf" target="_blank"> AGB mit Widerrufsbelehrung</a> und <a href="https://cdn.klarna.com/1.0/shared/content/legal/de_de/consumer_credit.pdf" target="_blank">Standardinformationen für Verbraucherkredite.</a> Übersteigt Ihr Einkauf mit Klarna Ratenkauf erstmals einen Betrag von 199,99 € erhalten Sie von Klarna einen Ratenkaufvertrag mit der Bitte um Unterzeichnung zugesandt. Ihr Kauf gilt solange als <a href="https://cdn.klarna.com/1.0/shared/content/legal/terms/EID/de_de/invoice?fee=0" target="_blank">Rechnungskauf.</a>
					</p>
<p><input type="checkbox" required/>Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. Meine <span id="consentxx"></span> kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.</p>
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

