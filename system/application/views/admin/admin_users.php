<table id="tablestyle" cellspacing="0">
	<tbody>
	<tr id="tableheadstyle">	
		<td>
			Username
		</td>
		<td>
			Name
		</td>
		<td>
			Accesstime
		</td>
		<td>
			Delete
		</td>
	</tr>
	{users}
	<tr id="tablerowstyle" onclick="document.location.href='<?=site_url()?>action/adminusers/show_edit/{id}'">
		<td>
			{username}
		</td>
		<td>
			{name}
		</td>
		<td>
			{accesstime}
		</td>
		<td>
			<a href="<?=site_url()?>action/adminusers/delete/{id}">X</a>
		</td>
	</tr>
	{/users}
	</tbody>
</table>
<br>
<a href="<?=site_url()?>action/adminusers/show_add">Add new user</a>
