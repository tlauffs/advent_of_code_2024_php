<?php

function bfs(array $start, array $end, array &$visited): int
{
    $rows = $end[0];
    $cols = $end[1];
    $directions = [[0, 1], [1, 0], [0, -1], [-1, 0]];

    // [y, x, steps]
    $queue = [[$start[0], $start[1], 0]];
    $visited[$start[0]][$start[1]] = true;

    while (!empty($queue)) {
        [$y, $x, $steps] = array_shift($queue);

        if ($y == $end[0] && $x == $end[1]) {
            return $steps;
        }

        foreach ($directions as [$dy, $dx]) {
            $ny = $y + $dy;
            $nx = $x + $dx;

            if (
                $ny >= 0 && $ny <= $rows && $nx >= 0 && $nx <= $cols
                && empty($visited[$ny][$nx])
            ) {
                $queue[] = [$ny, $nx, $steps + 1];
                $visited[$ny][$nx] = true;
            }
        }
    }

    return -1;
}

memory_reset_peak_usage();
$start_time = microtime(true);

// $filename = 'src/day18/input.test';
$filename = 'src/day18/input';

$input = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($input === false) {
    exit();
}

$visited = [];
$start = [0, 0];
// $end = [6, 6];
$end = [70, 70];
$pattern = '/(\d+),(\d+)/';

$numberOfSteps = 1024;
for ($i = 0; $i  < $numberOfSteps; $i++) {
    $corruptedByte = $input[$i];
    if (preg_match($pattern, $corruptedByte, $matches)) {
        $visited[$matches[2]][$matches[1]] = true;
    }
}

$part1 = bfs($start, $end, $visited);

// part 2
$low = 0;
$high = count($input) - 1;

while ($low < $high) {
    $mid = intdiv($low + $high, 2);

    $visited = [];
    for ($i = 0; $i < $mid; $i++) {
        $corruptedByte = $input[$i];
        if (preg_match($pattern, $corruptedByte, $matches)) {
            $visited[$matches[2]][$matches[1]] = true;
        }
    }

    if (bfs($start, $end, $visited) === -1) {
        $high = $mid;
    } else {
        $low = $mid + 1;
    }
}
$low--;

echo "Fastest path after 1024 bytes (part 1): {$part1}\n";
echo "Number of bytes until there is no more path (part 2): " . $low . "  | Byte: " . $input[$low] . "\n";

echo "Execution time: " . round(microtime(true) - $start_time, 4) . " seconds\n";
echo "   Peak memory: " . round(memory_get_peak_usage() / pow(2, 20), 4), " MiB\n\n";
