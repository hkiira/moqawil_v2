<?php
$content = file_get_contents('d:/wamp64/www/moqa/src/Controller/MetasalesController.php');
if (preg_match('/public function products\((.*?)\)\s*{(.*?)^    }/sm', $content, $matches)) {
    file_put_contents('d:/wamp64/www/moqa/src/Controller/extract_products.txt', $matches[0]);
}
if (preg_match('/public function addOrder\((.*?)\)\s*{(.*?)^    }/sm', $content, $matches)) {
    file_put_contents('d:/wamp64/www/moqa/src/Controller/extract_addOrder.txt', $matches[0]);
}
if (preg_match('/public function homeCategories\((.*?)\)\s*{(.*?)^    }/sm', $content, $matches)) {
    file_put_contents('d:/wamp64/www/moqa/src/Controller/extract_homeCategories.txt', $matches[0]);
}
if (preg_match('/public function customerOrders\((.*?)\)\s*{(.*?)^    }/sm', $content, $matches)) {
    file_put_contents('d:/wamp64/www/moqa/src/Controller/extract_customerOrders.txt', $matches[0]);
}
echo "Extracted";
