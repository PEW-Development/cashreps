<?php

include 'conn.php';

if (!isset($_GET['term'])){
    exit();
}

$dbcon->exec("SET NAMES 'utf8mb4'");


$term = strtolower(filter_var($_GET['term'], FILTER_SANITIZE_STRING) . "*");

// category filter function
function getCategoryFilter() {
    $categories = ['shoe', 'clothing', 'accessories', 'misc'];
    $filters = [];

    foreach($categories as $category) {
        if (isset($_GET[$category])) {
            $filters[] = "category='".$category."'";
        }
    }

    return !empty($filters) ? " AND (".join(" OR ", $filters).")" : "";
}

$categoryFilter = getCategoryFilter();

$priceFilters = "";
$shippingFilters = "";

if(isset($_GET['minprice'])) $priceFilters .= " AND price_us >= " . filter_var($_GET['minprice'], FILTER_SANITIZE_NUMBER_INT);
if(isset($_GET['maxprice'])) $priceFilters .= " AND price_us <= " . filter_var($_GET['maxprice'], FILTER_SANITIZE_NUMBER_INT);

if(isset($_GET['maxshippingus'])) $shippingFilters .= " AND shipping_us <= " . filter_var($_GET['maxshippingus'], FILTER_SANITIZE_NUMBER_INT);
if(isset($_GET['maxshippingeu'])) $shippingFilters .= " AND shipping_eu <= " . filter_var($_GET['maxshippingeu'], FILTER_SANITIZE_NUMBER_INT);

if($_GET['term'] != "") {
    // If :term parameter needs to be in the query
    $query = "SELECT DISTINCT * FROM cashreps WHERE MATCH(name, title) AGAINST(:term IN BOOLEAN MODE) $categoryFilter AND available = 1 $priceFilters $shippingFilters ORDER BY RAND() LIMIT 9";
    $stmt = $dbcon->prepare($query);
    $stmt->bindParam(':term', $term, PDO::PARAM_STR);
} else {
    // If :term parameter does not need to be in the query
    $query = "SELECT DISTINCT * FROM cashreps WHERE available = 1 $categoryFilter $priceFilters $shippingFilters ORDER BY RAND() LIMIT 9";
    $stmt = $dbcon->prepare($query);
}

$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);