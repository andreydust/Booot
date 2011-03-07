<script type="text/javascript" src="/admin/js/jquery.tree.js"></script>
<script type="text/javascript">

var listLink = '<?php echo $listLink?>';
$(function(){

	
	
	//Добавить корневой раздел
	$('#addButton').button({
		icons: {
        	primary: 'ui-icon-plus'
    	}
    }).click(function(){
    	$.tree.focused().create(false, -1);
    });

    //Добавить подраздел
	$('#addSubButton').button({
		icons: {
        	primary: 'ui-icon-plus'
    	}
	}).click(function(){
		var id = $.tree.focused().selected.find("a").attr("id").substr(1);
		var t = $.tree.focused();
		if(t.selected) t.create();
		else alert("Select a node first");
	});
	$('#addSubButton').button('disable');

	//Редактировать
	$('#editButton').button({
		icons: {
        	primary: 'ui-icon-pencil'
    	}
	}).click(function(){
		var id = $.tree.focused().selected.find("a").attr("id").substr(1);
		currentEditId = id;
		jEditWindow(id);
	});
	$('#editButton').button('disable');

	//Перейти к товарам
	$('#listButton').button({
		icons: {
        	primary: 'ui-icon-folder-open'
    	}
	}).click(function(){
		var id = $.tree.focused().selected.find("a").attr("id").substr(1);
		var link = listLink.replace('{id}', id);
		document.location = link;
		//jEditWindow(id);
	});
	$('#listButton').button('disable');
	
	//Удалить
	$('#delButton').button({
		icons: {
        	primary: 'ui-icon-close'
    	}
    }).click(function(){
    	$.tree.focused().remove();
    });
	$('#delButton').button('disable');
    

	$.extend($.tree.plugins, {
		"cookie" : {
			defaults : {
				prefix		: "",	// a prefix that will be used for all cookies for this tree
				options		: {
					expires: false,
					path: false,
					domain: false,
					secure: false
				},
				types : {
					selected	: true,		// should we set the selected cookie
					open		: true		// should we set the open cookie
				},
				keep_selected	: false,	// should we merge with the selected option or overwrite it
				keep_opened		: false		// should we merge with the opened option or overwrite it
			},
			set_cookie : function (type) {
				var opts = $.extend(true, {}, $.tree.plugins.cookie.defaults, this.settings.plugins.cookie);
				if(opts.types[type] !== true) return false;
				switch(type) {
					case "selected":
						if(this.settings.rules.multiple != false && this.selected_arr.length > 1) {
							var val = Array();
							$.each(this.selected_arr, function () {
								if(this.find('a').attr("id")) { val.push(this.find('a').attr("id")); }
							});
							val = val.join(",");
						}
						else var val = this.selected.find('a') ? this.selected.find('a').attr("id") : false;
						$.cookie(opts.prefix + 'selected', val, opts.options);
						break;
					case "open":
						var str = "";
						this.container.find("li.open").each(function (i) { if($(this).find('a').attr('id')) { str += $(this).find('a').attr('id') + ","; } });
						$.cookie(opts.prefix + 'open', str.replace(/,$/ig,""), opts.options);
						break;
				}
			},
			callbacks : {
				oninit : function (t) {
					var opts = $.extend(true, {}, $.tree.plugins.cookie.defaults, this.settings.plugins.cookie);
					var tmp = false;
					tmp = $.cookie(opts.prefix + 'open');
					if(tmp) {
						tmp = tmp.split(",");
						if(opts.keep_opened)	this.settings.opened = $.unique($.merge(tmp, this.settings.opened));
						else					this.settings.opened = tmp;
					}
					tmp = $.cookie(opts.prefix + 'selected');
					if(tmp) {
						tmp = tmp.split(",");
						if(opts.keep_selected)	this.settings.selected = $.unique($.merge(tmp, this.settings.opened));
						else					this.settings.selected = tmp;
					}
				},
				onchange	: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["selected"]); },
				onopen		: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["open"]); },
				onclose		: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["open"]); },
				ondelete	: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["open"]); },
				oncopy		: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["open"]); },
				oncreate	: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["open"]); },
				onmoved		: function() { $.tree.plugins.cookie.set_cookie.apply(this, ["open"]); }
			}
		}
	});

	 
	$("#datatree_<?php echo $table?>").tree({
		plugins : {
		cookie : { prefix : "jstree_<?php echo $table?>_" }
		},
		ui : {
			theme_path: "/admin/css/jsTree/default/style.css",
			theme_name : "default"
		},
		callback : {
			ondblclk : function(node, treeObj) {
				if($(node).hasClass('leaf')) {
					var id = $(node).find("a").attr("id").substr(1);
					var link = listLink.replace('{id}', id);
					document.location = link;
				}
				treeObj.toggle_branch.call(treeObj, node);
				treeObj.select_branch.call(treeObj, node);
			},
			onselect : function (node) {
				$('#editButton,#delButton,#addSubButton,#listButton').button('enable');
			},
			ondeselect : function (node) {
				$('#editButton,#delButton,#addSubButton,#listButton').button('disable');
			},
			oncreate : function(node, ref_node, type, TREE_OBJ, RB) {
				var name = TREE_OBJ.get_text(node);
				if($(ref_node).find("a").length!=0) {
					var ref_id = $(ref_node).find("a").attr("id").substr(1);
				} else {
					var ref_id = 0;
				}
				var data  = {"name": name, "ref_id": ref_id, "type": type};
				
				$.ajax({
					"url":		"<?php echo $link?>&create",
					"type":		"POST",
					"data":		data,
					"success":	function(result) {
						$(node).find("a").attr("id", "t" + result);
					}
				});
			},
			ondelete : function(node, TREE_OBJ, RB) {
				var id = $(node).find("a").attr("id").substr(1);
				var data  = {"id": id};

				$("#dialog-confirm").dialog({
					resizable: false,
					height:160,
					modal: true,
					buttons: {
						'Отмена': function() {
							$(this).dialog('close');
							$.tree.rollback(RB);
						},
						'Да': function() {
							$.ajax({
								"url":		"<?php echo $link?>&delete",
								"type":		"POST",
								"data":		data,
								"success":	function(result) {
									result;
								}
							});
							$(this).dialog('close');
						}
						
					}
				});
				return false;
			},
			onrename : function(node, TREE_OBJ, RB) {
				var id = $(node).find("a").attr("id").substr(1);
				var name = TREE_OBJ.get_text(node);
				var data  = {"id": id, "name": name};
				
				$.ajax({
					"url":		"<?php echo $link?>&rename",
					"type":		"POST",
					"data":		data,
					"success":	function(result) {
						result;
					}
				});
			},
			onmove : function (node, ref_node, type, TREE_OBJ, RB) {
				var id = $(node).find("a").attr("id").substr(1);
				var ref_id = $(ref_node).find("a").attr("id").substr(1);
				var data  = {"id": id, "ref_id": ref_id, "type": type};

				$.ajax({
					"url":		"<?php echo $link?>&move",
					"type":		"POST",
					"data":		data,
					"success":	function(result) {
						result;
					}
				});
			}
			
		},
		data : {
			type : "json",
			opts : {
				url : "<?php echo $link?>&json"
			}
		}
	});
	
	
});


</script>

<div class="controlPanel">
	<button id="addButton">Добавить корневую категорию</button>
	<button id="addSubButton">Добавить подкатегорию</button>
	<button id="editButton">Редактировать свойства</button>
	<button id="listButton">Перейти к товарам</button>
	<button id="delButton">Удалить</button>
</div>
<div id="datatree_<?php echo $table?>" class="datatree"></div>

<div id="dialog-confirm" style="display:none;font-size: 12px" title="Удалить категорию?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Вы точно хотите удалить категорию?</p>
</div>
