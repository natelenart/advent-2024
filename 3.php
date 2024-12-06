<?php

function mul($x, $y) {
    return (int)$x * (int)$y;
}

$contents = trim(file_get_contents('3-input.txt'));

$segments = [];
preg_match_all("#mul\(([0-9]{1,3}),([0-9]{1,3})\)#", $contents, $segments, PREG_SET_ORDER);

$total = 0;
foreach ($segments as $s) {
    $total += mul($s[1], $s[2]);
}

echo 'Part A: ' . $total . PHP_EOL;

$segments = [];
preg_match_all("#(do\(\)|don\'t\(\)|mul\(([0-9]{1,3}),([0-9]{1,3})\))#", $contents, $segments, PREG_SET_ORDER);

$total = 0;
$on = true;
foreach ($segments as $s) {
    if ($s[0] === 'do()') {
        $on = true;
    } elseif ($s[0] === 'don\'t()') {
        $on = false;
    } elseif ($on)  {
        $total += mul($s[2], $s[3]);
    }
}

echo 'Part B: ' . $total . PHP_EOL;