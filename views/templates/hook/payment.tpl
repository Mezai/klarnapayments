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

{if !$checkLocale}
	<p class="alert alert-warning">{l s='Please change your currency or language to match your country in order to shop with Klarna.' mod='klarnapayments'}</p>	
{else}

	{if $payment_invoice}
	<div class="container-fluid klarnainvoicepayment">
		<img id="klarna_logo" class="klarna_logo" src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=200" style="width:200px">
			<div class="row">
				<div class="col-sm-12">
					<div class="klarna-header">
						<div class="col-sm-6">
							<div class="container">
							<div class="row">

						<form action="{$validation_url|escape:'htmlall':'UTF-8'}" method="POST" id="klarna_invoice_payment">
						<h4 class="klarna_payment_description">{if isset($invoice_description)}{$invoice_description|escape:'htmlall':'UTF-8'}{/if}{if $klarna_locale == 'AT'}Klarna Rechnung{/if}
							{if $klarna_locale == 'DE'}Klarna Rechnung{/if}{if $klarna_locale == 'DK'}Klarna Faktura{/if}
							{if $klarna_locale == 'FI'}Klarna Lasku{/if}
							{if $klarna_locale == 'NL'}Klarna Achteraf betalen{/if}</h4>
						<div class="btn-group">
							
          					<input type="radio" class="klarna_payment_invoice" id="klarna_payment_invoice_1" name="klarna_payment_type"
          					value="{if isset($invoice_pclass_id)}{$invoice_pclass_id|escape:'htmlall':'UTF-8'}{else}-1{/if}" required/>
        					<label for="klarna_payment_invoice_1">{if isset($invoice_title)}{$invoice_title|escape:'htmlall':'UTF-8'}{/if}
        					{if $klarna_locale == 'AT'}Rechnung: In 14 Tagen bezahlen{/if}	
							{if $klarna_locale == 'DE'}Rechnung: In 14 Tagen bezahlen{/if}
							{if $klarna_locale == 'DK'}Faktura: Betal om 14 dage{/if}
							{if $klarna_locale == 'FI'}Lasku: Maksa 14 päivän kuluessa{/if}
							{if $klarna_locale == 'NL'}Achteraf betalen: binnen 14 dagen{/if}	
        					</label><br>
        				{if isset($klarna_special_id)}
  							
          					<input type="radio" class="klarna_payment_invoice_payinx" id="klarna_payment_invoice_2" name="klarna_payment_type" value="{$klarna_special_id|escape:'htmlall':'UTF-8'}" required/>
        					<label for="klarna_payment_invoice_2">{if isset($klarna_special_description)}{$klarna_special_description|escape:'htmlall':'UTF-8'}{/if}</label><br>
        				{/if}
        				</div>	
						<div class="required form-group">
							{if $klarna_locale == 'DE' || $klarna_locale == 'AT' || $klarna_locale == 'NL'}
        								<input type="radio" class="klarna_gender" id="klarna_payment_gender" name="klarna_payment_gender" value="1" required/>
        								<label for="klarna_payment_gender">{l s='Male' mod='klarnapayments'}</label><br>
        								<input type="radio" class="klarna_gender" id="klarna_payment_gender" name="klarna_payment_gender" value="0" required/>
        								<label for="klarna_payment_gender">{l s='Female' mod='klarnapayments'}</label><br>
        					{/if}	
									<label for="klarna_pno" class="required" style="display:block">{l s='Social security number:' mod='klarnapayments'}</label>
										<input type="text" class="klarna_pno_input" autocomplete="off" name="klarna_pno" pattern="{$klarna_pattern|escape:'htmlall':'UTF-8'}" placeholder="{$klarna_placeholder|escape:'htmlall':'UTF-8'}" id="klarna_pno_invoice" maxlength="11" required/>
										<div class="klarna_error_invoice"></div>
										<button type="submit" class="klarna-submit-button">{l s='Submit Payment' mod='klarnapayments'}</button>
									</div>
						
								
							</form>
						</div>
					</div>
				</div>
			<div class="row margin-b-2">
					<div class="col-sm-6 klarna_description_inv">
					
					</div>

				 </div>
			</div>
		</div>
	</div>
