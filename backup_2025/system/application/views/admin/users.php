<table id="tablestyle" cellspacing="0">
	<tbody>
	<tr id="tableheadstyle">	
		<td>
			Contact name
		</td>
		<td>
			Address
		</td>
		<td>
			Telephone
		</td>
		<td>
			Email
		</td>
		<td>
			Comments
		</td>	
		<td>
			Action
		</td>	
	</tr>
	<?php foreach($users as $item):?>

	<tr id="tablerowstyle" onclick="document.location.href='<?=site_url()?>action/users/show/<?=$item['id']?>'">
		<td>
			<?=$item['contact_name']?>
		</td>
		<td>
			<?=$item['fact_address']?>
		</td>
		<td>
			<?=$item['tel']?>
		</td>
		<td>
			<?=$item['email']?>
		</td>
		<td>
			<?=$item['comments']?>
		</td>
		<td>
			<a href="<?=site_url()?>action/users/delete_user/<?=$item['id'];?>">X</a>
		</td>	
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
<br>
<CENTER><?=$pagination?></CENTER>