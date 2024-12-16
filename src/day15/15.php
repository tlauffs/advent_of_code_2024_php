<?php

function parseMap(array $map): array
{
    $cols = strlen($map[0]);
    $robotPos = [0,0];
    $boxes = [];
    $walls = [];
    for ($row = 0; $row < count($map); $row++) {
        $boxes[$row] = [];
        $walls[$row] = [];
        $mapRow = str_split($map[$row]);
        for ($col = 0; $col < $cols; $col++) {
            $char = $mapRow[$col];
            if ($char === "O") {
                $boxes[$row][] = $col;
                continue;
            }
            if ($char === "#") {
                $walls[$row][] = $col;
                continue;
            }
            if ($char === "@") {
                $robotPos[0] = $row;
                $robotPos[1] = $col;
            }
        }
    }
    return [$robotPos, $boxes, $walls];
}

function robotStep(string $movement, array &$robotPos, array &$boxes, array &$walls): void
{
    $dirRow = 0;
    $dirCol = 0;

    switch ($movement) {
        case '^': $dirRow = -1;
            break;
        case 'v': $dirRow = 1;
            break;
        case '<': $dirCol = -1;
            break;
        case '>': $dirCol = 1;
            break;
    }


    $nextRow = $robotPos[0] + $dirRow;
    $nextCol = $robotPos[1] + $dirCol;

    // Check if wall
    if (in_array($nextCol, $walls[$nextRow] ?? [])) {
        return;
    }

    // Check if there's a box in the next position
    $boxIndex = array_search($nextCol, $boxes[$nextRow] ?? []);
    $pushedBoxes = [];

    // check if boxes can be moved
    $currentBoxRow = $nextRow;
    $currentBoxCol = $nextCol;
    while ($boxIndex !== false) {
        $pushedBoxes[] = [$currentBoxRow, $currentBoxCol];

        $currentBoxRow += $dirRow;
        $currentBoxCol += $dirCol;

        // if wall -> cant push boxes
        if (in_array($currentBoxCol, $walls[$currentBoxRow] ?? [])) {
            $pushedBoxes = [];
            return;
        }

        // Check if there's another box
        $boxIndex = array_search($currentBoxCol, $boxes[$currentBoxRow] ?? []);
    }

    // if there are box that can be pushed
    if ($pushedBoxes !== []) {
        echo "pushing boxes: " . json_encode($pushedBoxes) . "\n";
        $firstbox = $pushedBoxes[0];
        $row = $firstbox[0];
        $col = $firstbox[1];
        // remove first box
        $boxKey = array_search($col, $boxes[$row]);
        if ($boxKey !== false) {
            array_splice($boxes[$row], $boxKey, 1);
        }

        $lastbox = end($pushedBoxes);
        $newRow = $lastbox[0] + $dirRow;
        $newCol = $lastbox[1] + $dirCol;
        if (!isset($boxes[$newRow])) {
            $boxes[$newRow] = [];
        }
        $boxes[$newRow][] = $newCol;
    }

    // move robot
    $robotPos[0] = $nextRow;
    $robotPos[1] = $nextCol;
}

// $file = file_get_contents('src/day15/input.test');
$file = file_get_contents('src/day15/input');

if ($file === false) {
    exit();
}
$input = explode("\n", trim($file));

$emptyRow = array_search("", $input);

$map = array_slice($input, 0, $emptyRow);
$movements = str_split(implode('', array_slice($input, $emptyRow + 1)));

[$robotPos, $boxes, $walls] = parseMap($map);

echo json_encode($robotPos) . "\n" . json_encode($boxes) . "\n" . json_encode($walls) . "\n";
echo json_encode($movements) . "\n" ;

foreach ($movements as $movement) {
    echo "###############\n" . $movement . "\n" ;
    robotStep($movement, $robotPos, $boxes, $walls);
    echo json_encode($robotPos) . "\n" . json_encode($boxes) . "\n" . json_encode($walls) . "\n";
}

// calulate result
$sum = 0;
foreach ($boxes as $col => $row) {
    foreach ($row as $rowValue) {
        $sum += (100 * $col) + $rowValue;
    }
}

echo "Value of box positons: ". $sum . "\n";
