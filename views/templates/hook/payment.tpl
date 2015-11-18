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

{if !empty($klarna_data)}
{foreach from=$klarna_data item=value}
{/foreach}
{/if}

	{if empty($value.group.code) || $value.group.code == 'invoice'}
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
						<h4 class="klarna_payment_description">{if isset($value.group.title)}{$value.group.title}{/if}{if $klarna_locale == 'AT'}Klarna Rechnung{/if}
							{if $klarna_locale == 'DE'}Klarna Rechnung{/if}{if $klarna_locale == 'DK'}Klarna Faktura{/if}
							{if $klarna_locale == 'FI'}Klarna Lasku{/if}
							{if $klarna_locale == 'NL'}Klarna Achteraf betalen{/if}</h4>
						<div class="btn-group">
							
          					<input type="radio" class="klarna_payment_invoice" id="klarna_payment_invoice_1" name="klarna_payment_type"
          					value="{if !empty($value.pclass_id)}{$value.pclass_id}{else}-1{/if}"/>
        					<label for="klarna_payment_invoice_1">{if isset($value.title)}{$value.title}{/if}
        					{if $klarna_locale == 'AT'}Rechnung: In 14 Tagen bezahlen{/if}	
							{if $klarna_locale == 'DE'}Rechnung: In 14 Tagen bezahlen{/if}
							{if $klarna_locale == 'DK'}Faktura: Betal om 14 dage{/if}
							{if $klarna_locale == 'FI'}Lasku: Maksa 14 päivän kuluessa{/if}
							{if $klarna_locale == 'NL'}Achteraf betalen: binnen 14 dagen{/if}	
        					</label><br>
        				{if isset($klarna_special_id)}
  							
          					<input type="radio" class="klarna_payment_invoice_payinx" id="klarna_payment_invoice_2" name="klarna_payment_type" value="{$klarna_special_id}"/>
        					<label for="klarna_payment_invoice_2">{if isset($klarna_special_description)}{$klarna_special_description}{/if}</label><br>
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
					

					<p>{if isset($value.use_case)}{$value.use_case}{/if}</p><p><span id="invoicexx"></span></p>
					</div>

				 </div>
			</div>
		</div>
	</div>
</div>

		{/if}
     {/if}


{if !empty($klarna_data)}
{foreach from=$klarna_data item=value}
{/foreach}
{/if}

	{if empty($value.group.code) || $value.group.code == 'part_payment'}
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
  								<h4 class="klarna_payment_description">{if isset($value.group.title)}{$value.group.title}{/if}</h4>
  								<div class="btn-group">
  									{if isset($klarna_account_id)}
          								<input type="radio" class="klarna_payment_part_flexible" id="klarna_payment_part_1" name="klarna_payment_type" value="{if isset($value.pclass_id)}{$value.pclass_id}{/if}"/>
        								<label for="klarna_payment_part_1">{if isset($value.title)}{$value.title}{/if}</label><br>
        							{/if}
        							{if isset($KlarnaPClass[1])}
        							
          								<input type="radio" class="klarna_payment_part_fixed_1" id="klarna_payment_part_2" name="klarna_payment_type" value="{$KlarnaPClass[1]->getId()}"/><label for="klarna_payment_part_2">{displayPrice price=$klarna_calc_monthly1}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()}</label><br>
        							
        							{/if}
        							{if isset($KlarnaPClass[2])}
        							
          								<input type="radio" class="klarna_payment_part_fixed_2" id="klarna_payment_part_3" name="klarna_payment_type" value="{$KlarnaPClass[2]->getId()}"/>
        								<label for="klarna_payment_part_3">{displayPrice price=$klarna_calc_monthly2}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[2]->getDescription()}</label><br>
        							{/if}
        							{if isset($KlarnaPClass[3])}
        							
          								<input type="radio" class="klarna_payment_part_fixed_3" id="klarna_payment_part_4" name="klarna_payment_type" value="{$KlarnaPClass[3]->getId()}"/>
        								<label for="klarna_payment_part_4">{displayPrice price=$klarna_calc_monthly3}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[3]->getDescription()}</label><br>
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
     {/if}


<script type="text/javascript">
// <![CDATA[
var  warningPno = "{l s='Please check your social security number' mod='klarnapayments'}";
//]]>
</script>
{/if}