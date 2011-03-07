$(function(){
    if (!g_bGodmode)
        return;
    var bGodmode = $('<div id="btnGodmode" />').appendTo(document.body);

    if (g_bGodmodeSuspended)
        bGodmode.addClass('suspended');

    bGodmode.click(function(event){
        $(this)[g_bGodmodeSuspended?'removeClass':'addClass']('suspended');
        $.post('/Content/Godmode',{suspend:(g_bGodmodeSuspended?0:1)},function(response){
            window.location.reload();
        });
    });

});

$(function(){
    if (!g_bGodmode || g_bGodmodeSuspended)
        return;
    $('.jProductEditPlaceholder').each(function(){
        $(this).append('<a target="_blank" href="/admin/?module=Catalog&amp;method=Info&amp;top='+$(this).attr('topic_id')+'#open'+$(this).attr('product_id')+'"><img src="/admin/images/icons/pencil.png" /></a>');
    });
});
