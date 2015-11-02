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

{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='klarnapayments'}">{l s='Checkout' mod='klarnapayments'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Klarna part payment' mod='klarnapayments'}
{/capture}


<h2>{l s='Order summary' mod='klarnapayments'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='klarnapayments'}</p>
{elseif !$checkLocale}
	<p class="alert alert-warning">{l s='Please change your currency or language to match your country in order to shop with Klarna.' mod='klarnapayments'}</p>
{else}



<div class="container-fluid">
	<img id="klarna_logo" class="klarna_logo" src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=200">
			<div class="row">
				<div class="col-sm-12">
					<div class="page-header">
						<h4 class="klarna_payment_description">{l s='Klarna part payment' mod='klarnapayments'}</h4>
					</div>
				</div>
			</div>
			<div class="row margin-b-2">
				<div class="col-sm-6">

						{if $klarna_country == 'SE'}<h4>Delbetalning</h4>{/if}{if $klarna_country == 'DK'}<h4>Afbetaling</h4>{/if}
						{if $klarna_country == 'DE'}<h4>Ratenkauf</h4>{/if}{if $klarna_country == 'NO'}<h4>Delbetaling</h4>{/if}
						{if $klarna_country == 'FI'}<h4>Erämaksu</h4>{/if}
							<form action="{$link->getModuleLink('klarnapayments', 'paymentpart', [], true)|escape:'htmlall':'UTF-8'}" method="post">
  								<div class="required form-group">
  									<label for="select_klarna_method" class="required">{l s='Select a payment method' mod='klarnapayments'}</label>

  										<select class="form-control" id="select_klarna_method" name="select_klarna_method">
  											{if isset($klarna_account_id)}
  												{if ($klarna_account_minamount) < ($total)}
										    <option id="account_selected" class="klarna_account" name="select_account" value="{$klarna_account_id|escape:'htmlall':'UTF-8'}">{$klarna_account_description|escape:'htmlall':'UTF-8'}</option>
										    	{/if}
										    {/if}
										    {if isset($klarna_campaign_id_1)}
										    	{if ($klarna_campaign_minamount_1) < ($total)}
										    <option id="select_campaign1" class="klarna_campaign_1" value="{$klarna_campaign_id_1|escape:'htmlall':'UTF-8'}">{$klarna_campaign_description_1|strip_tags:'UTF-8'}</option>
										    	{/if}
										    {/if}
										    {if isset($klarna_campaign_id_2)}
										    	{if ($klarna_campaign_minamount_2) < ($total)}
										    <option id="select_campaign2" class="klarna_campaign_2" value="{$klarna_campaign_id_2|escape:'htmlall':'UTF-8'}">{$klarna_campaign_description_2|strip_tags:'UTF-8'}</option>
										    	{/if}
										    {/if}
										    {if isset($klarna_campaign_id_3)}
										    	{if ($klarna_campaign_minamount_3) < ($total)}
										    <option id="select_campaign3" class="klarna_campaign_3" value="{$klarna_campaign_id_3|escape:'htmlall':'UTF-8'}">{$klarna_campaign_description_3|strip_tags:'UTF-8'}</option>
										    	{/if}
										    {/if}
  										</select>

								</div>
								<div class="required form-group">

									<label for="klarna_pno" class="required">{l s='Social security number:' mod='klarnapayments'}</label>
										<input class="validate form-control" type="text" name="klarna_pno" pattern="{$klarna_pno_pattern|escape:'htmlall':'UTF-8'}" placeholder="{$klarna_pno_placeholder|escape:'htmlall':'UTF-8'}" id="klarna_pno" required>

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
 								{if $klarna_country == 'DE'}
 								<div class="required form-group">
 									<label for="klarna_accept_de" class="required">{l s='I agree to the terms and conditions.' mod='klarnapayments'}</label>
									<input type="checkbox" id="klarna_accept_de" name="klarna_accept_de" id="klarna_accept_de" required>
								</div>
								{/if}

							<p class="cart_navigation" id="cart_navigation">
			<button type="submit" id="button_klarna" class="button_klarna" name="submitKlarnaPayment">{l s='Submit Payment' mod='klarnapayments'}</button>
			<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="button_large">{l s='Other payment methods' mod='klarnapayments'}</a>
			</p>

			</form>

