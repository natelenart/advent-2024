<?php

function c($g, $i2, $i3, $i4, $j2, $j3, $j4) {
    if (isset($g[$i2][$j2]) && isset($g[$i3][$j3]) && isset($g[$i4][$j4])) {
        return $g[$i2][$j2] === 'M' && $g[$i3][$j3] === 'A' && $g[$i4][$j4] === 'S' ? 1 : 0;
    }

    return 0;
}

$fh = fopen('4-input.txt', 'r');

$grid = [];
do {
    $row = trim(fgets($fh));
    $grid[] = str_split($row);
} while ($row !== false && $row !== "");

fclose($fh);

$count = 0;
foreach ($grid as $i => $row) {
    foreach ($row as $j => $val) {
        if ($val !== 'X') {
            continue;
        }

        $count += c($grid, $i-1, $i-2, $i-3, $j, $j, $j); // up
        $count += c($grid, $i+1, $i+2, $i+3, $j, $j, $j); // down

        $count += c($grid, $i, $i, $i, $j-1, $j-2, $j-3); // back
        $count += c($grid, $i, $i, $i, $j+1, $j+2, $j+3); // forwards

        $count += c($grid, $i-1, $i-2, $i-3, $j-1, $j-2, $j-3); // up-back
        $count += c($grid, $i-1, $i-2, $i-3, $j+1, $j+2, $j+3); // up-forward

        $count += c($grid, $i+1, $i+2, $i+3, $j-1, $j-2, $j-3); // down-back
        $count += c($grid, $i+1, $i+2, $i+3, $j+1, $j+2, $j+3); // down-forwards
    }
}

echo 'Part A: ' . $count . PHP_EOL;


$count = 0;
foreach ($grid as $i => $row) {
    foreach ($row as $j => $val) {
        if ($val !== 'A') {
            continue;
        }

        if (!isset($grid[$i-1][$j-1])
            || !isset($grid[$i-1][$j+1])
            || !isset($grid[$i+1][$j-1])
            || !isset($grid[$i+1][$j+1])
        ) {
            continue;
        }

        if ($grid[$i-1][$j-1] === 'M') {
            if ($grid[$i-1][$j+1] === 'M') {
                if ($grid[$i+1][$j-1] === 'S' && $grid[$i+1][$j+1] === 'S') {
                    $count += 1;
                }
            }
            if ($grid[$i+1][$j-1] === 'M') {
                if ($grid[$i-1][$j+1] === 'S' && $grid[$i+1][$j+1] === 'S') {
                    $count += 1;
                }
            }
        }

        if ($grid[$i-1][$j-1] === 'S') {
            if ($grid[$i-1][$j+1] === 'S') {
                if ($grid[$i+1][$j-1] === 'M' && $grid[$i+1][$j+1] === 'M') {
                    $count += 1;
                }
            }
            if ($grid[$i+1][$j-1] === 'S') {
                if ($grid[$i-1][$j+1] === 'M' && $grid[$i+1][$j+1] === 'M') {
                    $count += 1;
                }
            }
        }
    }
}

echo 'Part B: ' . $count . PHP_EOL;