<?php

$fh = fopen('1-input.txt', 'r');

$a = [];
$b = [];

$line = false;
do {
    $line = fgets($fh);
    if ($line === false || trim($line) === "") {
        break;
    }
    $parts = explode(" ", $line);
    $a[] = (int)$parts[0];
    $b[] = (int)$parts[3];
} while ($line !== false);

fclose($fh);

sort($a);
sort($b);

$distance = 0;
for ($i = 0; $i<1000; $i++) {
    $dist = abs($b[$i]-$a[$i]);
    $distance += $dist;
}

echo 'Part A: ' . $distance . PHP_EOL;

$similarity = 0;

$set = array_count_values($b);
foreach ($a as $item) {
    $sim = $set[$item] ?? 0;
    if ($sim > 0) {
        $similarity += $item*$sim;
    }
}

echo 'Part B: ' . $similarity . PHP_EOL;
