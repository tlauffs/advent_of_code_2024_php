<?php

/*
* @param arry<int> $stones
*/
function calcStone(array $stones, int $steps): int
{
    $state = [];
    foreach ($stones as $stone) {
        $state[$stone] =  1;
    }
    for ($step = 0; $step < $steps; $step++) {
        $newState = [];
        foreach ($state as $stone => $count) {
            switch (true) {
                case ($stone === 0):
                    $newState[1] = ($newState[1] ?? 0) + $count;
                    break;
                case (strlen((string)$stone) % 2 === 0):
                    $stoneStr = (string)$stone;
                    $mid = (int)(strlen($stoneStr) / 2);
                    $left = intval(substr($stoneStr, 0, $mid));
                    $right = intval(substr($stoneStr, $mid));
                    $newState[$left] = ($newState[$left] ?? 0) + $count;
                    $newState[$right] = ($newState[$right] ?? 0) + $count;
                    break;
                default:
                    $newStone = (int)$stone * 2024;
                    $newState[$newStone] = ($newState[$newStone] ?? 0) + $count;
                    break;
            }
        }

        $state = $newState;
    }
    return array_sum($state);
}

$filename = 'src/day11/input';
$input = file_get_contents($filename);

if ($input === false) {
    exit("Failed to open input file.\n");
}

$stones = array_map('intval', explode(' ', $input));
echo calcStone($stones, 75) . "\n";
