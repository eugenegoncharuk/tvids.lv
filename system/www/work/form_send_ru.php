<?php 

function getFormSend($path, $msg){
	return 
	'<h1 id="style38">'.$msg.'</h1>
	<form name="contacts" action="'.$path.'" method="post">

	<table border="0" align="left">

	<tr>
	<td><span id="style46">Ваш вопрос</span><span id="style43"> <font color="#FF0000">*</font></span>
	<br>
	<textarea name="message" rows="8" cols="35" value="" size="5000">'.@$_POST['message'].'</textarea></td>
	</tr>

	<tr>
	<td width="40%"><span id="style46">Имя</span><span id="style43"> <font color="#FF0000">*</font></span>

	<br>
	<input name="name" style="width: 300px;" type="text" rows="1" value="'.@$_POST['name'].'" size="53"></td>
	</tr>

	<tr>
	<td><span id="style46">E-mail</span><span id="style43"> <font color="#FF0000">*</font></span>
	<br>
	<input type="text" style="width: 300px" name="email" rows="1" value="'.@$_POST['email'].'" size="53"></td>
	</tr>

	<tr>
	<td>
	<span id="style43">Телефон </span>

	<br>
	<input type="text" style="width: 300px" name="phone" rows="1" value="'.@$_POST['phone'].'" size="53"></td>
	</tr>
	<tr>
	<tr>
	<td><span id="style46">Дата</span><span id="style43"> или сезон проведения мероприятия <font color="#FF0000">*</font></span>
	<br>
	<input type="text" style="width: 300px;" name="date" rows="1" value="'.@$_POST['date'].'" size="53"></td>
	</tr>

	<tr align="left" valign="middle">
		<TD>

			<span id="style43">Код безопасности:</span>
			&nbsp;&nbsp;
			<img src="./work/images.php"></img>
			<br>
			<input type="text" name="entercode" rows="1" value="" size="6">	</td>
	</tr>	
	<tr align="left">
		<td height="45">
			<input type="submit" name="contact_submit" style="margin-left: 140px;" value="Отправить">

			<br><br>	</td>
	</tr>	
	</table>
	</form>';
}
?>