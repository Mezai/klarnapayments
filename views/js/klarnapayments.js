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

	$('#klarna_invoice_payment').validate({
		onkeyup: false,
		onfocusout: false,
		errorElement: "div",
		errorPlacement: function(error, element) {
			error.appendTo("div#error_invoice");
		},

		rules: {
            klarna_pno: {
                required: true,
                minlength: 5
            },
            klarna_de_constent : {
            	required: true
			}
        },
        messages: {
        	klarna_pno: 
          	{
            required: this.warningPno
          	},
          	klarna_de_constent: 
          	{
            required: this.warningConsent
          	}
        	
        },

    });

     $("#klarna_invoice_submit").on('click', function() {
         $("#klarna_invoice_payment").valid();  
     });



     $('#klarna_part_payment').validate({
     	onkeyup: false,
		onfocusout: false,
		errorElement: "div",
		errorPlacement: function(error, element) {
			error.appendTo("div#error_part");
		},
		rules: {
            klarna_pno: {
                required: true,
                minlength: 5
            },
            klarna_de_constent : {
            	required: true
			}
        },
        messages: {
        	klarna_pno: 
          	{
            required: this.warningPno
          	},
          	klarna_de_constent: 
          	{
            required: this.warningConsent
          	}
        	
        },

    });

     $("#klarna_part_submit").on('click', function() {
         $("#klarna_part_payment").valid();  
     });


});