<?php
session_start();
require_once 'db.php';

// Получение параметров из GET-запроса
$search = $_GET['search'] ?? '';
$manufacturer = $_GET['manufacturer'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';
$canFilter = ($_GET['canFilter'] ?? 'false') === 'true';

// Определяем роль текущего пользователя для проверки прав администратора
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? null;
$isAdmin = ($role == 'Администратор');

// Преобразуем строку сортировки в реальные поле и направление ORDER BY
$sortField = 'product_name';
$sortOrder = 'ASC';

switch ($sort) {
    case 'name_asc':        $sortField = 'product_name';         $sortOrder = 'ASC'; break;
    case 'name_desc':       $sortField = 'product_name';         $sortOrder = 'DESC'; break;
    case 'category_asc':    $sortField = 'cat.name_category';    $sortOrder = 'ASC'; break;
    case 'category_desc':   $sortField = 'cat.name_category';    $sortOrder = 'DESC'; break;
    case 'manufacturer_asc':$sortField = 'm.name_manufacturer';  $sortOrder = 'ASC'; break;
    case 'manufacturer_desc':$sortField = 'm.name_manufacturer'; $sortOrder = 'DESC'; break;
    case 'price_asc':       $sortField = 'price';                $sortOrder = 'ASC'; break;
    case 'price_desc':      $sortField = 'price';                $sortOrder = 'DESC'; break;
    case 'discount_asc':    $sortField = 'current_discount';     $sortOrder = 'ASC'; break;
    case 'discount_desc':   $sortField = 'current_discount';     $sortOrder = 'DESC'; break;
    case 'stock_asc':       $sortField = 'stock_quantity';       $sortOrder = 'ASC'; break;
    case 'stock_desc':      $sortField = 'stock_quantity';       $sortOrder = 'DESC'; break;
    default:                $sortField = 'product_name';         $sortOrder = 'ASC';
}

// Формируем основной SQL-запрос с JOIN для получения связанных данных
$sql = "SELECT p.*, cat.name_category, m.name_manufacturer, s.name_supplier 
        FROM products p
        LEFT JOIN product_categories cat ON p.id_product_category = cat.id_product_category
        LEFT JOIN manufacturers m ON p.id_manufacturer = m.id_manufacturer
        LEFT JOIN suppliers s ON p.id_supplier = s.id_supplier
        WHERE 1=1";

// Если включена фильтрация и задан поисковый запрос, добавляем условие LIKE для текстовых полей
if ($canFilter && $search != '') {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (p.product_name LIKE '%$search%' 
                OR p.description LIKE '%$search%' 
                OR m.name_manufacturer LIKE '%$search%' 
                OR s.name_supplier LIKE '%$search%')";
}
// Фильтр по производителю
if ($canFilter && $manufacturer != '') {
    $manuf = (int)$manufacturer;
    $sql .= " AND p.id_manufacturer = $manuf";
}
// Добавляем сортировку
$sql .= " ORDER BY $sortField $sortOrder";

$res = $conn->query($sql);
?>

<!-- Таблица товаров -->
<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Фото</th>
            <?php if ($canFilter): ?>
                <!-- Интерактивные заголовки с сортировкой (только для менеджера/админа) -->
                <th class="sortable" data-sort="name">Наименование</th>
                <th class="sortable" data-sort="category">Категория</th>
                <th class="sortable" data-sort="manufacturer">Производитель</th>
                <th class="sortable" data-sort="price">Цена</th>
                <th class="sortable" data-sort="discount">Скидка</th>
                <th class="sortable" data-sort="stock">Остаток</th>
            <?php else: ?>
                <!-- Обычные заголовки для гостя и авторизованного клиента -->
                <th>Наименование</th>
                <th>Категория</th>
                <th>Производитель</th>
                <th>Цена</th>
                <th>Скидка</th>
                <th>Остаток</th>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
                <th>Действия</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $res->fetch_assoc()):
        $discount = $row['current_discount'];
        $price = $row['price'];
        $finalPrice = $price * (100 - $discount) / 100;
        $isOut = $row['stock_quantity'] <= 0;
        $isHighDiscount = $discount > 12;
        
        // Определяем класс строки для подсветки
        $rowClass = '';
        if ($isOut) $rowClass = 'out-of-stock';
        elseif ($isHighDiscount) $rowClass = 'highlight-discount';
        ?>
        <tr class="<?= $rowClass ?>">
            <!-- Фото товара, при отсутствии – картинка-заглушка -->
            <td><img src="<?= !empty($row['image']) ? 'uploads/' . $row['image'] : 'assets/picture.png' ?>" class="product-img"></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= htmlspecialchars($row['name_category']) ?></td>
            <td><?= htmlspecialchars($row['name_manufacturer']) ?></td>
            <!-- Отображение цены: если есть скидка, старая цена перечёркнута, новая – рядом -->
            <td>
                <?php if ($discount > 0): ?>
                    <span class="old-price"><?= number_format($price, 2) ?> ₽</span>
                    <span class="new-price"><?= number_format($finalPrice, 2) ?> ₽</span>
                <?php else: ?>
                    <?= number_format($price, 2) ?> ₽
                <?php endif; ?>
            </td>
            <td><?= $discount ?>%</td>
            <td><?= $row['stock_quantity'] ?></td>
            <?php if ($isAdmin): ?>
                <!-- Кнопки редактирования и удаления (только для администратора) -->
                <td>
                    <a href="admin/product_edit.php?id=<?= $row['id_product'] ?>" class="btn btn-sm btn-warning">Редакт.</a>
                    <a href="admin/product_delete.php?id=<?= $row['id_product'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">Удалить</a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>