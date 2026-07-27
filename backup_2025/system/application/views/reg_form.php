<div id="reg_form_block">
	<center>
		<div id="reg_form_error_block">
			<?php echo validation_errors('<div id="error_reg">', '</div>'); ?>
			<div id="error_reg"><?=$this->form_validation->error_string;?></div>
		</div>
	</center>

	<form name="reg_form" action="<?=base_url()?>{cur_lang}/web/{action}" method="POST">
	<table cellspacing="0" cellpadding="0" border="0" width="100%"> 
	<tr>
	 <td align="center">
	   <table cellspacing="2" cellpadding="0" id="regform" border="0">
	      
	   <tr>
	    <td colspan="2" style="border: 0;">
	     <b>{basic_data}</b>
	    </td>
	   </tr>
	   
	   <tr>
	    <td>{name_surname_contact} <font color="red">*</font></td>
	    <td>
	     <input name="contact_name" type="text" maxlength="50" value="<?=@$_POST['contact_name']?>">
	    </td>
	   </tr>

  	   <tr>
	    <td>{fact_address} <font color="red">*</font></td>
	    <td>
	     <input name="fact_address" type="text" maxlength="100" value="<?=@$_POST['fact_address']?>">
	    </td>
	   </tr>
	
	    <tr>
	    <td>{tel} <font color="red">*</font></td>
	    <td>
	     <input name="tel" type="text" maxlength="20" value="<?=@$_POST['tel']?>">
	    </td>
	   </tr>
	   
	   <tr>
	    <td>e-mail <font color="red">*</font></td>
	    <td>
	     <input name="email" type="text" maxlength="30" value="<?=@$_POST['email']?>">
	    </td>
	   </tr>
	   
	   <tr>
	    <td>{comments}</td>
	    <td>
	     <textarea name="comments" maxlength="100"><?=@$_POST['comments']?></textarea>
	    </td>
	   </tr>

	   <tr class="submit">
	    <td align="center" colspan="2" style="border: 0;">
		 <br>
	     <input type="submit" class="button" value="{approve_button}" />
	    </td>
	   </tr>
	   </table>
		</p>
	 </td>
	</tr>
	</table>
	</form>
</div>
