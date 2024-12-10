<?php

/**
* @param array<bool> $visited
* @param array<bool> $destinations
* @param bool $calculateTrails wether to calucalte the number of diffrent trails from a starting points
*					 or the number of destionations you can reach
*/
function calculateRoutes(int $currentHeight, int $x, int $y, array &$visited, array &$destinations, bool $calculateTrails): int
{
    global $input;
    if ($x < 0 || $y < 0 || $y >= count($input) || $x >= count($input[0])) {
        return 0;
    }
    if ($currentHeight !== $input[$y][$x] || isset($visited["$y,$x"])) {
        return 0;
    }

    if ($currentHeight === 9) {
        if ($calculateTrails || !isset($destinations["$y,$x"])) {
            $destinations["$y,$x"] = true;
            return 1;
        }
        return 0;
    }

    if (!$calculateTrails) {
        $visited["$y,$x"] = true;
    }

    $nextHeight = $currentHeight + 1;
    return(calculateRoutes($nextHeight, $x + 1, $y, $visited, $destinations, $calculateTrails)
           + calculateRoutes($nextHeight, $x - 1, $y, $visited, $destinations, $calculateTrails)
           + calculateRoutes($nextHeight, $x, $y + 1, $visited, $destinations, $calculateTrails)
           + calculateRoutes($nextHeight, $x, $y - 1, $visited, $destinations, $calculateTrails));
}

$filename = 'src/day10/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}

$input = [];
foreach ($lines as $line) {
    $input[] = array_map('intval', (str_split(trim($line))));
}

#part 1
$total = 0;
foreach ($input as $yIndex => $y) {
    foreach ($y as $xIndex => $x) {
        if ($x === 0) {
            $visited = [];
            $destinations = [];
            $total += calculateRoutes($x, $xIndex, $yIndex, $visited, $destinations, false);
        }
    }
}
echo $total . "\n";

#part 2
$total = 0;
foreach ($input as $yIndex => $y) {
    foreach ($y as $xIndex => $x) {
        if ($x === 0) {
            $visited = [];
            $destinations = [];
            $total += calculateRoutes($x, $xIndex, $yIndex, $visited, $destinations, true);
        }
    }
}
echo $total . "\n";
