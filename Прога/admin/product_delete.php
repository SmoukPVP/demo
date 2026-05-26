<?php session_start(); if($_SESSION['user']['role']!='Администратор') die('Доступ запрещён');
require_once '../db.php';
$id = $_GET['id'];
$check = $conn->query("SELECT * FROM order_items WHERE id_product=$id");
if($check->num_rows>0) {
    die("<script>alert('Нельзя удалить товар, он присутствует в заказах!'); window.location='../index.php';</script>");
}
$conn->query("DELETE FROM products WHERE id_product=$id");
header('Location: ../index.php');