$(function(){


	$('.orderButton').click(function(){
		var id = $(this).data('productId');

		$.getJSON('/Catalog/ProductNameById', {'productId':id}, function(data){
			$('#OrderFormOrder').val(data.productName);
			$('#OrderFormProductId').val(id);
			$('#OrderFormPhone').focus();
		});

		return false;
	});

	$('#FastOrderForm').submit(function(){
		var error = false;
		if($('#OrderFormPhone').val() == '') {
			$('#OrderFormPhone').tooltip({'title':'Заполните номер вашего телефона', 'trigger':'manual'});
			$('#OrderFormPhone').tooltip('show');
			$('#OrderFormPhone').focus();
			$('#OrderFormPhone').blur(function(){
				$('#OrderFormPhone').tooltip('hide');
			});
			error = true;
		}

		if($('#OrderFormOrder').val() == '') {
			$('#OrderFormOrder').tooltip({'title':'Опишите ваш заказ', 'trigger':'manual'});
			$('#OrderFormOrder').tooltip('show');
			if(!error) $('#OrderFormOrder').focus();
			$('#OrderFormOrder').blur(function(){
				$('#OrderFormOrder').tooltip('hide');
			});
			error = true;
		}

		if(error) return false;
	});

	if(typeof(hideTraps) != 'undefined') hideTraps();

});