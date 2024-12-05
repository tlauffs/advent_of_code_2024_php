<?php

// $inputFile = 'src/day5/input.test';
$inputFile = 'src/day5/input';
$content = file_get_contents($inputFile);
if ($content === false) {
    exit();
}
$sections = preg_split("/\R\R+/", $content);
if ($sections === false) {
    exit();
}
$rules = array_map('trim', explode("\n", trim($sections[0])));
$inputs = array_map('trim', explode("\n", trim($sections[1])));

$formatedRules = [];
foreach ($rules as $rule) {
    [$left,$right] = explode('|', $rule);
    if (!isset($formatedRules[$left])) {
        $formatedRules[$left] = [];
    }
    $formatedRules[$left][] = $right;
}
$total = 0;
$totalUnordered = 0;
foreach ($inputs as $input) {
    $input = explode(',', $input);
    $missing = [];
    $rightOrder = true;
    $mid = intval(floor(count($input) / 2));
    foreach ($input as $value) {
        if (isset($formatedRules[$value])) {
            $missing = array_unique(array_merge($missing, $formatedRules[$value]));
        }
        $index = array_search($value, $missing);
        if ($index !== false) {
            unset($missing[$index]);
        }
    }
    #ordered
    if (empty(array_intersect($missing, $input))) {
        $total += $input[$mid];
        continue;
    }
    #unordered:
    foreach ($input as $value) {
        if (!isset($formatedRules[$value])) {
            continue;
        }
        $rules = $formatedRules[$value];
        if (count(array_intersect($rules, $input)) === $mid) {
            $totalUnordered += $value;
        }
    }
}
echo $total . "\n";
echo $totalUnordered . "\n";
