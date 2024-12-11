<?php

// $filename = 'src/day07/input.test';
$filename = 'src/day07/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}
$result = 0;
foreach ($lines as $line) {
    $input = explode(":", $line);
    $input[1] = array_map('intval', explode(" ", trim($input[1]))) ;
    if (checkEvaluates(intval($input[0]), $input[1])) {
        $result += intval($input[0]);
    }
}
echo $result;

/**
 * @param array<int> $input
 */
function checkEvaluates(int $goalValue, array $input, int $currentValue = 0): bool
{
    if (count($input) === 0 || $currentValue > $goalValue) {
        return $currentValue === $goalValue;
    }
    $inputValue = $input[0];
    return (checkEvaluates($goalValue, array_slice($input, 1), $currentValue * $inputValue))
        || (checkEvaluates($goalValue, array_slice($input, 1), $currentValue + $inputValue))
        || (checkEvaluates($goalValue, array_slice($input, 1), intval($currentValue . $inputValue)))
    ;
}
