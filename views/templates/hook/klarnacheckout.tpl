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

<div class="klarnaKcoChoosePayment">
  <div id="klarnaPaymentsKco" class="KlarnaCheckoutPaymentOption current">
    <h3>{l s='Klarna checkout' mod='klarnapayments'}</h3>
      <img src="https://cdn.klarna.com/1.0/shared/image/generic/badge/sv_se/checkout/short-blue.png?width=276&amp;eid={$klarna_eid|escape:'htmlall':'UTF-8'}" alt="Klarna Checkout">
  </div>
  <div id="klarnaCheckoutNormalPayment" class="klarnaCheckoutPaymentOption">
    <h3>{l s='Other payment options' mod='klarnapayments'}</h3><ul></ul></div>
  </div>
<div class="klarnaCheckoutCarrier klarnaCheckout" style="display: block;">
  <div><h2>{l s='Delivery options' mod='klarnapayments'}</h2>
    <table class="table table-bordered"><tbody><tr class="klarnaCarrierKco">

    </table>
    </div>
</div>
<div class="klarnapaymentsKCO">
  <div class="heading">
    <h2>{l s='Checkout express' mod='klarnapayments'}</h2>
    <p>{l s='Finish your purchase fast, safe and frictionless' mod='klarnapayments'}</p>
  </div>
  <div class="snippet">
    <div id="klarna-checkout-container" style="overflow-x: hidden;">
      <noscript> Please &lt;a href="http://enable-javascript.com"&gt;enable JavaScript&lt;/a&gt;.</noscript>
      {$snippet|escape:'htmlall':'UTF-8'}
    </div>
  </div>
</div>
