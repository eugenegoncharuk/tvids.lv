<div id="items">
	<? foreach ($rows as $item){?>
	<?php $item_url = isset($item['view_url']) ? $item['view_url'] : ($this->config->site_url().'web/show/'.$item['content_id']); ?>
	<div id="item_block">
		<div id="item">
			<div id="item_content_small" onclick="document.location.href='<?=$item_url; ?>'" style="cursor:pointer;">
				<?=$item['text']; ?>
			</div>
			<?php if (isset($item['pattern_name'])){ ?>
			<div id="item_name"><?=$item['pattern_name']; ?></div>
			<?php } ?>
			<span id="item_price"><?=number_format(@$item['price'], 2, ',', '');?> Euro</span>
			<center><div id="buy_button"><a href="<?=$item_url; ?>">{item_buy}</a></div></center>
		</div>
	</div>
	<? } ?>
</div>