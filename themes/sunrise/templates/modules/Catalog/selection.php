<div id="SelectBlock">
	<div id="selectMenu">
		<h2>Подбор</h2>
		<form action="" method="get">
		<input type="hidden" name="addGet" value="<?php echo $link?>" />
		
	<?php foreach ($types as $type) {
		switch ($type['type']) {
			
			
			case 'float': case 'range':?>
			<!-- Число -->
			<div class="Type">
			<?php if(!empty($type['desc'])) {?>
				<div class="TypeHint">
					<div class="TypeHintCorner"></div>
					<div class="TypeHintName"><?php echo $type['name']?></div>
					<div class="TypeHintText"><?php echo $type['desc']?></div>
					<div class="TypeHintClose"><span class="ajaxLink">Закрыть</span></div>
				</div>
			<?php }?>
				<div class="TypeName">
					<span>
						<label for="type<?php echo $type['groupKey']?>_<?php echo $type['typeKey']?>"><?php echo $type['name']?><?php echo empty($type['unit'])?'':', '.$type['unit']?></label>
						<?php if(!empty($type['desc'])) {?><span class="WhatIsThat7"></span><?php }?>
					</span>
				</div>
				<div class="Type_float">
					От
					<input
						name="select[<?php echo $type['groupKey']?>][<?php echo $type['typeKey']?>][from]"
						id="type<?php echo $type['groupKey']?>_<?php echo $type['typeKey']?>"
						value="<?php echo isset($type['val']['from'])?$type['val']['from']:''?>"
					/>
					до
					<input
						name="select[<?php echo $type['groupKey']?>][<?php echo $type['typeKey']?>][to]"
						value="<?php echo isset($type['val']['to'])?$type['val']['to']:''?>"
					/>
				</div>
			</div>
			<?php break;
			
			
			case 'yn':?>
			<!-- Да/Нет -->
			<div class="Type">
			<?php if(!empty($type['desc'])) {?>
				<div class="TypeHint">
					<div class="TypeHintCorner"></div>
					<div class="TypeHintName"><?php echo $type['name']?></div>
					<div class="TypeHintText"><?php echo $type['desc']?></div>
					<div class="TypeHintClose"><span class="ajaxLink">Закрыть</span></div>
				</div>
			<?php }?>
				<div class="Type_yn">
					<input
						type="checkbox"
						name="select[<?php echo $type['groupKey']?>][<?php echo $type['typeKey']?>]"
						value="Y"
						id="type<?php echo $type['groupKey']?>_<?php echo $type['typeKey']?>"
						<?php echo isset($type['val'])&&$type['val']=='Y'?'checked="checked"':''?>
					/>
					<label for="type<?php echo $type['groupKey']?>_<?php echo $type['typeKey']?>"><?php echo $type['name']?> <?php if(!empty($type['desc'])) {?><span class="WhatIsThat7"></span><?php }?></label>
				</div>
			</div>
			<?php break;
			
			
			case 'select':?>
			<!-- Селект -->
			<div class="Type">
			<?php if(!empty($type['desc'])) {?>
				<div class="TypeHint">
					<div class="TypeHintCorner"></div>
					<div class="TypeHintName"><?php echo $type['name']?></div>
					<div class="TypeHintText"><?php echo $type['desc']?></div>
					<div class="TypeHintClose"><span class="ajaxLink">Закрыть</span></div>
				</div>
			<?php }?>
				<div class="TypeName"><label for="type<?php echo $type['groupKey']?>_<?php echo $type['typeKey']?>"><?php echo $type['name']?> <?php if(!empty($type['desc'])) {?><span class="WhatIsThat7"></span><?php }?></label></div>
				<div class="Type_select">
					<select name="select[<?php echo $type['groupKey']?>][<?php echo $type['typeKey']?>]" id="type<?php echo $type['groupKey']?>_<?php echo $type['typeKey']?>">
						<option value="">—</option>
					<?php foreach ($type['select'] as $k=>$v) {
						if(isset($type['val']) && $type['val'] == $k) $selected = 'selected="selected"';
						else $selected = '';
					?>
						<option value="<?php echo $k?>" <?php echo $selected?>><?php echo $v?></option>
					<?php }?>
					</select>
				</div>
			</div>
			<?php break;
		}
	}?>
	
			<!--<div id="selectShowAll"><a class="ajaxLink" href="">Все параметры</a></div>-->
			
			<div>
				<button id="selectShow">Показать</button>
			<?php if(isset($_GET['select'])) {?>
				<a href="<?php echo $cleanLink?>" id="selectClear" class="ajaxLink">Отменить фильтр</a>
			<?php }?>
			</div>
		</form>
	</div>
</div>