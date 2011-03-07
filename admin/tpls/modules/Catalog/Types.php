<?php
$typetype = array('text'=>'Текстовый','float'=>'Числовой','yn'=>'Есть/Нет/Не задано','select'=>'Селект','range'=>'Диапазон');
?>
<div id="productTypes">
<style type="text/css">
.typesGroups { overflow: hidden; padding:5px; }
.typesGroup { float: left; width:300px; margin:1em 1em 0 0; padding: 0.3em 0 1em; font-size:0.9em; border:1px solid white; }
.typesGroup input, .typesGroup select { width: 95% !important; margin-bottom: 0.5em; }
.typesGroup textarea { width: 95%; height: 50px !important; }
.typesGroup:hover { border-color: #ddd; -webkit-box-shadow: 0px 0px 5px #bdbdbd;-moz-box-shadow: 0px 0px 5px #bdbdbd;box-shadow: 0px 0px 5px #bdbdbd;  }
.typesGroupTitle { font-size: 1.5em; color:#555; margin:0 0 0.5em .3em; }
.types { padding:0 0 0 0; margin:0 0 0.5em 0; }
.types li { padding:.1em 0 0 1.2em; margin:0 0 0 0; background:url("/admin/images/icons/arrow-000-small.png") no-repeat left top; display:block; }
.types li:hover { background-color: #eee; }
.type {  }

.typeYN { padding-bottom:.5em; }
.typeYN input { display:inline; width:auto !important; margin:0.2em 0 0 0; padding:0; vertical-align:middle; }
.typeYN label { display:inline !important; margin:0.2em 0 0 0; vertical-align:middle; }

.typeRange input { width:8em !important; }
.typeRange label { display:inline !important; }
.message { padding:4px; margin:0 30px 6px 30px; font-size:16px; }
#goToSpace { padding:2px 0 5px 10px; }
#goToSpaceLink { padding:3px 0 5px 24px; background: url(/admin/images/icons/rocket-fly.png) no-repeat left center; }
#goToSpaceLoading { padding:3px 0 5px 24px; background: url(/admin/images/loader20x20.gif) no-repeat left center; }
</style>

<div class="message"><?php echo $message?></div>

<div id="goToSpace">
	<a href="<?php echo $link?>&parse" id="goToSpaceLink">Попробовать получить данные из космоса</a>
</div>

<form action="<?php echo $link?>&add" method="post" class="typesGroups">
<input type="hidden" name="id" value="<?php echo $id?>">
<?php foreach ($types_groups as $gid=>$g) { ?>
	<div class="typesGroup">
		<div class="typesGroupTitle">
			<?php echo $g['name']?>
		</div>
		
		<ul class="types">
		<?php foreach($g['types'] as $tid=>$t) {?>
			<li>
				<div class="typeName" style="<?php echo $t['main']?'font-weight:bold;':''?>">
					<label for="type<?php echo $gid?>_<?php echo $tid?>"><?php echo $t['name']?><?php echo isset($t['unit'])&&!empty($t['unit'])?', '.$t['unit']:''?> (<?php echo $typetype[$t['type']]?>)</label>
				</div>
			<?php switch ($t['type']) {
				case 'text':
				?>
				<div class="type typeText">
					<input type="text" name="type[<?php echo $gid?>][<?php echo $tid?>]" value="<?php echo isset($types[$gid][$tid])?$types[$gid][$tid]:''?>" id="type<?php echo $gid?>_<?php echo $tid?>">
				</div>
				<?php
				break;
				
				case 'float':
				?>
				<div class="type typeFloat">
					<input type="text" name="type[<?php echo $gid?>][<?php echo $tid?>]" value="<?php echo isset($types[$gid][$tid])?$types[$gid][$tid]:''?>" id="type<?php echo $gid?>_<?php echo $tid?>">
				</div>
				<?php
				break;
				
				case 'yn':
				?>
				<div class="type typeYN">
					<input type="radio" name="type[<?php echo $gid?>][<?php echo $tid?>]" value="Y" <?php echo (isset($types[$gid][$tid])&&$types[$gid][$tid]=='Y')?'checked="checked"':''?> id="type<?php echo $gid?>_<?php echo $tid?>_Y"> <label for="type<?php echo $gid?>_<?php echo $tid?>_Y">Есть</label>
					<input type="radio" name="type[<?php echo $gid?>][<?php echo $tid?>]" value="N" <?php echo (isset($types[$gid][$tid])&&$types[$gid][$tid]=='N')?'checked="checked"':''?> id="type<?php echo $gid?>_<?php echo $tid?>_N"> <label for="type<?php echo $gid?>_<?php echo $tid?>_N">Нет</label>
					<input type="radio" name="type[<?php echo $gid?>][<?php echo $tid?>]" value="" <?php echo (!isset($types[$gid][$tid])||$types[$gid][$tid]=='')?'checked="checked"':''?> id="type<?php echo $gid?>_<?php echo $tid?>_U"> <label for="type<?php echo $gid?>_<?php echo $tid?>_U">Не задано</label>
				</div>
				<?php
				break;
				
				case 'select':
				?>
				<div class="type typeSelect">
					<select name="type[<?php echo $gid?>][<?php echo $tid?>]" id="type<?php echo $gid?>_<?php echo $tid?>">
						<option value="">Не задано</option>
					<?php foreach ($t['select'] as $selK=>$selV) {
						if(isset($types[$gid][$tid]) && $types[$gid][$tid] == $selK) $selected = 'selected="selected"';
						else $selected = '';
					?>
						<option value="<?php echo $selK?>" <?php echo $selected?>><?php echo $selV?></option>
					<?php }?>
					</select>
				</div>
				<?php
				break;
				
				case 'range':
				?>
				<div class="type typeRange">
					<input name="type[<?php echo $gid?>][<?php echo $tid?>][from]" value="<?php echo isset($types[$gid][$tid]['from'])?$types[$gid][$tid]['from']:''?>" id="type<?php echo $gid?>_<?php echo $tid?>">
					<label for="type<?php echo $gid?>_<?php echo $tid?>_to">→</label>
					<input name="type[<?php echo $gid?>][<?php echo $tid?>][to]" value="<?php echo isset($types[$gid][$tid]['to'])?$types[$gid][$tid]['to']:''?>" id="type<?php echo $gid?>_<?php echo $tid?>_to">
				</div>
				<?php
				break;
			}?>
				
			</li>
		<?php }?>
		</ul>
	</div>
<?php }?>
</form>

<script type="text/javascript">

var id = <?php echo $id?>;

//Ошибка ввода float'а
$('.typeFloat input, .typeRange input').keyup(function(){
	if($(this).val().match(/^[\-+]?[0-9]*\.?[0-9]+\b$/i) || $(this).val() == '') {
		$(this)
			.css({
				'background-image': 'none'
			})
			.attr('error','0');
	} else {
		$(this)
			.css({
				'background-image': 'url(/admin/images/icons/minus-octagon.png)',
				'background-position': 'right center',
				'background-repeat': 'no-repeat'
			})
			.attr('error','1');
	}
});

$('#goToSpaceLink').click(function(){
	var obj = $(this);
	obj.parent().html('<span id="goToSpaceLoading">Запрос стартовал, ждите возвращения!</span>');
	$.ajax({
		url: obj.attr('href'),
		type: 'POST',
		data: {"id": id},
		success: function(html) {
			$('#productTypes').html(html);
		}
	});
	return false;
});

var msgBar = $('.message');
if(msgBar.html() != '') msgBar.effect("highlight", {}, 3000);

</script>

</div>