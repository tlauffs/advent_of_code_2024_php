<?php

/**
 * Process one Group of Letters
 * @param int   $r
 * @param int   $c
 * @param array<array<string>> $grid
 * @param array<array<bool>> &$visited
 * @return array<int>
 */
function calcFloodFillCost(int $r, int $c, array $grid, array &$visited): array
{
    $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
    $rows = count($grid);
    $cols = count($grid[0]);
    $value = $grid[$r][$c];

    $group = [];
    $queue = [[$r, $c]];
    $visited[$r][$c] = true;

    $perimeter = 0;
    $sides = 0;
    $area = 1;

    while (!empty($queue)) {
        list($curR, $curC) = array_shift($queue);

        // Add cell to the group
        $group[] = [$curR, $curC];

        // Explore neighbors
        $matchingNeighbors = [];
        foreach ($directions as $dir) {
            $newR = $curR + $dir[0];
            $newC = $curC + $dir[1];

            // Check boundaries and visit status
            if ($newR >= 0 && $newR < $rows && $newC >= 0 && $newC < $cols
                && !$visited[$newR][$newC] && $grid[$newR][$newC] === $value) {
                $visited[$newR][$newC] = true;
                $queue[] = [$newR, $newC];
                $area++;

            }
            // Get neighbors
            if ($newR >= 0 && $newR < $rows && $newC >= 0 && $newC < $cols && $grid[$newR][$newC] === $value) {
                $matchingNeighbors["{$dir[0]},{$dir[1]}"] = true;
            }

            // add perimeter
            if ($newR < 0 || $newR >= $rows || $newC < 0 || $newC >= $cols || $grid[$newR][$newC] !== $value) {
                $perimeter++;
            }
        }

        // Get diagonal neighbors
        $diagonals = [[-1, -1], [1, 1], [1, -1], [-1, 1]];
        foreach ($diagonals as $diag) {
            $newR = $curR + $diag[0];
            $newC = $curC + $diag[1];
            if ($newR >= 0 && $newR < $rows && $newC >= 0 && $newC < $cols && $grid[$newR][$newC] === $value) {
                $matchingNeighbors["{$diag[0]},{$diag[1]}"] = true;
            }
        }

        // Calc sides by calculating corners
        // echo 'outward' . "\n";
        if (!isset($matchingNeighbors["0,-1"]) && !isset($matchingNeighbors["-1,0"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
        if (!isset($matchingNeighbors["1,0"]) && !isset($matchingNeighbors["0,-1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
        if (!isset($matchingNeighbors["-1,0"]) && !isset($matchingNeighbors["0,1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
        if (!isset($matchingNeighbors["1,0"]) && !isset($matchingNeighbors["0,1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }

        // echo 'inward' . "\n";
        // inward corners
        if (isset($matchingNeighbors["1,0"]) && isset($matchingNeighbors["0,1"]) &&  !isset($matchingNeighbors["1,1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
        if (isset($matchingNeighbors["-1,0"]) && isset($matchingNeighbors["0,1"]) &&  !isset($matchingNeighbors["-1,1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
        if (isset($matchingNeighbors["1,0"]) && isset($matchingNeighbors["0,-1"]) &&  !isset($matchingNeighbors["1,-1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
        if (isset($matchingNeighbors["-1,0"]) && isset($matchingNeighbors["0,-1"]) &&  !isset($matchingNeighbors["-1,-1"])) {
            // echo $value . " : " . $curC . "  " . $curR . "\n";
            $sides += 1;
        }
    }

    echo $area . "   " . $perimeter . "   " . $sides . "\n";
    return [$area * $perimeter, $area * $sides];
}

// $filename = 'src/day12/input.test';
$filename = 'src/day12/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}

$input = [];
foreach ($lines as $line) {
    $input[] = str_split($line);
}

$part1Cost = 0;
$part2Cost = 0;
$rows = count($input);
$cols = count($input[0]);
$visited = array_fill(0, $rows, array_fill(0, $cols, false));

for ($r = 0; $r < $rows; $r++) {
    for ($c = 0; $c < $cols; $c++) {
        if (!$visited[$r][$c]) {
            $result = calcFloodFillCost($r, $c, $input, $visited);
            $part1Cost += $result[0];
            $part2Cost += $result[1];
        }
    }
}

echo $part1Cost . "\n" . $part2Cost , "\n";
