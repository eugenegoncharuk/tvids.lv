<p>Request</p>
	<span style="float:left; width: 140px">Request id : </span>{req_id}<br>
	<span style="float:left; width: 140px">Request date : </span>{req_date}<br>
	<span style="float:left; width: 140px">Approve date : </span>{approve_date}<br>
	<span style="float:left; width: 140px">Status : </span>{status}<br>
	<!--<input type="submit" title="Submit" value="Submit">-->
<br>
<table id="tablestyle" cellspacing="0" cellpadding="0">
	<tbody>
	<tr id="tableheadstyle">	
		<td>
			Good
		</td>
		<td>
			Price
		</td>
		<td>
			Count
		</td>
		<td>
			Sum
		</td>
	</tr>
	<?php foreach($items as $item):?>

	<tr id="tablerowstyle">
		<td>
			<a href="<?=site_url()?>action/text/show_text/<?=$item['content_id']?>"><?=$item['text']?></a>
		</td>
		<td>
			<?=number_format(@$item['price'], 2, '.', '');?>
		</td>
		<td>
			<?=$item['count']?>
		</td>
		<td>
			<?=number_format(@$item['sum'], 2, '.', '');?>
			<? @$sum += @$item['sum'] ?>
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
<br/>
Sum: <?=number_format(@$sum, 2, '.', '');?> Ls