<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Администратор') {
    die("Доступ запрещён.");
}

require_once '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($id > 0);
$prod = null;

if ($isEdit) {
    $result = $conn->query("SELECT * FROM products WHERE id_product = $id");
    if ($result && $result->num_rows == 1) {
        $prod = $result->fetch_assoc();
    } else {
        die("Товар не найден.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $product_name = trim($_POST['product_name']);
    $category_id = (int)$_POST['category'];
    $manufacturer_id = (int)$_POST['manufacturer'];
    $supplier_id = (int)$_POST['supplier'];
    $price = (float)$_POST['price'];
    $discount = (int)$_POST['discount'];
    $stock = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    $unit = trim($_POST['unit']);
    $article = trim($_POST['article']);

    $errors = [];
    if (empty($product_name)) $errors[] = "Название обязательно.";
    if ($price < 0) $errors[] = "Цена не может быть отрицательной.";
    if ($discount < 0 || $discount > 100) $errors[] = "Скидка от 0 до 100.";
    if ($stock < 0) $errors[] = "Количество не может быть отрицательным.";

    $imagePath = $prod['image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed)) {
            $errors[] = "Только JPEG, PNG, GIF.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newName = uniqid() . '.' . $ext;
            $target = '../uploads/' . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                list($w, $h) = getimagesize($target);
                $newW = 300; $newH = 200;
                $src = imagecreatefromstring(file_get_contents($target));
                $dst = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
                imagejpeg($dst, $target, 90);
                imagedestroy($src); imagedestroy($dst);
                $imagePath = $newName;
                if ($isEdit && !empty($prod['image']) && file_exists('../uploads/'.$prod['image']) && $prod['image'] != $imagePath) {
                    unlink('../uploads/'.$prod['image']);
                }
            } else {
                $errors[] = "Ошибка загрузки фото.";
            }
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $sql = "UPDATE products SET 
                    product_name='$product_name',
                    id_product_category=$category_id,
                    id_manufacturer=$manufacturer_id,
                    id_supplier=$supplier_id,
                    price=$price,
                    current_discount=$discount,
                    stock_quantity=$stock,
                    description='$description',
                    measurement_unit='$unit',
                    article_number='$article',
                    image='$imagePath'
                    WHERE id_product=$id";
        } else {
            $sql = "INSERT INTO products 
                    (product_name, id_product_category, id_manufacturer, id_supplier, price, current_discount, stock_quantity, description, measurement_unit, article_number, image)
                    VALUES 
                    ('$product_name', $category_id, $manufacturer_id, $supplier_id, $price, $discount, $stock, '$description', '$unit', '$article', '$imagePath')";
        }
        if ($conn->query($sql)) {
            header('Location: ../index.php');
            exit;
        } else {
            $error = "Ошибка БД: " . $conn->error;
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

$categories = $conn->query("SELECT * FROM product_categories ORDER BY name_category");
$manufacturers = $conn->query("SELECT * FROM manufacturers ORDER BY name_manufacturer");
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name_supplier");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Редактирование' : 'Добавление' ?> товара</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .form-card { max-width: 800px; margin: 40px auto; border-radius: 15px; overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #007bff, #0056b3); }
        .help-text { font-size: 0.75rem; color: #6c757d; margin-top: 4px; }
        .current-img { max-height: 100px; margin-top: 10px; border-radius: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container">
    <div class="card shadow form-card">
        <div class="card-header text-white">
            <h4 class="mb-0"><?= $isEdit ? '✏️ Редактирование товара' : '➕ Новый товар' ?></h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Наименование *</label>
                        <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($prod['product_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ед. изм.</label>
                        <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($prod['measurement_unit'] ?? '') ?>" placeholder="шт, кг, м">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Категория</label>
                        <select name="category" class="form-select">
                            <?php while($c = $categories->fetch_assoc()): ?>
                                <option value="<?= $c['id_product_category'] ?>" <?= (isset($prod['id_product_category']) && $prod['id_product_category'] == $c['id_product_category']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name_category']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Производитель</label>
                        <select name="manufacturer" class="form-select">
                            <?php while($m = $manufacturers->fetch_assoc()): ?>
                                <option value="<?= $m['id_manufacturer'] ?>" <?= (isset($prod['id_manufacturer']) && $prod['id_manufacturer'] == $m['id_manufacturer']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['name_manufacturer']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Поставщик</label>
                        <select name="supplier" class="form-select">
                            <?php while($s = $suppliers->fetch_assoc()): ?>
                                <option value="<?= $s['id_supplier'] ?>" <?= (isset($prod['id_supplier']) && $prod['id_supplier'] == $s['id_supplier']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['name_supplier']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Артикул</label>
                        <input type="text" name="article" class="form-control" value="<?= htmlspecialchars($prod['article_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Цена (₽) *</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= htmlspecialchars($prod['price'] ?? '0') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Скидка (%)</label>
                        <input type="number" min="0" max="100" name="discount" class="form-control" value="<?= htmlspecialchars($prod['current_discount'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Кол-во на складе</label>
                        <input type="number" min="0" name="stock" class="form-control" value="<?= htmlspecialchars($prod['stock_quantity'] ?? '0') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($prod['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Фото товара</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif">
                        <div class="help-text">Формат: JPG, PNG, GIF.</div>
                        <?php if ($isEdit && !empty($prod['image'])): ?>
                            <img src="../uploads/<?= $prod['image'] ?>" class="current-img" alt="Текущее фото">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="../index.php" class="btn btn-secondary">← Назад</a>
                    <button type="submit" name="submit" class="btn btn-success px-4">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>