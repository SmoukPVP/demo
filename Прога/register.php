<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $surname = $conn->real_escape_string($_POST['surname']);
    $login = $conn->real_escape_string($_POST['login']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm = $conn->real_escape_string($_POST['confirm_password']);

    if ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif (empty($login)) {
        $error = 'Логин обязателен';
    } else {
        // Проверка уникальности логина
        $check = $conn->query("SELECT id_user FROM users WHERE login='$login'");
        if ($check && $check->num_rows > 0) {
            $error = 'Пользователь с таким логином уже существует';
        } else {
            $role_id = 3;
            $sql = "INSERT INTO users (id_role, last_name, first_name, surname, login, password) 
                    VALUES ($role_id, '$last_name', '$first_name', '$surname', '$login', '$password')";
            if ($conn->query($sql)) {
                $success = 'Регистрация прошла успешно! Теперь вы можете войти, используя логин и пароль.';
            } else {
                $error = 'Ошибка базы данных: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .reg-card { max-width: 500px; margin: 50px auto; }
    </style>
</head>
<body>
<div class="container">
    <div class="card reg-card shadow">
        <div class="card-header bg-success text-white">Регистрация нового клиента</div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <a href="login.php" class="btn btn-primary">Перейти к входу</a>
            <?php else: ?>
                <form method="post">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="last_name" class="form-control" placeholder="Фамилия" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="first_name" class="form-control" placeholder="Имя" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="surname" class="form-control" placeholder="Отчество">
                        </div>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="login" class="form-control" placeholder="Логин (любой)" required>
                        <small class="text-muted">Используйте этот логин для входа</small>
                    </div>
                    <div class="mt-2">
                        <input type="email" name="email" class="form-control" placeholder="Email (необязательно)">
                    </div>
                    <div class="mt-2">
                        <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                    </div>
                    <div class="mt-2">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Подтвердите пароль" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mt-3">Зарегистрироваться</button>
                    <a href="login.php" class="btn btn-link w-100 mt-2">Уже есть аккаунт? Войти</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>