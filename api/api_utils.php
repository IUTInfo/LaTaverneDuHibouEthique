<?php

/**
 * @var null | PDO $pdo
 */

use JetBrains\PhpStorm\NoReturn;

require_once '../models/Beer.php';
require_once '../models/Order.php';

#region Inputs

/**
 * Get the JSON content sent by the client
 *
 * @param string[] $mandatory_fields The fields that must be present in the JSON (otherwise a 400 error is returned)
 * @param string[] $optional_fields The optional fields that can be present in the JSON
 * @return array The JSON content in the same order as the mandatory fields concatenated with the optional fields
 */
function json_body(array $mandatory_fields = [], array $optional_fields = []): array {
    $content = file_get_contents('php://input');
    $decoded = $content === false || strlen($content) === 0 ? [] : json_decode($content, true);
    if ($decoded === null)
        bad_request();

    $missing_fields = [];
    $sorted_fields = [];
    foreach ($mandatory_fields as $field) {
        if (!array_key_exists($field, $decoded))
            $missing_fields[] = $field;
        else {
            $sorted_fields[] = $decoded[$field];
            unset($decoded[$field]);
        }
    }

    if (count($missing_fields) > 0)
        bad_request('Missing field in JSON: ' . implode(', ', $missing_fields));

    foreach ($optional_fields as $field) {
        if (!array_key_exists($field, $decoded))
            $sorted_fields[] = null;
        else
            $sorted_fields[] = $decoded[$field];
    }

    return $sorted_fields;
}

/**
 * Get the value of a GET parameter
 * If the parameter is not present, a 400 error is returned
 * @param string $name The name of the parameter
 * @return string The value of the parameter
 */
function mandatory_get_param(string $name): string
{
    if (isset($_GET[$name]))
        return $_GET[$name];
    bad_request("Missing GET parameter: " . $name);
}

/**
 * Get the GET parameters given by the client
 * @param string[] $mandatory_fields The fields that must be present in the GET parameters (otherwise a 400 error is returned)
 * @param string[] $optional_fields The optional fields that can be present in the GET parameters
 * @return array The content in the same order as the mandatory fields concatenated with the optional fields
 */
function get_params(array $mandatory_fields, array $optional_fields): array
{
    $arr = json_decode(json_encode($_GET), true);

    $missing_fields = [];
    $sorted_fields = [];
    foreach ($mandatory_fields as $field) {
        if (!array_key_exists($field, $arr))
            $missing_fields[] = $field;
        else {
            $sorted_fields[] = $arr[$field];
            unset($_GET[$field]);
        }
    }

    if (count($missing_fields) > 0)
        bad_request('Missing field in GET: ' . implode(', ', $missing_fields));

    foreach ($optional_fields as $field) {
        if (!array_key_exists($field, $arr))
            $sorted_fields[] = null;
        else
            $sorted_fields[] = $arr[$field];
    }

    return $sorted_fields;
}

/**
 * Get the value of a POST parameter
 * If the parameter is not present, a 400 error is returned
 * @param string $name The name of the parameter
 * @return string The value of the parameter
 */
function mandatory_post_param(string $name): string
{
    if (isset($_POST[$name]))
        return $_POST[$name];
    bad_request("Missing POST parameter: " . $name);
}

#endregion

#region Responses
json_response();

/**
 * Add a new header to the response to tell the client that the response is JSON
 * @return void
 */
function json_response(): void {
    header('Content-Type: application/json; charset=utf-8');
}

/**
 * Return the status code 201 (created) with the given content
 * @param mixed|null $content The content to send to the client
 * @return void
 */
#[NoReturn] function created(mixed $content = null): void {
    http_response_code(201);
    if ($content !== null)
        echo $content;
    exit();
}

/**
 * Return the status code 204 (no content)
 * @return void
 */
#[NoReturn] function no_content(): void {
    http_response_code(204);
    exit();
}

/**
 * Return the status code 404 (not found)
 * @return void
 */
#[NoReturn] function not_found(): void {
    http_response_code(404);
    exit();
}

/**
 * Return the status code 403 (forbidden)
 * @return void
 */
#[NoReturn] function forbidden(): void {
    http_response_code(403);
    exit();
}

/**
 * Send an error to the client with the given status code and reason
 * @param int $status_code The status code to send
 * @param ?string $reason The reason of the error
 * @return void
 */
#[NoReturn] function error(int $status_code, ?string $reason): void {
    http_response_code($status_code);
    echo json_encode(['status_code' => $status_code, 'error_message' => $reason]);
    exit();

}

