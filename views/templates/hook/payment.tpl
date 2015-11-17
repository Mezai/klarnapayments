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

{foreach from=$klarna_data item=value}
	{if $value.group.code == 'invoice'}
	<div class="container-fluid klarnainvoicepayment">
		<img id="klarna_logo" class="klarna_logo" src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=200" style="width:200px">
			<div class="row">
				<div class="col-sm-12">
					<div class="klarna-header">
						<div class="col-sm-6">
							<div class="container">
							<div class="row">

						<form action="{$validation_url|escape:'htmlall':'UTF-8'}" method="POST" id="klarna_part_payment">
						<h4 class="klarna_payment_description">{$value.group.title}</h4>
						<div class="btn-group">
							
          					<input type="radio" class="klarna_payment_invoice" id="klarna_payment_invoice_1" name="klarna_payment_type" value="{$value.pclass_id}"/>
        					<label for="klarna_payment_invoice_1">{$value.title}</label><br>
        				{if isset($klarna_special_id)}
  							
          					<input type="radio" class="klarna_payment_invoice_payinx" id="klarna_payment_invoice_2" name="klarna_payment_type" value="{$klarna_special_id}"/>
        					<label for="klarna_payment_invoice_2">{$klarna_special_description}</label><br>
        				{/if}
        				</div>	
						<div class="required form-group">
									<label for="klarna_pno" class="required" style="display:block">{l s='Social security number:' mod='klarnapayments'}</label>
										<input type="text" class="klarna_pno_input" autocomplete="off" name="klarna_pno" pattern="{$klarna_pattern|escape:'htmlall':'UTF-8'}" placeholder="{$klarna_placeholder|escape:'htmlall':'UTF-8'}" id="klarna_pno" maxlength="11" required/>
										<button type="submit" class="klarna-submit-button">{l s='Submit Payment' mod='klarnapayments'}</button>
									</div>
						
								
							</form>
						</div>
					</div>
				</div>
			<div class="row margin-b-2">
					<div class="col-sm-6 klarna_description_inv">
					

					<p>{$value.use_case}</p><p><span id="invoicexx"></span></p>
					</div>

				 </div>
			</div>
		</div>
	</div>
</div>


     {/if}
 {/foreach}


{foreach from=$klarna_data item=value}
	{if $value.group.code == 'part_payment'}

	<div class="container-fluid klarnapartpayment">
		<img id="klarna_logo" class="klarna_logo" src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=200" style="width:200px">
			<div class="row">

				<div class="col-sm-12">

					<div class="klarna-header">

					<div class="col-sm-6">
						<div class="container">
							<div class="row">
								<form action="{$validation_url|escape:'htmlall':'UTF-8'}" method="POST" id="klarna_part_payment">
  								<h4 class="klarna_payment_description">{$value.group.title}</h4>
  								<div class="btn-group">
  									
          								<input type="radio" class="klarna_payment_part_flexible" id="klarna_payment_part_1" name="klarna_payment_type" value="{$value.pclass_id}"/>
        								<label for="klarna_payment_part_1">{$value.title}</label><br>
        							{if !empty($KlarnaPClass[1])}
        							
          								<input type="radio" class="klarna_payment_part_fixed_1" id="klarna_payment_part_2" name="klarna_payment_type" value="{$KlarnaPClass[1]->getId()}"/><label for="klarna_payment_part_2">{displayPrice price=$klarna_calc_monthly1}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[1]->getDescription()}</label><br>
        							
        							{/if}
        							{if !empty($KlarnaPClass[2])}
        							
          								<input type="radio" class="klarna_payment_part_fixed_2" id="klarna_payment_part_3" name="klarna_payment_type" value="{$KlarnaPClass[2]->getId()}"/>
        								<label for="klarna_payment_part_3">{displayPrice price=$klarna_calc_monthly2}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[2]->getDescription()}</label><br>
        							{/if}
        							{if !empty($KlarnaPClass[3])}
        							
          								<input type="radio" class="klarna_payment_part_fixed_3" id="klarna_payment_part_4" name="klarna_payment_type" value="{$KlarnaPClass[3]->getId()}"/>
        								<label for="klarna_payment_part_4">{displayPrice price=$klarna_calc_monthly3}{l s=' in ' mod='klarnapayments'}{$KlarnaPClass[3]->getDescription()}</label><br>
        							{/if}
        						</div>
        							<div class="required form-group">
									<label for="klarna_pno" class="required" style="display:block">{l s='Social security number:' mod='klarnapayments'}</label>
										<input type="text" class="klarna_pno_input" autocomplete="off" name="klarna_pno" pattern="{$klarna_pattern|escape:'htmlall':'UTF-8'}" placeholder="{$klarna_placeholder|escape:'htmlall':'UTF-8'}" id="klarna_pno" maxlength="11" required/>						
      									
      								
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
 {/foreach}


<script type="text/javascript">
// <![CDATA[
var  warningPno = "{l s='Please check your social security number' mod='klarnapayments'}";
//]]>
</script>
{/if}