<?php
include_once 'api_utils.php';

allowed_methods(['GET', 'PUT']);

if (is_put()) {
    $beer = Beer::from_view();
    pdo_update('UPDATE beer SET description = ?, imagepath = ?, stock = ? WHERE beerid = ?', [
        $beer->getDescription(),
        $beer->getImagePath(),
        $beer->getStock(),
        $beer->getId()
    ]);
    exit();
}

if (is_get()) {
    $beer_id = verify_number(mandatory_get_param('id'));
    $data = pdo_query_single('SELECT * FROM beer WHERE beerid = ?', [$beer_id]);
    if ($data === false)
        not_found();
    $beer = Beer::from_data($data);
    echo json_encode($beer->to_view());
    exit();
}
