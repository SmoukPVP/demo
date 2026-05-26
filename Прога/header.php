<?php
// Запуск сессии, если ещё не запущена
if (session_status() === PHP_SESSION_NONE) session_start();

// Безопасное получение данных пользователя
$user = $_SESSION['user'] ?? null;

// Формирование ФИО только для авторизованных пользователей
$userFullName = '';
if ($user && isset($user['last_name']) && isset($user['first_name'])) {
    $userFullName = htmlspecialchars($user['last_name'] . ' ' . $user['first_name']);
    if (isset($user['surname']) && !empty($user['surname'])) {
        $userFullName .= ' ' . htmlspecialchars($user['surname']);
    }
}

// Определение роли
$role = '';
if ($user && isset($user['role'])) {
    $role = $user['role'];
} elseif (isset($_GET['guest']) && $_GET['guest'] == 1) {
    $role = 'Гость';
}
?>

<!-- Навигационная панель -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">СтройМатериалы</a>
        <div class="ms-auto">
            <?php if ($user): ?>
                <!-- Отображаем ФИО и роль авторизованного пользователя -->
                <span class="text-white me-3">
                    <?= $userFullName ?> (<?= htmlspecialchars($role) ?>)
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Выйти</a>
            <?php else: ?>
                <!-- Для гостя показываем соответствующую метку и кнопку входа -->
                <span class="text-white me-3">Гость</span>
                <a href="login.php" class="btn btn-outline-light btn-sm">Войти</a>
            <?php endif; ?>
        </div>
    </div>
</nav>