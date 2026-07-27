<FORM action="<?=site_url()?>action/menu/{action}/{id}" method="post">
	<span style="float:left; width: 80px">name : </span><input type="text" name="name" value="{name}" size="120"><br>
	{translations}
		<span style="float:left; width: 80px">{full} : </span><input type="text" name="lang_text_{short}" value="{lang_text}" size="120"><br>
	{/translations}
	<input type="submit" title="Submit" value="Submit">
</FORM>