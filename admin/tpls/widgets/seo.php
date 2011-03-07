<style type="text/css">
	#seoForm textarea { height:50px; }
	.message { padding:4px; margin:0 30px 6px -4px; font-size:16px; }
</style>

<div class="message"><?php echo $message?></div>

<form action="<?php echo $link?>" method="post" id="seoForm">
	<div>
		<label for="seoTitle">Title</label>
		<textarea name="title" id="seoTitle"><?php echo $title?></textarea>
		
		<label for="seoKeywords">Keywords</label>
		<textarea name="keywords" id="seoKeywords"><?php echo $keywords?></textarea>
		
		<label for="seoDescription">Description</label>
		<textarea name="description" id="seoDescription"><?php echo $description?></textarea>
	</div>
</form>

<script type="text/javascript">
var msgBar = $('.message');
if(msgBar.html() != '') msgBar.effect("highlight", {}, 3000);
</script>