// vim:ts=4:sw=4:noet:nowrap:sta
$(function(){
	var errlist = {
		'not_authorized' : 'Вы не авторизованы'
	};
	var process_block = function(reply) {
		var obj;
		try {
			obj = $.parseJSON(reply);
		} catch (err) {
			// not json, we don't want that anyway. ' + cnt + ' комментариев. <div class="delete">Удалить</div>')
		}
		if (obj) {
			if (obj.error) {
				var str = 'Произошла неведомая ошибка: '+obj.error;
				if (errlist[obj.error])
					str = 'Произошла ошибка: '+errlist[obj.error];
				alert(str);
			}
			return;
		}
		$('div#comments_outer').replaceWith(reply);
	};

	$('div.comment_add a.cmt_add').attr('disabled', false);
	$('div.comment_add a.cmt_add').click(function(e){
		e.preventDefault();
		var box = $(this).parent();
		var data = {
			author: box.find('input[name="author"]').val() || '',
			email: box.find('input[name="email"]').val() || '',
			hash: box.find('input[name="hash"]').val() || '',
			parent_id: box.find('input[name="parent_id"]').val() || '',
			text: box.find('textarea[name="text"]').val() || '',
			lib_comments: true,
			action: 'add'
		};
		var e = box.find('input[name="author"]');
		if (!data.author || !data.text) {
			return;
		}
		$(this).attr('disabled',true);
		$.ajax({
			cache: false,
			type: 'POST',
			url: '/',
			'data': data,
			dataType: 'html',
			success:process_block
		});
	});
	$('div.block_comments div.comment').each(function(i,e){
		var parent_id = $(e).attr('rel');
		var btn = $('<a href="#" class="cmt_answer">Ответить</a>').appendTo(e);
		btn.click(function(e){
			var box = $('div.comment_add:last').clone(true).attr('id', '').insertAfter(this);
			box.fadeIn('fast');
			box.find('label').each(function(i,e){
				var suff = Math.round(Math.random()*10000);
				var id = $(e).attr('for');
				box.find('#'+id).attr('id', id+suff);
				$(e).attr('for', id+suff);
			});
			box.find('input[name="parent_id"]').val(parent_id);
			$(this).remove();
			e.preventDefault();
		});
	});
	$('#comment_root').click(function(event){
		$(this).hide();
		event.preventDefault();
		$('#cmt_boxroot').fadeIn('fast');
	});
	if (typeof(g_bGodmode) == 'undefined' || typeof(g_bGodmodeSuspended) == 'undefined')
		return;
	if (!g_bGodmode || g_bGodmodeSuspended)
		return;
	if ($('#comments_outer input[name="is_admin"]').val()) {
		$('div.comment').each(function(i,e){
			var btn = $('<div class="btnDel" title="Удалить" />').prependTo(e);
			btn.hover(
				function(){
					btn.parents('div.comment:first').addClass('commentDelHover');
				},
				function(){
					btn.parents('div.comment:first').removeClass('commentDelHover');
				}
			);
			btn.click(function(){
				var data = {
					hash: $('#comments_outer input[name="hash"]').val() || '',
					comment_id: btn.parents('div.comment:first').attr('rel') || '',
					lib_comments: true,
					action: 'del'
				};
				$.ajax({
					cache: false,
					type: 'POST',
					url: '/',
					'data': data,
					dataType: 'html',
					success:process_block
				});
			});
		});

		$('#cmtMegaButton, #cmtSelect, #cmtTooltip').remove();
		var megaButton = $('<div id="cmtMegaButton" />').appendTo(document.body).attr('cClickable', true);
		var megaDiv = $('<div id="cmtSelect" />').appendTo(document.body);
		var megaTip = $('<div id="cmtTooltip" />').appendTo(document.body).attr('cClickable', true);
		var megaHandler = function(event) {
			if (event.type == 'mousedown' && ($(event.target).attr('cClickable') || $(event.target).parents('[cClickable="true"]').length) ) {
				return true;
			}
			event.preventDefault();
			if (event.type == 'mousedown' && event.button == 0) {
				megaDiv.data('moved', false);
				megaDiv.data('startx', event.pageX);
				megaDiv.data('starty', event.pageY);
				megaDiv.data('started', true);
				megaDiv.css({left:event.pageX, top:event.pageY,width:0,height:0}).show();
				if (!event.ctrlKey)
					$('div.comment').attr({overlapped:false,marked:false});
			}
			if (event.type == 'mousemove' && megaDiv.data('started')) {
				if (event.pageX != megaDiv.data('startx') || event.pageY != megaDiv.data('starty'))
					megaDiv.data('moved', true);
				if (event.pageX >= megaDiv.data('startx')) {
					megaDiv.css({width:event.pageX - megaDiv.data('startx')});
				} else {
					megaDiv.css({left:event.pageX,width:megaDiv.data('startx')-event.pageX});
				}
				if (event.pageY >= megaDiv.data('starty')) {
					megaDiv.css({height:event.pageY - megaDiv.data('starty')});
				} else {
					megaDiv.css({top:event.pageY,height:megaDiv.data('starty')-event.pageY});
				}

				var box = {
					top: megaDiv.offset().top,
					left: megaDiv.offset().left,
					right: megaDiv.offset().left + megaDiv.width(),
					bottom: megaDiv.offset().top + megaDiv.height()
				};

				$('div.comment').each(function(i,e){
					var ebox = {
						top: $(e).offset().top,
						left: $(e).offset().left,
						right: $(e).offset().left + $(e).width(),
						bottom: $(e).offset().top + $(e).height()
					};
					if ((ebox.top > box.top || ebox.top < box.bottom && ebox.bottom > box.top)
						&& (ebox.left > box.left || ebox.left < box.right && ebox.right > box.left)
						&& (ebox.right < box.right || ebox.right > box.left && ebox.left < box.right)
						&& ebox.bottom <= box.bottom) {
						$(e).attr('overlapped', true);
					} else {
						$(e).attr('overlapped', false);
					}
				});
			}
			if (event.type == 'mouseup' && megaDiv.data('started')) {
				if (!megaDiv.data('moved')) {
					$('div.comment:not(.comment-deleted)').each(function(i,e){
						if (event.pageX > $(e).offset().left && event.pageY > $(e).offset().top && event.pageX < $(e).offset().left+$(e).width() && event.pageY < $(e).offset().top+$(e).height()) {
							$(e).attr('overlapped', true);
						}
					});
				}
				$('div.comment[overlapped="true"]').attr('overlapped', false).filter(':not(.comment-deleted)').attr('marked',true);
				if ($('div.comment[marked="true"]').length) {
					var $count = $('div.comment[marked="true"]').length;
					var ctext = $count%10==1 && $count%100!=11 ? 'комментарий' : ($count%10 >=2 && $count%10 <= 4 && ($count%100 < 10 || $count%100 >= 20) ? 'комментария' : 'комментариев');
					var seltext = $count%10==1 && $count%100!=11 ? 'Выбран' : ($count%10 >=2 && $count%10 <= 4 && ($count%100 < 10 || $count%100 >= 20) ? 'Выбрано' : 'Выбрано');
					megaTip.html(seltext+' ' + $count + ' ' + ctext + '. <span class="delete">Удалить</div>');
					megaTip.animate({opacity:0.5}).animate({opacity:0.8});
				} else {
					megaTip.text('Вы не выбрали ни одного комментария');
					megaTip.animate({opacity:0.5}).animate({opacity:0.8});
				}
				megaDiv.data('started', false).hide();
			}
		};
		var megaKeyHandler = function(event) {
			if (event.type == 'keyup' && event.keyCode == 27) {
				megaButton.trigger('c.reset');
			}
		};
		megaButton.click(function(){
			if (!$(this).data('enabled')) {
				$(document.body).css('cursor', 'crosshair');
				$(document.body).bind('mousedown mousemove mouseup', megaHandler);
				$(window).bind('keyup', megaKeyHandler);
				$(this).data('enabled', true);
				megaTip.text('Выделите область с комментариями, которые вы хотите удалить. Если вы хотите отменить удаление комментариев, нажмите на кнопку с модерированием, либо нажмите Esc на клавиатуре').fadeIn();
				$(this).animate({'padding-left':30}).animate({'padding-left':20});
			} else {
				$(this).trigger('c.reset');
			}
		});
		megaButton.bind('c.reset', function(){
			$(document.body).css('cursor', 'auto');
			$(document.body).unbind('mousedown mousemove mouseup', megaHandler);
			$(window).unbind('keyup', megaKeyHandler);
			megaDiv.hide();
			$('div.comment[overlapped="true"], div.comment[marked="true"]').attr({overlapped:false,marked:false});
			$(this).data('enabled', false);
			megaTip.fadeOut();
			$(this).animate({'padding-left':30}).animate({'padding-left':0});
		});
		megaTip.click(function(event){
			if ($(event.target).is('.delete')) {
				var ids = [ ];
				$('div.comment[marked="true"]').each(function(i,e){
					ids.push($(e).attr('rel'));
				});
				var $count = ids.length;
				var ctext = $count%10==1 && $count%100!=11 ? 'комментария' : ($count%10 >=2 && $count%10 <= 4 && ($count%100 < 10 || $count%100 >= 20) ? 'комментариев' : 'комментариев');
				if (confirm('Подтвердите удаление '+$count+' '+ctext)) {
					megaButton.trigger('c.reset');
					var data = {
						hash: $('#comments_outer input[name="hash"]').val() || '',
						comment_ids: ids,
						lib_comments: true,
						action: 'mass_prune'
					};
					$.ajax({
						cache: false,
						type: 'POST',
						url: '/',
						'data': data,
						dataType: 'html',
						success:process_block
					});
				}
			}
		});
	}
});
