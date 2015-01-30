<?hh

$scores = Vector {53, 65, 99, 85, 42, 12, 72};
$passing_scores = $scores->filter(function($score) {
    return $score > 50;
});
echo "There are " . count($passing_scores) . " passing scores";
