<?hh
function halve($value) {
    return $value / 2;
}

$value = Vector {7, 10, 0, 5, 18};
$modified = $value->map('halve');

var_dump($modified);
