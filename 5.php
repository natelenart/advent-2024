<?php

$fh = fopen('5-input.txt', 'r');

$phase = 'rules';

$rules = [];
$updates = [];

do {
    $line = trim(fgets($fh));

    if (preg_match_all('#(.*)\|(.*)#', $line, $matches)) {
        $first = (int)$matches[1][0];
        $last = (int)$matches[2][0];
        if (! array_key_exists($first, $rules)) {
            $rules[$first] = [];
        }
        $rules[$first][] = $last;
    } elseif ($phase === 'rules') {
        $phase = 'updates';
    } elseif ($line !== '') {
        $updates[] = array_map(function($i) {
            return (int) $i;
        }, explode(",", $line));
    }
} while (
    empty($updates)
    || ($line !== '' && $line !== false)
);

fclose($fh);

$keep = [];
$discard = [];
foreach ($updates as $u) {
    $k = true;
    for ($i=1; $i < count($u); $i++) {
        $p = $u[$i-1];
        $x = $u[$i];

        if (isset($rules[$x])) {
            foreach ($rules[$x] as $r) {
                if ($r === $p) {
                    $k = false;
                }
            }
        }
    }

    if ($k) {
        $keep[] = $u;
    } else {
        $discard[] = $u;
    }
}

$sum = 0;
foreach ($keep as $k) {
    $mid = floor(count($k)/2);
    $sum += $k[$mid];
}

echo 'Part A: ' . $sum . PHP_EOL;

$fixed = [];
foreach ($discard as $u) {
    $c = count($u);
    for ($j=1; $j < $c; $j++) {
        for ($i=1; $i < $c; $i++) {
            $p = $u[$i-1];
            $x = $u[$i];

            if (isset($rules[$x])) {
                foreach ($rules[$x] as $r) {
                    if ($r === $p) {
                        $t = $x;
                        $u[$i] = $p;
                        $u[$i-1] = $t;
                    }
                }
            }
        }
    }

    $fixed[] = $u;
}

$sum = 0;
foreach ($fixed as $f) {
    $mid = floor(count($f)/2);
    $sum += $f[$mid];
}

echo 'Part B: ' . $sum . PHP_EOL;