/**
 * Return the status code 400 (bad request) with the given reason
 * @param string|null $reason The reason of the bad request
 * @return void
 */
#[NoReturn] function bad_request(?string $reason = null): void
{
    error(400, $reason);
}

/**
 * Return the status code 500 (internal error) with the given reason
 * @param string|null $reason The reason of the internal error
 * @return void
 */
#[NoReturn] function internal_error(?string $reason = null): void
{
    error(500, $reason);
}

#endregion

#region Methods
$method = strtoupper($_SERVER['REQUEST_METHOD']);

/**
 * Ensure the used method is one of the given methods, otherwise return a 405 error to the client
 * @param string[] $allowed_methods The allowed methods (GET, POST, PUT, DELETE, ...)
 * @return void
 */
function allowed_methods(array $allowed_methods): void
{
    $i = 0;
    $max_i = count($allowed_methods);
    while ($i < $max_i) {
        $allowed_methods[$i] = strtoupper($allowed_methods[$i]);
        $i++;
    }

    global $method;
    if (!in_array($method, $allowed_methods)) {
        error(405, 'Allowed methods: ' . implode(', ', $allowed_methods));
    }
}

/**
 * Check if the request method is GET
 * @return bool True if the request method is GET, false otherwise
 */
function is_get(): bool
{
    global $method;
    return $method === 'GET';
}

/**
 * Check if the request method is POST
 * @return bool True if the request method is POST, false otherwise
 */
function is_post(): bool
{
    global $method;
    return $method === 'POST';
}

/**
 * Check if the request method is PUT
 * @return bool True if the request method is PUT, false otherwise
 */
function is_put(): bool
{
    global $method;
    return $method === 'PUT';
}

/**
 * Check if the request method is DELETE
 * @return bool True if the request method is DELETE, false otherwise
 */
function is_delete(): bool
{
    global $method;
    return $method === 'DELETE';
}

#endregion

#region Database
$pdo = null;

/**
 * Initialize the PDO connection, this function is not meant to be used outside of this file (it's private function)
 * @return void
 */
function init_pdo(): void
{
    global $pdo;
    if ($pdo !== null)
        return;

    global $host, $dbname, $user, $password;
    include_once "../config.php";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    catch (PDOException $e) {
        internal_error($e->errorInfo[2]);
    }
}

$is_transaction_pending = false;

/**
 * Begin a transaction, after that, you will need to commit the transaction with the pdo_commit function
 * @return void
 */
function pdo_begin_transaction(): void {
    global $pdo, $is_transaction_pending;
    init_pdo();

    $pdo->beginTransaction();
    $is_transaction_pending = true;
}

/**
 * Commit the current transaction
 * @return void
 */
function pdo_commit(): void {
    global $pdo, $is_transaction_pending;

    if (!$is_transaction_pending)
        internal_error('You tired to commit a transaction but no transactions was begun.');

    if (!$pdo->commit())
        internal_error('An error occurred while committing the transaction');
}

/**
 * Fetch all the rows of the given query
 * @param string $query The SQL query
 * @param array $params The SQL params for the query
 * @return false|array FALSE on failure, ARRAY of rows on success
 */
function pdo_query_all(string $query, array $params): false|array
{
    global $pdo;
    init_pdo();

    try {
        $stmt = $pdo->prepare($query);
        if ($stmt->execute($params) === false)
            internal_error("Error while calling the database");
    }
    catch (PDOException $e) {
        internal_error($e->errorInfo[2]);
    }

    return $stmt->fetchAll();
}

/**
 * Fetch the first row of the given query
 * It will return an internal error to the client if more than one row is found
 * @param string $query The SQL query
 * @param array $params The SQL params for the query
 * @return false|array|null FALSE on failure, NULL if no result was found, ARRAY if one result was found
 */
function pdo_query_single(string $query, array $params): false|null|array
{
    $rs = pdo_query_all($query, $params);
    if ($rs === false)
        return false;

    $rows = count($rs);
    if ($rows > 1)
        internal_error("Wrong result on single query selection");

    return $rows === 0 ? null : $rs[0];
}

/**
 * Execute the given query
 * @param string $query The SQL query
 * @param array $params The SQL params for the query
 * @return array The number of affected rows and the last inserted id as [amount of affected rows, last insert id]
 */
