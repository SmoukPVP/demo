<?php
// Запуск сессии для работы с авторизацией
session_start();
require_once 'db.php';
require_once 'functions.php';

// Определяем текущего пользователя
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? ($_GET['guest'] == 1 ? 'Гость' : null);
// Фильтрация и сортировка доступны только менеджеру и администратору
$canFilter = ($role == 'Менеджер' || $role == 'Администратор');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список товаров</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr.highlight-discount { background-color: #F4A460 !important; }
        tr.out-of-stock { background-color: #ADD8E6 !important; }
        /* Оформление старой и новой цены */
        .old-price { text-decoration: line-through; color: red; margin-right: 8px; }
        .new-price { font-weight: bold; color: black; }
        /* Размер фото товара */
        img.product-img { width: 80px; height: 80px; object-fit: cover; }
        /* Курсор для кликабельных заголовков */
        th.sortable { cursor: pointer; user-select: none; }
        th.sortable:hover { background-color: #4a6a8a; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <?php if ($canFilter): ?>
    <!-- Панель фильтрации (только для менеджера/админа) -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" id="searchInput" class="form-control" placeholder="Поиск (название, описание, производитель, поставщик)...">
                </div>
                <div class="col-md-3">
                    <select id="manufacturerFilter" class="form-select">
                        <option value="">Все производители</option>
                        <?php
                        // Загрузка списка производителей для фильтра
                        $manuf = $conn->query("SELECT id_manufacturer, name_manufacturer FROM manufacturers");
                        while ($m = $manuf->fetch_assoc()):
                        ?>
                            <option value="<?= $m['id_manufacturer'] ?>"><?= htmlspecialchars($m['name_manufacturer']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Контейнер для таблицы товаров, загружается через AJAX -->
    <div id="productsContainer"></div>
</div>
<script>
    // Текущий параметр сортировки (по умолчанию по названию по возрастанию)
    let currentSort = 'name_asc';
    
    // Функция загрузки товаров с учётом поиска, фильтра, сортировки
    function loadProducts() {
        let search = document.getElementById('searchInput')?.value || '';
        let manuf = document.getElementById('manufacturerFilter')?.value || '';
        let canFilter = <?= json_encode($canFilter) ?>;
        let url = `ajax_products.php?search=${encodeURIComponent(search)}&manufacturer=${manuf}&sort=${currentSort}&canFilter=${canFilter}`;
        fetch(url)
            .then(res => res.text())
            .then(html => document.getElementById('productsContainer').innerHTML = html)
            .then(() => attachSortHandlers()); // после загрузки таблицы привязываем обработчики кликов на сортируемые заголовки
    }
    
    // Привязка обработчиков клика к элементам с классом sortable
    function attachSortHandlers() {
        document.querySelectorAll('.sortable').forEach(th => {
            th.removeEventListener('click', sortClickHandler);
            th.addEventListener('click', sortClickHandler);
        });
    }
    
    // Обработчик клика по заголовку таблицы
    function sortClickHandler(e) {
        let field = this.dataset.sort;     // получаем поле сортировки (name, category, manufacturer, price, discount, stock)
        let direction = 'asc';
        if (currentSort === field + '_asc') direction = 'desc';
        currentSort = field + '_' + direction;
        loadProducts();                    // перезагружаем таблицу с новым параметром сортировки
    }
    
    // Если доступна фильтрация, реагируем на изменения в полях поиска и фильтра
    <?php if ($canFilter): ?>
    document.getElementById('searchInput').addEventListener('input', loadProducts);
    document.getElementById('manufacturerFilter').addEventListener('change', loadProducts);
    <?php endif; ?>
    
    // Первоначальная загрузка таблицы
    loadProducts();
</script>
</body>
</html>