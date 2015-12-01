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

{capture name=path}{l s='Klarna payments' mod='klarnapayments'}{/capture}

<h1>{l s='Payment Error' mod='klarnapayments'}</h1>

<p>
{l s='There was an error in your transaction' mod='klarnapayments'}<br /><br />

<strong>{$klarna_error|unescape:"entity"}</strong>

<br/><br/>{l s='Please' mod='klarnapayments'} <a href="{$link->getPageLink('order', true)|escape:'htmlall':'UTF-8'}">{l s='click here' mod='klarnapayments'}</a> {l s='to return to checkout page' mod='klarnapayments'}.

<br /><br />{l s='For any questions or for further information, please contact our' mod='klarnapayments'} <a href="{$link->getPageLink('contact', true)|escape:'htmlall':'UTF-8'}">{l s='customer support' mod='klarnapayments'}</a>.

</p>
