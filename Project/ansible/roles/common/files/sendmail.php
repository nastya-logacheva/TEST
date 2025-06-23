<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $message = htmlspecialchars($_POST['message']);

  $to = "nastiasunkiss@gmail.com";
  $subject = "Новое сообщение с формы обратной связи";
  $body = "Имя: $name\nEmail: $email\nСообщение:\n$message";

  $headers = "From: $email";

  if (mail($to, $subject, $body, $headers)) {
    echo "Сообщение отправлено!";
  } else {
    echo "Ошибка при отправке.";
  }
}
?>
