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
 
$(document).ready(function () {
	var patternPno = new RegExp($('#klarna_pno_invoice').attr('pattern'));

	$.validator.setDefaults({
		errorClass: 'klarna_error',
		errorElement: 'div',
		focusInvalid: false,
		highlight: function(element) {
			$(element).closest('.control-group').addClass('klarna_error');
		},
		unhighlight: function(element) {
			$(element).closest('.control-group').removeClass('klarna_error');
		},
		eachValidField : function() {
					$(this).closest('.control-group').removeClass('klarna_error').addClass('success');
				},
		eachInvalidField : function() {
				$(this).closest('.control-group').removeClass('success').addClass('klarna_error');
		},
		submitHandler: function(form) {
			form.submit();
		}

		});

		$('#klarna_invoice_payment').validate({
			errorLabelContainer: "#error_invoice",
			rules: {
				klarna_pno: {
					required: {
					depends: function (element) {
						return ($("#klarna_payment_invoice_1").is(":checked") || $("#klarna_payment_invoice_2").is(":checked"));
						}
					},
					pattern: patternPno
				},
				klarna_de_constent: {
					required: true
				},
				klarna_payment_gender: {
					required: true
				},
				klarna_payment_type: {
					required:{ 
					depends: function(element) {
						return ($('#klarna_pno_invoice').val() == '');
					}

				}
			}

		},
		messages: {
			klarna_pno: {
				required: warningPno,
				pattern: patternValid
			},
			klarna_de_constent: {
				required: warningConsent
			},
			klarna_payment_type: {
				required: choosePayTypeKlarna
			}

		},

	});

	$('#klarna_part_payment').validate({
			errorLabelContainer: "#error_part",
			rules: {
				klarna_payment_type: {
					required:{ 
					depends: function(element) {
						return ($('#klarna_pno_part_payment').val() == '');
						}
					}
				},
				klarna_pno : {
					required: {
					depends: function(element) {
						return ($("#klarna_payment_part_1").is(":checked") || $("#klarna_payment_part_2").is(":checked") || $("#klarna_payment_part_3").is(":checked") || $("#klarna_payment_part_4").is(":checked"));
						}
					},
					pattern: patternPno
				},
				klarna_payment_gender: {
					required: true
				},
				klarna_de_constent: {
					required: true
				}

			},
			messages: {
				klarna_pno: {
					required: warningPno,
					pattern: patternValid
				},
				klarna_payment_type: {
					required: choosePayTypeKlarna
				},
				klarna_de_constent: {
					required: warningConsent
					}
			},

	});  
});