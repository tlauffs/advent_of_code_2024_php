<?php

$filename = 'src/day01/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}

$left = [];
$right = [];

foreach ($lines as $line) {
    $line = preg_replace('!\s+!', " ", $line);
    if ($line !== null) {
        $line = explode(" ", $line);
        $left[] = (int) $line[0];
        $right[] = (int) $line[1];
    }
}

sort($left);
sort($right);

/**
 * @param array<int> $left
 * @param array<int> $right
 */
function diffrence_score(array $left, array $right): int
{
    $differences = array_map(function ($l, $r) {
        return abs($l - $r);
    }, $left, $right);
    return array_sum($differences);
}

/**
 * @param array<int> $left
 * @param array<int> $right
 */
function similarity_score(array $left, array $right): int
{
    $frequency = array_count_values($right);
    $total = 0;
    foreach ($left as $value) {
        $total += $value * ($frequency[$value] ?? 0);
    }
    return $total;
}

echo("Difference: " . diffrence_score($left, $right) . "\n");
echo("Similarity: " . similarity_score($left, $right) . "\n");

exit();
