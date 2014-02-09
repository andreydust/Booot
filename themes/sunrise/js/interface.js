
$(function(){
	
	
	//Изображения везде
	$(".lightview").fancybox({
		'transitionIn'	: 'elastic'
	});
	
	//Окно перезвона
	$('#callMe span.ajaxLink').click(function(){
		$('#callMeWindow').fadeIn('fast','easeOutCubic');
		$('#callMeWindowFormName').focus();
	});
	$('#callMeWindowClose').click(function(){
		$('#callMeWindow').fadeOut('fast','easeOutCubic');
		$('#callMeWindowFormName').val('');
		$('#callMeWindowFormPhone').val('');
	});
	$('#callMeWindowSend').click(function(){
		$.ajax({
			type: 'post',
			url: '/Content/CallMe',
			data: {
				'name': $('#callMeWindowFormName').val(),
				'phone': $('#callMeWindowFormPhone').val()
			},
			success: function(reply){
				if(reply == 'fields_error') {
					alert('Вы не заполнили все поля!');
					return false;
				} else
				if(reply == 'error') {
					alert('Проблемы при отправке :(');
					return false;
				} else {
					$('#callMeWindowClose').trigger('click');
					$('#callMe span.ajaxLink').removeClass('ajaxLink').unbind().html('Спасибо, мы перезвони́м!');
				}
			}
		});
		//$('#callMeWindowClose').trigger('click');
		//$('#callMe span.ajaxLink').removeClass('ajaxLink').unbind().html('Спасибо, мы перезвони́м!');
	});

	//Поиск
	/*
	$('#searchBar div.example .ajaxLink').click(function(){
		$('#SearchString').val($(this).text()).focus();
		return false;
	});
	*/
	
	//Поиск, когда мы в каталоге
	/*
	$('#searchExpand .ajaxLink').click(function(){
		$('#catalogMenu .Active').removeClass('Active');
		$('#breadCrumbs').hide();
		$('#searchBar').fadeIn('fast','easeInCubic');
		$('#SearchString').focus();
		$(this).parent().hide();
	});
	*/
	
	
	//Лидеры продаж (скроллер)
	var ScrollerOneWidth = $('.ProductsScrollerProductW:first').width();
	var ScrollerCount = $('.ProductsScrollerProductW').length;
	var ScrollerSumWidth = ScrollerOneWidth * ScrollerCount;
	var ScrollerVisible = 4;
	var ScrollerCurrentProduct = 0;
	$('.ProductsScrollerWrapInner').width(ScrollerSumWidth);
	$('.ProductsScrollerRight').click(function(){
		var obj = $(this).parent();
		obj.find('.ProductsScrollerWrapInner').stop();
		var rightHidProducts = ScrollerCount - ScrollerCurrentProduct - ScrollerVisible;
		var nextProductPosition = rightHidProducts >= ScrollerVisible ? ScrollerCurrentProduct + ScrollerVisible : ScrollerCurrentProduct + rightHidProducts;
		var nextScrollerPosition = -(nextProductPosition * ScrollerOneWidth);
		obj.find('.ProductsScrollerWrapInner').animate({'left':nextScrollerPosition+'px'}, 1000, 'easeOutBack');
		ScrollerCurrentProduct = nextProductPosition;
	});
	$('.ProductsScrollerLeft').click(function(){
		var obj = $(this).parent();
		obj.find('.ProductsScrollerWrapInner').stop();
		var leftHidProducts = ScrollerCurrentProduct;
		var nextProductPosition = leftHidProducts >= ScrollerVisible ? ScrollerCurrentProduct - ScrollerVisible : ScrollerCurrentProduct - leftHidProducts;
		var nextScrollerPosition = -(nextProductPosition * ScrollerOneWidth);
		obj.find('.ProductsScrollerWrapInner').animate({'left':nextScrollerPosition+'px'}, 1000, 'easeOutBack');
		ScrollerCurrentProduct = nextProductPosition;
	});
	
	//Меню каталога
	$('#catalogTopMenu a.ajaxLink').click(function(){
		if($(this).parent().hasClass('Active')) return false;
		$(this).parent().parent().find('.Active').removeClass('Active');
		$(this).parent().addClass('Active');
		
		var id = parseInt($(this).attr('id').substr(9));
		
		var activeItem = $('#catalogSubMenu .Active');
		var subMenu = $('#catalogSubMenu');
		
		//if(subMenu.height() == 0) subMenu.css({ 'height': '0px' });
		subMenu.height(subMenu.height());
		subMenu.css({ 'overflow': 'hidden' });
		activeItem.removeClass('Active');
		$('#subMenuTopic' + id).addClass('Active');
		subMenu.animate({ 'height': $('#subMenuTopic' + id).height() }, 500, 'easeOutSine');
		
		

		
		return false;
	});
	/*
	$('#catalogMenu .catalogTopLevel .ctLink').click(function(){
		if($(this).parent().hasClass('Active')) return false;
		$(this).parent().parent().find('.Active').removeClass('Active');
		$(this).parent().addClass('Active');

		if($('#searchExpand').css('display') == 'none') {
			$('#breadCrumbs').show();
			$('#searchBar').hide();
			$('#searchExpand').show();
		}

		return false;
	});
	*/

	//Характеристика в подборе
	var hint = false;
	$('.WhatIsThat7').click(function(){
		preHint = hint;
		hint = $(this).parent().parent().parent().find('.TypeHint');
		if(hint.css('display') == 'block') {
			hint.find('.TypeHintClose .ajaxLink').trigger('click');
			return true;
		}
		if(preHint) preHint.find('.TypeHintClose .ajaxLink').trigger('click');
		hint.fadeIn('fast','easeInCubic');
	});
	$('.TypeHintClose .ajaxLink').click(function(){
		$(this).parent().parent().fadeOut('fast','easeOutCubic');
	});
	
	//Все бренды
	$('#allBrands').click(function(){
		$('#BrandsList input').attr('checked',true);
		return false;
	});
	//Снять бренды
	$('#allBrandsUnselect').click(function(){
		$('#BrandsList input').attr('checked',false);
		return false;
	});

	//Сравнить
	$(".ajaxCompare").click(function(){
		$(this).effect('transfer', { to: "#CompareBlockWrap", className: "ajaxCompareTransfer" }, 500, function(){
			$.ajax({
				type: 'post',
				url: '/Catalog/Compare',
				dataType: 'json',
				data: {
					'add': $(this).attr('id').substr(7)
				},
				success: function(reply){
					$('#CompareBlockWrap').html(reply.block);
				}
			});
		});
		$(this).fadeOut(400);
		
		return false;
	});
	//Убрать из сравнения
	$('.ajaxCompareRemove').live('click', function(){
		$.ajax({
			type: 'post',
			url: '/Catalog/Compare',
			dataType: 'json',
			data: {
				'del': $(this).attr('id').substr(13)
			},
			success: function(reply){
				$('#CompareBlockWrap').html(reply.block);
			}
		});
		$('#compare'+$(this).attr('id').substr(13)).fadeIn(400);
		
		return false;
	});
	$('#CompareTopics .ajaxLink').click(function(){
		var targetOffset = $($(this).attr('href')).offset().top;
		$('html,body').animate({scrollTop: targetOffset}, 800, 'easeInOutCirc');
		return false;
	});
	$('#compareClear').live('click', function(){
		
		$.ajax({
			type: 'post',
			url: '/Catalog/Compare',
			dataType: 'json',
			data: {
				'clean': true
			},
			success: function(reply){
				$('#CompareBlock').slideUp();
			}
		});
		$('.ajaxCompare').fadeIn(400);
		
		return false;
	});
	
	
	//Заказ в товаре
	//В корзину
	$('#AddBasket').click(function(){
		var obj = $(this);
		$.ajax({
			type: 'post',
			url: '/Basket/Add',
			data: {
				'id':		obj.parent().attr('id').substr(12),
				'format':	'one'
			},
			success: function(reply){
				if(!reply.result) {
					//alert("Извитите, что-то пошло не так :(");
				} else {
					$('#BasketBlockWrap').html(reply.block);
					$('#BuyButtonWrap').html(reply.buybutton);
					
					//obj.hide();
					//$('#MoreBlock').fadeIn('fast','easeInCubic');
					//$('#InBasketHint').html(reply.hint).slideDown('fast','easeInCubic');
				}
			}
		});
	});
	$('.AddBasketList').click(function(){
		var obj = $(this);
		$.ajax({
			type: 'post',
			url: '/Basket/Add',
			data: {
				'id':		obj.attr('id').substr(13),
				'format':	'inlist'
			},
			success: function(reply){
				if(!reply.result) {
					//alert("Извитите, что-то пошло не так :(");
				} else {
					$('#BasketBlockWrap').html(reply.block);
					obj.parent().html(reply.buybutton);
					
					//obj.hide();
					//$('#MoreBlock').fadeIn('fast','easeInCubic');
					//$('#InBasketHint').html(reply.hint).slideDown('fast','easeInCubic');
				}
			}
		});
	});

	
	//Изображения в товаре
	$('.SmallProductImage').click(function(){
		var parent = $(this).parent();
		if(parent.hasClass('Active')) return false;
		
		var id = $(this).attr('id').substr(12);

		$('.hiddenImages').attr('rel', 'productGallery');
		$('#hiddenImage' + id).attr('rel', 'productGalleryActive');
		
		$('#MainProductImage').attr('href', $(this).attr('href'));
		$('#MainProductImage img').attr('src', $(this).attr('rel'));
		parent.parent().find('.Active').removeClass('Active');
		parent.addClass('Active');
		
		$("#MainProductImage").fancybox({
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
		
		return false;
	});
	
	//Изображения в товаре
	$("#MainProductImage").fancybox({
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
	
	//Табы в товаре
	$('.ProductDetailsMenu div').click(function(){
		if($(this).hasClass('Active')) return false;
		
		var currentTab = $(this).parent().find('.Active');
		currentTab.removeClass('Active');
		$(currentTab.find('a').attr('href')).hide();
		$(this).addClass('Active');
		$($(this).find('a').attr('href')).show();
		
		location.hash = '#' + 'tab' + $(this).find('a').attr('href').substr(1);
		
		return false;
	});
	if($('.ProductDetailsMenu').length > 0) {
		$('.ProductDetailsMenuItem a[href=#' + location.hash.substr(4) + ']').trigger('click');
	}


	//Заглушка на сабмит формы
	$('#basketListForm').submit(function(){return false;});
	
	//Редактирование количества в корзине
	$('.editCount').keyup(function(){
		var id = $(this).attr('id').substr(5);
		var val = parseInt($(this).val());
		var parentObj = $(this).parent().parent();
		$.ajax({
			type: 'post',
			url: '/Basket/Edit',
			data: {
				'id':		id,
				'count':	val
			},
			success: function(reply){
				if(!reply.result) {
					//alert("Извитите, что-то пошло не так :(");
				} else {
					$('#BasketBlockWrap').html(reply.block);
					parentObj.find('.dPrice').html(reply.price);
					parentObj.find('.dTPrice').html(reply.tprice);
					$('#dSTCount').html(reply.totals.count);
					$('#dSTPrice').html(reply.totals.summ);
				}
			}
		});
	});
	
	//Убрать из корзины
	$('.blDel .ajaxLink').click(function(){
		var obj = $(this);
		$.ajax({
			type: 'post',
			url: '/Basket/Del',
			data: {
				'id': $(this).attr('id').substr(3)
			},
			success: function(reply){
				if(!reply.result) {
					alert("Извитите, что-то пошло не так :(");
				} else {
					if(reply.empty) {
						document.location = '';
						return false;
					}
					$('#BasketBlockWrap').html(reply.block);
					obj.parent().parent().remove();
					$('#dSTCount').html(reply.totals.count);
					$('#dSTPrice').html(reply.totals.summ);
				}
			}
		});
		
		return false;
	});
	
	//Варианты оплаты
	$('#PayMethodSelectVariants .ajaxLink').click(function(){
		var radio = $('#' + $(this).parent().attr('for'));
		$('#PayMethodSelectText div').hide();
		$(this).parent().parent().parent().find('.Active').removeClass('Active');
		$(this).parent().parent().addClass('Active');
		$($(this).attr('href')).show();
		radio.attr('checked', true);
		return false;
	});

	//Оформление заказа
	$('#OrderButton').click(function(){
		if($('#Ophone').val().trim() == '') {
			alert('Заполните ваш контактный телефон');
			$('#Ophone').focus();
			return false;
		}
		if($('#Omail').val().trim() != '' && !$('#Omail').val().trim().match(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i)) {
			alert('Почта заполнена неверно');
			$('#Omail').focus();
			return false;
		}
		
		$(this).css('visibility', 'hidden');
	});


	if(typeof(hideTraps) != 'undefined') hideTraps();

});
