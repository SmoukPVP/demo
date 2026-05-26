<?php
/**
 * Вычисляет цену со скидкой
 */
function getFinalPrice($price, $discount) {
    return $price * (100 - $discount) / 100;
}
?>