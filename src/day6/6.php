<?php

function parseInput($input)
{
    $map = [];
    $guardPosition = null;
    $guardDirection = null;

    $directions = ['^' => 'up', '>' => 'right', 'v' => 'down', '<' => 'left'];

    foreach (explode("\n", $input) as $y => $line) {
        $row = str_split($line);
        foreach ($row as $x => $char) {
            if (isset($directions[$char])) {
                $guardPosition = [$x, $y];
                $guardDirection = $directions[$char];
                $row[$x] = '.'; // Replace guard with empty space
            }
        }
        $map[] = $row;
    }

    return [$map, $guardPosition, $guardDirection];
}

function moveGuard(&$position, $direction)
{
    $movements = [
        'up' => [0, -1],
        'right' => [1, 0],
        'down' => [0, 1],
        'left' => [-1, 0],
    ];

    $position[0] += $movements[$direction][0];
    $position[1] += $movements[$direction][1];
}

function turnRight($direction)
{
    $order = ['up', 'right', 'down', 'left'];
    $index = array_search($direction, $order);
    return $order[($index + 1) % 4];
}

function detectLoopOrEscape($map, $guardPosition, $guardDirection)
{
    $rows = count($map);
    $cols = count($map[0]);

    $visited = [];
    $visitedCount = 0; // Counter for visited fields

    while (true) {
        $nextPosition = $guardPosition;
        moveGuard($nextPosition, $guardDirection);

        // Check if guard is out of bounds
        if ($nextPosition[0] < 0 || $nextPosition[0] >= $cols ||
            $nextPosition[1] < 0 || $nextPosition[1] >= $rows) {
            return $visitedCount; // Return count of fields visited before escape
        }

        // Check if next position is an obstacle
        if ($map[$nextPosition[1]][$nextPosition[0]] === '#') {
            $guardDirection = turnRight($guardDirection);
        } else {
            $guardPosition = $nextPosition;
            $key = implode(",", $guardPosition) . ",$guardDirection";

            if (isset($visited[$key])) {
                return "LOOP"; // Loop detected
            }

            $visited[$key] = true;
            $visitedCount++; // Increment visited field count
        }
    }
}


function findObstructionPositions($map, $guardPosition, $guardDirection)
{
    $rows = count($map);
    $cols = count($map[0]);

    $possiblePositions = 0;

    for ($y = 0; $y < $rows; $y++) {
        for ($x = 0; $x < $cols; $x++) {
            // Skip non-empty spaces and the guard's starting position
            if ($map[$y][$x] !== '.' || [$x, $y] == $guardPosition) {
                continue;
            }

            // Place obstruction temporarily
            $map[$y][$x] = '#';

            // Check if obstruction causes a loop
            if (detectLoopOrEscape($map, $guardPosition, $guardDirection) === "LOOP") {
                $possiblePositions++;
            }

            // Remove obstruction
            $map[$y][$x] = '.';
        }
    }

    return $possiblePositions;
}

function solvePuzzle($input)
{
    [$map, $guardPosition, $guardDirection] = parseInput($input);
    $part1Result = detectLoopOrEscape($map, $guardPosition, $guardDirection);
    $part2Result = findObstructionPositions($map, $guardPosition, $guardDirection);
    return [
        "part1" => $part1Result,
        "part2" => $part2Result,
    ];
}

// Read input and solve the puzzle
$filename = 'src/day6/input';
$input = trim(file_get_contents($filename));
$result = solvePuzzle($input);

echo "Part 1: " . $result['part1'] . "\n";
echo "Part 2: " . $result['part2'] . "\n";
