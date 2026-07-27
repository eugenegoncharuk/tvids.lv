<CENTER>
	<?php echo validation_errors('<div id="error_main">', '</div>'); ?>
	<div id="error_reg"><?=$this->form_validation->error_string;?></div>
</CENTER>
<form name="email_form" action="<?=$this->config->site_url()?>web/send_update" method="POST" id="forgot_password_block">
	<CENTER>
		e-mail: <input name="email" type="text" maxlength="30" value="<?=@$_POST['email']?>">
		<input type="submit" value="{approve_button}">
	</CENTER>
</form>