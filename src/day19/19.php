<?php

function countDesigns(string $design, array $towels, array &$memo): int
{
    if ($design === '') {
        return 1;
    }

    if (isset($memo[$design])) {
        return $memo[$design];
    }

    $totalWays = 0;

    // Try to match each towel pattern at the start of the design
    foreach ($towels as $pattern) {
        if (strpos($design, $pattern) === 0) {
            // Remove the matched part -> recursion for the rest of the design
            $remainingDesign = substr($design, strlen($pattern));
            $totalWays += countDesigns($remainingDesign, $towels, $memo);
        }
    }

    $memo[$design] = $totalWays;

    return $totalWays;
}

memory_reset_peak_usage();
$start_time = microtime(true);

// $filename = 'src/day19/input.test';
$filename = 'src/day19/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    echo "Failed to read input file.\n";
    return;
}

$towels = array_map('trim', explode(',', array_shift($lines) ?? ''));
$towelDesigns = $lines;

$pattern = "/^(" . implode("|", array_map('preg_quote', $towels)) . ")+$/";

$memo = [];

$possibleDesigns = 0;
$possibleDesignCombinations = 0;

foreach ($towelDesigns as $design) {
    // pure regex solution for part 1: works but is slow
    //
    // if (preg_match($pattern, $design)) {
    //     $possibleDesigns++;
    // }
    $result = countDesigns($design, $towels, $memo);
    if ($result > 0) {
        $possibleDesigns++;
    }
    $possibleDesignCombinations += countDesigns($design, $towels, $memo);

}

echo "Total possible designs (part1): {$possibleDesigns}\n";
echo "Total number of of ways to make all designs (part2): {$possibleDesignCombinations}\n";
echo "Execution time: " . round(microtime(true) - $start_time, 4) . " seconds\n";
echo "   Peak memory: " . round(memory_get_peak_usage() / pow(2, 20), 4) . " MiB\n\n";
