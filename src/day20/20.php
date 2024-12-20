<?php

memory_reset_peak_usage();
$start_time = microtime(true);

// $filename = 'src/day20/input.test';
$filename = 'src/day20/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    echo "Failed to read input file.\n";
    return;
}

$end = [];
$grid = [];
// get  end
foreach ($lines as $col => $line) {
    $grid[] = str_split($line);
    foreach ($grid[$col] as $row => $char) {
        if ($char === "E") {
            $end = [$col, $row];
        }
    }
}
if (count($end) === 0) {
    exit();
}

// build racepath
$racePath = [];
$racePath[$end[0]][$end[1]] = 0; // pos-x, pos-y -> distance to end
$distanceToEnd = 0;
$dir = [[1,0],[0,1],[-1,0],[0,-1]];
$position = $end;
$prevPos = null;
$visited = [];


while (true) {
    foreach ($dir as $d) {
        $nextPos = [$position[0] + $d[0],$position[1] + $d[1]];
        // dont move backwards
        if ($prevPos && $prevPos === $nextPos) {
            continue;
        }
        if ($grid[$nextPos[0]][$nextPos[1]] === 'S') {
            $distanceToEnd++;
            $racePath[$nextPos[0]][$nextPos[1]] = $distanceToEnd;
            break 2;
        }
        if ($grid[$nextPos[0]][$nextPos[1]] === '.') {
            $distanceToEnd++;
            $prevPos = $position;
            $position = $nextPos;
            $racePath[$nextPos[0]][$nextPos[1]] = $distanceToEnd;
            break;
        }
    }
}


//check shortcuts
// part 1
$numberOfShortcuts = 0;
foreach ($racePath as $col => $raceRow) {
    foreach ($raceRow as $row => $distance) {
        foreach ($dir as $d) {
            $distanceBeforeShortcut = $racePath[$col][$row];
            $shortcutCol = $col + ($d[0] * 2);
            $shortcutRow = $row + ($d[1] * 2);
            // valid shorcut
            if (isset($racePath[$shortcutCol][$shortcutRow])) {
                $distanceAfterShortcut = $racePath[$shortcutCol][$shortcutRow];
                // 2 picoseconds to jump the wall
                $distanceSaved = $distanceBeforeShortcut - $distanceAfterShortcut - 2;
                if ($distanceSaved >= 100) {
                    $numberOfShortcuts++;
                }
            }
        }
    }
}

// part 2
$numberOfShortcutsPart2 = 0;
foreach ($racePath as $col => $raceRow) {
    foreach ($raceRow as $row => $distance) {
        for ($dx = -20; $dx <= 20; $dx++) {
            for ($dy = -20; $dy <= 20; $dy++) {

                if ($dx === 0 && $dy === 0) {
                    continue;
                }

                // Manhattan distance > 20 -> shortcut takes more then 20 picoseconds
                if (abs($dx) + abs($dy) > 20) {
                    continue;
                }

                $shortcutCol = $col + $dx;
                $shortcutRow = $row + $dy;

                if (!(isset($racePath[$shortcutCol][$shortcutRow]))) {
                    continue;
                }

                $distanceBeforeShortcut = $racePath[$col][$row];
                $distanceAfterShortcut = $racePath[$shortcutCol][$shortcutRow];
                $distanceSaved = $distanceBeforeShortcut - $distanceAfterShortcut - abs($dx) - abs($dy);
                if ($distanceSaved >= 100) {
                    $numberOfShortcutsPart2++;
                }
            }
        }
    }
}





echo "# of 2 Picosecond shortcuts that save 100 picoseconds or more (part1): {$numberOfShortcuts}\n";
echo "# of <=20 Picosecond shortcuts that save 100 picoseconds or more (part2): {$numberOfShortcutsPart2}\n";
echo "Execution time: " . round(microtime(true) - $start_time, 4) . " seconds\n";
echo "   Peak memory: " . round(memory_get_peak_usage() / pow(2, 20), 4) . " MiB\n\n";
