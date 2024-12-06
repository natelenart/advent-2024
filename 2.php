<?php

function p($arr) {
    echo implode(", ", $arr) . PHP_EOL;
}

function check($row) {
    $s = true;
    $dir = null;
    $prev = $row[0];
    foreach ($row as $idx => $num) {
        if ($idx === 0) {
            continue;
        }
        if (abs($num - $prev) > 3) {
            $s = false;
            break;
        }
        if ($idx === 1) {
            $dir = $num > $prev ? 'up' : 'down';
        }
        if ($num === $prev) {
            $s = false;
            break;
        }
        if ($num > $prev && $dir === 'down') {
            $s = false;
            break;
        }
        if ($num < $prev && $dir === 'up') {
            $s = false;
            break;
        }
        $prev = $num;
    }

    return $s;
}

$fh = fopen('2-input.txt', 'r');

$rows = [];

$line = false;
do {
    $line = fgets($fh);
    if ($line === false || trim($line) === "") {
        break;
    }
    $parts = explode(" ", $line);
    $rows[] = array_map(function($part) {
        return (int)$part;
    }, $parts);
} while ($line !== false);

fclose($fh);

$safe = 0;
foreach ($rows as $i => $row) {
    if (check($row)) {
        $safe += 1;
    }
}

echo 'Part A: ' . $safe . PHP_EOL;

$safe = 0;

// 681 is too low
// 707 is ... too low? I wasn't paying attention
// 738 is too high
foreach ($rows as $row) {
    if (check($row)) {
        $safe += 1;
        continue;
    }

    for ($idx = 0; $idx < count($row); $idx++) {
        $r = [];
        foreach ($row as $i => $item) {
            if ($i != $idx) {
                $r[] = $item;
            }
        }
        if (check($r)) {
            $safe += 1;
            break;
        }
    }
}

echo 'Part B: ' . $safe . PHP_EOL;
