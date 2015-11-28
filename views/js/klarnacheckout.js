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
	$('.klarnaCarrierKco form').submit(function(e){
		$(this).find('.button').hide();
		$(this).append(klarnaKcoLoader());
	});

	var id = '.klarnaKcoGiftWrapping #giftMessage';
	var formGroup = $(id).closest('.form-group');

	function toggle(show) {
		if (show) {
			formGroup.show();
		} else {
			formGroup.hide();
		}
	}

	var checked = $('#enableGiftWrapping').is(':checked');
	toggle(checked);

	$('#enableGiftWrapping').change(function() {
		toggle(this.checked);
	});

	$('.klarnaGiftWrapping form').submit(function() {
		$(this).find('.button').hide();
		$(this).append(klarnaKcoLoader());

	});

	var submitBtn = $('.klarnaGiftWrapping form button');
	submitBtn.hide();
	var onChange = function(){
		submitBtn.show();
		$(this).unbind(onChange);
	};

	$('.klarnaGiftWrapping form input').change(onChange);

	function klarnaKcoLoader() {
		return '<span class="klarnaKcoLoader"></span>';
	}

	var ps = {};

	function togglePS(show) {
		if (!show) {
			ps.checkout = $('#center_column .opc-main-block').detach();
			ps.strayHeader=jQuery('h1.step-num').detach();

		} else {
			ps.checkout.appendTo('#center_column');
			ps.strayHeader.prependTo('#opc_payment_methods.opc-main-block');
		}
	}

	function toggleKCO(show) {
		$('.klarnapaymentsKCO').toggle(show);
	}

	function initCheckout() {
		togglePS(false);
		toggleKCO(true);
		setCurrent('#klarnaPaymentsKco');
	}

	function showPS() {
		togglePS(true);
		toggleKCO(false);
		setCurrent('#klarnaCheckoutNormalPayment');
	}

	function setCurrent(element) {
		$('.KlarnaCheckoutPaymentOption').removeClass('.current');
		$(element).addClass('current');
	}


	$(document).on('click', '#klarnaPaymentsKco', initCheckout);

	$(document).on('click', '#klarnaCheckoutNormalPayment', showPS);

	initCheckout();

	$('.cart_navigation').remove();

	 $(document).on('click','.cart_quantity_up,.cart_quantity_down,.cart_quantity_delete,.delivery_option_radio','input[name=submitAddDiscount]', function(){
	 	window._klarnaCheckout(function (api) {
   			api.suspend();
   			api.resume();
		});

	});
});







