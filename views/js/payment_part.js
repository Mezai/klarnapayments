/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(function() {
	$(".col-sm-6.info.campaign1").hide();
    $(".col-sm-6.info.campaign2").hide();
    $(".col-sm-6.info.campaign3").hide();
	
    $('#select_klarna_method').change(function(){
        if($( "#select_klarna_method option:selected" ).attr('class') == 'klarna_account') {
            $('.col-sm-6.info.account').show(); 
        } else {
            $('.col-sm-6.info.account').hide(); 
        } 

        if($( "#select_klarna_method option:selected" ).attr('class') == 'klarna_campaign_1') {
            $('.col-sm-6.info.campaign1').show(); 
        } else {
            $('.col-sm-6.info.campaign1').hide(); 
        }
        if($( "#select_klarna_method option:selected" ).attr('class') == 'klarna_campaign_2') {
            $('.col-sm-6.info.campaign2').show(); 
        } else {
            $('.col-sm-6.info.campaign2').hide(); 
        }
        if($( "#select_klarna_method option:selected" ).attr('class') == 'klarna_campaign_3') {
            $('.col-sm-6.info.campaign3').show(); 
        } else {
            $('.col-sm-6.info.campaign3').hide(); 
        } 


    });
});

$(document).ready(function() {
    $('#klarna_form').submit(function() {
        var abort = false;
        $("div.klarna_error").remove();
        $(':input[required]').each(function() {
            if ($(this).val() ==='') {
                $(this).after('<div class="klarna_error">This is a required field</div>');
                abort = true;
            }
    });
        if (abort) { return false; } else { return true; }
    })
});

$(document).ready(function() {
$(':input[placeholder]').blur(function() {
        $("div.klarna_error").remove();
        var pattern = $(this).attr('pattern');
        var placeholder = $(this).attr('placeholder');
        var isValid = $(this).val().search(pattern) >= 0;

        if (!isValid) {
            $(this).focus();
            $(this).after('<div class="klarna_error">This is a required field</div>');
        }
        }) 
});




