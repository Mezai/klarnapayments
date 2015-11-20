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

$(document).ready(function() {
	$("body").on('change', 'input[name=klarna_payment_type]', function() {
    var $klarna_description_part = $('.klarna_description_part');
    var $klarna_description_inv = $('.klarna_description_inv');
    var loadImage = baseDir + "modules/klarnapayments/views/img/loading_spinner" + ".gif";
    var image = '<img src="' + loadImage + '" />';

      if ($(this).is(':checked')) {
        $.ajax({
          type: 'POST',
          typeOfPayment: $(this).attr('class'),
          url: baseDir + 'modules/klarnapayments/klarna_ajax.php',
          data: 'method=myMethod&id_data=' + $(this).attr('class'),
          dataType: 'json',
          beforeSend: function()
          {
              if (this.typeOfPayment === 'klarna_payment_part_fixed_3' || this.typeOfPayment === 'klarna_payment_part_fixed_2' || this. typeOfPayment === 'klarna_payment_part_fixed_1' || this.typeOfPayment === 'klarna_payment_part_flexible') {
              $('.klarna_description_part').html(image);
                } else if (this.typeOfPayment === 'klarna_payment_invoice_payinx' || this.typeOfPayment === 'klarna_payment_invoice') {
              $('.klarna_description_inv').html(image);
              }
          },
          
          }).done(function(data)
          {

          if (this.typeOfPayment === 'klarna_payment_part_fixed_3' || this.typeOfPayment === 'klarna_payment_part_fixed_2' || this. typeOfPayment === 'klarna_payment_part_fixed_1' || this.typeOfPayment === 'klarna_payment_part_flexible') {  
              $('.klarna_description_part').html(data);
            } else if(this.typeOfPayment === 'klarna_payment_invoice_payinx' || this.typeOfPayment === 'klarna_payment_invoice') {
              $('.klarna_description_inv').html(data);
            }
          }).fail(function (jqXHR, textStatus) {
                alert("Request failed");
        });
      } else {
        $klarna_description_part.hide();
        $klarna_description_inv.hide();
      }

    });
	
});
