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

<!-- Block klarna fetch address -->

<div id="klarna_get_address" class="block">
		<h3 class="page-subheading">{l s='Klarna get address' mod='klarnapayments'}</h3>
		<div class="block_content">
			<form action="{$link->getPageLink('order-opc', true)|escape:'htmlall':'UTF-8'}" method="post" id="getAddressForm">
				<label for="pno">
				Get address
				</label>
				<input type="text" id="getAddressInput" pattern="{literal}^[0-9]{6,6}(([0-9]{2,2}[-\+]{1,1}[0-9]{4,4})|([-\+]{1,1}[0-9]{4,4})|([0-9]{4,6}))${/literal}" placeholder="YYMMDD-NNNN" maxlength="11" class="inputPno" name="pno" size="18" autocomplete="off" />
				<input type="submit" id="getAddressSubmit" value="ok" class="button_small" name="submitKlarnaAddress" />
			</form>
		{if isset($msg)}
			<p class="{if $address_error}warning_pno{else}success_pno{/if}">{$msg|escape:'htmlall':'UTF-8'}</p>
		{/if}
		</div>
</div>

<!--- Block klarna fetch address -->

{if isset($klarna_firstname) && isset($klarna_lastname)}
<!-- Fill the fields with klarna address. -->
<script type="text/javascript">
$(document).ready(function() {
	
	var klarnaFirstName = "{$klarna_firstname|strip_tags:'UTF-8'}";
	var klarnaLastName = "{$klarna_lastname|strip_tags:'UTF-8'}";
	var klarnaEmail = "{$klarna_email|escape:'htmlall':'UTF-8'}";
	var klarnaTelNo = "{$klarna_telno|escape:'htmlall':'UTF-8'}";
	var klarnaCellNo = "{$klarna_cellno|escape:'htmlall':'UTF-8'}";
	var klarnaCareOf = "{$klarna_careof|strip_tags:'UTF-8'}";
	var klarnaStreet = "{$klarna_street|strip_tags:'UTF-8'}";
	var klarnaZip = "{$klarna_zip|escape:'htmlall':'UTF-8'}";
	var klarnaCity = "{$klarna_city|strip_tags:'UTF-8'}";


	$('#customer_firstname').val(klarnaFirstName);
	$('#firstname').val(klarnaFirstName);
	$('#customer_lastname').val(klarnaLastName);
	$('#lastname').val(klarnaLastName);
	$('#email').val(klarnaEmail);
	$('#phone').val(klarnaTelNo);
	$('#phone_mobile').val(klarnaCellNo);
	$('#address1').val(klarnaStreet);
	$('#postcode').val(klarnaZip);
	$('#city').val(klarnaCity);
	$('#address2').val(klarnaCareOf);
	$('#id_country').val("18"); //Always Sweden



	$("input").each(function() {
	if ( $(this).val().length !== 0) {
  		$(this).parent().addClass('form-ok');

  		}	//Setting the form-ok class
	});

});

</script>
{/if}

