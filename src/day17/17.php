<?php

function run(int $a, int $b, int $c, array $program): array
{
    $combo = function ($operand) use (&$a, &$b, &$c) {
        return match ($operand) {
            0, 1, 2, 3 => $operand,
            4 => $a,
            5 => $b,
            6 => $c,
            default => 7
        };
    };

    $i = 0;
    $out = [];
    while ($i < count($program)) {
        $op = $program[$i];
        $operand = $program[$i + 1];
        switch ($op) {
            case 0:
                $a = intdiv($a, pow(2, $combo($operand)));
                break;
            case 1:
                $b = $b ^ $operand;
                break;
            case 2:
                $b = $combo($operand) % 8;
                break;
            case 3:
                if ($a == 0) {
                    break;
                }
                $i = $operand;
                continue 2;
            case 4:
                $b = $b ^ $c;
                break;
            case 5:
                $out[] = $combo($operand) % 8;
                break;
            case 6:
                $b = intdiv($a, pow(2, $combo($operand)));
                break;
            case 7:
                $c = intdiv($a, pow(2, $combo($operand)));
                break;
        }
        $i += 2;
    }
    return $out;
}

function backtrack(int $input, int $position, array &$program): bool|int
{
    // programs match
    if ($position > count($program)) {
        return $input;
    }
    // try cases
    for ($i = 0; $i < 8; $i++) {
        // bitshift
        $_input = $input << 3 | $i;
        $out = run($_input, 0, 0, $program);
        if ($out == array_slice($program, -$position)) {
            $result = backtrack($_input, $position + 1, $program);
            if ($result !== false) {
                return $result;
            }
        }
    }
    return false;
}

memory_reset_peak_usage();
$start_time = microtime(true);

$file = file_get_contents("src/day17/input");
// $file = file_get_contents("src/day17/input.test");

if ($file === false) {
    return;
}

preg_match_all("/\d+/", $file, $input);

$program = array_map("intval", $input[0]);
$regA = array_shift($program);
$regB = array_shift($program);
$regC = array_shift($program);

if ($regA === null || $regB === null || $regC === null) {
    exit();
}

echo json_encode($program) ."\n" . $regA ."\n". $regB ."\n". $regC ."\n";

$part1 = 0;
$part1 = implode(",", run($regA, $regB, $regC, $program));

$part2 = 0;
$part2 = backtrack(0, 1, $program);

echo "program input (part 1): {$part1}\n";
echo "program input that returns original input (part 2): {$part2}\n";

echo "Execution time: ".round(microtime(true) - $start_time, 4)." seconds\n";
echo "   Peak memory: ".round(memory_get_peak_usage() / pow(2, 20), 4), " MiB\n\n";
