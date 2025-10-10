<?php
	$to = 'kirja2006@gmail.com';
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
	$message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

	$err = "";
	if ($name != "%%NOTHING%%" && trim ($name) == "")
		$err = "name";
	else if ($phone != "%%NOTHING%%" && trim ($phone) == "")
		$err = "phone";
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		$err = "email";
	else if ($subject != "%%NOTHING%%" && trim ($subject) == "")
		$err = "subject";
	else if ($message != "%%NOTHING%%" && trim ($message) == "")
		$err = "message";

	if ($err != "") {
		echo $err;
		exit;
	}

	$user_name = $name == "%%NOTHING%%" ? "" : "Сообщение отправил(а) <b>".$name."</b>.<br>";
	$user_phone = $phone == "%%NOTHING%%" ? "" : "Телефон: <b>".$phone."</b>.<br>";
	$user_email = $email == "%%NOTHING%%" ? "" : "Email: <b>".$email."</b>.<br>";
	$user_message = $message == "%%NOTHING%%" ? '' : "<b>Текст сообщения:</b><br>".$message;
	$msg = "На вашем сайте было сформировано новое сообщение!<br>".$user_name.$user_phone.$user_email.$user_message;

	$subject_mess = $subject == "%%NOTHING%%" ? "Новое сообщение с сайта" : $subject_mess;
	$mail_subject = "=?utf-8?B?".base64_encode($subject_mess)."?=";
	$headers = "From: $email\r\nReply-to: $email\r\nContent-type: text/html; charset=utf-8\r\n";
	$success = mail ($to, $mail_subject, $msg, $headers);

	// Дальше не нужно ничего изменять
	$params = array(
		'user_name' => $name,
		'user_phone' => $phone,
		'user_email' => $email,
		'user_message' => $message,
		'subject_mess' => $subject,
		'date' => date('d-m-Y H:i:s'),
		'project_id' => 'YazNo1NKsOIx'
	);

	$myCurl = curl_init();
	curl_setopt_array($myCurl, array(
		CURLOPT_URL => 'https://crafty.site/curl/form.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($params)
	));
	$response = curl_exec($myCurl);
	curl_close($myCurl);

	echo $success;
?>