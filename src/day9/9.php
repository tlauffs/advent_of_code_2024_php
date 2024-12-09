<?php

function part1(string $text): void
{
    $input = array_map('intval', str_split($text));

    $blocks = [];
    $fileId = 0;
    foreach ($input as $index => $value) {
        if ($index % 2 === 0) {
            $blocks = array_merge($blocks, array_fill(0, $value, $fileId));
            $fileId += 1;
        }
        if ($index % 2 !== 0) {
            $blocks = array_merge($blocks, array_fill(0, $value, '.'));
        }
    }

    $startPointer = 0;
    $endPointer = count($blocks) - 1;

    $checksum = 0;

    while ($endPointer >= $startPointer) {
        if ($blocks[$startPointer] !== '.') {
            $checksum += $startPointer * $blocks[$startPointer];
            $startPointer++;
            continue;
        }

        while ($blocks[$endPointer] === '.') {
            $endPointer--;
        }
        if ($endPointer >= $startPointer) {
            $checksum += $startPointer * $blocks[$endPointer];
            $endPointer--;
            $startPointer++;
        }
    }
    echo $checksum . "\n";
}

function part2(string $text): void
{
    $blocks = [];
    $fileId = 0;
    foreach (str_split(trim($text)) as $index => $value) {
        if ($index % 2 === 0) {
            $blocks[] = [$fileId, intval($value), false];
            $fileId += 1;
        }
        if ($index % 2 !== 0) {
            $blocks[] = ['.', intval($value),false];
        }
    }

    $position = 0;
    $checksum = 0;
    for ($i = 0; $i < count($blocks); $i++) {
        $block = &$blocks[$i];

        // @phpstan-ignore-next-line
        if ($block[2]) {
            continue;
        }
        if ($block[0] !== ".") {
            $block[2] = true;

            # add to checksum
            for ($h = 0; $h < $block[1]; $h++) {
                $checksum += $block[0] * $position;
                $position++;
            }
            continue;
        }

        $freespace = $block[1];
        $block[2] = true;

        for ($j = count($blocks) - 1; $j >= 0 ; $j--) {
            $moveblock = &$blocks[$j];
            if ($moveblock[0] === ".") {
                continue;
            }
            // @phpstan-ignore-next-line
            if ($moveblock[1] <= $freespace && !$moveblock[2]) {
                # add to checksum
                for ($k = 0; $k < $moveblock[1]; $k++) {
                    $checksum += $moveblock[0] * $position;
                    $position++;
                }
                $moveblock[0] = ".";

                $freespace = $freespace - $moveblock[1];
                $j = count($blocks) - 1;
            }
            if ($freespace === 0) {
                break;
            }
        }
        $position += $freespace;
    }
    echo $checksum . "\n";
}

// $filename = 'src/day9/input.test';
$filename = 'src/day9/input';
$text = file_get_contents($filename);

if (!is_string($text)) {
    exit();
}

part1($text);
part2($text);
