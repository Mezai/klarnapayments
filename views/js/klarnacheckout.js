/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function(){

	function init()
	{
		togglePresta(false);
		toggleKlarnaCheckout(true);
		setCurrent('#klarnaPaymentsKco');

		if (typeof klarnaOnePage !== 'undefined')
		{
			if(klarnaOnePage === "1")
			{
				$('.klarnaKcoChoosePayment').detach();
			}

		}
	}
	function showPrestashop()
	{
		togglePresta(true);
		toggleKlarnaCheckout(false);
		setCurrent('#klarnaCheckoutNormalPayment');
	}
	function showKlarna()
	{
		togglePresta(false);
		toggleKlarnaCheckout(true);
		setCurrent('#klarnaCheckoutNormalPayment');
	}

	function togglePresta(show)
	{
		
		if (!show)
		{
			$('#center_column .opc-main-block').hide();

		} else {
			$('#center_column .opc-main-block').show();
			
		}
	}

	function toggleKlarnaCheckout(show)
	{

		if (!show) {
		$('.klarnapaymentsKCO').hide();
		$('.klarnaCheckoutCarrier').hide();

		} else {
		$('.klarnapaymentsKCO').show();
		$('.klarnaCheckoutCarrier').show();	
		}
	}

	function setCurrent(element)
	{
		var psPayHeaderAdressSpan = $("h1.step-num span").filter(function() { return ($(this).text() === '1') });
		var psPayHeaderAdress = psPayHeaderAdressSpan.parents('h1.step-num');
		var psPayHeaderSpan = $("h1.step-num span").filter(function() { return ($(this).text() === '3') });
		var psPayHeader = psPayHeaderSpan.parents('h1.step-num');

		$('.KlarnaCheckoutPaymentOption').removeClass('.current');
		$(element).addClass('current');

		if (element == '#klarnaPaymentsKco') {
			$(psPayHeaderAdress).hide();
			$(psPayHeader).hide();
		} else if(element == '#klarnaCheckoutNormalPayment') {
			$(psPayHeaderAdress).show();
			$(psPayHeader).show();
			$(psPayHeader).prependTo('#opc_payment_methods.opc-main-block');
			$(psPayHeaderAdress).prependTo('#opc_account.opc-main-block');
	
		}

	}

	$(document).on('click', '#klarnaPaymentsKco', showKlarna);

	$(document).on('click', '#klarnaCheckoutNormalPayment', showPrestashop);

	init();	

	 $(document).on('click','.cart_quantity_up,.cart_quantity_down,.cart_quantity_delete', 'input[name=submitAddDiscount]', function(){
	 	window._klarnaCheckout(function (api) {
   			api.suspend();
   			api.resume();
		});

	});

});