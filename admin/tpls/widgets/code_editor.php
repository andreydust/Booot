<style type="text/css">
#err_info { text-align: right; margin: 1.2em .3em 0 0; }
.controlPanel { margin-bottom: 1.2em; }
</style>
<script type="text/javascript" src="/admin/js/jquery.tree.js"></script>
<script type="text/javascript" src="/admin/js/CodeMirror/codemirror.js"></script>
<script type="text/javascript">
$(function(){
    var make_file_edit = function(file){
        $('#edit_source_wrap').empty().append('<textarea />');
        $('#err_info').empty();
        var ta = $('#edit_source_wrap > textarea')[0];
        $.ajax({
            url: '?module=System&method=json_editbox&f='+file,
            dataType: 'text',
            success: function(reply){
                $(ta).text(reply);
                $.getJSON('?module=System&method=json_editbox_options&f='+file, function(data){
                    var editor = CodeMirror.fromTextArea(ta, {
                        height: '600px',
                        parserfile: data.parser_files,
                        stylesheet: data.style_sheets,
                        path: "/admin/js/CodeMirror/"
                    });
                    if (data.can_edit) {
                        $('#err_info').html('<button id="btn_save_tpl">Сохранить</button>');
                        $('#btn_save_tpl').button();
                        $('#btn_save_tpl').click(function(){
                            var content = editor.getCode();
                            $.post('?module=System&method=file_submit&file='+file, { text : content }, function(reply){
                            });
                        });
                    } else {
                        $('#err_info').html('<div class="error">Невозможно редактировать файл "'+file+'"</div>');
                    }
                });
            }
        });
    };
    $('#tpledit_filetree').tree({
        ui : {
            theme_path : '/admin/css/jsTree/default/style.css',
            theme_name : 'default'
        },
        callback : {
            onselect : function(node) {
                if ($(node).hasClass('leaf')) {
                    var f = node.getAttribute('rel');
                    if (typeof(f) == 'undefined')
                        return;
                    make_file_edit(f);
                }
                return false;
            }
        }
    });

	//Назад
    var backLink = '<?php echo $modulelink?>';
	$('#backButton').button({
		icons: {
        	primary: 'ui-icon-arrowthick-1-w'
    	}
    }).click(function(){
		document.location = backLink;
	});
});
</script>

<div class="controlPanel">
	<button id="backButton">Назад</button>
</div>
	
<div id="edit_source_wrap"></div>
<div id="err_info"></div>