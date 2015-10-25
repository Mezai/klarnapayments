$(document).ready(function(){
	$('.klarnaCarrierKco form').submit(function(e){
		$(this).find('.button').hide();
		$(this).append(klarnaKcoLoader());
	});
});


$(document).ready(function(){
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

});

function klarnaKcoLoader() {
	return '<span class="klarnaKcoLoader"></span>';
}


$(document).ready(function(){
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

	 // $('document').on('click','.cart_quantity_up,.cart_quantity_down,.cart_quantity_delete', function() {
	 // 	$('#klarnaPaymentsKco').click();


	 $(document).on('click','.cart_quantity_up,.cart_quantity_down,.cart_quantity_delete', function(){
	 	$('#klarnaPaymentsKco').click();
	});
});