function pdo_update(string $query, array $params): array
{
    global $pdo;
    init_pdo();

    $paramsLength = count($params);
    for ($i = 0; $i < $paramsLength; ++$i) {
        $param = $params[$i];
        if (gettype($param) === 'boolean')
            $params[$i] = $param ? 1 : 0;
    }

    $stmt = $pdo->prepare($query);
    if ($stmt->execute($params) === false)
        internal_error('Error while calling the database');

    return [$stmt->rowCount(), $pdo->lastInsertId()];
}

#endregion

#region User

/**
 * Ensure the user is authenticated, otherwise return a 401 error to the client
 * @return void
 */
function require_auth(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['userid']))
        return;

    // Try to authenticate with cookies
    if (isset($_COOKIE['tasksteptoken'])) {
        $token = urldecode($_COOKIE['tasksteptoken']);

        $existing_user = pdo_query_single('SELECT user_id FROM user WHERE BINARY token = ? AND disable_date IS NULL', [$token]);
        if ($existing_user !== null) {
            $_SESSION['userid'] = $existing_user['user_id'];
            update_auth_cookies($token);
            return;
        }
    }

    error(401, 'You need to be logged to do this action');
}

/**
 * Update (or renew) the authentication cookies
 * @param ?string $token The user token to set
 * @return void
 */
function update_auth_cookies(?string $token): void
{
    setcookie('tasksteptoken', urlencode($token ?? ''), [
        'expires' => $token == null ? -1 : time()+60*60*24*30*11,
        'httponly' => true,
        'samesite' => 'strict'
    ]);
}

/**
 * Get the id of the logged user (check if the user is logged first)
 * @return int The id of the logged user
 */
function get_logged_userid(): int
{
    require_auth();
    return $_SESSION['userid'];
}

/**
 * Fetch the logged user (check if the user is logged first)
 * @return User The logged user
 */
function get_logged_user(): User
{
    require_auth();
    $id = $_SESSION['userid'];
    $rs = pdo_query_single('SELECT user_name, password, theme FROM user WHERE user_id = ?', [$id]);
    if ($rs === false || $rs === null)
        internal_error('Unable to fetch current user');

    return new User($id, $rs['user_name'], $rs['password'], $rs['theme']);
}

#endregion

#region Verify

/**
 * Check and process a give text
 * @param mixed $text The text to process
 * @param int $min_length The inclusive minimum length of the text
 * @param int $max_length The inclusive maximum length of the text
 * @param bool $allow_null If true, null is allowed
 * @param bool $allow_empty If true, empty string is allowed
 * @param bool $convert_to_null_if_empty If true, empty string will be converted to null
 * @param bool $trim If true, the text will be trimmed
 * @return string|null The processed text
 */
function verify_text(mixed $text, int $min_length = -1, int $max_length = -1, bool $allow_null = false, bool $allow_empty = false, bool $convert_to_null_if_empty = true, bool $trim = true): string|null
{
    if ($convert_to_null_if_empty && $text === '')
        $text = null;

    if ($text === null && !$allow_null)
        bad_request('Null text');

    if ($text === null)
        return null;

    if (gettype($text) !== 'string')
        bad_request('Invalid text');

    if ($trim)
        $text = trim($text);

    if ($text === '' && !$allow_empty)
        bad_request('Empty text');

    if ($min_length !== -1 && strlen($text) < $min_length)
        bad_request('Text too short');

    if ($max_length !== -1 && strlen($text) > $max_length)
        bad_request('Text too long');

    return $text;
}

/**
 * Check and process a given color
 * The color must be an integer between 0 and 16777215
 * @param mixed $color The color to process
 * @return int The processed color
 */
function verify_color(mixed $color): int
{
    if (gettype($color) === 'string') {
        if (preg_match('/\d+/', $color) === 1)
            $color = intval($color);
        else
            bad_request('Invalid color');
    }

    if (gettype($color) !== 'integer')
        bad_request('Invalid color');

    if ($color < 0 || $color > 16777215)
        bad_request('Invalid color');

    return $color;
}

/**
 * Check and process a given number
 * @param mixed $number The number to process
 * @param bool $must_be_positive If true, the number must be positive
 * @param bool $allow_zero If true, zero is allowed
 * @param bool $allow_null If true, null is allowed
 * @param ?int $min_value The minimum value the number should be (null to not check)
 * @param ?int $max_value The maximum value the number should be (null to not check)
 * @return ?int The processed number
 */
