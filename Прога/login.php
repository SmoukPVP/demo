<?php 
session_start(); 
require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; } 
        .login-card { max-width: 400px; margin: 100px auto; }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card shadow">
        <div class="card-header bg-primary text-white">Авторизация</div>
        <div class="card-body">
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])): 
                $login = $conn->real_escape_string($_POST['login']);
                $pass = $conn->real_escape_string($_POST['password']);
                $sql = "SELECT u.*, r.role FROM users u JOIN roles r ON u.id_role = r.id_role WHERE u.login='$login' AND u.password='$pass'";
                $res = $conn->query($sql);
                if ($res && $res->num_rows == 1):
                    $user = $res->fetch_assoc();
                    $_SESSION['user'] = $user;
                    header('Location: index.php');
                    exit;
                else: ?>
                    <div class="alert alert-danger">Неверный логин или пароль</div>
                <?php endif; 
            endif; ?>
            <form method="post">
                <input type="text" name="login" class="form-control mb-2" placeholder="Логин" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Пароль" required>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
                <a href="index.php?guest=1" class="btn btn-link w-100 mt-2">Продолжить как гость</a>
                <hr>
                <a href="register.php" class="btn btn-outline-success w-100">Зарегистрироваться</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>