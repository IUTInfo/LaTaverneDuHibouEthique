<?php
include_once 'api_utils.php';

allowed_methods(['GET', 'POST']);

if (is_get()) {
    $order_id = verify_number(mandatory_get_param('id'));
    $data = pdo_query_all('SELECT * FROM orders LEFT JOIN order_beer ON orders.orderid = order_beer.orderid WHERE orders.orderid = ?', [$order_id]);

    if ($data === false)
        internal_error('Unable to fetch order from database');

    if (count($data) === 0)
        not_found();

    $order = Order::from_data($data[0]);
    $beers = [];
    foreach ($data as $row)
        $beers[$row['beerid']] = $row['amount'];

    $order->setBeers($beers);

    echo json_encode($order->to_view());
    exit();
}

if (is_post()) {
    $order = Order::from_view();

    pdo_begin_transaction();
    foreach ($order->getBeers() as $beer => $amount) {
        $beer = verify_number($beer);
        $amount = verify_number($amount);

        $data = pdo_query_single('SELECT stock FROM beer WHERE beerid = ?', [$beer]);
        if ($data === false)
            internal_error('Unable to fetch beer from database');

        if ($data['stock'] < $amount)
            bad_request('Not enough stock for beer ' . $beer);
    }

    list($_, $id) = pdo_update('INSERT INTO orders (firstname, lastname, pigeonnumber, address) VALUES (?, ?, ?, ?)', [
        $order->getFirstName(),
        $order->getLastName(),
        $order->getPigeonNumber(),
        $order->getAddress()
    ]);

    foreach ($order->getBeers() as $beer => $amount) {
        pdo_update('INSERT INTO order_beer (orderid, beerid, amount) VALUES (?, ?, ?)', [
            $id,
            $beer,
            $amount
        ]);
    }

    foreach ($order->getBeers() as $beer => $amount) {
        pdo_update('UPDATE beer SET stock = stock - ? WHERE beerid = ?', [
            $amount,
            $beer
        ]);
    }
    pdo_commit();

    exit();
}
