<?php
include_once 'api_utils.php';

allowed_methods(['POST']);

$beers = [];
$rawBeers = json_decode(curl_request('https://iutdijon.u-bourgogne.fr/intra/iq/webservices/house.php?function=list'), true);
foreach ($rawBeers as $beer) {
    $beerid = $beer['id'];
    $name = $beer['name'];
    $type = $beer['type'];
    $alcohol = $beer['alcohol'];
    $price = $beer['price'];
    $beers[] = new Beer($beerid, $name, $type, $alcohol, $price, null, null, 0);
}

$dataBeers = pdo_query_all('SELECT * FROM beer', []);
if ($dataBeers === false)
    internal_error('Unable to fetch beers from database');

$existingBeers = [];
foreach ($dataBeers as $dataBeer)
    $existingBeers[] = Beer::from_data($dataBeer);

$beersToAdd = [];
$beersToDelete = [];

foreach ($beers as $beer) {
    $found = false;
    foreach ($existingBeers as $existingBeer) {
        if ($beer->getId() === $existingBeer->getId()) {
            $found = true;
            break;
        }
    }
    if (!$found)
        $beersToAdd[] = $beer;
}

foreach ($existingBeers as $existingBeer) {
    $found = false;
    foreach ($beers as $beer) {
        if ($beer->getId() === $existingBeer->getId()) {
            $found = true;
            break;
        }
    }
    if (!$found)
        $beersToDelete[] = $existingBeer;
}

pdo_begin_transaction();
foreach ($beersToAdd as $beer) {
    pdo_update('INSERT INTO beer (beerid, name, type, alcohol, price) VALUES (?, ?, ?, ?, ?)', [
        $beer->getId(),
        $beer->getName(),
        $beer->getType(),
        $beer->getAlcohol(),
        $beer->getPrice()
    ]);
}

foreach ($beersToDelete as $beer) {
    pdo_update('DELETE FROM beer WHERE beerid = ?', [
        $beer->getId()
    ]);
}
pdo_commit();
