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

<div class="klarna-info">
	<img src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=200" width="200" alt="{l s='Klarna' mod='klarnapayments'}"/>
	<div style="clear:both;"</div>

	<div class="col-sm-12">
		<h2>{l s='Klarna invoice and part payments' mod='klarnapayments'}</h2>
		<p>
		{l s='With invoice, your customers can shop easily by only giving minimal information and avoiding long registration processes.' mod='klarnapayments'}
		{l s='The customer recieves the goods first and pays later, which makes shopping safe.' mod='klarnapayments'}
		{l s='With part payment, the customers have the opportunity to split their payments and decide how much they would like to pay each month.' mod='klarnapayments'}
		</p>
		<p>
		{l s='This module features' mod='klarnapayments'}
		<ul class="klarna-list">
			<li>Klarna invoice payments</li>
			<li>Klarna part payments</li>
			<li>Klarna get address function (only for Sweden)</li>
		</ul>
		</p>
	</div>

	<div class="col-sm-6">
		<h2>{l s='Create a Klarna account' mod='klarnapayments'}</h2>
		<p>
		{l s='To recieve payments via Klarna start by signing an agreement with Klarna' mod='klarnapayments'}	
		</p>
		<a href="https://klarna.com" target="_blank" class="klarna-button-blue">{l s='Sign up with Klarna' mod='klarnapayments'}</a>
	</div>
	
	<div class="col-sm-6">
		<h2>{l s='Documentation and Support' mod='klarnapayments'}</h2>
		<p>
			<a href="{$module_dir|escape:'htmlall':'UTF-8'}instructions/readme_en.pdf" target="_blank">{l s='Click here to read the manual' mod='klarnapayments'}</a>
		</p>
		<p>
			<a href="https://addons.prestashop.com/" target="_blank">{l s='Click here to contact us on Prestashop addons' mod='klarnapayments'}</a>
		</p>	

	</div>

	<div class="col-sm-12">
		<h2>{l s='Setting up the invoice fee' mod='klarnapayments'}</h2>
		<p>
		{l s='To set up the invoice fee follow below steps' mod='klarnapayments'}
		</p>
		<ol class="klarna-list">
			<li>{l s='Start with creating a new product called for example: Invoice fee' mod='klarnapayments'}</li>
			<li>{l s='In the information section set the status enabled to : No' mod='klarnapayments'}</li>
			<li>{l s='In the prices section set the price for the invoice fee' mod='klarnapayments'}</li>
			<li>{l s='In the quantities section set the quantity and preferably when out of stock to : allow orders.' mod='klarnapayments'}</li>
		</ol>			
	</div>	
	<div style="clear:both;"></div>
</div>	
