<div id="items">
	<? foreach ($rows as $item){?>
	<div id="item_block">
		<div id="item">
			<div id="item_content_small" onclick="document.location.href='<?=$this->config->site_url()?>web/show/<?=$item['content_id']; ?>'" style="cursor:pointer;">
				<?=$item['text']; ?>
			</div>
			<span id="item_price"><?=number_format(@$item['price'], 2, ',', '');?> Euro / <?=number_format(@$item['price'] * 0.702804, 2, ',', '');?> Ls</span>
			<center><div id="buy_button"><a href="<?=$this->config->site_url()?>web/show/<?=$item['content_id']; ?>">{item_buy}</a></div></center>
		</div>
	</div>	
	<? } ?>
</div>