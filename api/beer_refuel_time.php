<?php
include_once 'api_utils.php';

allowed_methods(['GET']);

$beer_id = verify_number(mandatory_get_param('id'));

echo curl_request('https://iutdijon.u-bourgogne.fr/intra/iq/webservices/house.php?function=delivery&bierid=' . $beer_id);
