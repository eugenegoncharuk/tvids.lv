<div id="cart_table">
<center>
{your_cart}
<br>
<br>
<table id="tablestyle" border="0" cellspacing="4" cellpadding="0" width="80%">
<tr id="tableheadstyle">
	<td width="30">&nbsp;</td>
	<td><b>{item_name}</b></td>
	<td align="right" width="50"><b>{item_count}</b></td>
	<td width="45">&nbsp;</td>
	<td align="right" width="60"><b>{item_price}</b></td>
	<td align="right" width="50"><b>{item_sum}</b></td>
	<td align="right" width="20">&nbsp;</td>
</tr>

<? foreach($cart_items as $item) { ?>
<tr valign="bottom">
	<td align="left">
		<?=$item['pos']?>.
	</td>	
	<td align="left">
		<a href="<?=isset($item['view_url']) ? $item['view_url'] : ($this->config->site_url().'web/show/'.$item['node_id']) ?>">
			<?=$item['text']?>
		</a>
	</td>
	<td align="right">
		<?=$item['count']?>
	</td>
	<td align="right">
		<a id="plus_button" title="{plus_count_title}" href="<?=$this->config->site_url()?>web/add_item/<?=$item['node_id']?>"></a>
		<a id="minus_button" title="{minus_count_title}" href="<?=$this->config->site_url()?>web/delete_item/<?=$item['node_id']?>"></a>
	</td>
	<td align="right"><?=number_format(@$item['price'], 2, '.', '');?></td>
	<td align="right"><?=number_format(@$item['sum'], 2, '.', '');?></td>
	<td align="right"><a id="del_button" title="{delete_row_title}" href="<?=$this->config->site_url()?>web/delete_all_items/<?=$item['node_id']?>"></a></td>
</tr>	
<? } ?>
<tr>
	<td align="right" colspan="6">
	<br>
		{sum_all}:  <?=number_format(round(@$summary/121*100, 2), 2, '.', '');?> Euro
	<br>
		PVN:   <?=number_format(round(@$summary/121*21, 2), 2, '.', '');?> Euro
	<br>
		{sum_all} + PVN:   <?=number_format(@$summary, 2, '.', '');?> Euro
	</td>
	<td>&nbsp;</td>
</tr>	
</table>
<br><br>
<?php if (@$this->session->userdata('web_user_id')!='') {?>
	<b><a href="<?=$this->config->site_url()?>web/buy_items_already_reg">{cart_buy}</a></b>
<?php }else{?>
	<b><a href="<?=$this->config->site_url()?>web/view_buy_items_reg">{cart_buy_register}</a></b>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b><a href="<?=$this->config->site_url()?>web/view_buy_items">{cart_buy_not_reg}</a></b>
<?php } ?>	
</center>
</div>