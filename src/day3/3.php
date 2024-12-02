<?php

$filename = 'src/day3/input';
$text = file_get_contents($filename);

$regex = "/mul\(\d{1,3},\d{1,3}\)|do\(\)|don\'t\(\)/";
if (!is_string($text)) {
    exit();
}
preg_match_all($regex, $text, $matches);
$total = 0;
$enabled = true;
foreach ($matches[0] as $match) {
    if ((preg_match("/mul\((\d{1,3}),(\d{1,3})\)/", $match, $numbers) && $enabled)) {
        $total += $numbers[1] * $numbers[2];
    }
    if ($match === "do()") {
        $enabled = true;
    }
    if ($match === "don't()") {
        $enabled = false;
    }
}
echo $total . "\n";
