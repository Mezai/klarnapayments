$(document).ready(function(e) {
	$("input[type='radio'][name='klarna_payment_type']").click(function() {
    var $klarna_description_part = $('.klarna_description_part');
    var $klarna_description_inv = $('.klarna_description_inv');
    var loadImage = baseDir + "modules/klarnapayments/views/img/loading_spinner" + ".gif";
    var image = '<img src="' + loadImage + '" />';

      if ($(this).attr('checked')) {
        $klarna_description_part.show();
        $.ajax({
          type: 'POST',
          typeOfPayment: $(this).attr('class'),
          url: baseDir + 'modules/klarnapayments/klarna_ajax.php',
          data: 'method=myMethod&id_data=' + $(this).attr('class'),
          beforeSend: function(){
            if (this.typeOfPayment === 'klarna_payment_part_fixed_3' || this.typeOfPayment === 'klarna_payment_part_fixed_2' || this. typeOfPayment === 'klarna_payment_part_fixed_1' || this.typeOfPayment === 'klarna_payment_part_flexible') {
              $('.klarna_description_part').html(image);
            } else if (this.typeOfPayment === 'klarna_payment_invoice_payinx' || this.typeOfPayment === 'klarna_payment_invoice') {
              $('.klarna_description_inv').html(image);
            }
            },
          dataType: 'json',
          success: function(data) {

          if (this.typeOfPayment === 'klarna_payment_part_fixed_3' || this.typeOfPayment === 'klarna_payment_part_fixed_2' || this. typeOfPayment === 'klarna_payment_part_fixed_1' || this.typeOfPayment === 'klarna_payment_part_flexible') {  
              $('.klarna_description_part').html(data);
            } else if(this.typeOfPayment === 'klarna_payment_invoice_payinx' || this.typeOfPayment === 'klarna_payment_invoice') {
              $('.klarna_description_inv').html(data);
            }
          },
          error: function (request, status, error) {
                alert(request.responseText);
          }
      });

      } else {
        $klarna_description_part.hide();
        $klarna_description_inv.hide();
      }
	
  	})

});
