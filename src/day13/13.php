<?php

/**
 * Solves a system of linear equations:
 * A: [A_x, A_y], B: [B_x, B_y], Prize: [P_x, P_y]
 * Solves the system of equations: A_x * x + B_x * y = P_x and A_y * x + B_y * y = P_y
 *
 * @param array<array<int>> $machine
 */
function solveMachine(array $machine): int
{
    list($A_x, $A_y) = $machine['A'];
    list($B_x, $B_y) = $machine['B'];
    list($P_x, $P_y) = $machine['Prize'];

    // Calculate the determinant D of the coefficient matrix
    $D = $A_x * $B_y - $A_y * $B_x;

    // no solution
    if ($D == 0) {
        return 0;
    }

    // Calculate the determinant D_x for x
    $D_x = $P_x * $B_y - $P_y * $B_x;

    // Calculate the determinant D_y for y
    $D_y = $A_x * $P_y - $A_y * $P_x;

    // Calculate x and y
    $x = $D_x / $D;
    $y = $D_y / $D;

    echo 'x' . "  " . $x . 'y' . "  " . $y . "\n";

    if (is_int($x) && $x > 0 && is_int($y) && $y > 0) {
        return 3 * $x + $y;
    }
    return 0;
}

$filename = 'src/day13/input.test';
// $filename = 'src/day13/input.test2';
$filename = 'src/day13/input';
$lines = file_get_contents($filename);

if ($lines === false) {
    exit();
}

$inputArray = explode("\n\n", $lines);

$machines = [];
foreach ($inputArray as $entry) {
    if (preg_match('/Button A: X\+(\d+), Y\+(\d+)/', $entry, $matchA) &&
        preg_match('/Button B: X\+(\d+), Y\+(\d+)/', $entry, $matchB) &&
        preg_match('/Prize: X=(\d+), Y=(\d+)/', $entry, $matchPrize)) {

        $machines[] = [
            'A' => [(int)$matchA[1], (int)$matchA[2]],
            'B' => [(int)$matchB[1], (int)$matchB[2]],
          // part 1
            // 'Prize' => [(int)$matchPrize[1], (int)$matchPrize[2]],
            'Prize' => [(int)$matchPrize[1] + 10000000000000, (int)$matchPrize[2] + 10000000000000],
        ];
    }
}

$prizesWon = 0;
$totalTokens = 0;
foreach ($machines as $machine) {
    $result = solveMachine($machine);
    if ($result !== 0) {
        $prizesWon++;
        $totalTokens += $result;
    }
}
echo "Prizes Won: " . $prizesWon . "\n";
echo "Total Tokens: " . $totalTokens . "\n";
