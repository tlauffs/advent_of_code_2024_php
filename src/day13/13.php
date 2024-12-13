<?php


function gcd(int $a, int $b): int
{
    while ($b != 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return abs($a);
}

/**
* @param array<string, array<int, int>> $machine
*/
function isSolutionPossible($machine): bool
{
    $Ax = $machine['A'][0];
    $Ay = $machine['A'][1];
    $Bx = $machine['B'][0];
    $By = $machine['B'][1];
    $Px = $machine['Prize'][0];
    $Py = $machine['Prize'][1];

    // greatest common denomiter
    $gcdX = gcd($Ax, $Bx);
    $gcdY = gcd($Ay, $By);

    return ($Px % $gcdX == 0) && ($Py % $gcdY == 0);
}

/**
* @param array<array<string, array<int, int>>> $machines
* @return array<int>
*/
function findMinimumTokens($machines): array
{
    $totalTokens = 0;
    $prizesWon = 0;

    foreach ($machines as $machine) {
        // check if solution is possible
        if (!isSolutionPossible($machine)) {
            continue;
        }

        list($x_a, $y_a) = $machine['A'];
        list($x_b, $y_b) = $machine['B'];
        list($x_p, $y_p) = $machine['Prize'];

        $minCost = PHP_INT_MAX;
        $foundSolution = false;

        $maxPresses = 100000;
        // Brute-force over button pressed
        for ($n_a = 0; $n_a <= $maxPresses; $n_a++) {
            for ($n_b = 0; $n_b <= $maxPresses; $n_b++) {
                // Check if the presses align with the prize
                $x_reached = $n_a * $x_a + $n_b * $x_b;
                $y_reached = $n_a * $y_a + $n_b * $y_b;

                if ($x_reached == $x_p && $y_reached == $y_p) {
                    $cost = 3 * $n_a + $n_b;
                    if ($cost < $minCost) {
                        $minCost = $cost;
                        $foundSolution = true;
                    }
                }
            }
        }

        if ($foundSolution) {
            $totalTokens += $minCost;
            $prizesWon++;
        }
    }

    return [
        'prizesWon' => $prizesWon,
        'totalTokens' => $totalTokens,
    ];
}

$filename = 'src/day13/input.test';
// $filename = 'src/day13/input';
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
            'Prize' => [(int)$matchPrize[1] + 10000000000000, (int)$matchPrize[2] + 10000000000000],
        ];
    }
}

$result = findMinimumTokens($machines);
echo "Prizes Won: " . $result['prizesWon'] . "\n";
echo "Total Tokens: " . $result['totalTokens'] . "\n";
