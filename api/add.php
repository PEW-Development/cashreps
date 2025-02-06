<?php

include 'conn.php';

if (isset($_GET['key'])){
    if ($_GET['key'] != "api-key-here"){
        exit();
    }
}else{
    exit();
}

$shoewords = ['Jordan', 'af1', 'Jo 4', 'kobe', 'B23', 'Bathing Ape', 'Coconut 350', 'dunk', 'SK8', 'airmax', 'trainer', 'air max', 'J0rdan', 'A1r', 'Low', 'NAI-KE', 'nike sb', 'aj4', 'yeezy', 'converse', 'sneakers', 'sneaker', 'boots', 'heels', 'flats', 'oxfords', 'loafers', 'sandals', 'slides', 'mules', 'clogs', 'ballet', 'running', 'trainers', 'cleats', 'wedges', 'espadrilles', 'slippers', 'shoes', 'bapesta']; // put shoe specific keywords here
$clothingwords = ['tee', 'tshirt', 't-shirt', 'pants', 'sweatpants', 'hoodie', 'Fleece', 'hooded', 'Tracksuit', 'sweater', 'TNF', 'north face', 'jacket', 'jersey', 'football', 'jerseys', 'stone island', 'belt', 'Zip Up', 'shorts', 'puffer', 'nike tech', 'tech']; // put clothing specific keywords here
$accessorieswords = ['air pods', 'jbl', 'speaker', 'crease', 'cap', 'chain', 'pillow', 'plush', 'glasses', 'sunglasses', 'mask', 'balaclava', 'condom', 'keychain', 'axe', 'rug', 'purse', 'bag', 'backpack', 'wallet', 'belt', 'jewlery', 'hat', 'socks', 'underwear'];

$dbcon->exec("SET NAMES 'utf8mb4'");

$json = json_decode(file_get_contents("php://input"), true);

$name = $json['name'];
$title = $json['title'];
$seller = $json['seller'];
$price_cn = $json['price_cn'];
$price_us = $json['price_us'];
$image = $json['image'];
$shipping_eu = $json['shipping_eu'];
$shipping_us = $json['shipping_us'];
$link = $json['link'];

$price_ru = intval($price_cn) * 13.29;
$shipping_ru = intval($json['shipping_eu']) - 5 * 9.45;

function determineCategory($title, $categoryWords) {
    foreach($categoryWords as $word) {
        if (strpos(strtolower($title), strtolower($word)) !== false) {
            return true;
        }
    }
    return false;
}

if (determineCategory($title, $shoewords)) {
    $category = 'shoe';
} else if (determineCategory($title, $clothingwords)) {
    $category = 'clothing';
} else if (determineCategory($title, $accessorieswords)) {
    $category = 'accessories';
} else {
    $category = 'misc';
}


$check = $dbcon->prepare("SELECT * FROM cashreps WHERE name = :name OR title = :title AND seller = :seller");
$check->bindParam(':name', $name);
$check->bindParam(':seller', $seller);
$check->bindParam(':title', $title);
$check->execute();

if($check->rowCount() == 0) {   

    $sth = $dbcon->prepare("INSERT INTO cashreps (name, title, seller, price_cn, price_us, price_ru, image, shipping_eu, shipping_us, shipping_ru, link, category) VALUES (:name, :title, :seller, :price_cn, :price_us, :price_ru, :image, :shipping_eu, :shipping_us, :shipping_ru, :link, :category)");

    $sth->bindParam(':name', $name);
    $sth->bindParam(':title', $title);
    $sth->bindParam(':seller', $seller);
    $sth->bindParam(':price_cn', $price_cn);
    $sth->bindParam(':price_us', $price_us);
    $sth->bindParam(':price_us', $price_ru);
    $sth->bindParam(':image', $image);
    $sth->bindParam(':shipping_eu', $shipping_eu);
    $sth->bindParam(':shipping_us', $shipping_us);
    $sth->bindParam(':shipping_ru', $shipping_us);
    $sth->bindParam(':link', $link);
    $sth->bindParam(':category', $category);

    $sth->execute();

}

echo "done";

?>