</div>

     {/if}



	{if $payment_part}

	<div class="container-fluid klarnapartpayment">
		<img id="klarna_logo" class="klarna_logo" src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=200" style="width:200px">
			<div class="row">

				<div class="col-sm-12">

					<div class="klarna-header">

					<div class="col-sm-6">
						<div class="container">
							<div class="row">
								<form action="{$validation_url|escape:'htmlall':'UTF-8'}" method="POST" id="klarna_part_payment">
  								<h4 class="klarna_payment_description">{if isset($partpayment_description)}{$partpayment_description|escape:'htmlall':'UTF-8'}{/if}
  								{if $klarna_locale == 'DK'}Afbetaling{/if}
								{if $klarna_locale == 'DE'}Ratenkauf{/if}
								{if $klarna_locale == 'FI'}Erämaksu{/if}	
  								</h4>
  								<div class="btn-group">
  									{if isset($klarna_account_id)}
          								<input type="radio" class="klarna_payment_part_flexible" id="klarna_payment_part_1" name="klarna_payment_type" value="{if isset($partpayment_pclass_id)}{$partpayment_pclass_id|escape:'htmlall':'UTF-8'}{/if}" required/>
        								<label for="klarna_payment_part_1">{if isset($partpayment_title)}{$partpayment_title|escape:'htmlall':'UTF-8'}{/if}
        								{if $klarna_locale == 'FI'}Joustava erämaksu – Maksa omassa tahdissasi{/if}		
        								</label><br>
        							{/if}
        							{if isset($KlarnaPClass[1]) &&  ($total > $klarna_min_amount1)}
        							
          								<input type="radio" class="klarna_payment_part_fixed_1" id="klarna_payment_part_2" name="klarna_payment_type" value="{$KlarnaPClass[1]->getId()|escape:'htmlall':'UTF-8'}" required/><label for="klarna_payment_part_2">{displayPrice price=$klarna_calc_monthly1}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()|strip_tags:'UTF-8'}</label><br>
        							
        							{/if}
        							{if isset($KlarnaPClass[2]) && ($total > $klarna_min_amount2)}
        							
          								<input type="radio" class="klarna_payment_part_fixed_2" id="klarna_payment_part_3" name="klarna_payment_type" value="{$KlarnaPClass[2]->getId()|escape:'htmlall':'UTF-8'}" required/>
        								<label for="klarna_payment_part_3">{displayPrice price=$klarna_calc_monthly2}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[2]->getDescription()|strip_tags:'UTF-8'}</label><br>
        							{/if}
        							{if isset($KlarnaPClass[3]) && ($total > $klarna_min_amount3)}
        							
          								<input type="radio" class="klarna_payment_part_fixed_3" id="klarna_payment_part_4" name="klarna_payment_type" value="{$KlarnaPClass[3]->getId()|escape:'htmlall':'UTF-8'}" required/>
        								<label for="klarna_payment_part_4">{displayPrice price=$klarna_calc_monthly3}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[3]->getDescription()|strip_tags:'UTF-8'}</label><br>
        							{/if}
        						</div>
        							<div class="required form-group">
        							{if $klarna_locale == 'DE' || $klarna_locale == 'AT' || $klarna_locale == 'NL'}
        								<input type="radio" class="klarna_gender" id="klarna_payment_gender" name="klarna_payment_gender" value="1" required/>
        								<label for="klarna_payment_gender">{l s='Male' mod='klarnapayments'}</label><br>
        								<input type="radio" class="klarna_gender" id="klarna_payment_gender" name="klarna_payment_gender" value="0" required/>
        								<label for="klarna_payment_gender">{l s='Female' mod='klarnapayments'}</label><br>
        							{/if}	
									<label for="klarna_pno" class="required" style="display:block">{l s='Social security number:' mod='klarnapayments'}</label>
										<input type="text" class="klarna_pno_input" autocomplete="off" name="klarna_pno" pattern="{$klarna_pattern|escape:'htmlall':'UTF-8'}" placeholder="{$klarna_placeholder|escape:'htmlall':'UTF-8'}" id="klarna_pno_part_payment" maxlength="11" required/>						
      									<div class="klarna_error_part"></div>
      								
      								<button type="submit" class="klarna-submit-button">{l s='Submit Payment' mod='klarnapayments'}</button>
      								</div>
      							</form>
  							</div>
						</div>
						
				</div>
			<div class="row margin-b-2">
					<div class="col-sm-6 klarna_description_part">
					
					
					
					</div>

				 </div>

			</div>
		</div>
</div>

	
     {/if}


<script type="text/javascript">
// <![CDATA[
var  warningPno = "{l s='Please check your social security number' mod='klarnapayments'}";
//]]>
</script>
{/if}