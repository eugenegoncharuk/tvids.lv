<?php

session_start();
if (@$_POST['contact_submit']!=null){

	$entercode = @$_POST['entercode'];
	$session_code = @$_SESSION['random_code'];
	@$_SESSION['random_code'].=@$_SESSION['random_code'];
	$msg="Īsziņa ir nosūtīta!";
	$msgSended = true;
	
	if (md5(strtolower($entercode)) != $session_code){
		$msg = "Lūdzu ievādiet pareizu apstiprināšanas kodu!";
	}
	else
	{
			$name = $_POST['name'];
			$email = $_POST['email'];
			$message = $_POST['message'];
			$date = $_POST['date'];
			$phone = $_POST['phone'];
			$ip = @$_SERVER['REMOTE_ADDR'];

			$mailto = "vivafiesta@inbox.lv";
			$required = array("name","email","message","date");
			$n = 0;
			$is_error = false;
			do {
			  $r = $required[$n];
			  if(!$$r || $$r=="") {
				  $msg = "Lūdzu aizpildiet visus laukus apzīmētus ar <font color=\"#FF0000\">*</font> !";
				  $is_error = true;
				  break;
			  }
			  $n++;
			} while ($n != count($required));

			if (!$is_error && !preg_match("/[-0-9a-z_]+@[-0-9a-z_^\.]+\.[a-z]{2,3}/i", $email))
			{
				  $msg = "Nepareizi ievādīta email adrese !";
				  $is_error = true;
		    }

			if (!$is_error){
				$message = trim($message);
				$email = htmlspecialchars(stripslashes($email));
				//$mailfrom = "From: $email";
				$headers = "From: $email\r\n".
						   "MIME-Version: 1.0\r\n" .
						   "Content-Type: text/plain; charset=utf-8\r\n" .
						   "Content-Transfer-Encoding: 8bit\r\n\r\n";
				$body = "---- НАЧАЛО ФОРМЫ СВЯЗИ ----\n\nСообщение:\n$message\n\n\nОтправлено с IP: $ip\nИмя: $name\nEmail: $email\nТелефон: $phone\nДата: $date\n\n---- КОНЕЦ ФОРМЫ СВЯЗИ ----";
				$base_subject = "From ".$email;
				mail($mailto, $base_subject, $body, $headers);
			}
		}
}
?>