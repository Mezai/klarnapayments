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

$('document').ready(function() {

  var $inputHidden = $("input[type='hidden'][name='saveBtn'][value='1']");
  $inputHidden.remove();

  $('body').on('change', 'input[name=KLARNA_CHECKOUT_CHECKBOX]', toggleCheckbox);

  	function toggleCheckbox()
  	{

	  	if ($('input[name=KLARNA_CHECKOUT_CHECKBOX]:checked').val() === '0')
	  	{
	  		$('input[name=KLARNA_CHECKOUT_CHECKBOX_REQUIRED]').parents('.form-group').hide();
	  		$('input[name=KLARNA_CHECKOUT_CHECKBOX_CHECKED]').parents('.form-group').hide();
	  		$('input[name=KLARNA_CHECKOUT_CHECKBOX_TEXT]').parents('.form-group').hide();
	  	} else {
	  		$('input[name=KLARNA_CHECKOUT_CHECKBOX_REQUIRED]').parents('.form-group').show();
	  		$('input[name=KLARNA_CHECKOUT_CHECKBOX_CHECKED]').parents('.form-group').show();
	  		$('input[name=KLARNA_CHECKOUT_CHECKBOX_TEXT]').parents('.form-group').show();
	  	}
  	};

  	toggleCheckbox();
});