function verify_number(mixed $number, bool $must_be_positive = true, bool $allow_zero = false, bool $allow_null = false, ?int $min_value = null, ?int $max_value = null): ?int
{
    if (gettype($number) === 'string') {
        if (preg_match('/\d+/', $number) === 1)
            $number = intval($number);
        else if (strlen($number) !== 0)
            bad_request('Number cannot be empty: ' . $number);
        else
            $number = null;
    }

    if ($number === null && !$allow_null)
        bad_request('Number cannot be null: ' . $number);

    if ($number !== null) {
        if (gettype($number) !== 'integer')
            bad_request('Number is not an integer: ' . $number);

        if ($number < 0 && $must_be_positive)
            bad_request('Number must be positive: ' . $number);

        if ($number === 0 && !$allow_zero)
            bad_request('Number cannot be zero: ' . $number);

        if ($min_value !== null && $number < $min_value)
            bad_request('Number is too small: ' . $number);

        if ($max_value !== null && $number > $max_value)
            bad_request('Number is too big: ' . $number);
    }

    return $number;
}

/**
 * Verify a date input is in the format YYYY-MM-DD with valid values
 * @param mixed $raw_datetime The date to check
 * @param bool $with_time TRUE to have the hh:mm:ss.nnnnnn part, FALSE to just have YYYY-MM-DD
 * @param bool $allowEmpty TRUE to return null if the raw_datetime is empty or null, FALSE to raise a bad request if null or empty raw_datetime
 * @return ?string The date in the format YYYY-MM-DD hh:mm:ss.nnnnnn depending on "with_time"
 */
function verify_date(mixed $raw_datetime, bool $with_time = true, bool $remove_time = true, bool $allowEmpty = false): ?string {
    if ($allowEmpty && $raw_datetime === null || $raw_datetime === '')
        return null;

    if (gettype($raw_datetime) !== 'string')
        bad_request('Bad date format: ' . $raw_datetime);

    if (!$with_time && $remove_time && strlen($raw_datetime) > 10)
        $raw_datetime = substr($raw_datetime, 0, 10);

    $regex = '/^\d{4}-\d{2}-\d{2}$/';
    if ($with_time)
        $regex = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{6}$/';

    if (preg_match($regex, $raw_datetime) !== 1)
        bad_request('Bad date format: ' . $raw_datetime);

    if ($with_time)
        list($date, $time) = explode(' ', $raw_datetime);
    else
        $date = $raw_datetime;

    list($year, $month, $day) = explode('-', $date);

    $year = intval($year);
    $month = intval($month);
    $day = intval($day);

    if ($year < 1970 || $year > 2037)
        bad_request('Date year is out of range');

    if ($month < 1 || $month > 12)
        bad_request('Date month is out of range');

    $max_day = [
        1 => 31,
        2 => 27,
        3 => 31,
        4 => 30,
        5 => 31,
        6 => 30,
        7 => 31,
        8 => 31,
        9 => 30,
        10 => 31,
        11 => 30,
        12 => 31
    ];
    if ($year % 400 === 0 || ($year % 4 === 0 && $year !== 100))
        $max_day[2] = 28;

    if ($day < 1 || $day > $max_day[$month])
        bad_request('Date day is out of range');

    if ($with_time) {
        list($hour, $minutes, $sec_and_nano) = explode(':', $time);
        list($seconds, $nano) = explode('.', $sec_and_nano);

        $hour = intval($hour);
        $minutes = intval($minutes);
        $seconds = intval($seconds);
        $nano = intval($nano);

        if ($hour > 23)
            bad_request('Date hour is out of range');

        if ($minutes > 59)
            bad_request('Date minute is out of range');

        if ($seconds > 59)
            bad_request('Date second is out of range');
    }

    return $date;
}

/**
 * @param mixed $value
 * @param bool $allow_null
 * @return bool|null
 */
function verify_bool(mixed $value, bool $allow_null = false): bool|null
{
    if ($value === null && !$allow_null)
        bad_request('Invalid boolean');

    if (gettype($value) === 'string') {
        if ($value === 'true')
            return true;
        if ($value === 'false')
            return false;
    }

    if (gettype($value) === 'integer') {
        if ($value === 1)
            return true;
        if ($value === 0)
            return false;
    }

    if (gettype($value) !== 'boolean')
        bad_request('Invalid boolean');

    return $value;
}

#endregion

function curl_request(string $url, string $method = 'GET', array $headers = [], string $body = '', bool $return_headers = false): string
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, $return_headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    $result = curl_exec($ch);

    if ($result === false)
        internal_error('Curl error: ' . curl_error($ch));

    curl_close($ch);

    return $result;
}

#[NoReturn] function handleExceptions(Throwable $ex): void
{
    internal_error($ex->__toString());
}

set_exception_handler('handleExceptions');
