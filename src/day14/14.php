<?php

/**
* @param array<array<int[]>> $robots
* @param array<int> $roomSize
*/
function calculateEndPositions(array $robots, array $roomSize): int
{
    $roomMidPoints = [ (int)floor($roomSize[0] / 2), (int)floor($roomSize[1] / 2) ];
    $steps = 100;
    $safetyFactor = [0,0,0,0];
    foreach ($robots as [$initialPosition, $velocity]) {
        // calculate position
        $endPosition = [
        ($initialPosition[0] + $steps * $velocity[0]) % ($roomSize[0] + 1),
        ($initialPosition[1] + $steps * $velocity[1]) % ($roomSize[1] + 1)
        ];

        // handle negatives
        $endPosition[0] = $endPosition[0] < 0 ? $endPosition[0] + $roomSize[0] + 1 : $endPosition[0];
        $endPosition[1] = $endPosition[1] < 0 ? $endPosition[1] + $roomSize[1] + 1 : $endPosition[1];
        // echo json_encode($initialPosition) .  " " . json_encode($velocity) . " " . json_encode($endPosition) . "\n";

        // top left quardrent
        if ($endPosition[0] < $roomMidPoints[0] && $endPosition[1] < $roomMidPoints[1]) {
            $safetyFactor[0] += 1;
            continue;
        }
        // top right quardrent
        if ($endPosition[0] > $roomMidPoints[0] && $endPosition[1] < $roomMidPoints[1]) {
            $safetyFactor[1] += 1;
            continue;
        }
        // bottom left quardrent
        if ($endPosition[0] < $roomMidPoints[0] && $endPosition[1] > $roomMidPoints[1]) {
            $safetyFactor[2] += 1;
            continue;
        }
        // bottom right quardrent
        if ($endPosition[0] > $roomMidPoints[0] && $endPosition[1] > $roomMidPoints[1]) {
            $safetyFactor[3] += 1;
            continue;
        }
    }
    return  array_reduce($safetyFactor, fn ($x, $y) => $x * $y, 1);

}
/**
* @param array<array<int[]>> $robots
* @param array<int> $roomSize
*/
function drawStep(array &$robots, array $roomSize, int $step): void
{
    $room = array_fill(0, $roomSize[1] + 1, array_fill(0, $roomSize[0] + 1, ' '));

    // @phpstan-ignore-next-line
    $image = imagecreatetruecolor($roomSize[0] + 1, $roomSize[1] + 1);
    $white = imagecolorallocate($image, 255, 255, 255); // white background
    $black = imagecolorallocate($image, 0, 0, 0); // color for grid lines or robot markers
    $robotColor = imagecolorallocate($image, 0, 0, 255);
    // @phpstan-ignore-next-line
    imagefill($image, 0, 0, $white);

    $robotPositions = [];
    foreach ($robots as &$robot) {
        list($initialPosition, $velocity) = $robot;
        $robotPositions[] = $initialPosition[0];

        // echo $initialPosition[0] . " " . $initialPosition[1] . "\n";
        // @phpstan-ignore-next-line
        imagesetpixel($image, $initialPosition[0], $initialPosition[1], $robotColor);
        $endPosition = [
            ($initialPosition[0] +  $velocity[0]) % ($roomSize[0] + 1),
            ($initialPosition[1]  + $velocity[1]) % ($roomSize[1] + 1)
            ];

        $endPosition[0] = $endPosition[0] < 0 ? $endPosition[0] + $roomSize[0] + 1 : $endPosition[0];
        $endPosition[1] = $endPosition[1] < 0 ? $endPosition[1] + $roomSize[1] + 1 : $endPosition[1];

        $robot[0] = $endPosition;
    }


    // only draw if min 8 connected robots -> can be christmas tree
    sort($robotPositions);
    $consecutive = 0;
    for ($i = 1; $i < count($robotPositions); $i++) {
        if ($robotPositions[$i] == $robotPositions[$i - 1] + 1) {
            $consecutive++;
            if ($consecutive >= 4) {
                $filename = sprintf("src/day14/images/step_%06d.png", $step);
                imagepng($image, $filename);
                break;
            }
        } else {
            $consecutive = 0;
        }
    }
    imagedestroy($image);
}

// $filename = 'src/day14/input.test';
$filename = 'src/day14/input';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    exit();
}

$robots = [];
$pattern = '/-?\d+,-?\d+/';
foreach ($lines as $robot) {
    if (preg_match_all($pattern, $robot, $matches)) {
        $robots[] = array_map(function ($x) {
            return array_map('intval', explode(',', $x));
        }, $matches[0]);
    }
}
$roomSize = [100,102];
// $roomSize = [6,10];

// part 1
$safetySum = calculateEndPositions($robots, $roomSize);
echo $safetySum . "\n";


//part 2 (draw steps)
$stepsToDraw = 10000;
echo "generating images...\n";
for ($i = 0; $i < $stepsToDraw; $i++) {
    drawStep($robots, $roomSize, $i);
}
echo "done\n";
