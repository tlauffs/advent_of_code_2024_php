<?php

$directions = [[1,0],[-1,0],[0,1],[0,-1]];

function floodFill(array $maze, int $row, int $col, array $dir, ?array $prevDir, int $score, array $path, int &$minscore, array &$memo, array &$allPaths): int
{
    global $directions;

    if ($maze[$row][$col] === "#") {
        return PHP_INT_MAX;
    }

    if ($score > $minscore) {
        return PHP_INT_MAX;
    }

    //add to score and path
    if ($prevDir !== null) {
        switch (true) {
            case $dir === $prevDir:
                $score += 1;
                break;
            case abs($dir[0]) === abs($prevDir[0]):
                $score += 2001;
                break;
            case abs($dir[0]) !== abs($prevDir[0]):
                $score += 1001;
                break;
        }
    }

    if ($maze[$row][$col] === "E") {
        // new best path
        if ($score < $minscore) {
            $minscore = $score;
            $allPaths = [$path];
        } elseif ($score === $minscore) {
            $allPaths[] = $path;
        }
        return $score;
    }

    // if already calulated
    if (isset($memo[$row][$col])) {
        // becuase we dont know the future best path and rotating cost up to 1000 we have to add leway
        if ($memo[$row][$col] + 1000 < $score) {
            return PHP_INT_MAX;
        }

    }
    if (!isset($memo[$row][$col]) || $memo[$row][$col] > $score) {
        $memo[$row][$col] = $score;

    }

    return min(
        floodFill($maze, $row + ($directions[0][0]), $col + ($directions[0][1]), $directions[0], $dir, $score, array_merge($path, [[$row, $col]]), $minscore, $memo, $allPaths),
        floodFill($maze, $row + ($directions[1][0]), $col + ($directions[1][1]), $directions[1], $dir, $score, array_merge($path, [[$row, $col]]), $minscore, $memo, $allPaths),
        floodFill($maze, $row + ($directions[2][0]), $col + ($directions[2][1]), $directions[2], $dir, $score, array_merge($path, [[$row, $col]]), $minscore, $memo, $allPaths),
        floodFill($maze, $row + ($directions[3][0]), $col + ($directions[3][1]), $directions[3], $dir, $score, array_merge($path, [[$row, $col]]), $minscore, $memo, $allPaths),
    );
}


// $filename = 'src/day16/input.test';
$filename = 'src/day16/input';
// $filename = 'src/day16/input.test2';

$input = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($input === false) {
    exit();
}
$maze = [];
$startRow = 0;
$startCol = 0;
$dir = [0, 1];
foreach ($input as $row => $line) {
    $maze[$row] = str_split($line);
    foreach ($maze[$row] as $col => $value) {
        if ($value === "S") {
            $startRow = $row;
            $startCol = $col;
        }
    }
}

$minscore = PHP_INT_MAX;
$memo = [];

$path = [];
$path = [];
$allPaths = [];

$result = floodFill($maze, $startRow, $startCol, $dir, null, 0, $path, $minscore, $memo, $allPaths);
echo "Shortest path score (part 1) :" . $result . "\n";

// get all best paths and subpaths
$uniquePoints = [];
$bestPointCount = 1;
foreach ($allPaths as $path) {
    foreach ($path as $point) {
        $pointString = json_encode($point);
        if (!in_array($pointString, $uniquePoints)) {
            $uniquePoints[] = $pointString;
            $bestPointCount++;
        }
    }
}

echo "Number of best seats (part 2) :" . $bestPointCount . "\n";
