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
    $mark = $beer['mark'];
    $beers[] = new Beer($beerid, $name, $type, $alcohol, $price, $mark, null, null, 0);
}

$dataBeers = pdo_query_all('SELECT * FROM beer', []);
if ($dataBeers === false)
    internal_error('Unable to fetch beers from database');

$existingBeers = [];
foreach ($dataBeers as $dataBeer)
    $existingBeers[] = Beer::from_data($dataBeer);

$beersToAdd = [];
$beersToDelete = [];
$beersToUpdate = [];

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

foreach ($beers as $beer) {
    foreach ($existingBeers as $existingBeer) {
        if ($beer->getId() === $existingBeer->getId()) {
            if ($beer->getName() !== $existingBeer->getName() ||
                $beer->getType() !== $existingBeer->getType() ||
                $beer->getAlcohol() !== $existingBeer->getAlcohol() ||
                $beer->getPrice() !== $existingBeer->getPrice() ||
                $beer->getMark() !== $existingBeer->getMark()) {
                $beersToUpdate[] = $beer;
            }
            break;
        }
    }
}

pdo_begin_transaction();
foreach ($beersToAdd as $beer) {
    pdo_update('INSERT INTO beer (beerid, name, type, alcohol, price, mark) VALUES (?, ?, ?, ?, ?, ?)', [
        $beer->getId(),
        $beer->getName(),
        $beer->getType(),
        $beer->getAlcohol(),
        $beer->getPrice(),
        $beer->getMark()
    ]);
}

foreach ($beersToDelete as $beer) {
    pdo_update('DELETE FROM beer WHERE beerid = ?', [
        $beer->getId()
    ]);
}

foreach ($beersToUpdate as $beer) {
    pdo_update('UPDATE beer SET name = ?, type = ?, alcohol = ?, price = ?, mark = ? WHERE beerid = ?', [
        $beer->getName(),
        $beer->getType(),
        $beer->getAlcohol(),
        $beer->getPrice(),
        $beer->getMark(),
        $beer->getId()
    ]);
}
pdo_commit();
