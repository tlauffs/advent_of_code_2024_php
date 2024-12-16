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
                $boxes[$row][] = [($col * 2),$col * 2 + 1, false];
                continue;
            }
            if ($char === "#") {
                $walls[$row][] = ($col * 2);
                $walls[$row][] = $col * 2 + 1;
                continue;
            }
            if ($char === "@") {
                $robotPos[0] = $row;
                $robotPos[1] = $col * 2;
            }
        }
    }
    return [$robotPos, $boxes, $walls];
}

function moveBoxVertical(&$boxes, &$box, $row, $dirRow, &$walls): bool
{

    foreach ($boxes[$row] as $boxIndex => &$currentBox) {
        if ($currentBox === $box) {
            // remove from current row
            array_splice($boxes[$row], $boxIndex, 1);
            break;
        }
    }
    // set box as moved
    $box[2] = true;

    $nextRow = $row + $dirRow;
    $boxes[$nextRow][] = $box;

    // if wall -> boxes cant be pushed
    if (in_array($box[0], $walls[$nextRow] ?? []) ||
        in_array($box[1], $walls[$nextRow] ?? [])) {
        return false;
    }

    // Check for boxes in the next row
    foreach ($boxes[$nextRow] as &$newBox) {
        if ((in_array($box[0], $newBox) || in_array($box[1], $newBox)) && !$newBox[2]) {
            // recursivly push all boxes
            $result = moveBoxVertical($boxes, $newBox, $nextRow, $dirRow, $walls);
            if ($result === false) {
                return false;
            }

        }
    }
    return true;
}

function robotStep(string $movement, array &$robotPos, array &$boxes, array &$walls): void
{
    $dirRow = 0;
    $dirCol = 0;
    $direction = "";

    switch ($movement) {
        case '^':
            $dirRow = -1;
            $direction = "vertical";
            break;
        case 'v':
            $dirRow = 1;
            $direction = "vertical";
            break;
        case '<':
            $dirCol = -1;
            $direction = "horizontal";
            break;
        case '>':
            $dirCol = 1;
            $direction = "horizontal";
            break;
    }

    $nextRow = $robotPos[0] + $dirRow;
    $nextCol = $robotPos[1] + $dirCol;

    // Check if wall
    if (in_array($nextCol, $walls[$nextRow] ?? [])) {
        return;
    }

    $pushedBoxes = [];
    $currentBoxRow = $nextRow;
    $currentBoxCol = $nextCol;
    if ($direction === "horizontal") {
        while (true) {
            $foundBox = false;
            foreach ($boxes[$currentBoxRow] as $boxIndex => &$box) {
                if (in_array($currentBoxCol, $box)) {
                    $pushedBoxes[] = &$box;
                    $foundBox = true;
                    break;
                }
            }

            $currentBoxCol += 2 * $dirCol;

            // if free space -> all boxes that need to be moved found
            if (!$foundBox) {
                break;
            }


            if (in_array($currentBoxCol, $walls[$currentBoxRow] ?? [])) {
                return;
            }
        }
        // if there are box that can be pushed
        foreach ($pushedBoxes as &$box) {
            $box[0] += $dirCol;
            $box[1] += $dirCol;
        }
    }

    if ($direction === "vertical") {
        $tempBoxes = $boxes;

        // Call the moveBoxVertical function
        foreach ($tempBoxes[$currentBoxRow] as &$box) {
            if (in_array($currentBoxCol, $box)) {
                $movedBoxes = [];
                $canPush = moveBoxVertical($tempBoxes, $box, $currentBoxRow, $dirRow, $walls);
                if ($canPush) {
                    $boxes = $tempBoxes;
                    foreach ($boxes as &$row) {
                        foreach ($row as &$box) {
                            $box[2] = false;
                        }
                    }

                } else {
                    // robot cant move
                    return;
                }
            }
        }
    }

    // move robot
    $robotPos[0] = $nextRow;
    $robotPos[1] = $nextCol;
}

function drawGrid($robotPos, $boxes, $walls, $x, $y)
{
    // ANSI colors
    $robotColor = "\033[38;5;213m";
    $resetColor = "\033[0m";

    $grid = array_fill(0, $y, array_fill(0, $x, ' '));

    $grid[$robotPos[0]][$robotPos[1]] = $robotColor . '@' . $resetColor;

    foreach ($walls as $rowIndex => $wallRow) {
        foreach ($wallRow as $wallCol) {
            $grid[$rowIndex][$wallCol] = '#';
        }
    }

    foreach ($boxes as $boxIndex => $boxRow) {
        foreach ($boxRow as $box) {
            $grid[$boxIndex][$box[0]] =  '[';
            $grid[$boxIndex][$box[1]] =  ']';
        }
    }

    // Draw the grid
    foreach ($grid as $row) {
        echo implode(' ', $row) . "\n";
    }
    echo "\n";
}

// $file = file_get_contents('src/day15/input.test');
// $file = file_get_contents('src/day15/input.test2');
$file = file_get_contents('src/day15/input');

if ($file === false) {
    exit();
}
$input = explode("\n", trim($file));

$emptyRow = array_search("", $input);

$map = array_slice($input, 0, $emptyRow);
$movements = str_split(implode('', array_slice($input, $emptyRow + 1)));


[$robotPos, $boxes, $walls] = parseMap($map);

$y = count($map);
$x = strlen($map[0]) * 2;
echo "\033[2J\033[H";

foreach ($movements as $movement) {
    echo "moving in direction : " . $movement . "\n" ;
    robotStep($movement, $robotPos, $boxes, $walls);
    // echo json_encode($robotPos) . "\n" . json_encode($boxes) . "\n" . json_encode($walls) . "\n";
    drawGrid($robotPos, $boxes, $walls, $x, $y);
    usleep(50000);
    // clear terminal
    echo "\033[2J\033[H";
    // echo "Press Enter to continue...\n";
    // fgets(STDIN);
}

// calulate result
$sum = 0;
foreach ($boxes as $col => $boxes) {
    foreach ($boxes as $box) {
        $sum += (100 * $col) + $box[0];
    }
}

echo "Value of box positons: ". $sum . "\n";