</div>
			{if isset($klarna_account_id)}
			<div id="account_selected" class="col-sm-6 info account">
					<div class="caption">
						<h4>{if $klarna_country == 'NL'}Flexibel, in uw eigen tempo betalen{/if}
							{if $klarna_country == 'SE'}Konto – Betala i din egen takt{/if}
							{if $klarna_country == 'DK'}Konto – Betal i dit eget tempo{/if}
							{if $klarna_country == 'FI'}Tili – Maksa omassa tahdissasi{/if}
							{if $klarna_country == 'DE'}Flexibel – in Ihrem eigenen Tempo bezahlen{/if}
							{if $klarna_country == 'NO'}Konto – Betal i ditt eget tempo{/if}
						</h4>
						{if $klarna_country == 'NL'}
						<img src="{$this_path|escape:'htmlall':'UTF-8'}/views/img/amfbanner.jpg" style="width:100%;">
						<br />
						{/if}
						<table class="table">
							{if $klarna_country == 'SE'}
							<tbody>
      							<tr>
      								<td>Rörlig årsränta</td>
      								<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>

      							</tr>
      							<tr>
							        <td>Uppläggningsavgift</td>
							        <td>{displayPrice price=$klarna_account_startfee}</td>
							    </tr>
							    <tr>
							        <td>Administrationsavgift</td>
							        <td>{displayPrice price=$klarna_account_invfee}{l s=' / månad' mod='klarnapayments'}</td>
							    </tr>
							    <tr>
							        <td>Delbetala från</td>
							        <td>{displayPrice price=$klarna_account_monthly_cost}{l s=' / månad' mod='klarnapayments'}</td>
							    </tr>
							</tbody>
							{/if}
							{if $klarna_country == 'DK'}
							<tbody>
								<tr>
									<td>Årlig rente</td>
									<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Oprettelsesgebyr</td>
									<td>{displayPrice price=$klarna_account_startfee}</td>
								</tr>
								<tr>
									<td>Administrationsgebyr</td>
									<td>{displayPrice price=$klarna_account_invfee}{l s=' /md' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Delbetala fra</td>
									<td>{displayPrice price=$klarna_account_monthly_cost}{l s=' /md' mod='klarnapayments'}</td>
								</tr>
							</tbody>
							{/if}
							{if $klarna_country == 'DE'}
							<tbody>
								<tr>
									<td>Sollzinssatz (variabel)</td>
									<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Ratenkaufgebühr</td>
									<td>{displayPrice price=$klarna_account_invfee}{l s=' /Monat' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Moatliche Mindestrate für diesen Einkauf</td>
									<td>{displayPrice price=$klarna_account_monthly_cost}{l s=' /Monat' mod='klarnapayments'}</td>
								</tr>
							</tbody>
							{/if}
							{if $klarna_country == 'FI'}
							<tbody>
								<tr>
									<td>Vuosikorko</td>
									<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Aloitusmaksu</td>
									<td>{displayPrice price=$klarna_account_startfee}</td>
								</tr>
								<tr>
									<td>Hallinnointimaksu</td>
									<td>{displayPrice price=$klarna_account_invfee}{l s=' /kk' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Maksa erissä alkaen</td>
									<td>{displayPrice price=$klarna_account_monthly_cost}{l s=' /kk' mod='klarnapayments'}</td>
								</tr>
							</tbody>
							{/if}
						{if $klarna_country == 'NO'}
							<tbody>
								<tr>
									<td>Årsrente</td>
									<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Etableringsgebyr</td>
									<td>{displayPrice price=$klarna_account_startfee}</td>
								</tr>
								<tr>
									<td>Fakturagebyr</td>
									<td>{displayPrice price=$klarna_account_invfee}{l s=' /mån' mod='klarnapayments'}</td>
								</tr>
								<tr>
									<td>Delbetal fra</td>
									<td>{displayPrice price=$klarna_account_monthly_cost}{l s=' /mån' mod='klarnapayments'}</td>
								</tr>
							</tbody>
							{/if}
						{if $klarna_country == 'NL'}
						<tbody>
							<tr>
								<td>Jarlikse rente</td>
								<td>Factuurkosten</td>
								<td>Maandelijkse kosten</td>
							</tr>
							<tr>
								<td>{$klarna_account_interest|escape:'htmlall':'UTF-8'}{l s='%' mod='klarnapayments'}</td>
								<td>0 €</td>
								<td>{$klarna_account_monthly_cost|escape:'htmlall':'UTF-8'}</td>
							</tr>
						</tbody>
						{/if}
					</table>
					{if $klarna_country == 'SE'}
					<p>Exempel: Säg att du köper för 10 000 kr. Administrativ avgift är 29 kr/mån och rörlig årsränta är 19,9%. Du delbetalar 955 kr/mån i 12 mån. Årlig effektiv ränta blir då 29,22% och totalbeloppet för ditt köp om 10 000 kr blir 11 458 kr.</p>
					{/if}
					{if $klarna_country == 'NO'}
					<p>Eksempel: For et kjøp på 10 000 kr, er renten 22% og effektiv rente er 36,12% ved betalning over 12 måneder. Total kredittkjøpspris 11 771 kr.</p>
					{/if}
					{if $klarna_country == 'DE'}
					<p>Verfügungsrahmen ab 199,99 € (abhängig von der Höhe Ihrer Einkäufe), effektiver Jahreszins 18,07%* und Gesamtbetrag 218, 57€* (*bei Ausnutzung des vollen Verfügungsrahmens und Rückzahlung in 12 monatlichen Raten je 18,21 €). Hier finden Sie <a href="https://cdn.klarna.com/1.0/shared/content/legal/terms/EID/de_de/account" target="_blank">weitere Informationen,</a><a href="https://cdn.klarna.com/1.0/shared/content/legal/de_de/account/terms.pdf" target="_blank"> AGB mit Widerrufsbelehrung</a> und <a href="https://cdn.klarna.com/1.0/shared/content/legal/de_de/consumer_credit.pdf" target="_blank">Standardinformationen für Verbraucherkredite.</a> Übersteigt Ihr Einkauf mit Klarna Ratenkauf erstmals einen Betrag von 199,99 € erhalten Sie von Klarna einen Ratenkaufvertrag mit der Bitte um Unterzeichnung zugesandt. Ihr Kauf gilt solange als <a href="https://cdn.klarna.com/1.0/shared/content/legal/terms/EID/de_de/invoice?fee=0" target="_blank">Rechnungskauf.</a>
					</p>
					<p>Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. Meine <span id="consentxx"></span> kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.</p>
					{/if}
					{if $klarna_country == 'DK'}
					<p>Lad os sige, at du køber for 10.000 kr. Administrativt gebyr er 39 kr./md., og rørlig årsrente er 22,70 %. Du betaler 978 kr. af hver måned 12 mdr. Den årlige effektive rente bliver da 35,41 %, total omkostning 1740 kr og totalprisen bliver 11.740 kr.</p>
					{/if}
					{if $klarna_country == 'FI'}
					<p>Esimerkki: Sanotaan, että teet 1,000 € ostoksen. Hallinnointimaksu on 3,95 €/kk ja tämänhetkinen vuosikorko 22 %. Maksat erissä 98 €/kk 12 kuukauden ajan. Todelliseksi vuosikoroksi tulee silloin 34,63 % ja 1,000 € ostoksesi kokonaissummaksi 1,171 €.</p>
					{/if}
					{if $klarna_country == 'NL'}
					<table class="table">
						<tbody>
							<tr>
								<td>Total kredietbedrag</td>
								<td>Duur van de kredietovereenkomst*</td>
								<td>Variabele debetrentevoet</td>
								<td>Jaarlijks kosten-percentage(JKP)</td>
								<td>Total te betalen bedrag*</td>
								<td>Termijnbedrag*</td>
							</tr>
							<tr>
								<td>50 €</td>
								<td>6 mnd</td>
								<td>13 %</td>
								<td>13.8 %</td>
								<td>52 €</td>
								<td>9 €</td>
							</tr>
							<tr>
								<td>100 €</td>
								<td>12 mnd</td>
								<td>13.0 %</td>
								<td>13.8 %</td>
								<td>107 €</td>
								<td>9 €</td>
							</tr>
							<tr>
								<td>250 €</td>
								<td>24 mnd</td>
								<td>13.0 %</td>
								<td>13.8 %</td>
								<td>285 €</td>
								<td>12 €</td>

							</tr>

						</tbody>
					</table>
					<p>*Het gaat hier om een indicatie, werkelijke looptijd of bedrag kan varieren.</p>
					{/if}
					<span id="accountxx"></span>
					</div>
				</div>
				{/if}
				{if isset($klarna_campaign_id_1)}
				<div id="campaign_selected_1" class="col-sm-6 info campaign1">
					<div class="caption">
						<h4>{if $klarna_country == 'SE'}
							{displayPrice price=$klarna_campaign_monthly_cost_1} / mån i {$klarna_campaign_months_1|escape:'htmlall':'UTF-8'} månader
							{elseif $klarna_country == 'DK'}
							{displayPrice price=$klarna_campaign_monthly_cost_1} / måned i {$klarna_campaign_months_1|escape:'htmlall':'UTF-8'} måneder
							{elseif $klarna_country == 'FI'}
							{displayPrice price=$klarna_campaign_monthly_cost_1} / kk {$klarna_campaign_months_1|escape:'htmlall':'UTF-8'} kuukauden ajan
							{elseif $klarna_country == 'NO'}
							{displayPrice price=$klarna_campaign_monthly_cost_1} / mån i {$klarna_campaign_months_1|escape:'htmlall':'UTF-8'} måneder
							{/if}
						</h4>

						<table class="table">
							<tbody>
      							<tr>
      								<td>{if $klarna_country == 'SE'}Fast årsränta{elseif $klarna_country == 'DK'}Årlig rente{elseif $klarna_country == 'FI'}Vuosikorko{elseif $klarna_country == 'NO'}Årsrente{/if}</td>
      								<td>{$klarna_campaign_interest_1|escape:'htmlall':'UTF-8'} %</td>

      							</tr>
      							{if $klarna_country == 'SE' || $klarna_country == 'DK' || $klarna_country == 'FI'}
      							<tr>
							        <td>{if $klarna_country == 'SE'}Effektiv ränta{elseif $klarna_country == 'DK'}Effektiv rente{elseif $klarna_country == 'FI'}Todellinen vuosikorko{/if}</td>
							        <td>{$klarna_campaign_calc_apr_1|escape:'htmlall':'UTF-8'} %</td>
							    </tr>
							    {/if}
							    <tr>
							        <td>{if $klarna_country == 'SE'}Uppläggningsavgift{elseif $klarna_country == 'DK'}Oprettelsesgebyr{elseif $klarna_country == 'FI'}Aloitusmaksu{elseif $klarna_country == 'NO'}Etableringsgebyr{/if}</td>
							        <td>{displayPrice price=$klarna_campaign_startfee_1}</td>
							    </tr>
							    <tr>
							        <td>{if $klarna_country == 'SE'}Administrationavgift{elseif $klarna_country == 'DK'}Administrationsgebyr{elseif $klarna_country == 'FI'}Hallinnointimaksu{elseif $klarna_country == 'NO'}Fakturagebyr{/if}</td>
							        <td>{displayPrice price=$klarna_campaign_invfee_1} / mån</td>
							    </tr>
							    <tr>
							    	<td>{if $klarna_country == 'SE'}Månadskostnad{elseif $klarna_country == 'DK'}Månedlig omkostning{elseif $klarna_country == 'FI'}Kuukausikustannus{elseif $klarna_country == 'NO'}Beløp per måned{/if}</td>
							    	<td>{displayPrice price=$klarna_campaign_monthly_cost_1} / mån</td>
							    </tr>
							    {if $klarna_country == 'SE' || $klarna_country == 'DK' || $klarna_country == 'FI'}
							    <tr>
							    	<td>{if $klarna_country == 'SE'}Totalkostnad{elseif $klarna_country == 'DK'}Kreditkøbspris{elseif $klarna_country == 'FI'}Loppusumma{/if}</td>
							    	<td>{displayPrice price=$klarna_campaign_credit_cost_1}</td>
							    </tr>
							    {/if}
							    {if $klarna_country == 'DK'}
							    <tr>
							    	<td>Total omkostning</td>
							    	<td>{displayPrice price=$klarna_campaign_credit_cost_1 - $total}</td>
							    </tr>
							    {/if}

							</tbody>

					</table>
					{if $klarna_country == 'SE'}
					<p>Exempel: Säg att du köper för 10 000 kr. Administrativ avgift är 29 kr/mån och rörlig årsränta är 19,9%. Du delbetalar 955 kr/mån i 12 mån. Årlig effektiv ränta blir då 29,22% och totalbeloppet för ditt köp om 10 000 kr blir 11 458 kr.</p>
					{/if}
					{if $klarna_country == 'NO'}
					<p>Dette kjøpet på {$total|escape:'htmlall':'UTF-8'} kr, med {$klarna_campaign_startfee_1|escape:'htmlall':'UTF-8'} kr i etableringsgebyr, {$klarna_campaign_interest_1|escape:'htmlall':'UTF-8'} % rente og nedbetaling over {$klarna_campaign_months_1|escape:'htmlall':'UTF-8'} måneder, har en effektiv rente på {$klarna_campaign_calc_apr_1|escape:'htmlall':'UTF-8'} %. Total kredittkjøpspris {$klarna_campaign_credit_cost_1|escape:'htmlall':'UTF-8'} kr.</p>
					{/if}



					<span id="accountxx"></span>
					</div>

				</div>
				{/if}
				{if isset($klarna_campaign_id_2)}
				<div id="campaign_selected_2" class="col-sm-6 info campaign2">
					<div class="caption">
						<h4>{if $klarna_country == 'SE'}
							{displayPrice price=$klarna_campaign_monthly_cost_2} / mån i {$klarna_campaign_months_2|escape:'htmlall':'UTF-8'} månader
							{elseif $klarna_country == 'DK'}
							{displayPrice price=$klarna_campaign_monthly_cost_2} / måned i {$klarna_campaign_months_2|escape:'htmlall':'UTF-8'} måneder
							{elseif $klarna_country == 'FI'}
							{displayPrice price=$klarna_campaign_monthly_cost_2} / kk {$klarna_campaign_months_2|escape:'htmlall':'UTF-8'} kuukauden ajan
							{elseif $klarna_country == 'NO'}
							{displayPrice price=$klarna_campaign_monthly_cost_2} / mån i {$klarna_campaign_months_2|escape:'htmlall':'UTF-8'} måneder
							{/if}</h4>
						<table class="table">
							<tbody>
      							<tr>
      								<td>{if $klarna_country == 'SE'}Fast årsränta{elseif $klarna_country == 'DK'}Årlig rente{elseif $klarna_country == 'FI'}Vuosikorko{elseif $klarna_country == 'NO'}Årsrente{/if}</td>
      								<td>{$klarna_campaign_interest_2|escape:'htmlall':'UTF-8'} %</td>

      							</tr>
      							{if $klarna_country == 'SE' || $klarna_country == 'DK' || $klarna_country == 'FI'}
      							<tr>
							        <td>{if $klarna_country == 'SE'}Effektiv ränta{elseif $klarna_country == 'DK'}Effektiv rente{elseif $klarna_country == 'FI'}Todellinen vuosikorko{/if}</td>
							        <td>{$klarna_campaign_calc_apr_2|escape:'htmlall':'UTF-8'} %</td>
							    </tr>
							    {/if}
							    <tr>
							        <td>{if $klarna_country == 'SE'}Uppläggningsavgift{elseif $klarna_country == 'DK'}Oprettelsesgebyr{elseif $klarna_country == 'FI'}Aloitusmaksu{elseif $klarna_country == 'NO'}Etableringsgebyr{/if}</td>
							        <td>{displayPrice price=$klarna_campaign_startfee_2}</td>
							    </tr>
							    <tr>
							        <td>{if $klarna_country == 'SE'}Administrationavgift{elseif $klarna_country == 'DK'}Administrationsgebyr{elseif $klarna_country == 'FI'}Hallinnointimaksu{elseif $klarna_country == 'NO'}Fakturagebyr{/if}</td>
							        <td>{displayPrice price=$klarna_campaign_invfee_2} / mån</td>
							    </tr>
							    <tr>
							    	<td>{if $klarna_country == 'SE'}Månadskostnad{elseif $klarna_country == 'DK'}Månedlig omkostning{elseif $klarna_country == 'FI'}Kuukausikustannus{elseif $klarna_country == 'NO'}Beløp per måned{/if}</td>
							    	<td>{displayPrice price=$klarna_campaign_monthly_cost_2} / mån</td>
							    </tr>
							    {if $klarna_country == 'SE' || $klarna_country == 'DK' || $klarna_country == 'FI'}
							    <tr>
							    	<td>{if $klarna_country == 'SE'}Totalkostnad{elseif $klarna_country == 'DK'}Kreditkøbspris{elseif $klarna_country == 'FI'}Loppusumma{/if}</td>
							    	<td>{displayPrice price=$klarna_campaign_credit_cost_2}</td>
							    </tr>
							    {/if}
							    {if $klarna_country == 'DK'}
							    <tr>
							    	<td>Total omkostning</td>
							    	<td>{displayPrice price=$klarna_campaign_credit_cost_2 - $total}</td>
							    </tr>
							    {/if}
							</tbody>
					</table>
					{if $klarna_country == 'SE'}
					<p>Exempel: Säg att du köper för 10 000 kr. Administrativ avgift är 29 kr/mån och rörlig årsränta är 19,9%. Du delbetalar 955 kr/mån i 12 mån. Årlig effektiv ränta blir då 29,22% och totalbeloppet för ditt köp om 10 000 kr blir 11 458 kr.</p>
					{/if}
					{if $klarna_country == 'NO'}
					<p>Dette kjøpet på {$total|escape:'htmlall':'UTF-8'} kr, med {$klarna_campaign_startfee_2|escape:'htmlall':'UTF-8'} kr i etableringsgebyr, {$klarna_campaign_interest_2|escape:'htmlall':'UTF-8'} % rente og nedbetaling over {$klarna_campaign_months_2|escape:'htmlall':'UTF-8'} måneder, har en effektiv rente på {$klarna_campaign_calc_apr_2|escape:'htmlall':'UTF-8'} %. Total kredittkjøpspris {$klarna_campaign_credit_cost_2|escape:'htmlall':'UTF-8'} kr.</p>
					{/if}


					<span id="accountxx"></span>
					</div>

				</div>
				{/if}
				{if isset($klarna_campaign_id_3)}
				<div id="campaign_selected_3" class="col-sm-6 info campaign3">
					<div class="caption">
						<h4>{if $klarna_country == 'SE'}
							{displayPrice price=$klarna_campaign_monthly_cost_3} / mån i {$klarna_campaign_months_3|escape:'htmlall':'UTF-8'} månader
							{elseif $klarna_country == 'DK'}
							{displayPrice price=$klarna_campaign_monthly_cost_3} / måned i {$klarna_campaign_months_3|escape:'htmlall':'UTF-8'} måneder
							{elseif $klarna_country == 'FI'}
							{displayPrice price=$klarna_campaign_monthly_cost_3} / kk {$klarna_campaign_months_3|escape:'htmlall':'UTF-8'} kuukauden ajan
							{elseif $klarna_country == 'NO'}
							{displayPrice price=$klarna_campaign_monthly_cost_3} / mån i {$klarna_campaign_months_3|escape:'htmlall':'UTF-8'} måneder
							{/if}</h4>
						<table class="table">
							<tbody>
      							<tr>
      								<td>{if $klarna_country == 'SE'}Fast årsränta{elseif $klarna_country == 'DK'}Årlig rente{elseif $klarna_country == 'FI'}Vuosikorko{elseif $klarna_country == 'NO'}Årsrente{/if}</td>
      								<td>{$klarna_campaign_interest_3|escape:'htmlall':'UTF-8'} %</td>

      							</tr>
      							{if $klarna_country == 'SE' || $klarna_country == 'DK' || $klarna_country == 'FI'}
      							<tr>
							        <td>{if $klarna_country == 'SE'}Effektiv ränta{elseif $klarna_country == 'DK'}Effektiv rente{elseif $klarna_country == 'FI'}Todellinen vuosikorko{/if}</td>
							        <td>{$klarna_campaign_calc_apr_3|escape:'htmlall':'UTF-8'} %</td>
							    </tr>
							    {/if}
							    <tr>
							        <td>{if $klarna_country == 'SE'}Uppläggningsavgift{elseif $klarna_country == 'DK'}Oprettelsesgebyr{elseif $klarna_country == 'FI'}Aloitusmaksu{elseif $klarna_country == 'NO'}Etableringsgebyr{/if}</td>
							        <td>{displayPrice price=$klarna_campaign_startfee_3}</td>
							    </tr>
							    <tr>
							        <td>{if $klarna_country == 'SE'}Administrationavgift{elseif $klarna_country == 'DK'}Administrationsgebyr{elseif $klarna_country == 'FI'}Hallinnointimaksu{elseif $klarna_country == 'NO'}Fakturagebyr{/if}</td>
							        <td>{displayPrice price=$klarna_campaign_invfee_3} / mån</td>
							    </tr>
							    <tr>
							    	<td>{if $klarna_country == 'SE'}Månadskostnad{elseif $klarna_country == 'DK'}Månedlig omkostning{elseif $klarna_country == 'FI'}Kuukausikustannus{elseif $klarna_country == 'NO'}Beløp per måned{/if}</td>
							    	<td>{displayPrice price=$klarna_campaign_monthly_cost_3} / mån</td>
							    </tr>
							    {if $klarna_country == 'SE' || $klarna_country == 'DK' || $klarna_country == 'FI'}
							    <tr>
							    	<td>{if $klarna_country == 'SE'}Totalkostnad{elseif $klarna_country == 'DK'}Kreditkøbspris{elseif $klarna_country == 'FI'}Loppusumma{/if}</td>
							    	<td>{displayPrice price=$klarna_campaign_credit_cost_3}</td>
							    </tr>
							    {/if}
							    {if $klarna_country == 'DK'}
							    <tr>
							    	<td>Total omkostning</td>
							    	<td>{displayPrice price=$klarna_campaign_credit_cost_3 - $total}</td>
							    </tr>
							    {/if}
							</tbody>
					</table>
					{if $klarna_country == 'SE'}
					<p>Exempel: Säg att du köper för 10 000 kr. Administrativ avgift är 29 kr/mån och rörlig årsränta är 19,9%. Du delbetalar 955 kr/mån i 12 mån. Årlig effektiv ränta blir då 29,22% och totalbeloppet för ditt köp om 10 000 kr blir 11 458 kr.</p>
					{/if}
					{if $klarna_country == 'NO'}
					<p>Dette kjøpet på {$total|escape:'htmlall':'UTF-8'} kr, med {$klarna_campaign_startfee_3|escape:'htmlall':'UTF-8'} kr i etableringsgebyr, {$klarna_campaign_interest_3|escape:'htmlall':'UTF-8'} % rente og nedbetaling over {$klarna_campaign_months_3|escape:'htmlall':'UTF-8'} måneder, har en effektiv rente på {$klarna_campaign_calc_apr_3|escape:'htmlall':'UTF-8'} %. Total kredittkjøpspris {$klarna_campaign_credit_cost_3|escape:'htmlall':'UTF-8'} kr.</p>
					{/if}

					<span id="accountxx"></span>
					</div>

				</div>
				{/if}

			</div>
			<br />

</div>


<script>
		new Klarna.Terms.Account({
			el: 'accountxx',
			eid: "{$klarna_merchant_eid|escape:'htmlall':'UTF-8'}",
			locale: "{$klarna_language|escape:'htmlall':'UTF-8'}",
			type: "{$klarna_device|escape:'htmlall':'UTF-8'}"
			});
</script>
{if $klarna_country == 'DE'}
<script>
		new Klarna.Terms.Consent({
    		el: 'consentxx',
    		eid: "{$klarna_merchant_eid|escape:'htmlall':'UTF-8'}",
    		locale: "{$klarna_language|escape:'htmlall':'UTF-8'}",
    		type: "{$klarna_device|escape:'htmlall':'UTF-8'}"
});
</script>
{/if}
{/if}
