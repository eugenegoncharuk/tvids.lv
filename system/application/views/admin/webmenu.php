<FORM action="<?=site_url()?>action/menu/{action}/{id}" method="post">
	<div style="margin-bottom:10px; overflow:hidden;">
		<span style="float:left; width: 80px">name : </span><input type="text" name="name" value="{name}" size="120">
	</div>
	{translations}
	<div style="margin-bottom:10px; overflow:hidden;">
		<span style="float:left; width: 80px">{full} : </span><input type="text" name="lang_text_{short}" value="{lang_text}" size="120">
	</div>
	{/translations}
	<div style="margin-bottom:10px; overflow:hidden;">
		<span style="float:left; width: 80px">Тип : </span><select name="node_type">{node_type_options}</select>
	</div>
	<input type="submit" title="Submit" value="Submit">
</FORM>