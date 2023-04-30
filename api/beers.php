<?php
include_once 'api_utils.php';
allowed_methods(['GET']);

$rows = pdo_query_all('SELECT * FROM beer', []);
if ($rows === false)
    internal_error('Unable to fetch beers from database');

$beers = [];
foreach ($rows as $row) {
    $beers[] = Beer::from_data($row)->to_view();
}

echo json_encode($beers);
