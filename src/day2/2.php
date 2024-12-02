<?php

$filename = 'src/day2/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}

$total_safe = 0;

/**
 * @param array<int> $array
 */
function checkAsc(array $array): bool
{
    for ($i = 0; $i < count($array) - 1; $i++) {
        $tmp = $array[$i + 1] - $array[$i];
        if (0 >= $tmp || $tmp > 3) {
            return false;
        }
    }
    return true;
}

/**
 * @param array<int> $array
 */
function checkDec(array $array): bool
{
    for ($i = 0; $i < count($array) - 1; $i++) {
        $tmp = $array[$i + 1] - $array[$i];
        if (0 <= $tmp || $tmp < -3) {
            return false;
        }
    }
    return true;
}

foreach ($lines as $line) {
    $line = array_map('intval', explode(' ', $line));
    $asending = true;
    $asc_error_index = [];
    $decending = true;
    $dec_error_index = [];

    for ($i = 0; $i < count($line) - 1; $i++) {
        $tmp = $line[$i + 1] - $line[$i];

        if ($asending && (0 >= $tmp || $tmp > 3)) {
            if (count($asc_error_index) > 1) {
                $asending = false;
            }
            $asc_error_index[] = $i;
            if ($i === count($line) - 2) {
                $asc_error_index[] = $i + 1;
            }
        }

        if ($decending && (0 <= $tmp || $tmp < -3)) {
            if (count($dec_error_index) > 1) {
                $decending = false;
            }
            $dec_error_index[] = $i;
            if ($i === count($line) - 2) {
                $dec_error_index[] = $i + 1;
            }
        }

        if (!$asending && !$decending) {
            break;
        }

        if ($i === count($line) - 2) {
            if ($asending) {
                if (count($asc_error_index) === 0) {
                    $total_safe += 1;
                    break;
                }
                foreach ($asc_error_index as $index) {
                    $tempLine = array_values(array_diff_key($line, [$index => $line[$index]]));
                    # TODO: Optimize: no need to check full line again => O(m*3n) -> O(m*n) numberOfLines*lineLength
                    if (checkAsc($tempLine)) {
                        $total_safe += 1;
                        break;
                    }
                }

            }
            if ($decending) {
                if (count($dec_error_index) === 0) {
                    $total_safe += 1;
                    break;
                }
                foreach ($dec_error_index as $index) {
                    # TODO: Optimize: no need to check full line again => O(m*3n) -> O(m*n)
                    $tempLine = array_values(array_diff_key($line, [$index => $line[$index]]));
                    if (checkDec($tempLine)) {
                        $total_safe += 1;
                        break;
                    }

                }
            }
        }
    }
}

echo 'Safe: ' . $total_safe . "\n";
