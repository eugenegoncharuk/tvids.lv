<FORM action="<?=site_url()?>action/adminusers/{action}/{id}" method="post">
<p>Editing user</p>
<div class="error"><?=$this->validation->error_string;?></div>
	<span style="float:left; width: 140px">Name : </span><input type="text" name="name" value="{name}"><br>
	<span style="float:left; width: 140px">Username : </span><input type="text" name="username" value="{username}"><br>
	<span style="float:left; width: 140px">Password : </span><input type="password" name="password" value=""><br>
	<span style="float:left; width: 140px">Password 2 (repeat) : </span><input type="password" name="password2" value=""><br>	
	<input type="submit" title="Submit" value="Submit">
</FORM>