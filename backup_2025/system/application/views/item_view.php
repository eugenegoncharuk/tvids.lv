<center>
	<div id="item_content">
		<?=$text ?>
	</div>
</center>	
<center>
	<div id="item_price">
		<span><?=number_format(@$price, 2, ',', '');?> Euro / <?=number_format(@$price * 0.702804, 2, ',', '');?> Ls</span>
		<div id="buy_button"><a href="<?=$this->config->site_url()?>web/buy/<?=$content_id ?>">{item_buy}</a></div>
	</div>
</center>
