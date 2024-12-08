<?php

/**
* @param array<int> $newAntenna
* @param array<int[]> $antennas
* @param array<int> $bounds
* @return array<int[]>
*/
function calculateAntinodes(array $newAntenna, array $antennas, array $bounds): array
{
    $result = [];
    foreach ($antennas as $antenna) {
        $slope = [$newAntenna[1] - $antenna[1], $newAntenna[0] - $antenna[0]];
        $antinode1 = $newAntenna;
        while (withinBounds($antinode1, $bounds)) {
            $result[] = $antinode1;
            $antinode1 = [$antinode1[0] + $slope[1], $antinode1[1] + $slope[0]];
        }
        $antinode2 = $antenna;
        while (withinBounds($antinode2, $bounds)) {
            $result[] = $antinode2;
            $antinode2 = [$antinode2[0] - $slope[1], $antinode2[1] - $slope[0]];
        }
    }
    return $result;
}

/**
* @param array<int> $point
* @param array<int> $bounds
*/
function withinBounds(array $point, array $bounds): bool
{
    [$x,$y] = $point;
    if ($x < $bounds[0] && $x >= 0
        && $y < $bounds[1] && $y >= 0) {
        return true;
    }
    return false;
}

// $filename = 'src/day8/input.test';
$filename = 'src/day8/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    exit();
}

$bounds = [strlen($lines[0]), count($lines)];

$antinodes = [];
$antennas = [];
foreach ($lines as $yIndex => $line) {
    foreach (str_split($line) as $xIndex => $char) {
        if ($char === ".") {
            continue;
        }
        if (!array_key_exists($char, $antennas)) {
            $antennas[$char] = [] ;
        }
        $newAntinodes = calculateAntinodes([$xIndex, $yIndex], $antennas[$char], $bounds);
        foreach ($newAntinodes as $antinode) {
            if (!in_array($antinode, $antinodes)) {
                $antinodes[] = $antinode;
            }
        }
        $antennas[$char][] = [$xIndex, $yIndex];
    }
}

echo 'Number of Antinodes: ' . count($antinodes) . "\n";
