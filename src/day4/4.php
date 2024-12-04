<?php

// $filename = 'src/day4/input.test';
$filename = 'src/day4/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}
/**
 * @param array<string> $lines
 */
function matchWord(array $lines, int $lineIndex, int $charIndex, string $word, int $dx, int $dy): bool
{
    global $lineLength;
    global $lineCount;

    for ($i = 0; $i < 4; $i++) {
        $x = $charIndex + $i * $dx;
        $y = $lineIndex + $i * $dy;

        // Check boundaries
        if ($y < 0 || $y >= $lineCount || $x < 0 || $x >= $lineLength) {
            return false;
        }

        if ($lines[$y][$x] !== $word[$i]) {
            return false;
        }
    }
    return true;
}

$lineLength = strlen($lines[0]);
$lineCount = count($lines);
$matchCount = 0;
$directions = [
    [0, 1],
    [1, 0],
    [1, 1],
    [1, -1],
];

# part 1
foreach ($lines as $lineIndex => $line) {
    foreach (str_split($line) as $charIndex => $char) {
        foreach ($directions as [$dx, $dy]) {
            if (matchWord($lines, $lineIndex, $charIndex, "XMAS", $dx, $dy)) {
                $matchCount++;
            }
            if (matchWord($lines, $lineIndex, $charIndex, "SAMX", $dx, $dy)) {
                $matchCount += 1;
            }

        }
    }
}
echo "Total matches: $matchCount\n";

# part 2
$total_pattern_matches = 0;
for ($lineIndex = 1; $lineIndex < $lineCount - 1; $lineIndex++) {
    $line = $lines[$lineIndex];
    for ($charIndex = 1; $charIndex < strlen($line) - 1; $charIndex++) {
        if ($line[$charIndex] === "A") {
            $corners = [
                $lines[$lineIndex - 1][$charIndex - 1],
                $lines[$lineIndex - 1][$charIndex + 1],
                $lines[$lineIndex + 1][$charIndex - 1],
                $lines[$lineIndex + 1][$charIndex + 1]
            ];
            if (
                count(array_filter($corners, fn ($x) => $x === "M")) === 2 &&
                count(array_filter($corners, fn ($x) => $x === "S")) === 2 &&
                $corners[0] !== $corners[3]
            ) {
                $total_pattern_matches += 1;
            }
        }
    }
}
echo "Total pattern matches: $total_pattern_matches\n";
