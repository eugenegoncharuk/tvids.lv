<table border="1" cellspacing="4" cellpadding="0" width="80%">
<tr>
	<td width="30">&nbsp;</td>
	<td>{item_name}</td>
	<td align="right" width="50">{item_count}</td>
	<td align="right" width="50">{item_price}</td>
	<td align="right" width="50">{item_sum}</td>
</tr>

<? foreach($cart_items as $item) { ?>
<tr>
	<td align="left">
		<?=$item['pos']?>.
	</td>	
	<td align="left">
		<a href="<?=$this->config->site_url()?>web/show/<?=$item['node_id']?>">
			<?=$item['text']?>
		</a>						
	</td>
	<td align="right"><?=$item['count']?></td>
	<td align="right"><?=number_format(@$item['price'], 2, '.', '');?></td>
	<td align="right"><?=number_format(@$item['sum'], 2, '.', '');?></td>
</tr>	
<? } ?>

<tr>
	<td align="right" colspan="5">
	<br>
		{sum_all}:  <?=number_format(round(@$summary/121*100, 2), 2, '.', '');?> Euro
	<br>
		PVN:   <?=number_format(round(@$summary/121*21, 2), 2, '.', '');?> Euro
	<br>
		{sum_all} + PVN:   <?=number_format(@$summary, 2, '.', '');?> Euro
	</td>
</tr>	
</table>