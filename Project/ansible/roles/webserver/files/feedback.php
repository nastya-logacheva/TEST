<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "nastiasunkiss@gmail.com";
    $subject = "Новое сообщение с формы обратной связи";

    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    // Подключение к PostgreSQL
    $conn = pg_connect("host=192.168.56.13 dbname=feedback user=postgres password=postgres");

    if (!$conn) {
        die("<h2>Ошибка подключения к базе данных.</h2>");
    }

    // Сохраняем сообщение в базу
    $res = pg_query_params($conn,
        "INSERT INTO feedback (name, email, message) VALUES ($1, $2, $3)",
        array($name, $email, $message)
    );

    if (!$res) {
        echo "<h2>Ошибка при вставке в БД: " . pg_last_error($conn) . "</h2>";
    }

    // Отправка письма
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n";
    $body = "Имя: $name\nEmail: $email\n\nСообщение:\n$message";

    if (mail($to, $subject, $body, $headers)) {
        echo "<h2>Спасибо! Ваше сообщение отправлено и сохранено.</h2>";
    } else {
        echo "<h2>Ошибка при отправке письма. Данные всё равно сохранены в базу.</h2>";
    }

    pg_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма обратной связи</title>
</head>
<body>
    <h1>Связаться с нами</h1>
    <form method="post">
        <label>Имя: <input type="text" name="name" required></label><br><br>
        <label>Email: <input type="email" name="email" required></label><br><br>
        <label>Сообщение:<br><textarea name="message" rows="5" cols="40" required></textarea></label><br><br>
        <input type="submit" value="Отправить">
    </form>
</body>
</html>
