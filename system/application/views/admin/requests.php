<FORM action="<?=site_url()?>action/requests/{action}" method="POST">
	Search: <input type="text" name="search_str" value="{search_str}"/>
	<input type="submit" value="submit">
</FORM>
<br>
<? if ($action == 'show_unapproved') { ?>
	Show unapproved
<? } else { ?> <a href="<?=site_url()?>action/requests/show_unapproved">Show unapproved</a><? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;

<? if ($action == 'show_approved') { ?>
	Show approved
<? } else { ?><a href="<?=site_url()?>action/requests/show_approved">Show approved</a><? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;

<? if ($action == 'show_declined') { ?>
	Show processed
<? } else { ?><a href="<?=site_url()?>action/requests/show_declined">Show processed</a><? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<br><br>	
<table id="tablestyle" cellspacing="0" cellpadding="0">
	<tbody>
	<tr id="tableheadstyle">	
		<td>
			Request id
		</td>
		<td>
			Request date
		</td>
		<td>
			Approve date
		</td>
		<td>
			Contact name ( Address )
		</td>
		<td width="30">
			&nbsp;
		</td>
	</tr>
	<?php foreach($reqs as $item):?>

	<tr id="tablerowstyle" onclick="document.location.href='<?=site_url()?>action/requests/show_request/<?=$item['request_id']?>'">
		<td>
			<?=$item['req_id']?>
		</td>
		<td>
			<?=$item['req_date']?>
		</td>
		<td>
			<?=$item['approve_date']?>
		</td>
		<td>
			<a href="<?=site_url()?>action/users/show/<?=$item['info_id'];?>">
				<?=$item['contact_name']?> ( <?=$item['fact_address']?> )
			</a>
		</td>
		<td>
			<?php if ($approve!='') { ?> 
				<a href="<?=site_url()?>action/requests/{approve}/<?=$item['request_id'];?>">V</a>
			<?php } ?>	
			
			<?php if ($approve!='' && $decline!=''){ ?>
			|
			<?php } ?>
			
			<?php if ($decline!='') { ?> 
				<a href="<?=site_url()?>action/requests/{decline}/<?=$item['request_id'];?>">X</a>
			<?php } ?>	
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
<br>
<CENTER><?=@$pagination?></CENTER>
<br>
<? if ($action == 'show_unapproved') { ?>
	Show unapproved
<? } else { ?> <a href="<?=site_url()?>action/requests/show_unapproved">Show unapproved</a><? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;

<? if ($action == 'show_approved') { ?>
	Show approved
<? } else { ?><a href="<?=site_url()?>action/requests/show_approved">Show approved</a><? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;

<? if ($action == 'show_declined') { ?>
	Show processed
<? } else { ?><a href="<?=site_url()?>action/requests/show_declined">Show processed</a><? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;
