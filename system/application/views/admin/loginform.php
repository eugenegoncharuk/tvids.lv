<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Jadmin</title>
<base href="<?php echo base_url()?>system/www/">
<link href="admin/css/admin.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div class="loginform">
<?//$this->load->view('elements/language');?>
<form action=<?=site_url().'adm'?> method="post">
<fieldset>
<legend>{admin_box_title}</legend>

<p>{admin_form_title}</p>
<div class="error"><?=$this->validation->error_string;?></div>
	<label for="username"><input type="text" name="username" tabindex="1" id="username" value="<?=@$_POST['username']?>"/>{admin_form_username}:
	</label>
	<label for="password"><input type="password" name="password" tabindex="2" id="password" />{admin_form_password}:
	</label>
	<!--<label for="remember_me"><input type="checkbox" name="remember_me" value="1" tabindex="3" id="remember_me" />Remember me ?
		</label>-->
	<label for="submit">
    <input name="Submit" type="submit" id="submit" tabindex="4" value="{admin_box_title}" />
	</label>
 </fieldset>
</form>
</div>

</body>
</html>
